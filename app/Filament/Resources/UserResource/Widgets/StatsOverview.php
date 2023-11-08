<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\Scopes\IsActiveScope;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Ammount (Active Users)', "Rp. " . number_format(User::sum('ammount_balance'), 2, ', ', '.'))
                ->description('')
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('success'),
            Stat::make('Total Users (Active)', User::withoutGlobalScopes([IsActiveScope::class])->active(true)->count()),
            Stat::make('Total Users (Non-Active)', User::withoutGlobalScopes([IsActiveScope::class])->active(false)->count()),
            // Stat::make('Total Posts with Verify Users', Post::whereRelation('creator', 'is_verify', true)->count()),
        ];
    }
}
