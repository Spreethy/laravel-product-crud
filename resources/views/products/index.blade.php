<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Products</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">ID</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Supplier</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Price</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Stock</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($products as $product)
                            <tr>
                                <td class="px-6 py-4 text-gray-500">{{ $product->id }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:underline font-medium">{{ $product->name }}</a>
                                    @if (! $product->is_active)
                                        <span class="ms-2 inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-gray-100 text-gray-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500">{{ $product->category->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $product->supplier->name ?? '—' }}</td>
                                <td class="px-6 py-4">${{ number_format($product->price, 2) }}</td>
                                <td class="px-6 py-4">{{ $product->stock }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:underline">Edit</a>
                                    @if (auth()->user()->isAdmin())
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    No products yet.
                                    <a href="{{ route('products.create') }}" class="text-blue-600 hover:underline block mt-2">Create your first product</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
