<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('ยืนยันคำสั่งซื้อ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow p-6 space-y-6">

                <form method="POST" action="{{ route('orders.store') }}">
                    @csrf

                    {{-- hidden inputs --}}
                    @if(isset($is_buy_now) && $is_buy_now)
                        {{-- Buy Now --}}
                        <input type="hidden" name="products[0][product_id]" value="{{ $product_id }}">
                        <input type="hidden" name="products[0][quantity]" value="{{ $quantity }}">
                    @else
                        {{-- Cart --}}
                        @foreach($cart->items as $item)
                            <input type="hidden" name="products[{{ $loop->index }}][product_id]" value="{{ $item->product_id }}">
                            <input type="hidden" name="products[{{ $loop->index }}][quantity]" value="{{ $item->quantity }}">
                        @endforeach
                    @endif

                    {{-- ข้อมูลผู้รับ --}}
                    <div class="space-y-4">
                        <h3 class="font-semibold text-lg">ข้อมูลผู้รับ</h3>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">ชื่อ-นามสกุล</label>
                            <input type="text" name="name"
                                value="{{ old('name', Auth::user()->name) }}"
                                class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                            @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">ที่อยู่จัดส่ง</label>
                            <textarea name="address" rows="3"
                                class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300"
                                placeholder="กรอกที่อยู่จัดส่ง">{{ old('address', Auth::user()->address) }}</textarea>
                            @error('address') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">เบอร์โทรศัพท์</label>
                            <input type="text" name="phone"
                                value="{{ old('phone', Auth::user()->phone_number) }}"
                                class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                            @error('phone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">วิธีชำระเงิน</label>
                            <select name="payment_method"
                                class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                                <option value="promptpay" {{ old('payment_method') == 'promptpay' ? 'selected' : '' }}>PromptPay</option>
                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>บัตรเครดิต</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>โอนเงิน</option>
                                <option value="cash_on_delivery" {{ old('payment_method') == 'cash_on_delivery' ? 'selected' : '' }}>เก็บเงินปลายทาง</option>
                            </select>
                            @error('payment_method') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- รายการสินค้า --}}
                    <div class="border-t pt-4 space-y-4">
                        <h3 class="font-semibold text-lg">รายการสินค้า</h3>

                        @php
                            $displayItems = isset($is_buy_now) && $is_buy_now
                                ? $items
                                : $cart->items;
                        @endphp

                        @foreach($displayItems as $item)
                            <div class="flex items-center gap-4">
                                @php $product = $item['product'] ?? $item->product; @endphp
                                @if(str_starts_with($product->image, 'http'))
                                    <img src="{{ $product->image }}" class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <img src="{{ asset('storage/products/' . $product->image) }}" class="w-16 h-16 object-cover rounded-lg">
                                @endif
                                <div class="flex-1">
                                    <p class="font-medium">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-500">฿{{ number_format($product->price, 2) }} x {{ $item['quantity'] ?? $item->quantity }}</p>
                                </div>
                                <p class="font-semibold">฿{{ number_format($product->price * ($item['quantity'] ?? $item->quantity), 2) }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- สรุปราคา --}}
                    @php
                        $total = isset($is_buy_now) && $is_buy_now
                            ? $items->sum(fn($i) => $i['product']->price * $i['quantity'])
                            : $cart->items->sum(fn($i) => $i->product->price * $i->quantity);
                    @endphp

                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>ยอดสินค้า</span>
                            <span>฿{{ number_format($total, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>ค่าจัดส่ง</span>
                            <span>฿50.00</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg pt-2 border-t">
                            <span>รวมทั้งหมด</span>
                            <span class="text-pink-500">฿{{ number_format($total, 2) }}</span>
                        </div>
                    </div>

                    {{-- ปุ่ม --}}
                    <div class="border-t pt-4 flex justify-between items-center">
                        @if(isset($is_buy_now) && $is_buy_now)
                            <a href="javascript:history.back()" class="text-sm text-gray-500 hover:underline">
                                ← กลับไปสินค้า
                            </a>
                        @else
                            <a href="{{ route('carts.index') }}" class="text-sm text-gray-500 hover:underline">
                                ← กลับไปตะกร้า
                            </a>
                        @endif
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            ยืนยันสั่งซื้อ
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>