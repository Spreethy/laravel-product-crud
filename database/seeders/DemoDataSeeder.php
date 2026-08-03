<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', Role::Admin)->first() ?? User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => Role::Admin,
        ]);

        User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@example.com',
            'role' => Role::Staff,
        ]);

        $categories = collect(['Electronics', 'Clothing', 'Office Supplies', 'Home & Kitchen'])
            ->map(fn (string $name) => Category::factory()->create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]));

        $suppliers = Supplier::factory()->count(5)->create();

        $products = Product::factory()->count(20)->create([
            'stock' => 0,
            'reorder_level' => 5,
            'category_id' => fn () => $categories->random()->id,
            'supplier_id' => fn () => $suppliers->random()->id,
        ]);

        foreach ($products as $product) {
            StockMovement::record(
                $product,
                StockMovement::TYPE_IN,
                fake()->numberBetween(15, 80),
                'Initial stock',
                $admin
            );
        }

        Product::factory()->count(6)->create([
            'stock' => 0,
            'reorder_level' => fake()->numberBetween(8, 15),
            'category_id' => fn () => $categories->random()->id,
            'supplier_id' => fn () => $suppliers->random()->id,
        ])->each(function (Product $product) use ($admin) {
            StockMovement::record(
                $product,
                StockMovement::TYPE_IN,
                fake()->numberBetween(0, 4),
                'Low-stock item',
                $admin
            );
        });
    }
}
