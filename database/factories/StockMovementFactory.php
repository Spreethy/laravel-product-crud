<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockMovement>
 */
class StockMovementFactory extends Factory
{
    protected $model = StockMovement::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $previous = fake()->numberBetween(0, 100);

        return [
            'product_id' => Product::factory(),
            'type' => StockMovement::TYPE_IN,
            'quantity' => fake()->numberBetween(1, 50),
            'previous_stock' => $previous,
            'new_stock' => $previous,
            'reason' => fake()->optional()->sentence(),
            'user_id' => User::factory(),
        ];
    }
}
