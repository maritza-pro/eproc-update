<?php

declare(strict_types = 1);

namespace App\Console\Commands;

use Artisan;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;

class CreateFilament extends Command implements PromptsForMissingInput
{
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Filament resource';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:filament {resource}';

    /**
     * Prompt for missing input arguments using the returned questions.
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'resource' => ['Please provide the resource name:', 'E.g. User, Product'],
        ];
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $resource = $this->argument('resource');
        $resource = Str::of($resource)->headline()->studly()->toString();

        Artisan::call('make:filament-resource', [
            'name' => $resource,
            '--view' => true,
            '--generate' => true,
            '--soft-deletes' => true,
        ]);

        $this->info("Filament resource '{$resource}' created successfully.");
    }
}
