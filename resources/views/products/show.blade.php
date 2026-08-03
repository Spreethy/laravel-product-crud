<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $product->name }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow p-6 max-w-lg mx-auto">
                <h1 class="text-2xl font-bold mb-4">{{ $product->name }}</h1>

                <dl class="space-y-3">
                    <div class="flex">
                        <dt class="w-32 text-gray-500">SKU</dt>
                        <dd>{{ $product->sku ?? '—' }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-32 text-gray-500">Price</dt>
                        <dd>${{ number_format($product->price, 2) }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-32 text-gray-500">Stock</dt>
                        <dd>
                            {{ $product->stock }}
                            @if ($product->stock <= $product->reorder_level)
                                <span class="ms-2 inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-red-100 text-red-700">Low stock</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex">
                        <dt class="w-32 text-gray-500">Reorder Level</dt>
                        <dd>{{ $product->reorder_level }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-32 text-gray-500">Category</dt>
                        <dd>{{ $product->category->name ?? '—' }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-32 text-gray-500">Supplier</dt>
                        <dd>{{ $product->supplier->name ?? '—' }}</dd>
                    </div>
                    <div class="flex">
                        <dt class="w-32 text-gray-500">Status</dt>
                        <dd>{{ $product->is_active ? 'Active' : 'Inactive' }}</dd>
                    </div>
                    @if ($product->description)
                        <div class="flex">
                            <dt class="w-32 text-gray-500">Description</dt>
                            <dd>{{ $product->description }}</dd>
                        </div>
                    @endif
                    <div class="flex">
                        <dt class="w-32 text-gray-500">Created</dt>
                        <dd>{{ $product->created_at->format('M d, Y') }}</dd>
                    </div>
                </dl>

                <div class="mt-6 flex space-x-3">
                    <a href="{{ route('products.edit', $product) }}" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">Edit</a>
                    <a href="{{ route('stock.create', ['product_id' => $product->id]) }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Record Stock</a>
                    <a href="{{ route('products.index') }}" class="text-gray-600 hover:underline py-2">Back</a>
                </div>
            </div>

            <div class="bg-white rounded shadow p-6 max-w-lg mx-auto mt-6">
                <h2 class="text-lg font-semibold mb-4">Recent Stock Movements</h2>

                @forelse ($recentMovements as $movement)
                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                        <div class="flex items-center gap-3">
                            @php
                                $typeBadge = [
                                    'in' => ['bg-green-100 text-green-700', 'In'],
                                    'out' => ['bg-red-100 text-red-700', 'Out'],
                                    'adjustment' => ['bg-amber-100 text-amber-700', 'Adj'],
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $typeBadge[$movement->type][0] }}">
                                {{ $typeBadge[$movement->type][1] }}
                            </span>
                            <span class="text-sm text-gray-700">{{ $movement->quantity }}</span>
                            <span class="text-xs text-gray-500">{{ $movement->previous_stock }} → {{ $movement->new_stock }}</span>
                        </div>
                        <span class="text-xs text-gray-500">{{ $movement->created_at->format('M d, H:i') }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No stock movements yet.</p>
                @endforelse

                <div class="mt-4">
                    <a href="{{ route('stock.index', ['product_id' => $product->id]) }}" class="text-indigo-600 hover:underline text-sm">View full history →</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
