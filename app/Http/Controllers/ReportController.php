<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function valuation()
    {
        $products = Product::with(['category', 'supplier'])
            ->orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'name' => $p->name,
                'sku' => $p->sku,
                'category' => $p->category?->name,
                'supplier' => $p->supplier?->name,
                'stock' => $p->stock,
                'price' => (float) $p->price,
                'value' => round($p->stock * $p->price, 2),
            ]);

        $totalValue = round($products->sum('value'), 2);

        return view('reports.valuation', compact('products', 'totalValue'));
    }

    public function stockLevels()
    {
        $products = Product::orderBy('name')
            ->get()
            ->map(fn ($p) => [
                'name' => $p->name,
                'sku' => $p->sku,
                'stock' => $p->stock,
                'reorder_level' => $p->reorder_level,
                'status' => $p->stock <= $p->reorder_level ? 'Low' : 'OK',
            ]);

        return view('reports.stock_levels', compact('products'));
    }

    public function movements(Request $request)
    {
        $movements = StockMovement::with(['product', 'user'])
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->from, fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->get();

        $inTotal = $movements->where('type', 'in')->sum('quantity');
        $outTotal = $movements->where('type', 'out')->sum('quantity');

        return view('reports.movements', compact('movements', 'inTotal', 'outTotal'));
    }

    public function suppliers()
    {
        $suppliers = Supplier::withCount('products')
            ->withSum('products as stock_value', \Illuminate\Support\Facades\DB::raw('stock * price'))
            ->orderBy('name')
            ->get()
            ->map(fn ($s) => [
                'name' => $s->name,
                'contact' => $s->contact_name,
                'product_count' => $s->products_count,
                'stock_value' => round($s->stock_value ?? 0, 2),
            ]);

        return view('reports.suppliers', compact('suppliers'));
    }

    public function categories()
    {
        $categories = Category::withCount('products')
            ->withSum('products as stock_value', \Illuminate\Support\Facades\DB::raw('stock * price'))
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => [
                'name' => $c->name,
                'product_count' => $c->products_count,
                'stock_value' => round($c->stock_value ?? 0, 2),
            ]);

        return view('reports.categories', compact('categories'));
    }

    public function exportValuation()
    {
        return $this->csv('inventory-valuation.csv', [
            'Name', 'SKU', 'Category', 'Supplier', 'Stock', 'Price', 'Value',
        ], $this->valuationRows());
    }

    public function exportStockLevels()
    {
        return $this->csv('stock-levels.csv', [
            'Name', 'SKU', 'Stock', 'Reorder Level', 'Status',
        ], $this->stockLevelRows());
    }

    public function exportSuppliers()
    {
        return $this->csv('supplier-summary.csv', [
            'Name', 'Contact', 'Product Count', 'Stock Value',
        ], $this->supplierRows());
    }

    public function exportCategories()
    {
        return $this->csv('category-summary.csv', [
            'Name', 'Product Count', 'Stock Value',
        ], $this->categoryRows());
    }

    private function valuationRows(): Collection
    {
        return Product::with(['category', 'supplier'])->orderBy('name')->get()->map(fn ($p) => [
            $p->name, $p->sku, $p->category?->name, $p->supplier?->name,
            $p->stock, number_format($p->price, 2), number_format($p->stock * $p->price, 2),
        ]);
    }

    private function stockLevelRows(): Collection
    {
        return Product::orderBy('name')->get()->map(fn ($p) => [
            $p->name, $p->sku, $p->stock, $p->reorder_level,
            $p->stock <= $p->reorder_level ? 'Low' : 'OK',
        ]);
    }

    private function supplierRows(): Collection
    {
        return Supplier::withCount('products')
            ->withSum('products as stock_value', \Illuminate\Support\Facades\DB::raw('stock * price'))
            ->orderBy('name')
            ->get()
            ->map(fn ($s) => [
                $s->name, $s->contact_name, $s->products_count, number_format($s->stock_value ?? 0, 2),
            ]);
    }

    private function categoryRows(): Collection
    {
        return Category::withCount('products')
            ->withSum('products as stock_value', \Illuminate\Support\Facades\DB::raw('stock * price'))
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => [
                $c->name, $c->products_count, number_format($c->stock_value ?? 0, 2),
            ]);
    }

    private function csv(string $filename, array $headers, Collection $rows)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $lines = [implode(',', $headers)];
        foreach ($rows as $row) {
            $lines[] = implode(',', array_map(fn ($cell) => '"'.str_replace('"', '""', (string) $cell).'"', $row));
        }

        return response(implode("\n", $lines))
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
