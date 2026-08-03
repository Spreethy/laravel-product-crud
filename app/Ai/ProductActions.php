<?php

namespace App\Ai;

use App\Models\Product;
use App\Models\User;

class ProductActions
{
    public function list(User $user, array $action): array
    {
        $products = Product::with(['category', 'supplier'])->get();
        if ($products->isEmpty()) {
            return ['message' => 'No products found.', 'action' => 'product.list'];
        }

        $lines = $products->map(fn ($p) => sprintf(
            '- **%s** (ID: %d) $%s, stock: %d, category: %s',
            $p->name,
            $p->id,
            number_format($p->price, 2),
            $p->stock,
            $p->category?->name ?? 'none',
        ));

        return ['message' => "**Products:**\n".$lines->implode("\n"), 'action' => 'product.list'];
    }

    public function search(User $user, array $action): array
    {
        $product = Product::where('name', 'like', '%'.$this->name($action).'%')->first();

        return $product
            ? $this->describe($product)
            : ['message' => "No product found matching \"{$this->name($action)}\".", 'action' => null];
    }

    public function show(User $user, array $action): array
    {
        $product = $this->findProduct($action);
        if (! $product) {
            return ['message' => 'Product not found.', 'action' => null];
        }

        return $this->describe($product);
    }

    public function create(User $user, array $action): array
    {
        $data = $action['product'] ?? [];
        $name = $data['name'] ?? null;

        if (! $name) {
            return ['message' => 'A product name is required to create a product.', 'action' => null];
        }

        $existing = Product::where('name', $name)->first();
        if ($existing) {
            return ['message' => "Product **{$name}** already exists (ID: {$existing->id}). Use product.update to modify it.", 'action' => null];
        }

        $product = Product::create([
            'name' => $name,
            'sku' => $data['sku'] ?? null,
            'description' => $data['description'] ?? '',
            'price' => $data['price'] ?? 0,
            'stock' => $data['stock'] ?? 0,
            'reorder_level' => $data['reorder_level'] ?? 0,
        ]);

        return ['message' => "Product **{$product->name}** created successfully! (ID: {$product->id})", 'action' => 'product.create'];
    }

    public function update(User $user, array $action): array
    {
        $product = $this->findProduct($action);
        if (! $product) {
            return ['message' => 'Product not found.', 'action' => null];
        }

        $product->update(array_filter($action['product'] ?? []));

        return ['message' => "Product **{$product->name}** updated successfully!", 'action' => 'product.update'];
    }

    public function delete(User $user, array $action): array
    {
        if (! $user->isAdmin()) {
            return ['message' => 'Only admins can delete products.', 'action' => null];
        }

        $product = $this->findProduct($action);
        if (! $product) {
            return ['message' => 'Product not found.', 'action' => null];
        }

        $product->delete();

        return ['message' => "Product **{$product->name}** deleted successfully!", 'action' => 'product.delete'];
    }

    private function findProduct(array $action): ?Product
    {
        if (! empty($action['id'])) {
            return Product::find($action['id']);
        }

        $name = $this->name($action);
        if ($name) {
            return Product::where('name', 'like', "%{$name}%")->first();
        }

        return null;
    }

    private function name(array $action): string
    {
        return $action['name'] ?? $action['product']['name'] ?? '';
    }

    private function describe(Product $product): array
    {
        $message = sprintf(
            "**%s** (ID: %d)\n- SKU: %s\n- Price: $%s\n- Stock: %d\n- Reorder level: %d\n- Category: %s\n- Supplier: %s",
            $product->name,
            $product->id,
            $product->sku ?: 'none',
            number_format($product->price, 2),
            $product->stock,
            $product->reorder_level,
            $product->category?->name ?? 'none',
            $product->supplier?->name ?? 'none',
        );

        if ($product->description) {
            $message .= "\n- Description: {$product->description}";
        }

        return ['message' => $message, 'action' => 'product.show'];
    }
}
