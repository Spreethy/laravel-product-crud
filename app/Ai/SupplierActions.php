<?php

namespace App\Ai;

use App\Models\Supplier;
use App\Models\User;

class SupplierActions
{
    public function list(User $user, array $action): array
    {
        $suppliers = Supplier::withCount('products')->orderBy('name')->get();
        if ($suppliers->isEmpty()) {
            return ['message' => 'No suppliers found.', 'action' => 'supplier.list'];
        }

        $lines = $suppliers->map(fn ($s) => "- **{$s->name}** (ID: {$s->id}, {$s->products_count} products)");

        return ['message' => "**Suppliers:**\n".$lines->implode("\n"), 'action' => 'supplier.list'];
    }

    public function create(User $user, array $action): array
    {
        if (! $user->isAdmin()) {
            return ['message' => 'Only admins can create suppliers.', 'action' => null];
        }

        $name = $action['name'] ?? null;
        if (! $name) {
            return ['message' => 'A supplier name is required.', 'action' => null];
        }

        $supplier = Supplier::create([
            'name' => $name,
            'contact_name' => $action['contact_name'] ?? null,
            'email' => $action['email'] ?? null,
            'phone' => $action['phone'] ?? null,
            'address' => $action['address'] ?? null,
            'notes' => $action['notes'] ?? null,
        ]);

        return ['message' => "Supplier **{$supplier->name}** created successfully! (ID: {$supplier->id})", 'action' => 'supplier.create'];
    }

    public function update(User $user, array $action): array
    {
        if (! $user->isAdmin()) {
            return ['message' => 'Only admins can update suppliers.', 'action' => null];
        }

        $supplier = $this->find($action);
        if (! $supplier) {
            return ['message' => 'Supplier not found.', 'action' => null];
        }

        $supplier->update(array_filter([
            'name' => $action['name'] ?? null,
            'contact_name' => $action['contact_name'] ?? null,
            'email' => $action['email'] ?? null,
            'phone' => $action['phone'] ?? null,
            'address' => $action['address'] ?? null,
            'notes' => $action['notes'] ?? null,
        ]));

        return ['message' => "Supplier **{$supplier->name}** updated successfully!", 'action' => 'supplier.update'];
    }

    public function delete(User $user, array $action): array
    {
        if (! $user->isAdmin()) {
            return ['message' => 'Only admins can delete suppliers.', 'action' => null];
        }

        $supplier = $this->find($action);
        if (! $supplier) {
            return ['message' => 'Supplier not found.', 'action' => null];
        }

        $supplier->delete();

        return ['message' => "Supplier **{$supplier->name}** deleted successfully!", 'action' => 'supplier.delete'];
    }

    private function find(array $action): ?Supplier
    {
        if (! empty($action['id'])) {
            return Supplier::find($action['id']);
        }
        if (! empty($action['name'])) {
            return Supplier::where('name', 'like', "%{$action['name']}%")->first();
        }

        return null;
    }
}
