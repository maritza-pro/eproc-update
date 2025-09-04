<?php

declare(strict_types = 1);

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class MasterProcurements extends Cluster
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Procurement';

    protected static ?int $navigationSort = 2;

}
