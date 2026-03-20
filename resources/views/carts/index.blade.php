<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: var(--secondary);">
            {{ __('Cart') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <p class="mb-4 text-green-600">{{ session('success') }}</p>
            @endif

            @if (!$cart || $cart->items->isEmpty())
                <p class="text-gray-500">Cart is empty</p>
            @else
                {{-- ✅ เปลี่ยนจาก dark:bg-gray-800 → bg-white, เพิ่ม border --}}
                <div class="bg-white shadow-sm border border-gray-200 rounded-2xl overflow-hidden">
                    <table class="w-full text-left">
                        {{-- ✅ หัวตาราง: เปลี่ยนจาก dark:bg-gray-700 → bg-gray-50 --}}
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="p-4 text-sm font-medium text-gray-500">สินค้า</th>
                                <th class="p-4 text-sm font-medium text-gray-500">ราคา</th>
                                <th class="p-4 text-sm font-medium text-gray-500">จำนวน</th>
                                <th class="p-4 text-sm font-medium text-gray-500">รวม</th>
                                <th class="p-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cart->items as $item)
                                {{-- ✅ border สีอ่อน --}}
                                <tr class="border-t border-gray-100">
                                    <td class="p-4 flex items-center gap-3">
                                        @if (str_starts_with($item->product->image, 'http'))
                                            <img src="{{ $item->product->image }}" class="w-14 h-14 object-cover rounded-xl">
                                        @else
                                            <img src="{{ asset('storage/products/' . $item->product->image) }}" class="w-14 h-14 object-cover rounded-xl">
                                        @endif
                                        {{-- ✅ ชื่อสินค้าสีดำ --}}
                                        <span class="text-gray-800 font-medium">{{ $item->product->name }}</span>
                                    </td>
                                    {{-- ✅ ราคาสีเทา --}}
                                    <td class="p-4 text-gray-600">฿{{ number_format($item->product->price, 2) }}</td>
                                    <td class="p-4">
                                        <form method="POST" action="{{ route('carts.update', $item->cart_item_id) }}">
                                            @csrf
                                            @method('PATCH')
                                            {{-- ✅ ปุ่ม +/- สไตล์ light --}}
                                            <div class="flex border border-gray-200 rounded-lg w-fit bg-white">
                                                <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}" class="px-3 py-1 text-gray-600 hover:bg-gray-50">-</button>
                                                <span class="w-12 text-center border-x border-gray-200 py-1 text-gray-800">{{ $item->quantity }}</span>
                                                <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="px-3 py-1 text-gray-600 hover:bg-gray-50">+</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="p-4 text-gray-800 font-medium">฿{{ number_format($item->product->price * $item->quantity, 2) }}</td>
                                    <td class="p-4">
                                        <form method="POST" action="{{ route('carts.destroy', $item->cart_item_id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-400 hover:text-red-600 text-sm">ลบ</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- ✅ Summary section: Price + Shipping แบบรูปที่ 2 --}}
                    <div class="px-4 pt-4 border-t border-gray-100">
                        <div class="flex justify-between text-sm text-gray-500 mb-1">
                            <span>Price</span>
                            <span>฿{{ number_format($cart->items->sum(fn($item) => $item->product->price * $item->quantity), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500 mb-3">
                            <span>Shipping Fee</span>
                            <span>฿50.00</span>
                        </div>
                    </div>

                    {{-- ✅ Total: สีชมพู แบบรูปที่ 2 --}}
                    <div class="px-4 pb-4 flex justify-between items-center border-t border-gray-100 pt-3">
                        <span class="font-bold text-gray-800 text-lg">Total</span>
                        <span class="text-2xl font-bold text-pink-500">
                            ฿{{ number_format($cart->items->sum(fn($item) => $item->product->price * $item->quantity) + 50, 2) }}
                        </span>
                    </div>

                    {{-- ปุ่ม checkout --}}
<div class="p-4 flex justify-end border-t">
    <a href="{{ route('orders.confirm') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700">
        Order Now
    </a>
</div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>