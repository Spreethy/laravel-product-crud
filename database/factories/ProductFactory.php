<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'sku' => fake()->unique()->bothify('SKU-####-####'),
            'description' => fake()->optional()->sentence(),
            'price' => fake()->randomFloat(2, 1, 500),
            'stock' => fake()->numberBetween(0, 100),
            'reorder_level' => fake()->numberBetween(0, 10),
            'is_active' => true,
            'category_id' => Category::factory(),
            'supplier_id' => Supplier::factory(),
        ];
    }
}
