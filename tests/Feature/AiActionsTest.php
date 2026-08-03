<?php

namespace Tests\Feature;

use App\Ai\ActionRegistry;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiActionsTest extends TestCase
{
    use RefreshDatabase;

    private function handle(User $user, array $action): array
    {
        return app(ActionRegistry::class)->handle($user, $action);
    }

    public function test_product_list_action(): void
    {
        $staff = User::factory()->create();
        Product::factory()->create(['name' => 'Widget']);

        $result = $this->handle($staff, ['action' => 'product.list']);

        $this->assertSame('product.list', $result['action']);
        $this->assertStringContainsString('Widget', $result['message']);
    }

    public function test_product_create_action(): void
    {
        $staff = User::factory()->create();

        $result = $this->handle($staff, ['action' => 'product.create', 'product' => ['name' => 'New Product', 'price' => 5]]);

        $this->assertSame('product.create', $result['action']);
        $this->assertDatabaseHas('products', ['name' => 'New Product']);
    }

    public function test_staff_cannot_delete_product_via_ai(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create();

        $result = $this->handle($staff, ['action' => 'product.delete', 'id' => $product->id]);

        $this->assertStringContainsString('Only admins', $result['message']);
        $this->assertNotSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_admin_can_delete_product_via_ai(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $result = $this->handle($admin, ['action' => 'product.delete', 'id' => $product->id]);

        $this->assertSame('product.delete', $result['action']);
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_category_create_requires_admin(): void
    {
        $staff = User::factory()->create();

        $result = $this->handle($staff, ['action' => 'category.create', 'name' => 'Hacked']);

        $this->assertStringContainsString('Only admins', $result['message']);
        $this->assertDatabaseMissing('categories', ['name' => 'Hacked']);

        $admin = User::factory()->admin()->create();
        $result = $this->handle($admin, ['action' => 'category.create', 'name' => 'Electronics']);

        $this->assertSame('category.create', $result['action']);
        $this->assertDatabaseHas('categories', ['name' => 'Electronics']);
    }

    public function test_supplier_create_requires_admin(): void
    {
        $staff = User::factory()->create();

        $result = $this->handle($staff, ['action' => 'supplier.create', 'name' => 'Acme']);

        $this->assertStringContainsString('Only admins', $result['message']);
        $this->assertDatabaseMissing('suppliers', ['name' => 'Acme']);
    }

    public function test_stock_in_action_updates_stock(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);

        $result = $this->handle($staff, ['action' => 'stock.in', 'id' => $product->id, 'quantity' => 10]);

        $this->assertStringContainsString('stock now 15', $result['message']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 15]);
    }

    public function test_stock_out_cannot_exceed_stock_via_ai(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['stock' => 3]);

        $result = $this->handle($staff, ['action' => 'stock.out', 'id' => $product->id, 'quantity' => 10]);

        $this->assertStringContainsString('Cannot move out', $result['message']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 3]);
    }

    public function test_low_stock_report_action(): void
    {
        $staff = User::factory()->create();
        Product::factory()->create(['stock' => 1, 'reorder_level' => 5]);

        $result = $this->handle($staff, ['action' => 'low_stock']);

        $this->assertSame('low_stock', $result['action']);
        $this->assertStringContainsString('Low-stock products', $result['message']);
    }

    public function test_unknown_action_returns_message(): void
    {
        $staff = User::factory()->create();

        $result = $this->handle($staff, ['action' => 'nope']);

        $this->assertStringContainsString('Unknown action', $result['message']);
    }
}
