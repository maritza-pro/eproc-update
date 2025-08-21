<?php

declare(strict_types = 1);

namespace App\Filament\Resources\VendorResource\Widgets;

use App\Enums\VendorStatus;
use App\Filament\Resources\VendorResource\Pages\ListVendors;
use App\Models\Vendor;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class OverviewVendorWidget extends BaseWidget
{
    use InteractsWithPageTable;
    
    protected function getStats(): array
    {
        $data = Vendor::query()
        ->where('is_blacklisted', false)
        ->select('verification_status', DB::raw('count(*) as total'))
        ->groupBy('verification_status')
        ->pluck('total', 'verification_status');

        $approved = $data->get(VendorStatus::Approved->value) ?? 0;
        $pending = $data->get(VendorStatus::Pending->value) ?? 0;
        $rejected = $data->get(VendorStatus::Rejected->value) ?? 0;
        $blacklisted = Vendor::query()->where('is_blacklisted', true)->count();

        return [
            Stat::make('Approved', $approved),
            Stat::make('Pending', $pending),
            Stat::make('Rejected', $rejected),
            Stat::make('Blacklisted', $blacklisted),
        ];
    }

    protected function getTablePage(): string
    {
        return ListVendors::class;
    }
}
