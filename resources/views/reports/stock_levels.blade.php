<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Stock Levels</h2>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('reports.export_stock_levels') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Export CSV
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">SKU</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Reorder Level</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-6 py-4 font-medium">{{ $product['name'] }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $product['sku'] }}</td>
                                <td class="px-6 py-4 text-right">{{ $product['stock'] }}</td>
                                <td class="px-6 py-4 text-right">{{ $product['reorder_level'] }}</td>
                                <td class="px-6 py-4">
                                    @if ($product['status'] === 'Low')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-red-100 text-red-700">Low</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-green-100 text-green-700">OK</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
