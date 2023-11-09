<?php

namespace App\Filament\Resources\TransactionResource\Widgets;

use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TransactionOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $in = Transaction::query()->type('in')->sum('ammount');
        $out = Transaction::query()->type('out')->sum('ammount');
        $total = $in - $out;
        return [
            Stat::make('Total Incoming Transactions', "Rp. " . number_format($in, 2, ', ', '.'))
                ->description('')
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('success'),
            Stat::make('Total Outgoing Transactions', "Rp. " . number_format($out, 2, ', ', '.'))
                ->description('')
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('danger'),
            Stat::make('Total Transactions (In - Out)', "Rp. " . number_format($total, 2, ', ', '.'))
                ->description('')
                ->descriptionIcon('heroicon-o-credit-card')
                ->color('info'),
        ];
    }
}
