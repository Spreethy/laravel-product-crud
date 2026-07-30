<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500">Total Products</div>
                        <div class="text-3xl font-bold text-indigo-600">{{ $totalProducts }}</div>
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
                        <div class="text-sm text-gray-500">Quick Actions</div>
                        <div class="mt-2 space-y-1">
                            <a href="{{ route('products.index') }}" class="block text-indigo-600 hover:underline">View Products</a>
                            <a href="{{ route('products.create') }}" class="block text-indigo-600 hover:underline">Add Product</a>
                            <a href="{{ route('chat.index') }}" class="block text-indigo-600 hover:underline">AI Chat</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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
