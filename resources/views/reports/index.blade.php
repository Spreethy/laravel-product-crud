<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <a href="{{ route('reports.valuation') }}" class="bg-white rounded shadow p-6 hover:shadow-md transition-shadow">
                    <h3 class="font-semibold text-lg text-indigo-600">Inventory Valuation</h3>
                    <p class="mt-2 text-sm text-gray-500">Total stock value per product, with totals.</p>
                </a>
                <a href="{{ route('reports.stock_levels') }}" class="bg-white rounded shadow p-6 hover:shadow-md transition-shadow">
                    <h3 class="font-semibold text-lg text-indigo-600">Stock Levels</h3>
                    <p class="mt-2 text-sm text-gray-500">Current stock vs reorder level for every product.</p>
                </a>
                <a href="{{ route('reports.movements') }}" class="bg-white rounded shadow p-6 hover:shadow-md transition-shadow">
                    <h3 class="font-semibold text-lg text-indigo-600">Movement Log</h3>
                    <p class="mt-2 text-sm text-gray-500">All stock movements with in/out totals.</p>
                </a>
                <a href="{{ route('reports.suppliers') }}" class="bg-white rounded shadow p-6 hover:shadow-md transition-shadow">
                    <h3 class="font-semibold text-lg text-indigo-600">Supplier Summary</h3>
                    <p class="mt-2 text-sm text-gray-500">Products and stock value per supplier.</p>
                </a>
                <a href="{{ route('reports.categories') }}" class="bg-white rounded shadow p-6 hover:shadow-md transition-shadow">
                    <h3 class="font-semibold text-lg text-indigo-600">Category Summary</h3>
                    <p class="mt-2 text-sm text-gray-500">Products and stock value per category.</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
