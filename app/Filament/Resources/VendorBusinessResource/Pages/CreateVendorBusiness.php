<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorBusinessResource\Pages;

use App\Filament\Resources\VendorBusinessResource;
use Filament\Resources\Pages\CreateRecord;

class CreateVendorBusiness extends CreateRecord
{
    protected static string $resource = VendorBusinessResource::class;
}
