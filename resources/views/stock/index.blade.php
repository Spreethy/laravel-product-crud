<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Stock Movements</h2>
            <a href="{{ route('stock.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Record Movement
            </a>
        </div>
    </x-slot>

    @if (session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('stock.index') }}" class="bg-white rounded shadow p-4 mb-6 flex flex-wrap items-end gap-4">
                <div>
                    <label for="product_id" class="block text-xs font-medium text-gray-500 mb-1">Product</label>
                    <select name="product_id" id="product_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">All products</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(request('product_id') == $product->id)>{{ $product->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="type" class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                    <select name="type" id="type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                        <option value="">All types</option>
                        <option value="in" @selected(request('type') === 'in')>Stock In</option>
                        <option value="out" @selected(request('type') === 'out')>Stock Out</option>
                        <option value="adjustment" @selected(request('type') === 'adjustment')>Adjustment</option>
                    </select>
                </div>
                <div>
                    <label for="from" class="block text-xs font-medium text-gray-500 mb-1">From</label>
                    <input type="date" name="from" id="from" value="{{ request('from') }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                </div>
                <div>
                    <label for="to" class="block text-xs font-medium text-gray-500 mb-1">To</label>
                    <input type="date" name="to" id="to" value="{{ request('to') }}" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                </div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-semibold hover:bg-indigo-700">Filter</button>
                <a href="{{ route('stock.index') }}" class="text-sm text-gray-600 hover:underline py-2">Reset</a>
            </form>

            <div class="bg-white rounded shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Before → After</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Reason</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">By</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($movements as $movement)
                            <tr>
                                <td class="px-6 py-4 font-medium">
                                    <a href="{{ route('products.show', $movement->product) }}" class="text-blue-600 hover:underline">{{ $movement->product->name }}</a>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $typeBadge = [
                                            'in' => ['bg-green-100 text-green-700', 'In'],
                                            'out' => ['bg-red-100 text-red-700', 'Out'],
                                            'adjustment' => ['bg-amber-100 text-amber-700', 'Adjustment'],
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold {{ $typeBadge[$movement->type][0] }}">
                                        {{ $typeBadge[$movement->type][1] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $movement->quantity }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $movement->previous_stock }} → {{ $movement->new_stock }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $movement->reason }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $movement->user->name ?? 'System' }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $movement->created_at->format('M d, Y H:i') }}</td>
                                <td class="px-6 py-4 text-right">
                                    @if (auth()->user()->isAdmin())
                                        <form action="{{ route('stock.destroy', $movement) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Revert this movement and restore stock?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Revert</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">No stock movements found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
