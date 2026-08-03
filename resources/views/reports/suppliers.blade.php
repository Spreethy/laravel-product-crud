<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Supplier Summary</h2>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('reports.export_suppliers') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
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
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Supplier</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Contact</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Products</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Stock Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td class="px-6 py-4 font-medium">{{ $supplier['name'] }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $supplier['contact'] }}</td>
                                <td class="px-6 py-4 text-right">{{ $supplier['product_count'] }}</td>
                                <td class="px-6 py-4 text-right font-medium">${{ number_format($supplier['stock_value'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">No suppliers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
