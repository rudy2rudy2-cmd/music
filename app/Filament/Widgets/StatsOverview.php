<?php

namespace App\Filament\Widgets;

use App\Models\License;
use App\Models\Platform;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Total Platforms', Platform::count())
                ->description('Active and inactive platforms')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),
            Stat::make('Total Licenses', License::count())
                ->description('Total sold licenses')
                ->descriptionIcon('heroicon-m-key')
                ->color('warning'),
            Stat::make('Total Revenue', '$' . number_format(License::join('platforms', 'licenses.platform_id', '=', 'platforms.id')->sum('platforms.price'), 2))
                ->description('Estimated revenue')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
