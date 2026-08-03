<?php

namespace App\Http\Controllers;

use App\Models\StockAlert;
use Illuminate\Http\Request;

class StockAlertController extends Controller
{
    public function index(Request $request)
    {
        $alerts = StockAlert::with(['product', 'resolver'])
            ->when($request->boolean('show_resolved'), fn ($q) => $q, fn ($q) => $q->open())
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('alerts.index', compact('alerts'));
    }

    public function resolve(StockAlert $stockAlert)
    {
        $stockAlert->markResolved(auth()->user());

        return back()->with('success', 'Alert marked as resolved.');
    }
}
