<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Movement Log</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form method="GET" action="{{ route('reports.movements') }}" class="bg-white rounded shadow p-4 mb-6 flex flex-wrap items-end gap-4">
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
                <a href="{{ route('reports.movements') }}" class="text-sm text-gray-600 hover:underline py-2">Reset</a>
            </form>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white rounded shadow p-6">
                    <div class="text-sm text-gray-500">Total Stock In</div>
                    <div class="text-3xl font-bold text-green-600">{{ $inTotal }}</div>
                </div>
                <div class="bg-white rounded shadow p-6">
                    <div class="text-sm text-gray-500">Total Stock Out</div>
                    <div class="text-3xl font-bold text-red-600">{{ $outTotal }}</div>
                </div>
            </div>

            <div class="bg-white rounded shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Qty</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Before → After</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">By</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($movements as $movement)
                            <tr>
                                <td class="px-6 py-4 font-medium">{{ $movement->product->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ ucfirst($movement->type) }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $movement->quantity }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $movement->previous_stock }} → {{ $movement->new_stock }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $movement->user->name ?? 'System' }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $movement->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">No movements found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
