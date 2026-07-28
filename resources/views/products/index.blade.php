@extends('layouts.app')

@section('title', 'Products')

@section('content')
    <div class="bg-white rounded shadow overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-6 py-4">
                            <a href="{{ route('products.show', $product) }}" class="text-blue-600 hover:underline font-medium">{{ $product->name }}</a>
                        </td>
                        <td class="px-6 py-4">${{ number_format($product->price, 2) }}</td>
                        <td class="px-6 py-4">{{ $product->stock }}</td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <a href="{{ route('products.edit', $product) }}" class="text-indigo-600 hover:underline">Edit</a>
                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
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
@endsection
