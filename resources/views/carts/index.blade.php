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
                <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="p-4">สินค้า</th>
                                <th class="p-4">ราคา</th>
                                <th class="p-4">จำนวน</th>
                                <th class="p-4">รวม</th>
                                <th class="p-4"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cart->items as $item)
                                <tr class="border-t">
                                    <td class="p-4 flex items-center gap-3">
    @if (str_starts_with($item->product->image, 'http'))
        <img src="{{ $item->product->image }}" class="w-16 h-16 object-cover rounded">
    @else
        <img src="{{ asset('storage/products/' . $item->product->image) }}" class="w-16 h-16 object-cover rounded">
    @endif
    {{ $item->product->name }} {{-- ✅ เพิ่มชื่อสินค้า --}}
</td>
                                    <td class="p-4">฿{{ number_format($item->product->price, 2) }}</td>
                                    <td class="p-4">
                                        <form method="POST" action="{{ route('carts.update', $item->cart_item_id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <div class="flex border rounded-lg w-fit">
                                                <button type="submit" name="quantity" value="{{ $item->quantity - 1 }}" class="px-3 py-1">-</button>
                                                <span class="w-12 text-center border-x py-1">{{ $item->quantity }}</span>
                                                <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="px-3 py-1">+</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td class="p-4">฿{{ number_format($item->product->price * $item->quantity, 2) }}</td>
                                    <td class="p-4">
                                        <form method="POST" action="{{ route('carts.destroy', $item->cart_item_id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:underline">ลบ</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- ราคารวม --}}
                    <div class="p-4 flex justify-between items-center border-t">
                        <span class="font-bold text-lg">Total:</span>
                        <span class="text-2xl font-bold text-green-600">
                            ฿{{ number_format($cart->items->sum(fn($item) => $item->product->price * $item->quantity), 2) }}
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