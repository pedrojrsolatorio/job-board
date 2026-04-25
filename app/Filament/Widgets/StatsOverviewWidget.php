<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\JobListing;
use App\Models\JobApplication;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->icon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Active Jobs', JobListing::where('status', 'active')->count())
                ->icon('heroicon-o-briefcase')
                ->color('success'),

            Stat::make('Applications', JobApplication::count())
                ->icon('heroicon-o-document-text')
                ->color('warning'),

            Stat::make('Revenue', '$' . number_format(
                Payment::where('status', 'completed')->sum('amount') / 100,
                2
            ))
                ->icon('heroicon-o-currency-dollar')
                ->color('success'),
        ];
    }
}
