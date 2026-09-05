<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class OrderStatusChart extends ChartWidget
{
    protected ?string $heading = 'Orders by Status';
    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $data = \App\Models\Order::query()
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $labels = [
            'pending' => 'Pending',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'returned' => 'Returned',
        ];

        $chartData = [];
        $chartLabels = [];
        $colors = [];

        $colorMap = [
            'pending' => '#f59e0b',    // amber
            'confirmed' => '#3b82f6',  // blue
            'processing' => '#8b5cf6', // violet
            'shipped' => '#06b6d4',    // cyan
            'delivered' => '#10b981',  // emerald
            'cancelled' => '#ef4444',  // red
            'returned' => '#6b7280',   // gray
        ];

        foreach ($labels as $key => $label) {
            $chartLabels[] = $label;
            $chartData[] = $data[$key] ?? 0;
            $colors[] = $colorMap[$key] ?? '#cccccc';
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $chartData,
                    'backgroundColor' => $colors,
                ],
            ],
            'labels' => $chartLabels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
