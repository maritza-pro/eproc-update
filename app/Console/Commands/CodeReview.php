<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CodeReview extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Review code for best practices and improvements';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'code-review {--path= : Path to directory}';

    private array $excludedDirectories = [
        'vendor',
        'node_modules',
        'storage',
        'bootstrap/cache',
        'tests',
    ];

    private array $excludedFiles = [
        '.ide.helper.php',
        '.ide.model.php',
    ];

    private string $geminiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemma-3-27b-it:generateContent';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $apiKey = config('app.gemini_api_key');

        if (! $apiKey) {
            $this->error('GEMINI_API_KEY belum diatur di .env');

            return $this::FAILURE;
        }

        $missing = 0;
        $this->info('Scanning...');
        $basePath = base_path();

        $rii = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        $rii = iterator_to_array($rii, false);
        usort($rii, fn ($a, $b): int => strcmp($a->getPathname(), $b->getPathname()));

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

            $this->info("\n📄 Review file : {$file->getPathname()}");
            $code = (string) file_get_contents($file->getPathname());

            if (str_contains($code, '@CodeReview')) {
                $this->warn('⚠️  File has already been reviewed, skip.');

                continue;
            }
            $prompt = $this->getPrompt() . $code;
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

            if ($response->failed()) {
                $this->error('🚨 Error: ' . $response->body());

                exit();
            }

            $review = trim($response->json('candidates.0.content.parts.0.text') ?? '');
            $this->info("📝 Review: {$review}");

            if (strtolower($review) === 'ok') {
                $marker = "/** @CodeReview:OK */\n\n";

                if (str_starts_with($code, '<?php')) {
                    $code = preg_replace('/^<\?php\s*/', "<?php\n\n" . (string) $marker, $code, 1);
                } else {
                    $code = "{$marker}{$code}";
                }
                file_put_contents($file->getPathname(), $code);

                continue;
            }

            if (strtolower($review) === 'ok') {
                $marker = "/** @CodeReview:OK */\n\n";
                $code = preg_replace('/^<\?php\s*/', "<?php\n\n" . $marker, $code, 1);
                file_put_contents($file->getPathname(), $code);

                continue;
            }

            if ($review !== '') {
                $lines = explode("\n", $review);
                $docblockLines = [];
                $todoLines = [];

                foreach ($lines as $line) {
                    if (preg_match('/^\s*5\./', $line) || stripos($line, 'TODO:') !== false) {
                        $todoLines[] = preg_replace('/^\s*5\.\s*/', '', $line);
                    } else {
                        $docblockLines[] = $line;
                    }
                }

                $comment = "/**\n * @CodeReview\n" .
                           collect($docblockLines)
                               ->map(fn ($line) => ' * ' . trim($line))
                               ->implode("\n") .
                           "\n */\n\n";

                if (! empty($todoLines)) {
                    foreach ($todoLines as $todo) {
                        // @phpstan-ignore argument.type
                        $comment .= '// TODO: ' . trim((string) preg_replace('/^TODO:\s*/i', '', $todo)) . "\n";
                    }
                    $comment .= "\n";
                }

                // Sisipkan setelah <?php
                $code = preg_replace('/^<\?php\s*/', "<?php\n\n" . $comment, $code, 1);

                file_put_contents($file->getPathname(), $code);
                sleep(3);
            }
        }

        return $this::SUCCESS;
    }

    private function getPrompt(): string
    {
        return <<<'PROMPT'
Anda adalah reviewer senior Laravel.
Tinjau kode berikut dan berikan hasil review singkat, berikan TODO jika ada yang perlu diperbaiki.

Jika kode sudah baik dan tidak ada yang perlu diperbaiki, jawab hanya dengan "OK" jangan tambahkan komentar apapun dan hilangkan poin-poin.
Jawab **dalam bahasa Indonesia**, singkat, to the point dan wajib plaint text tanpa symbol apapun.

Kode:

PROMPT;

    }

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
