<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StockAlert;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockAlertTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_alert_is_created_when_stock_hits_reorder_level(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'reorder_level' => 5]);

        StockMovement::record($product, StockMovement::TYPE_OUT, 5, null, $staff);

        $this->assertDatabaseHas('stock_alerts', [
            'product_id' => $product->id,
            'type' => StockAlert::TYPE_LOW_STOCK,
            'status' => StockAlert::STATUS_OPEN,
        ]);
    }

    public function test_alert_is_auto_resolved_when_stock_rises_above_reorder_level(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5, 'reorder_level' => 5]);

        StockMovement::record($product, StockMovement::TYPE_IN, 1, null, $staff);
        $alert = StockAlert::where('product_id', $product->id)->first();

        $this->assertDatabaseHas('stock_alerts', [
            'id' => $alert->id,
            'status' => StockAlert::STATUS_RESOLVED,
        ]);
        $this->assertNotNull($alert->refresh()->resolved_at);
    }

    public function test_creating_a_low_stock_product_creates_alert(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->post(route('products.store'), [
            'name' => 'Low Product',
            'price' => 10,
            'stock' => 2,
            'reorder_level' => 5,
        ])->assertRedirect(route('products.index'));

        $product = Product::where('name', 'Low Product')->first();
        $this->assertDatabaseHas('stock_alerts', [
            'product_id' => $product->id,
            'status' => StockAlert::STATUS_OPEN,
        ]);
    }

    public function test_admin_can_view_alerts_list(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create(['stock' => 1, 'reorder_level' => 5]);

        $this->actingAs($admin)->get(route('alerts.index'))->assertOk();
    }

    public function test_staff_can_view_alerts_list(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['stock' => 1, 'reorder_level' => 5]);

        $this->actingAs($staff)->get(route('alerts.index'))->assertOk();
    }

    public function test_alert_can_be_manually_resolved(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['stock' => 3, 'reorder_level' => 5]);
        $alert = StockAlert::open()->where('product_id', $product->id)->first();

        $this->actingAs($staff)->post(route('alerts.resolve', $alert))->assertRedirect();

        $this->assertDatabaseHas('stock_alerts', [
            'id' => $alert->id,
            'status' => StockAlert::STATUS_RESOLVED,
            'resolved_by' => $staff->id,
        ]);
    }

    public function test_alert_is_reopened_when_stock_drops_again(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['stock' => 3, 'reorder_level' => 5]);
        $alert = StockAlert::open()->where('product_id', $product->id)->first();

        $this->actingAs($staff)->post(route('alerts.resolve', $alert));

        StockMovement::record($product, StockMovement::TYPE_OUT, 1, null, $staff);

        $this->assertDatabaseHas('stock_alerts', [
            'product_id' => $product->id,
            'status' => StockAlert::STATUS_OPEN,
        ]);
    }
}
