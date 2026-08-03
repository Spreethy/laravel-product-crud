<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Record Stock Movement</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded shadow p-6 max-w-lg mx-auto">
                <form method="POST" action="{{ route('stock.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                        <select name="product_id" id="product_id"
                                class="w-full border rounded px-3 py-2 @error('product_id') border-red-500 @enderror">
                            <option value="">Select a product</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected(old('product_id', $preselected) == $product->id)>
                                    {{ $product->name }} (stock: {{ $product->stock }})
                                </option>
                            @endforeach
                        </select>
                        @error('product_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <div class="flex gap-6">
                            <label class="inline-flex items-center">
                                <input type="radio" name="type" value="in" @checked(old('type', 'in') === 'in') class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ms-2 text-sm text-gray-700">Stock In</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="type" value="out" @checked(old('type') === 'out') class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ms-2 text-sm text-gray-700">Stock Out</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="type" value="adjustment" @checked(old('type') === 'adjustment') class="border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ms-2 text-sm text-gray-700">Adjustment</span>
                            </label>
                        </div>
                        @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input type="number" name="quantity" id="quantity" min="0" value="{{ old('quantity') }}"
                               class="w-full border rounded px-3 py-2 @error('quantity') border-red-500 @enderror">
                        <p class="text-xs text-gray-500 mt-1">For Stock In/Out this is the amount moved. For Adjustment this is the new stock value.</p>
                        @error('quantity') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                        <textarea name="reason" id="reason" rows="2"
                                  class="w-full border rounded px-3 py-2 @error('reason') border-red-500 @enderror">{{ old('reason') }}</textarea>
                        @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('stock.index') }}" class="text-gray-600 hover:underline">Cancel</a>
                        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded hover:bg-blue-600">Record</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
