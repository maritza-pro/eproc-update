<?php

declare(strict_types=1);

namespace App\Filament\Resources\BidResource\Pages;

use App\Filament\Resources\BidResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBid extends CreateRecord
{
    protected static string $resource = BidResource::class;
}
