<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Bar -->
            <div class="mb-6">
                <form action="{{ route('products.index') }}" method="GET" class="flex gap-2">
                    <input type="text" name="search" placeholder="Search products by name or description..."
                           value="{{ $search ?? '' }}"
                           class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Search
                    </button>
                    @if ($search ?? false)
                        <a href="{{ route('products.index') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                            Clear
                        </a>
                    @endif
                </form>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($products as $product)
                    <a href="{{ route('products.show', $product->product_id) }}" 
                       class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden hover:shadow-lg transition">
                        @if ($product->image)
    @if (str_starts_with($product->image, 'http'))
        <img src="{{ $product->image }}" class="w-full h-48 object-cover">
    @else
        <img src="{{ asset('storage/products/' . $product->image) }}" class="w-full h-48 object-cover">
    @endif
@else
    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
        <span class="text-gray-400">No Image</span>
    </div>
@endif
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $product->name }}</h3>
                            <p class="text-green-600 font-bold mt-1">฿{{ number_format($product->price, 2) }}</p>
                            <p class="text-sm text-gray-500 mt-1">remaining: {{ $product->stock_number }}</p>
                        </div>
                    </a>
                @empty
                    <p class="text-gray-500">{{ $search ? 'No products found matching your search.' : 'No products available' }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>