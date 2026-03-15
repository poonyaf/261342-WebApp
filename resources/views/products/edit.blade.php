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

                    {{-- ชื่อสินค้า --}}
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">ชื่อสินค้า</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}"
                            class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                        @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- คำอธิบาย --}}
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">คำอธิบาย</label>
                        <textarea name="description" rows="3"
                            class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">{{ old('description', $product->description) }}</textarea>
                        @error('description') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- ราคา --}}
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">ราคา (฿)</label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0"
                            class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                        @error('price') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- จำนวนสต็อก --}}
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">จำนวนสต็อก</label>
                        <input type="number" name="stock_number" value="{{ old('stock_number', $product->stock_number) }}" min="0"
                            class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                        @error('stock_number') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- รูปภาพปัจจุบัน --}}
                    @if($product->image)
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">รูปภาพปัจจุบัน</label>
                        @if(str_starts_with($product->image, 'http'))
                            <img src="{{ $product->image }}" class="w-32 h-32 object-cover rounded-lg">
                        @else
                            <img src="{{ route('product.photo', ['filename' => basename($product->image)]) }}" 
             class="w-full max-h-80 object-contain rounded-xl">
                        @endif
                    </div>
                    @endif

                    {{-- รูปภาพใหม่ --}}
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">เปลี่ยนรูปภาพ (ถ้าต้องการ)</label>
                        <input type="file" name="image" accept="image/*"
                            class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                        @error('image') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- ปุ่ม --}}
                    <div class="flex justify-between items-center pt-2">
                        <a href="{{ route('admin.products') }}" class="text-sm text-gray-500 hover:underline">
                            ← กลับ
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 text-sm">
                           save change
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>