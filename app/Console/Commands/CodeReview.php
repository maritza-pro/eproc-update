<?php

declare(strict_types=1);

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
        'bootstrap/cache',
        'app/Console/Commands',
        'bin',
        'config',
        'node_modules',
        'storage',
        'tests',
        'vendor',
    ];

    private array $excludedFiles = [
        '.ide.helper.php',
        '_ide_helper.php',
        '_ide_helper_models.php',
        '.ide.model.php',
        'rector.php',
    ];

    // private string $geminiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemma-3-27b-it:generateContent';
    private string $geminiEndpoint = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

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
            ])->post($this->geminiEndpoint . '?key=' . $apiKey, [
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

                return $this::FAILURE;
            }

            $rawReview = (string) ($response->json('candidates.0.content.parts.0.text') ?? '');
            $review = trim($rawReview);

            $this->info("📝 Review: {$review}");

            if ($this->isOkReview($review) || $review === '') {
                $code = $this->insertOkMarker($code);
                file_put_contents($file->getPathname(), $code);
                $this->info('✅ Marked as OK.');
                sleep(1);

                continue;
            }

            $todoItems = $this->extractTodoItems($review);

            if (empty($todoItems)) {
                $this->info('ℹ️  No actionable items detected → Mark as OK.');
                $code = $this->insertOkMarker($code);
                file_put_contents($file->getPathname(), $code);
                sleep(1);

                continue;
            }

            $code = $this->insertTodoMarker($code, $todoItems);
            file_put_contents($file->getPathname(), $code);

            $this->info('🛠️  Marked with TODO list.');

            sleep(4);
        }

        return $this::SUCCESS;
    }

    private function extractTodoItems(string $review): array
    {
        $items = [];

        foreach (preg_split('/\R/u', $review) ?: [] as $line) {
            $trim = trim($line);

            if ($trim === '') {
                continue;
            }

            if (preg_match('/^(?:-|\*)\s+(.+)$/u', $trim, $m)) {
                $items[] = trim($m[1]);

                continue;
            }

            if (preg_match('/^\d+\.\s+(.+)$/u', $trim, $m)) {
                $items[] = trim($m[1]);

                continue;
            }

            if (stripos($trim, 'TODO:') === 0) {
                $items[] = trim((string) preg_replace('/^TODO:\s*/i', '', $trim));

                continue;
            }
        }

        // Unik + rapikan
        return array_values(array_unique(array_map(fn (string $v): string => rtrim($v, '.'), $items)));
    }

    private function getPrompt(): string
    {
        return <<<'PROMPT'
Anda adalah reviewer senior Laravel Filament.
Tinjau kode berikut dan berikan hasil review singkat.

Jika kode sudah baik dan tidak ada yang perlu diperbaiki, jawab hanya dengan:
OK

Jika ADA isu **critical**, jawab hanya dalam bentuk daftar poin (satu poin per baris) tanpa awalan angka:
- Jelaskan singkat apa yang perlu diperbaiki
- Jangan menuliskan isu non-critical (minor/major) dalam output.

Jangan gunakan simbol selain "- " di awal baris untuk setiap poin. Jangan bungkus dengan markdown/code fence.

Rule:
- PSR-12 compliance
- Laravel filament best practices
- Hindari penggunaan DB::raw
- Maintainability and readability
- Potential improvements or simplifications
- Risks or hidden bugs (especially cross-DB differences)
- Reusability / cleaner approach

BAHASA:
- Bahasa Indonesia profesional, ringkas, to the point.

BATASAN & HEURISTIK (agar fokus dan tidak false positive):
- Penggunaan Eloquent `value('id')` untuk mengambil satu kolom adalah BENAR dan BUKAN isu (jangan sarankan `first()`/`firstOrFail()` kecuali ada kebutuhan akses field lain atau perlu exception semantics).
- Penggunaan DB::transaction untuk mengelompokkan transaksi adalah BENAR dan BUKAN isu.
- Penggunaan Gate::defineGates insteadof HasHexaLite adalah BENAR dan BUKAN isu.
- Hardcoded string seperti `'User'` BUKAN isu kritikal KECUALI konteks multi-tenant/i18n/konfigurasi dinamis sehingga berpotensi salah role. Jika tidak terbukti, abaikan.
- Jangan menilai gaya wording notifikasi, preferensi naming, atau saran kosmetik sebagai kritikal.
- Jangan sarankan DB::raw, hindari penggunaan DB::raw (flag jika ada).

Definisi **critical** (hanya keluarkan ini):
- Pelanggaran fatal PSR-12 atau bug yang bisa bikin error runtime atau ubah perilaku data
- Antipattern Filament yang bikin form/table/aksi gagal jalan atau menyebabkan data corruption
- Masalah kompatibilitas DB (MySQL/PostgreSQL) yang bisa bikin migration/query fail atau data salah
- Isu performa berat (N+1 jelas, missing index pada kolom yang jelas dipakai filter/join besar)
- Masalah security (mass assignment berbahaya, authorization bolong di Action/Page/RelationManager)
- Hal yang menghambat deploy/CI/CD (misconfig env/queue/cache yang pasti gagal)

Kode:

PROMPT;
    }

    private function insertOkMarker(string $code): string
    {
        $marker = "/** @CodeReview:OK */\n\n";

        if (str_contains($code, '/** @CodeReview:OK */')) {
            return $code;
        }

        if (str_starts_with($code, '<?php')) {
            return (string) preg_replace('/^<\?php\s*/', "<?php\n\n{$marker}", $code, 1);
        }

        return $marker . $code;
    }

    private function insertTodoMarker(string $code, array $items): string
    {
        $lines = array_map(
            fn (string $i): string => ' * - ' . trim($i, " \t\n\r\0\x0B"),
            array_filter($items, fn ($v): bool => $v !== '')
        );

        $comment = "/** @CodeReview:TODO \n*\n" .
            implode("\n", $lines) .
            "\n*/\n\n";

        if (str_starts_with($code, '<?php')) {
            return (string) preg_replace('/^<\?php\s*/', "<?php\n\n{$comment}", $code, 1);
        }

        return $comment . $code;
    }

    private function isExcluded(string $path): bool
    {
        foreach ($this->excludedDirectories as $excluded) {
            if (str_contains($path, DIRECTORY_SEPARATOR . $excluded . DIRECTORY_SEPARATOR)) {
                return true;
            }
        }

        return array_any(
            $this->excludedFiles,
            fn (string $excludedFile): bool => str_ends_with($path, DIRECTORY_SEPARATOR . $excludedFile)
                || basename($path) === $excludedFile
        );
    }

    private function isOkReview(string $review): bool
    {
        $clean = $this->normalizeOk($review);

        return $clean === 'ok';
    }

    private function normalizeOk(string $text): string
    {
        $t = trim($text);
        $t = (string) preg_replace('/^```[a-zA-Z0-9]*\s*|\s*```$/m', '', $t);
        $t = str_replace('`', '', $t);
        $t = str_replace(['**', '*', '"', "'"], '', $t);

        return strtolower(trim((string) preg_replace('/[^a-z]/', '', $t)));
    }
}
