<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Incoming Transactions', "Rp. " . number_format(Transaction::query()->type('in')->sum('ammount'), 2, ', ', '.'))
                ->description('')
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('success'),
            Stat::make('Total Outgoing Transactions', "Rp. " . number_format(Transaction::query()->type('out')->sum('ammount'), 2, ', ', '.'))
                ->description('')
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('danger'),
        ];
    }
}
