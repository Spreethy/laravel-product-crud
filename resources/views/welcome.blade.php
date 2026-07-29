<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name') }} - Product Manager</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900">
        <header class="bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div></div>
                    <nav class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">Register</a>
                            @endif
                        @endauth
                    </nav>
                </div>
            </div>
        </header>

        <main>
            <div class="bg-gradient-to-br from-indigo-50 via-white to-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
                    <div class="max-w-3xl mx-auto text-center">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight">Manage Your Products with Ease</h1>
                        <p class="mt-6 text-lg sm:text-xl text-gray-600 leading-relaxed">A simple, powerful product management system. Create, track, and organize your inventory with real-time stock control.</p>
                        <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                            @auth
                                <a href="{{ route('products.index') }}" class="inline-flex items-center px-8 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-base text-white shadow-sm hover:bg-indigo-700">View Products</a>
                            @else
                                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-3 bg-indigo-600 border border-transparent rounded-lg font-semibold text-base text-white shadow-sm hover:bg-indigo-700">Get Started Free</a>
                                <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-3 border border-gray-300 rounded-lg font-semibold text-base text-gray-700 bg-white shadow-sm hover:bg-gray-50">Sign In</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-gray-900">Create Products</h3>
                        <p class="mt-2 text-sm text-gray-600">Add new products with name, price, description, and stock quantity in seconds.</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-gray-900">Track Stock</h3>
                        <p class="mt-2 text-sm text-gray-600">Monitor inventory levels and know exactly how many items you have at all times.</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <h3 class="mt-4 font-semibold text-gray-900">Manage Listings</h3>
                        <p class="mt-2 text-sm text-gray-600">Edit, update, or remove products with a clean, intuitive interface.</p>
                    </div>
                </div>
            </div>

            @if ($products->count())
                <div class="bg-gray-50 border-t border-gray-200">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                        <h2 class="text-2xl font-bold text-gray-900">Latest Products</h2>
                        <p class="mt-2 text-gray-600">Check out some of the products managed in this system.</p>
                        <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($products as $product)
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                                    <h3 class="font-semibold text-gray-900 text-lg">{{ $product->name }}</h3>
                                    @if ($product->description)
                                        <p class="mt-2 text-sm text-gray-600 line-clamp-2">{{ $product->description }}</p>
                                    @endif
                                    <div class="mt-4 flex items-center justify-between">
                                        <span class="text-2xl font-bold text-indigo-600">${{ number_format($product->price, 2) }}</span>
                                        <span class="text-sm {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $product->stock > 0 ? $product->stock . ' in stock' : 'Out of stock' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @auth
                            <div class="mt-8 text-center">
                                <a href="{{ route('products.index') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">View all products &rarr;</a>
                            </div>
                        @endauth
                    </div>
                </div>
            @endif

            <div class="bg-indigo-600">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
                    <h2 class="text-2xl sm:text-3xl font-bold text-white">Ready to get started?</h2>
                    <p class="mt-3 text-indigo-100 text-lg">Create an account and start managing your products today.</p>
                    <div class="mt-6">
                        @auth
                            <a href="{{ route('products.create') }}" class="inline-flex items-center px-6 py-3 bg-white border border-transparent rounded-lg font-semibold text-base text-indigo-600 shadow-sm hover:bg-indigo-50">Add Your First Product</a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-white border border-transparent rounded-lg font-semibold text-base text-indigo-600 shadow-sm hover:bg-indigo-50">Create Free Account</a>
                        @endauth
                    </div>
                </div>
            </div>
        </main>

        <footer class="bg-white border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <p class="text-center text-sm text-gray-500">&copy; {{ date('Y') }} All rights reserved.</p>
            </div>
        </footer>
    </body>
</html>