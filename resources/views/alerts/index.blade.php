<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Stock Alerts</h2>
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
            <div class="bg-white rounded shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        {{ request()->boolean('show_resolved') ? 'Showing all alerts' : 'Showing open alerts' }}
                    </p>
                    <a href="{{ request()->boolean('show_resolved') ? route('alerts.index') : route('alerts.index', ['show_resolved' => 1]) }}" class="text-sm text-indigo-600 hover:underline">
                        {{ request()->boolean('show_resolved') ? 'Show open only' : 'Show resolved too' }}
                    </a>
                </div>

                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Reorder Level</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Resolved</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($alerts as $alert)
                            <tr>
                                <td class="px-6 py-4 font-medium">
                                    <a href="{{ route('products.show', $alert->product) }}" class="text-blue-600 hover:underline">{{ $alert->product->name }}</a>
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $alert->product->stock }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $alert->product->reorder_level }}</td>
                                <td class="px-6 py-4">
                                    @if ($alert->status === \App\Models\StockAlert::STATUS_OPEN)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-red-100 text-red-700">Open</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-green-100 text-green-700">Resolved</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500">
                                    @if ($alert->resolved_at)
                                        {{ $alert->resolved_at->format('M d, Y') }}
                                        @if ($alert->resolver) by {{ $alert->resolver->name }} @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if ($alert->status === \App\Models\StockAlert::STATUS_OPEN)
                                        <form action="{{ route('alerts.resolve', $alert) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-indigo-600 hover:underline">Resolve</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">No alerts found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $alerts->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
