<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Inventory Valuation</h2>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('reports.export_valuation') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Export CSV
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 bg-white rounded shadow p-6">
                <div class="text-sm text-gray-500">Total Inventory Value</div>
                <div class="text-3xl font-bold text-indigo-600">${{ number_format($totalValue, 2) }}</div>
            </div>

            <div class="bg-white rounded shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Supplier</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-6 py-4 font-medium">{{ $product['name'] }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $product['category'] }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $product['supplier'] }}</td>
                                <td class="px-6 py-4 text-right">{{ $product['stock'] }}</td>
                                <td class="px-6 py-4 text-right">${{ number_format($product['price'], 2) }}</td>
                                <td class="px-6 py-4 text-right font-medium">${{ number_format($product['value'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="5" class="px-6 py-3 text-right text-sm font-medium text-gray-500">Total</td>
                            <td class="px-6 py-3 text-right font-bold">${{ number_format($totalValue, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
