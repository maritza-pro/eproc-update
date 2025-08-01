<?php

declare(strict_types = 1);

namespace App\Filament\Resources\BankResource\Pages;

use App\Filament\Resources\BankResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBank extends CreateRecord
{
    protected static string $resource = BankResource::class;
}
