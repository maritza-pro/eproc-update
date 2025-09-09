<?php

declare(strict_types = 1);

namespace App\Filament\Resources\BusinessFieldResource\Pages;

use App\Filament\Resources\BusinessFieldResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBusinessField extends CreateRecord
{
    protected static string $resource = BusinessFieldResource::class;
}
