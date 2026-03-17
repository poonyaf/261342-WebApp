<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm p-6">

                <form method="POST" action="{{ route('products.update', $product->product_id) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- Product Name --}}
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Product Name</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                            class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                        @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Description</label>
                        <textarea name="description" rows="3"
                            class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">{{ old('description', $product->description) }}</textarea>
                        @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Price --}}
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">ราคา (฿)</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0"
                            class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                        @error('price') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Stock Amount --}}
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Remaining</label>
                        <input type="number" name="stock_number" value="{{ old('stock_number', $product->stock_number) }}" min="0"
                            class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                        @error('stock_number') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Current Image --}}
                    @if($product->image)
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Current Image</label>
                        @if(str_starts_with($product->image, 'http'))
                            <img src="{{ $product->image }}" class="w-32 h-32 object-cover rounded-lg">
                        @else
                            <img src="{{ route('product.photo', ['filename' => basename($product->image)]) }}" 
             class="w-full max-h-80 object-contain rounded-xl">
                        @endif
                    </div>
                    @endif

                    {{-- New Image --}}
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Change Image (Optional)</label>
                        <input type="file" name="image" accept="image/*"
                            class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                        @error('image') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Button --}}
                    <div class="flex justify-between items-center pt-2">
                        <a href="{{ route('admin.products') }}" class="text-sm text-gray-500 hover:underline">
                            ← Back
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 text-sm">
                           Save Changes
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>