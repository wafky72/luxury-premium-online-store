<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Order::whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])->sum('total');
        $orderCount = Order::count();
        $avgOrderValue = $orderCount > 0 ? $totalRevenue / $orderCount : 0;
        $customerCount = User::count(); // Fallback to all users if roles are not yet seeded

        return [
            Stat::make('Total Revenue', Number::currency($totalRevenue, 'BDT'))
                ->description('Total sales from confirmed orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('success'),
            Stat::make('Total Orders', $orderCount)
                ->description('Total orders placed on the store')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->chart([15, 4, 10, 2, 12, 4, 11])
                ->color('warning'),
            Stat::make('Avg Order Value', Number::currency($avgOrderValue, 'BDT'))
                ->description('Average spending per order')
                ->descriptionIcon('heroicon-m-calculator')
                ->chart([3, 5, 2, 8, 4, 10, 6])
                ->color('info'),
            Stat::make('Total Customers', $customerCount)
                ->description('Unique registered customers')
                ->descriptionIcon('heroicon-m-users')
                ->chart([2, 10, 5, 12, 8, 15, 10])
                ->color('primary'),
        ];
    }
}
