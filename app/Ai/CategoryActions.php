<?php

namespace App\Ai;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class CategoryActions
{
    public function list(User $user, array $action): array
    {
        $categories = Category::withCount('products')->orderBy('name')->get();
        if ($categories->isEmpty()) {
            return ['message' => 'No categories found.', 'action' => 'category.list'];
        }

        $lines = $categories->map(fn ($c) => "- **{$c->name}** (ID: {$c->id}, {$c->products_count} products)");

        return ['message' => "**Categories:**\n".$lines->implode("\n"), 'action' => 'category.list'];
    }

    public function create(User $user, array $action): array
    {
        if (! $user->isAdmin()) {
            return ['message' => 'Only admins can create categories.', 'action' => null];
        }

        $name = $action['name'] ?? null;
        if (! $name) {
            return ['message' => 'A category name is required.', 'action' => null];
        }

        $existing = Category::where('name', $name)->first();
        if ($existing) {
            return ['message' => "Category **{$name}** already exists.", 'action' => null];
        }

        $category = Category::create([
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $action['description'] ?? null,
        ]);

        return ['message' => "Category **{$category->name}** created successfully! (ID: {$category->id})", 'action' => 'category.create'];
    }

    public function update(User $user, array $action): array
    {
        if (! $user->isAdmin()) {
            return ['message' => 'Only admins can update categories.', 'action' => null];
        }

        $category = $this->find($action);
        if (! $category) {
            return ['message' => 'Category not found.', 'action' => null];
        }

        if (! empty($action['name'])) {
            $category->name = $action['name'];
            $category->slug = Str::slug($action['name']);
        }
        if (array_key_exists('description', $action)) {
            $category->description = $action['description'];
        }
        $category->save();

        return ['message' => "Category **{$category->name}** updated successfully!", 'action' => 'category.update'];
    }

    public function delete(User $user, array $action): array
    {
        if (! $user->isAdmin()) {
            return ['message' => 'Only admins can delete categories.', 'action' => null];
        }

        $category = $this->find($action);
        if (! $category) {
            return ['message' => 'Category not found.', 'action' => null];
        }

        $category->delete();

        return ['message' => "Category **{$category->name}** deleted successfully!", 'action' => 'category.delete'];
    }

    private function find(array $action): ?Category
    {
        if (! empty($action['id'])) {
            return Category::find($action['id']);
        }
        if (! empty($action['name'])) {
            return Category::where('name', 'like', "%{$action['name']}%")->first();
        }

        return null;
    }
}
