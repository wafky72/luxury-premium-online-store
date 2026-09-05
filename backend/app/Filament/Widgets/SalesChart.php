<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SalesChart extends ChartWidget
{
    protected ?string $heading = 'Monthly Sales Growth';
    
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Simple mock trend if Trend package is not installed, but I'll assume standard counts
        $data = Order::selectRaw('DATE(created_at) as date, SUM(total) as sum')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Daily Sales (BDT)',
                    'data' => $data->map(fn ($value) => $value->sum),
                    'fill' => 'start',
                    'tension' => 0.4,
                    'borderColor' => '#fbbf24',
                    'backgroundColor' => 'rgba(251, 191, 36, 0.1)',
                ],
            ],
            'labels' => $data->map(fn ($value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
