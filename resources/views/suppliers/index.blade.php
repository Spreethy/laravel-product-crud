<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Suppliers</h2>
            @if (auth()->user()->isAdmin())
                <a href="{{ route('suppliers.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    Add Supplier
                </a>
            @endif
        </div>
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
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Name</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Contact</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="text-left px-6 py-3 text-xs font-medium text-gray-500 uppercase">Products</th>
                            <th class="text-right px-6 py-3 text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($suppliers as $supplier)
                            <tr>
                                <td class="px-6 py-4 font-medium">{{ $supplier->name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $supplier->contact_name }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $supplier->email }}</td>
                                <td class="px-6 py-4 text-gray-500">{{ $supplier->products_count }}</td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    @if (auth()->user()->isAdmin())
                                        <a href="{{ route('suppliers.edit', $supplier) }}" class="text-indigo-600 hover:underline">Edit</a>
                                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="inline"
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
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No suppliers yet.
                                    <a href="{{ route('suppliers.create') }}" class="text-blue-600 hover:underline block mt-2">Add your first supplier</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $suppliers->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
