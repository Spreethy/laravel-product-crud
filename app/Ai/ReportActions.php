<?php

namespace App\Ai;

use App\Models\StockAlert;
use App\Models\User;

class ReportActions
{
    public function lowStock(User $user, array $action): array
    {
        $alerts = StockAlert::with('product')->open()->latest()->get();
        if ($alerts->isEmpty()) {
            return ['message' => 'No low-stock alerts. All products are above their reorder level.', 'action' => 'low_stock'];
        }

        $lines = $alerts->map(fn ($a) => sprintf(
            '- **%s** (stock: %d, reorder level: %d)',
            $a->product->name,
            $a->product->stock,
            $a->product->reorder_level,
        ));

        return ['message' => "**Low-stock products ({$alerts->count()}):**\n".$lines->implode("\n"), 'action' => 'low_stock'];
    }

    public function valuation(User $user, array $action): array
    {
        $total = round(\App\Models\Product::selectRaw('SUM(stock * price) as total')->value('total') ?? 0, 2);
        $count = \App\Models\Product::count();

        return [
            'message' => "**Inventory valuation**\n- Products: {$count}\n- Total inventory value: \${$total}",
            'action' => 'report_valuation',
        ];
    }

    public function summary(User $user, array $action): array
    {
        $products = \App\Models\Product::count();
        $categories = \App\Models\Category::count();
        $suppliers = \App\Models\Supplier::count();
        $totalStock = \App\Models\Product::sum('stock');
        $openAlerts = StockAlert::open()->count();

        return [
            'message' => "**Inventory summary**\n- Products: {$products}\n- Categories: {$categories}\n- Suppliers: {$suppliers}\n- Total stock: {$totalStock}\n- Open low-stock alerts: {$openAlerts}",
            'action' => 'report_summary',
        ];
    }
}
