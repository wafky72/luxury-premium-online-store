<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class CustomerGrowthChart extends ChartWidget
{
    protected ?string $heading = 'New Customers per Month';
    protected ?string $maxHeight = '250px';

    protected function getData(): array
    {
        $data = \App\Models\User::query()
            ->select(\Illuminate\Support\Facades\DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'New Customers',
                    'data' => array_values($data),
                    'fill' => 'start',
                    'borderColor' => '#8b5cf6',
                    'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
