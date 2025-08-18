<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Widgets;

use App\Enums\VendorStatus;
use App\Filament\Resources\VendorResource\Pages\ListVendors;
use App\Models\Vendor;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OverviewVendorWidget extends BaseWidget
{
    use InteractsWithPageTable;
    
    protected function getStats(): array
    {
        $data = Vendor::query();

        $approved = $data->where('verification_status', VendorStatus::Approved)->count();
        $pending = $data->where('verification_status', VendorStatus::Pending)->count();
        $rejected = $data->where('verification_status', VendorStatus::Rejected)->count();

        return [
            Stat::make('Approved', $approved),
            Stat::make('Pending', $pending),
            Stat::make('Rejected', $rejected),
        ];
    }

    protected function getTablePage(): string
    {
        return ListVendors::class;
    }
}
