<?php

namespace App\Filament\Widgets;

use App\Models\TransactionType;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TransactionSumByTypeBarChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static string $chartId = 'transactionSumByTypeBarChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Total Ammount Transaction By Type';

	protected static ?string $loadingIndicator = 'Loading...';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $year = date('Y');

        $q1 = TransactionType::query()
        ->selectRaw("transaction_types.type AS x,
        (
            SELECT CEIL(SUM(b.ammount)) FROM transactions b
            WHERE transaction_types.id = b.transaction_type_id
            AND EXTRACT(YEAR FROM b.created_at) = $year
            AND EXTRACT(MONTH FROM b.created_at) = 1
        ) AS y")
        ->groupBy(['transaction_types.id', 'transaction_types.type'])
        ->get()
        ->toArray();

        $q2 = TransactionType::query()
        ->selectRaw("transaction_types.type AS x,
        (
            SELECT CEIL(SUM(b.ammount)) FROM transactions b
            WHERE transaction_types.id = b.transaction_type_id
            AND EXTRACT(YEAR FROM b.created_at) = $year
            AND EXTRACT(MONTH FROM b.created_at) = 2
        ) AS y")
        ->groupBy(['transaction_types.id', 'transaction_types.type'])
        ->get()
        ->toArray();

        $q3 = TransactionType::query()
        ->selectRaw("transaction_types.type AS x,
        (
            SELECT CEIL(SUM(b.ammount)) FROM transactions b
            WHERE transaction_types.id = b.transaction_type_id
            AND EXTRACT(YEAR FROM b.created_at) = $year
            AND EXTRACT(MONTH FROM b.created_at) = 3
        ) AS y")
        ->groupBy(['transaction_types.id', 'transaction_types.type'])
        ->get()
        ->toArray();

        $q4 = TransactionType::query()
        ->selectRaw("transaction_types.type AS x,
        (
            SELECT CEIL(SUM(b.ammount)) FROM transactions b
            WHERE transaction_types.id = b.transaction_type_id
            AND EXTRACT(YEAR FROM b.created_at) = $year
            AND EXTRACT(MONTH FROM b.created_at) = 4
        ) AS y")
        ->groupBy(['transaction_types.id', 'transaction_types.type'])
        ->get()
        ->toArray();

        $q5 = TransactionType::query()
        ->selectRaw("transaction_types.type AS x,
        (
            SELECT CEIL(SUM(b.ammount)) FROM transactions b
            WHERE transaction_types.id = b.transaction_type_id
            AND EXTRACT(YEAR FROM b.created_at) = $year
            AND EXTRACT(MONTH FROM b.created_at) = 5
        ) AS y")
        ->groupBy(['transaction_types.id', 'transaction_types.type'])
        ->get()
        ->toArray();

        $q6 = TransactionType::query()
        ->selectRaw("transaction_types.type AS x,
        (
            SELECT CEIL(SUM(b.ammount)) FROM transactions b
            WHERE transaction_types.id = b.transaction_type_id
            AND EXTRACT(YEAR FROM b.created_at) = $year
            AND EXTRACT(MONTH FROM b.created_at) = 6
        ) AS y")
        ->groupBy(['transaction_types.id', 'transaction_types.type'])
        ->get()
        ->toArray();

        $q7 = TransactionType::query()
        ->selectRaw("transaction_types.type AS x,
        (
            SELECT CEIL(SUM(b.ammount)) FROM transactions b
            WHERE transaction_types.id = b.transaction_type_id
            AND EXTRACT(YEAR FROM b.created_at) = $year
            AND EXTRACT(MONTH FROM b.created_at) = 7
        ) AS y")
        ->groupBy(['transaction_types.id', 'transaction_types.type'])
        ->get()
        ->toArray();

        $q8 = TransactionType::query()
        ->selectRaw("transaction_types.type AS x,
        (
            SELECT CEIL(SUM(b.ammount)) FROM transactions b
            WHERE transaction_types.id = b.transaction_type_id
            AND EXTRACT(YEAR FROM b.created_at) = $year
            AND EXTRACT(MONTH FROM b.created_at) = 8
        ) AS y")
        ->groupBy(['transaction_types.id', 'transaction_types.type'])
        ->get()
        ->toArray();

        $q9 = TransactionType::query()
        ->selectRaw("transaction_types.type AS x,
        (
            SELECT CEIL(SUM(b.ammount)) FROM transactions b
            WHERE transaction_types.id = b.transaction_type_id
            AND EXTRACT(YEAR FROM b.created_at) = $year
            AND EXTRACT(MONTH FROM b.created_at) = 9
        ) AS y")
        ->groupBy(['transaction_types.id', 'transaction_types.type'])
        ->get()
        ->toArray();

        $q10 = TransactionType::query()
        ->selectRaw("transaction_types.type AS x,
        (
            SELECT CEIL(SUM(b.ammount)) FROM transactions b
            WHERE transaction_types.id = b.transaction_type_id
            AND EXTRACT(YEAR FROM b.created_at) = $year
            AND EXTRACT(MONTH FROM b.created_at) = 10
        ) AS y")
        ->groupBy(['transaction_types.id', 'transaction_types.type'])
        ->get()
        ->toArray();

        $q11 = TransactionType::query()
        ->selectRaw("transaction_types.type AS x,
        (
            SELECT CEIL(SUM(b.ammount)) FROM transactions b
            WHERE transaction_types.id = b.transaction_type_id
            AND EXTRACT(YEAR FROM b.created_at) = $year
            AND EXTRACT(MONTH FROM b.created_at) = 11
        ) AS y")
        ->groupBy(['transaction_types.id', 'transaction_types.type'])
        ->get()
        ->toArray();

        $q12 = TransactionType::query()
        ->selectRaw("transaction_types.type AS x,
        (
            SELECT CEIL(SUM(b.ammount)) FROM transactions b
            WHERE transaction_types.id = b.transaction_type_id
            AND EXTRACT(YEAR FROM b.created_at) = $year
            AND EXTRACT(MONTH FROM b.created_at) = 12
        ) AS y")
        ->groupBy(['transaction_types.id', 'transaction_types.type'])
        ->get()
        ->toArray();

        $data_in = [$q1[0], $q2[0], $q3[0], $q4[0], $q5[0], $q6[0], $q7[0], $q8[0], $q9[0], $q10[0], $q11[0], $q12[0]];
        $data_out = [$q1[1], $q2[1], $q3[1], $q4[1], $q5[1], $q6[1], $q7[1], $q8[1], $q9[1], $q10[1], $q11[1], $q12[1]];

        // dd(json_encode($data_in));

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Incoming Transactions (In)',
                    'data' => $data_in,
                ],
                [
                    'name' => 'Outgoing Transactions (Out)',
                    'data' => $data_out,
                ],
            ],
            'xaxis' => [
                'categories' => ['January', 'Febuary', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['rgb(232, 121, 249)', 'rgb(132, 21, 149)'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
        ];
    }
}
