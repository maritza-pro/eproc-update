<?php

declare(strict_types=1);

namespace App\Filament\Resources\BidItemResource\Pages;

use App\Filament\Resources\BidItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBidItem extends CreateRecord
{
    protected static string $resource = BidItemResource::class;
}
