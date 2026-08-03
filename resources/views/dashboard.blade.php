<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Inventory Value</div>
                        <div class="text-3xl font-bold text-indigo-600">${{ number_format($inventoryValue, 2) }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Total Products</div>
                        <div class="text-3xl font-bold text-gray-900">{{ $totalProducts }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Total Stock</div>
                        <div class="text-3xl font-bold text-green-600">{{ $totalStock }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Low Stock Alerts</div>
                        <div class="text-3xl font-bold {{ $lowStockCount > 0 ? 'text-red-600' : 'text-gray-900' }}">{{ $lowStockCount }}</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Categories</div>
                        <div class="text-3xl font-bold text-gray-900">{{ $totalCategories }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Suppliers</div>
                        <div class="text-3xl font-bold text-gray-900">{{ $totalSuppliers }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Quick Actions</div>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('products.index') }}" class="block text-indigo-600 hover:underline">View Products</a>
                            <a href="{{ route('stock.create') }}" class="block text-indigo-600 hover:underline">Record Stock Movement</a>
                            <a href="{{ route('reports.index') }}" class="block text-indigo-600 hover:underline">Reports</a>
                            <a href="{{ route('chat.index') }}" class="block text-indigo-600 hover:underline">AI Chat</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold text-lg mb-4">Low Stock Alerts</h3>
                        @if ($openAlerts->count())
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                                        <th class="pb-2">Product</th>
                                        <th class="pb-2">Stock</th>
                                        <th class="pb-2">Reorder Level</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($openAlerts as $alert)
                                        <tr>
                                            <td class="py-2">
                                                <a href="{{ route('products.show', $alert->product) }}" class="text-indigo-600 hover:underline">{{ $alert->product->name }}</a>
                                            </td>
                                            <td class="py-2 text-red-600 font-medium">{{ $alert->product->stock }}</td>
                                            <td class="py-2">{{ $alert->product->reorder_level }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-gray-500">No low-stock alerts. All products are above their reorder level.</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="font-semibold text-lg mb-4">Recent Stock Movements</h3>
                        @if ($recentMovements->count())
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                                        <th class="pb-2">Product</th>
                                        <th class="pb-2">Type</th>
                                        <th class="pb-2">Qty</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($recentMovements as $m)
                                        <tr>
                                            <td class="py-2">
                                                <a href="{{ route('products.show', $m->product) }}" class="text-indigo-600 hover:underline">{{ $m->product->name }}</a>
                                            </td>
                                            <td class="py-2">
                                                @php
                                                    $typeBadge = [
                                                        'in' => ['bg-green-100 text-green-700', 'In'],
                                                        'out' => ['bg-red-100 text-red-700', 'Out'],
                                                        'adjustment' => ['bg-amber-100 text-amber-700', 'Adj'],
                                                    ];
                                                @endphp
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $typeBadge[$m->type][0] }}">{{ $typeBadge[$m->type][1] }}</span>
                                            </td>
                                            <td class="py-2">{{ $m->quantity }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-gray-500">No stock movements yet. <a href="{{ route('stock.create') }}" class="text-indigo-600 hover:underline">Record one</a>.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-semibold text-lg mb-4">Recent Products</h3>
                    @if ($recentProducts->count())
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                                    <th class="pb-2">Name</th>
                                    <th class="pb-2">Price</th>
                                    <th class="pb-2">Stock</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($recentProducts as $p)
                                    <tr>
                                        <td class="py-2">
                                            <a href="{{ route('products.show', $p) }}" class="text-indigo-600 hover:underline">{{ $p->name }}</a>
                                        </td>
                                        <td class="py-2">${{ number_format($p->price, 2) }}</td>
                                        <td class="py-2">{{ $p->stock }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500">No products yet. <a href="{{ route('products.create') }}" class="text-indigo-600 hover:underline">Create one</a>.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
