<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_view_report_pages(): void
    {
        $staff = User::factory()->create();
        Product::factory()->create(['price' => 10, 'stock' => 5]);

        $this->actingAs($staff)->get(route('reports.index'))->assertOk();
        $this->actingAs($staff)->get(route('reports.valuation'))->assertOk();
        $this->actingAs($staff)->get(route('reports.stock_levels'))->assertOk();
        $this->actingAs($staff)->get(route('reports.movements'))->assertOk();
        $this->actingAs($staff)->get(route('reports.suppliers'))->assertOk();
        $this->actingAs($staff)->get(route('reports.categories'))->assertOk();
    }

    public function test_valuation_report_computes_totals(): void
    {
        $staff = User::factory()->create();
        Product::factory()->create(['price' => 10, 'stock' => 5]);
        Product::factory()->create(['price' => 20, 'stock' => 2]);

        $response = $this->actingAs($staff)->get(route('reports.valuation'));

        $response->assertOk();
        $response->assertSee('$90.00');
    }

    public function test_movement_report_sums_in_and_out(): void
    {
        $staff = User::factory()->create();
        $product = Product::factory()->create(['stock' => 0]);
        StockMovement::record($product, StockMovement::TYPE_IN, 10, null, $staff);
        StockMovement::record($product, StockMovement::TYPE_OUT, 4, null, $staff);

        $response = $this->actingAs($staff)->get(route('reports.movements'));

        $response->assertOk();
        $response->assertSee('Total Stock In');
    }

    public function test_staff_cannot_export_csv(): void
    {
        $staff = User::factory()->create();

        $this->actingAs($staff)->get(route('reports.export_valuation'))->assertForbidden();
        $this->actingAs($staff)->get(route('reports.export_stock_levels'))->assertForbidden();
        $this->actingAs($staff)->get(route('reports.export_suppliers'))->assertForbidden();
        $this->actingAs($staff)->get(route('reports.export_categories'))->assertForbidden();
    }

    public function test_admin_can_export_valuation_csv(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Electronics']);
        $supplier = Supplier::factory()->create(['name' => 'Acme']);
        Product::factory()->create([
            'name' => 'Widget',
            'sku' => 'SKU-1',
            'price' => 15,
            'stock' => 3,
            'category_id' => $category->id,
            'supplier_id' => $supplier->id,
        ]);

        $response = $this->actingAs($admin)->get(route('reports.export_valuation'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $response->assertHeader('Content-Disposition', 'attachment; filename="inventory-valuation.csv"');
        $response->assertSee('Widget');
        $response->assertSee('SKU-1');
    }

    public function test_admin_can_export_supplier_summary_csv(): void
    {
        $admin = User::factory()->admin()->create();
        Supplier::factory()->create(['name' => 'Acme', 'contact_name' => 'Jane']);

        $response = $this->actingAs($admin)->get(route('reports.export_suppliers'));

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $response->assertSee('Acme');
    }
}
