<?php

namespace App\Filament\Widgets;

use App\Models\Kyc;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class KycOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total KYC Request', Kyc::count()),
            Stat::make('Pending KYC Request', Kyc::where('status', 'pending')->count()),
            Stat::make('Approved KYC Request', Kyc::where('status', 'approved')->count()),
        ];
    }
}
