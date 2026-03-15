<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('สินค้าที่ชอบ') }}
        </h2>
    </x-slot>

    <div class="py-12" style="background-color: #fff5f7;">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="flex items-center gap-2 mb-6">
               <a href="{{ route('dashboard') }}" class="text-sm text-gray-500 hover:underline">← กลับ</a>
            </div>

            <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <span class="text-yellow-400">★</span>
                สินค้าที่ชอบ ({{ $wishlists->count() }} รายการ)
            </h1>

            {{-- Wishlist Items --}}
            <div class="space-y-4">
                @forelse($wishlists as $wishlist)
                <div class="bg-white rounded-2xl shadow-sm p-4 flex items-center gap-4">

                    {{-- รูปสินค้า --}}
                    <div class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0">
                        @if(str_starts_with($wishlist->product->image, 'http'))
                            <img src="{{ $wishlist->product->image }}" class="w-full h-full object-cover">
                        @else
                            <img src="{{ asset('storage/products/' . $wishlist->product->image) }}" class="w-full h-full object-cover">
                        @endif
                    </div>

                    {{-- ข้อมูลสินค้า --}}
                    <div class="flex-1">
                        <p class="font-semibold text-gray-800">{{ $wishlist->product->name }}</p>
                        <p class="text-pink-500 font-bold mt-1">฿{{ number_format($wishlist->product->price, 2) }}</p>
                    </div>

                    {{-- ปุ่ม --}}
                    <div class="flex items-center gap-2">
                        {{-- เพิ่มในตะกร้า --}}
                        <form method="POST" action="{{ route('carts.store') }}">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $wishlist->product->product_id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit"
                                class="flex items-center gap-1 px-4 py-2 bg-pink-400 hover:bg-pink-500 text-white text-sm rounded-full transition">
                                🛒 เพิ่มในตะกร้า
                            </button>
                        </form>

                        {{-- ลบออกจาก wishlist --}}
                        <form method="POST" action="{{ route('wishlist.destroy', $wishlist->wishlist_id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="p-2 text-red-400 hover:text-red-600 transition">
                                🗑
                            </button>
                        </form>
                    </div>

                </div>
                @empty
                <div class="bg-white rounded-2xl shadow-sm p-8 text-center text-gray-400">
                   No items in your wishlist yet. Start adding some products you love! 💖
                </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>