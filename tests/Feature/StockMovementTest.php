<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockMovementTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_record_stock_in(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);

        $response = $this->actingAs($staff)->post(route('stock.store'), [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 10,
            'reason' => 'Restock',
        ]);

        $response->assertRedirect(route('stock.index'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 15]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'in',
            'quantity' => 10,
            'previous_stock' => 5,
            'new_stock' => 15,
        ]);
    }

    public function test_staff_can_record_stock_out(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['stock' => 20]);

        $this->actingAs($staff)->post(route('stock.store'), [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 6,
            'reason' => 'Sold',
        ])->assertRedirect(route('stock.index'));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 14]);
    }

    public function test_stock_out_cannot_exceed_current_stock(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['stock' => 3]);

        $this->actingAs($staff)->post(route('stock.store'), [
            'product_id' => $product->id,
            'type' => 'out',
            'quantity' => 10,
        ])->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
    }

    public function test_adjustment_sets_absolute_stock(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['stock' => 50]);

        $this->actingAs($staff)->post(route('stock.store'), [
            'product_id' => $product->id,
            'type' => 'adjustment',
            'quantity' => 25,
            'reason' => 'Physical count',
        ])->assertRedirect(route('stock.index'));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 25]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'type' => 'adjustment',
            'quantity' => 25,
            'previous_stock' => 50,
            'new_stock' => 25,
        ]);
    }

    public function test_stock_ledger_is_viewable(): void
    {
        $staff = User::factory()->create();
        Product::factory()->create();

        $this->actingAs($staff)->get(route('stock.index'))->assertOk();
    }

    public function test_staff_cannot_revert_a_movement(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);
        $movement = StockMovement::record($product, StockMovement::TYPE_IN, 5, null, $staff);

        $this->actingAs($staff)->delete(route('stock.destroy', $movement))->assertForbidden();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 15]);
        $this->assertDatabaseHas('stock_movements', ['id' => $movement->id]);
    }

    public function test_admin_can_revert_a_movement_restoring_stock(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['stock' => 10]);
        $movement = StockMovement::record($product, StockMovement::TYPE_IN, 5, null, $admin);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 15]);

        $this->actingAs($admin)->delete(route('stock.destroy', $movement))->assertRedirect(route('stock.index'));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 10]);
        $this->assertDatabaseMissing('stock_movements', ['id' => $movement->id]);
    }
}
