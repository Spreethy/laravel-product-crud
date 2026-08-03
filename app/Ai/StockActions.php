<?php

namespace App\Ai;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;

class StockActions
{
    public function in(User $user, array $action): array
    {
        return $this->movement($user, $action, StockMovement::TYPE_IN, 'added to');
    }

    public function out(User $user, array $action): array
    {
        return $this->movement($user, $action, StockMovement::TYPE_OUT, 'removed from');
    }

    public function adjust(User $user, array $action): array
    {
        return $this->movement($user, $action, StockMovement::TYPE_ADJUSTMENT, 'adjusted to');
    }

    public function history(User $user, array $action): array
    {
        $product = $this->findProduct($action);
        if (! $product) {
            return ['message' => 'Product not found.', 'action' => null];
        }

        $movements = $product->movements()->with('user')->latest()->limit(10)->get();
        if ($movements->isEmpty()) {
            return ['message' => "No stock movements for **{$product->name}**.", 'action' => 'stock.history'];
        }

        $lines = $movements->map(fn ($m) => sprintf(
            '- %s %d (stock %d → %d) on %s',
            ucfirst($m->type),
            $m->quantity,
            $m->previous_stock,
            $m->new_stock,
            $m->created_at->format('M d, Y H:i'),
        ));

        return ['message' => "**{$product->name}** recent movements:\n".$lines->implode("\n"), 'action' => 'stock.history'];
    }

    private function movement(User $user, array $action, string $type, string $verb): array
    {
        $product = $this->findProduct($action);
        if (! $product) {
            return ['message' => 'Product not found.', 'action' => null];
        }

        $quantity = (int) ($action['quantity'] ?? 0);

        if ($quantity < 1 && $type !== StockMovement::TYPE_ADJUSTMENT) {
            return ['message' => 'A positive quantity is required.', 'action' => null];
        }

        if ($type === StockMovement::TYPE_OUT && $quantity > $product->stock) {
            return ['message' => "Cannot move out more than the current stock ({$product->stock}).", 'action' => null];
        }

        $movement = StockMovement::record(
            $product,
            $type,
            $quantity,
            $action['reason'] ?? null,
            $user,
        );

        return [
            'message' => sprintf(
                'Stock %s for **%s** (stock now %d).',
                $verb,
                $product->name,
                $movement->new_stock,
            ),
            'action' => 'stock.'.$type,
        ];
    }

    private function findProduct(array $action): ?Product
    {
        if (! empty($action['id'])) {
            return Product::find($action['id']);
        }
        if (! empty($action['name'])) {
            return Product::where('name', 'like', "%{$action['name']}%")->first();
        }

        return null;
    }
}
