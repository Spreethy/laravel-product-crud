<?php

namespace App\Ai;

use App\Models\User;

class ActionRegistry
{
    /**
     * @var array<string, array{0: object, 1: string}>
     */
    private array $handlers;

    public function __construct(
        private ProductActions $products,
        private CategoryActions $categories,
        private SupplierActions $suppliers,
        private StockActions $stock,
        private ReportActions $reports,
    ) {
        $this->handlers = [
            'product.list' => [$products, 'list'],
            'product.search' => [$products, 'search'],
            'product.show' => [$products, 'show'],
            'product.create' => [$products, 'create'],
            'product.update' => [$products, 'update'],
            'product.delete' => [$products, 'delete'],
            'category.list' => [$categories, 'list'],
            'category.create' => [$categories, 'create'],
            'category.update' => [$categories, 'update'],
            'category.delete' => [$categories, 'delete'],
            'supplier.list' => [$suppliers, 'list'],
            'supplier.create' => [$suppliers, 'create'],
            'supplier.update' => [$suppliers, 'update'],
            'supplier.delete' => [$suppliers, 'delete'],
            'stock.in' => [$stock, 'in'],
            'stock.out' => [$stock, 'out'],
            'stock.adjust' => [$stock, 'adjust'],
            'stock.history' => [$stock, 'history'],
            'low_stock' => [$reports, 'lowStock'],
            'report_valuation' => [$reports, 'valuation'],
            'report_summary' => [$reports, 'summary'],
        ];
    }

    public function handle(User $user, array $action): array
    {
        $name = $action['action'] ?? null;

        if (! $name || ! isset($this->handlers[$name])) {
            return [
                'message' => $name ? "Unknown action: {$name}" : 'The model did not return an actionable response.',
                'action' => null,
            ];
        }

        [$handler, $method] = $this->handlers[$name];

        return $handler->{$method}($user, $action);
    }
}
