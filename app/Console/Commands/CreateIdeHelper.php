<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CreateIdeHelper extends Command
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make IDE helper';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:ide-helper';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        if (app()->isLocal()) {
            $this->info('Generating IDE Helper Files...');
            Artisan::call('ide-helper:generate');
            $this->info('Generating IDE Helper Models Files...');
            Artisan::call('ide-helper:models', [
                '--nowrite' => true,
            ]);
        }
    }
}
