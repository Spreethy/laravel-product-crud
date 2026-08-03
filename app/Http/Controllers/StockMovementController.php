<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $movements = StockMovement::with(['product', 'user'])
            ->when($request->product_id, fn ($q) => $q->where('product_id', $request->product_id))
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->from, fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $products = Product::orderBy('name')->get();

        return view('stock.index', compact('movements', 'products'));
    }

    public function create(Request $request)
    {
        $products = Product::orderBy('name')->get();
        $preselected = $request->integer('product_id') ?: null;

        return view('stock.create', compact('products', 'preselected'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,adjustment',
            'quantity' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($data['product_id']);

        if (in_array($data['type'], ['in', 'out']) && $data['quantity'] < 1) {
            return back()->withErrors(['quantity' => 'Quantity must be at least 1.']);
        }

        if ($data['type'] === 'out' && $data['quantity'] > $product->stock) {
            return back()->withErrors(['quantity' => 'Cannot move out more than the current stock.']);
        }

        StockMovement::record(
            $product,
            $data['type'],
            $data['quantity'],
            $data['reason'] ?? null,
            auth()->user(),
        );

        return redirect()->route('stock.index')
            ->with('success', 'Stock movement recorded successfully.');
    }

    public function destroy(StockMovement $stockMovement)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $stockMovement->revert();

        return redirect()->route('stock.index')
            ->with('success', 'Stock movement reverted.');
    }
}
