<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class TransactionSumByTypePieChart extends ApexChartWidget
{
	/**
	 * Chart Id
	 *
	 * @var string
	 */
	protected static string $chartId = 'transactionSumByTypePieChart';

	/**
	 * Widget Title
	 *
	 * @var string|null
	 */
	protected static ?string $heading = 'Number of Transactions by Type';

	protected static ?string $loadingIndicator = 'Loading...';

	// protected function getFooter(): string|View
	// {
	// 	return new HtmlString('<p class="text-danger-500">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>');
	// }

	/**
	 * Chart options (series, labels, types, size, animations...)
	 * https://apexcharts.com/docs/options
	 *
	 * @return array
	 */
	protected function getOptions(): array
	{
		$year = date('Y');
		$datas = Transaction::query()
			->select(DB::Raw('tt.name, COUNT(transactions.id) AS value'))
			->join('transaction_types AS tt', 'transactions.transaction_type_id', '=', 'tt.id')
			->where(DB::raw('EXTRACT(YEAR FROM transactions.created_at)'), $year)
			->groupBy('tt.name')
			->orderBy('tt.name', 'ASC')
			->get();

		// dd($datas);

		foreach ($datas as $data)
		{
			$series[] = ceil($data->value);
			$labels[] = $data->name;
		}

		// dd($labels);

		return [
			'chart' => [
				'type' => 'pie',
				'height' => 300,
			],
			'series' => $series,
			'labels' => $labels,
			'legend' => [
				'labels' => [
					'fontFamily' => 'inherit',
				],
			],
			'colors' => ['#007bff', '#E91E63']
		];
	}
}
