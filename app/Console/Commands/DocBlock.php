<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DocBlock extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'PHP Documentation Block';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'docblock {--dry-run : Simulate and show auto-generated docblocks} {--write} : Write auto-generated docblocks';

    private array $excludedDirectories = [
        'vendor',
        'node_modules',
        'storage',
        'bootstrap/cache',
        'tests',
    ];

    private array $excludedFiles = [
        '.ide.helper.php',
    ];

    private string $geminiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemma-3-27b-it:generateContent';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (empty(config('app.gemini_api_key'))) {
            $this->error('Missing Gemini API key');

            return 1;
        }

        $missing = 0;
        $this->info('Scanning...');
        $basePath = base_path();

        $rii = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $rii = iterator_to_array($rii, false);
        usort($rii, fn ($a, $b): int => strcmp($a->getPathname(), $b->getPathname()));

        $dryRun = $this->option('dry-run');
        $write = $this->option('write');

        foreach ($rii as $file) {
            if (! $file->isFile()) {
                continue;
            }

            if ($file->getExtension() !== 'php') {
                continue;
            }

            if ($this->isExcluded($file->getPathname())) {
                continue;
            }

            $this->info('→ Scanning: ' . $file->getPathname());
            $lines = file($file->getPathname());
            $fileContent = file_get_contents($file->getPathname());
            // TODO : @vheins mang tolong yang ini ya buat phpstannya
            $lineCount = count($lines);
            $modified = false;

            for ($i = 0; $i < $lineCount; $i++) {
                $line = $lines[$i];

                if (preg_match('/^\s*(public|protected|private)?\s*function\s+\w+/', $line)) {
                    $prevLine = $lines[$i - 1] ?? '';

                    if (! preg_match('/.*\*\/.*/m', $prevLine)) {
                        $missing++;
                        $this->warn("Missing docblock: {$file->getPathname()}: Line " . ($i + 1));
                        $this->warn('                 → ' . trim($line));

                        // Extract full function
                        $functionCode = $lines[$i];
                        $j = $i + 1;
                        $braceCount = substr_count($lines[$i], '{') - substr_count($lines[$i], '}');

                        while ($braceCount > 0 && $j < $lineCount) {
                            $functionCode .= $lines[$j];
                            $braceCount += substr_count($lines[$j], '{') - substr_count($lines[$j], '}');
                            $j++;
                        }

                        if ($dryRun || $write) {
                            // TODO : @vheins mang tolong yang ini ya buat phpstannya
                            $docblock = $this->generateDocBlockWithGeminiAPI($fileContent, $functionCode); // ?? $this->generateDocBlockWithOllama($command, $fileContent, $functionCode);

                            if ($docblock) {
                                if ($dryRun) {
                                    $this->info('  ┌ Generated DocBlock:');

                                    foreach (explode("\n", $docblock) as $docLine) {
                                        $this->info('  │ ' . rtrim($docLine));
                                    }

                                    $this->info('  └');
                                }

                                if ($write) {
                                    $missing--;
                                    $indentedDocblock = $this->indentDocBlock($docblock, $line);
                                    array_splice($lines, $i, 0, $indentedDocblock);
                                    $modified = true;
                                    $i += count($indentedDocblock); // Skip newly inserted docblock
                                    $lineCount = count($lines); // Update line count
                                }
                            } else {
                                $this->error('  [!] No DocBlock returned from LLM');
                            }
                        }

                    }
                }
            }

            if ($write && $modified) {
                file_put_contents($file->getPathname(), implode('', $lines));
                $this->info("→ Updated: {$file->getPathname()}");
                $this->fixStyle($file->getPathname());
                $this->info("→ Fixed Style: {$file->getPathname()}");
            }
        }

        $this->info('Done!');

        if ($missing > 0) {
            $this->error("Missing DocBlock: {$missing}");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Fixes the style of a PHP file.
     *
     * Runs pint to format the file.
     */
    private function fixStyle(string $path): void
    {
        Process::run("vendor/bin/pint {$path}");
    }

    /**
     * Generates a DocBlock using the Gemini API.
     *
     * This function sends the file content and function code to the Gemini API to generate a PHPDoc block.
     */
    private function generateDocBlockWithGeminiAPI(string $fileContent, string $functionCode): ?string
    {
        $this->info('→ Generate DocBlock with Gemini API');
        $prompt = <<<PROMPT
You are given a PHP class file:

{$fileContent}

Generate a clean PHPDoc block for the following function:

{$functionCode}

The doc block must include only:
- A one-line title (short summary).
- A short description (1 concise sentences max 100 characters).

There must be exactly **one blank lines** between the title and the description.

Do NOT include any @param, @return, or other information.

Return only the PHPDoc block, starting with /** and ending with */. Do not include any extra text or explanation.
PROMPT;

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($this->geminiEndpoint . '?key=' . config('app.gemini_api_key'), [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
        ]);

        $json = $response->json();

        if ($response->ok() && isset($json['candidates'][0]['content']['parts'][0]['text'])) {
            $docblock = trim($json['candidates'][0]['content']['parts'][0]['text']);
            sleep(4);

            return $this->getDocBlock($docblock);
        }

        return null;
    }

    /**
     * Extracts the DocBlock from a given text.
     *
     * @param  string  $text  The text to extract the DocBlock from.
     * @return string|null The extracted DocBlock, or null if not found.
     */
    private function getDocBlock(string $text): ?string
    {
        $start = strpos($text, '/**');
        $end = strrpos($text, '*/');

        if ($start === false || $end === false) {
            return null;
        }

        return substr($text, $start, $end - $start + 2);
    }

    /**
     * Indents a DocBlock to match the indentation of the code line.
     *
     * @param  string  $docblock  The DocBlock to indent.
     * @param  string  $codeLine  The code line to match the indentation to.
     * @return array The indented DocBlock as an array of lines.
     */
    private function indentDocBlock(string $docblock, string $codeLine): array
    {
        preg_match('/^\s*/', $codeLine, $matches);
        $indent = $matches[0] ?? '';

        return array_map(
            fn (string $line): string => $indent . rtrim($line) . PHP_EOL,
            explode("\n", $docblock)
        );
    }

    /**
     * Check if a given path is excluded.
     *
     * @param  string  $path  The path to check.
     */
    private function isExcluded(string $path): bool
    {
        foreach ($this->excludedDirectories as $excluded) {
            if (str_contains($path, DIRECTORY_SEPARATOR . $excluded . DIRECTORY_SEPARATOR)) {
                return true;
            }
        }

        return array_any($this->excludedFiles, fn ($excludedFile): bool => str_ends_with($path, DIRECTORY_SEPARATOR . $excludedFile) || basename($path) === $excludedFile);
    }
}
