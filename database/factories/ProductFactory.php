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

    protected static array $englishNames = [
        'Wireless Mouse', 'Mechanical Keyboard', 'USB-C Hub', 'HDMI Cable 2m',
        'Bluetooth Speaker', 'Laptop Stand', 'Desk Lamp', 'Office Chair',
        'Notebook A5', 'Stapler', 'Paper Clips 100pcs', 'Fountain Pen',
        'Sticky Notes Pack', 'File Folder', 'Printer Paper 500', 'Whiteboard Markers',
        'Coffee Mug', 'Electric Kettle', 'Non-stick Pan', 'Cutlery Set',
        'SSD 1TB', 'External Hard Drive', 'Webcam HD', 'Headset Pro',
        'Mouse Pad', 'Monitor Arm',
    ];

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => static::uniqueEnglishName(),
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

    private static function uniqueEnglishName(): string
    {
        static $index = 0;
        $index++;

        return static::$englishNames[($index - 1) % count(static::$englishNames)];
    }
}
