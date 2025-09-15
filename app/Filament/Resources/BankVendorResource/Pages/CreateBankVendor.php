<?php

declare(strict_types=1);

namespace App\Filament\Resources\BankVendorResource\Pages;

use App\Filament\Resources\BankVendorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBankVendor extends CreateRecord
{
    protected static string $resource = BankVendorResource::class;
}
