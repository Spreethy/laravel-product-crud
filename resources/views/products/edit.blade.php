<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Product</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow p-6 max-w-lg mx-auto">
                <h1 class="text-2xl font-bold mb-6">Edit Product</h1>

                <form action="{{ route('products.update', $product) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}"
                               class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="sku" class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                        <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku) }}"
                               class="w-full border rounded px-3 py-2 @error('sku') border-red-500 @enderror">
                        @error('sku') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full border rounded px-3 py-2 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                            <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $product->price) }}"
                                   class="w-full border rounded px-3 py-2 @error('price') border-red-500 @enderror">
                            @error('price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="stock" class="block text-sm font-medium text-gray-700 mb-1">Stock</label>
                            <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}"
                                   class="w-full border rounded px-3 py-2 @error('stock') border-red-500 @enderror">
                            @error('stock') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="reorder_level" class="block text-sm font-medium text-gray-700 mb-1">Reorder Level</label>
                        <input type="number" name="reorder_level" id="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}"
                               class="w-full border rounded px-3 py-2 @error('reorder_level') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">Low-stock alert threshold for this product.</p>
                        @error('reorder_level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="mb-4">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category_id" id="category_id"
                                    class="w-full border rounded px-3 py-2 @error('category_id') border-red-500 @enderror">
                                <option value="">No category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                            <select name="supplier_id" id="supplier_id"
                                    class="w-full border rounded px-3 py-2 @error('supplier_id') border-red-500 @enderror">
                                <option value="">No supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected(old('supplier_id', $product->supplier_id) == $supplier->id)>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product->is_active))
                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <span class="ms-2 text-sm text-gray-700">Active product</span>
                        </label>
                        @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('products.index') }}" class="text-gray-600 hover:underline">Cancel</a>
                        <button type="submit" class="bg-indigo-500 text-white px-6 py-2 rounded hover:bg-indigo-600">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
