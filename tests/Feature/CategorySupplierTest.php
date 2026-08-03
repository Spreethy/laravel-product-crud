<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategorySupplierTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_categories_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('categories.index'))->assertOk();
    }

    public function test_staff_can_view_categories_index_but_cannot_manage(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->get(route('categories.index'))->assertOk();
        $this->actingAs($staff)->get(route('categories.create'))->assertForbidden();
        $this->actingAs($staff)->post(route('categories.store'), ['name' => 'Hacked'])->assertForbidden();
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('categories.store'), [
            'name' => 'Electronics',
            'description' => 'Devices and gadgets',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Electronics', 'slug' => 'electronics']);
    }

    public function test_admin_can_update_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($admin)->put(route('categories.update', $category), [
            'name' => 'New Name',
            'description' => 'Updated',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New Name']);
    }

    public function test_admin_can_soft_delete_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)->delete(route('categories.destroy', $category))->assertRedirect(route('categories.index'));

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    public function test_admin_can_view_suppliers_index(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get(route('suppliers.index'))->assertOk();
    }

    public function test_staff_can_view_suppliers_index_but_cannot_manage(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->get(route('suppliers.index'))->assertOk();
        $this->actingAs($staff)->post(route('suppliers.store'), ['name' => 'Hacked'])->assertForbidden();
    }

    public function test_admin_can_create_supplier(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('suppliers.store'), [
            'name' => 'Acme Corp',
            'contact_name' => 'Jane Doe',
            'email' => 'jane@acme.test',
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['name' => 'Acme Corp', 'email' => 'jane@acme.test']);
    }

    public function test_admin_can_soft_delete_supplier(): void
    {
        $admin = User::factory()->admin()->create();
        $supplier = Supplier::factory()->create();

        $this->actingAs($admin)->delete(route('suppliers.destroy', $supplier))->assertRedirect(route('suppliers.index'));

        $this->assertSoftDeleted('suppliers', ['id' => $supplier->id]);
    }

    public function test_product_can_be_created_with_category_and_supplier(): void
    {
        $staff = User::factory()->create();
        $category = Category::factory()->create();
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($staff)->post(route('products.store'), [
            'name' => 'Smart Watch',
            'price' => 199.99,
            'stock' => 10,
            'reorder_level' => 2,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
        ]);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('products', [
            'name' => 'Smart Watch',
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
        ]);
    }

    public function test_staff_cannot_delete_a_product(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($staff)->delete(route('products.destroy', $product))->assertForbidden();
        $this->assertNotSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_admin_can_delete_a_product(): void
    {
        $admin = User::factory()->admin()->create();
        $product = Product::factory()->create();

        $this->actingAs($admin)->delete(route('products.destroy', $product))->assertRedirect(route('products.index'));

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }
}
