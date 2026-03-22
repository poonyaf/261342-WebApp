<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Manage Products
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('admin.products') }}">
                <div class="bg-white rounded-2xl shadow-sm border border-pink-100 p-4 flex gap-3 items-center">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search products..."
                        class="flex-1 border-gray-200 rounded-xl p-3 text-sm focus:ring-pink-500 focus:border-pink-500">
                    <button type="submit" class="px-6 py-3 bg-pink-400 hover:bg-pink-500 text-white rounded-xl text-sm font-medium">
                        Search
                    </button>
                </div>
            </form>

            {{-- ปุ่มเพิ่มสินค้า --}}
            <div class="flex justify-end">
                <a href="{{ route('products.create') }}"
                   class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 text-sm">
                    + Add Product
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">ID</th>
                            <th class="px-6 py-3 text-left">Image</th>
                            <th class="px-6 py-3 text-left">Name</th>
                            <th class="px-6 py-3 text-left">Price</th>
                            <th class="px-6 py-3 text-left">Stock</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($products as $product)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-400">{{ $product->product_id }}</td>
                            <td class="px-6 py-4">
                                @if($product->image)
                                    @if(str_starts_with($product->image, 'http'))
                                        <img src="{{ $product->image }}" class="w-12 h-12 object-cover rounded-lg">
                                    @else
                                        <img src="{{ route('product.photo', ['filename' => basename($product->image)]) }}" 
                                             class="w-12 h-12 object-cover rounded-lg">
                                    @endif
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg"></div>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $product->name }}</td>
                            <td class="px-6 py-4 text-gray-600">฿{{ number_format($product->price, 2) }}</td>
                            <td class="px-6 py-4">
                                <span @class([
                                    'px-2 py-1 rounded-full text-xs font-semibold',
                                    'bg-red-100 text-red-700'       => $product->stock_number <= 0,
                                    'bg-yellow-100 text-yellow-700' => $product->stock_number > 0 && $product->stock_number <= 5,
                                    'bg-green-100 text-green-700'   => $product->stock_number > 5,
                                ])>
                                    {{ $product->stock_number }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    <a href="{{ route('products.edit', $product->product_id) }}"
                                       class="px-3 py-1 bg-blue-500 text-white rounded-lg text-xs hover:bg-blue-600">
                                        Edit
                                    </a>
                                    <form method="POST" action="{{ route('admin.products.destroy', $product->product_id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Are you sure?')"
                                            class="px-3 py-1 bg-red-500 text-white rounded-lg text-xs hover:bg-red-600">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>