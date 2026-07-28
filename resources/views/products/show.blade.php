@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="bg-white rounded shadow p-6 max-w-lg mx-auto">
        <h1 class="text-2xl font-bold mb-4">{{ $product->name }}</h1>

        <dl class="space-y-3">
            <div class="flex">
                <dt class="w-24 text-gray-500">Price</dt>
                <dd>${{ number_format($product->price, 2) }}</dd>
            </div>
            <div class="flex">
                <dt class="w-24 text-gray-500">Stock</dt>
                <dd>{{ $product->stock }}</dd>
            </div>
            @if ($product->description)
                <div class="flex">
                    <dt class="w-24 text-gray-500">Description</dt>
                    <dd>{{ $product->description }}</dd>
                </div>
            @endif
            <div class="flex">
                <dt class="w-24 text-gray-500">Created</dt>
                <dd>{{ $product->created_at->format('M d, Y') }}</dd>
            </div>
        </dl>

        <div class="mt-6 flex space-x-3">
            <a href="{{ route('products.edit', $product) }}" class="bg-indigo-500 text-white px-4 py-2 rounded hover:bg-indigo-600">Edit</a>
            <a href="{{ route('products.index') }}" class="text-gray-600 hover:underline py-2">Back</a>
        </div>
    </div>
@endsection
