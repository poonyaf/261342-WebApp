<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Confirm Order') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow p-6 space-y-6">

            @php
                $shippingFee = 50;
                $displayItems = (isset($is_buy_now) && $is_buy_now) ? $items : $cart->items;
                $subtotal = $displayItems->sum(fn($i) => ($i['product'] ?? $i->product)->price * ($i['quantity'] ?? $i->quantity));
                $grandTotal = $subtotal + $shippingFee;
            @endphp

                <form method="POST" action="{{ route('orders.store') }}">
                    @csrf

                    {{-- hidden inputs --}}
                    @if(isset($is_buy_now) && $is_buy_now)
                        <input type="hidden" name="products[0][product_id]" value="{{ $product_id }}">
                        <input type="hidden" name="products[0][quantity]" value="{{ $quantity }}">
                    @else
                        @foreach($cart->items as $item)
                            <input type="hidden" name="products[{{ $loop->index }}][product_id]" value="{{ $item->product_id }}">
                            <input type="hidden" name="products[{{ $loop->index }}][quantity]" value="{{ $item->quantity }}">
                        @endforeach
                    @endif

                    {{-- ข้อมูลผู้รับ --}}
                    <div class="space-y-4">
                        <h3 class="font-semibold text-lg">Recipient Information</h3>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}"
                                class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                            @error('name') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Shipping Address</label>
                            <textarea name="address" rows="3"
                                class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300"
                                placeholder="กรอกที่อยู่จัดส่ง">{{ old('address', Auth::user()->address) }}</textarea>
                            @error('address') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Telephone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', Auth::user()->phone_number) }}"
                                class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                            @error('phone') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Payment Method</label>
                            <select name="payment_method"
                                class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                                <option value="promptpay">PromptPay</option>
                                <option value="credit_card">Credit Card</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cash_on_delivery">Cash on Delivery</option>
                            </select>
                            @error('payment_method') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- รายการสินค้า --}}
                    <div class="border-t pt-4 space-y-4">
                        <h3 class="font-semibold text-lg">Product List</h3>

                        @foreach($displayItems as $item)
                            @php
                                $product = $item['product'] ?? $item->product;
                                $qty = $item['quantity'] ?? $item->quantity;
                            @endphp
                            <div class="flex items-center gap-4">
                                @if(str_starts_with($product->image, 'http'))
                                    <img src="{{ $product->image }}" class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <img src="{{ route('product.photo', ['filename' => basename($product->image)]) }}"
                                         class="w-16 h-16 object-cover rounded-lg shadow-sm border border-gray-100">
                                @endif
                                <div class="flex-1">
                                    <p class="font-medium">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-500">฿{{ number_format($product->price, 2) }} x {{ $qty }}</p>
                                </div>
                                <p class="font-semibold">฿{{ number_format($product->price * $qty, 2) }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- สรุปราคา --}}
                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Price</span>
                            <span>฿{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Shipping Fee</span>
                            <span>฿{{ number_format($shippingFee, 2) }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg pt-2 border-t">
                            <span>Total</span>
                            <span class="text-pink-500">฿{{ number_format($grandTotal, 2) }}</span>
                        </div>
                    </div>

                    {{-- ปุ่ม --}}
                    <div class="border-t pt-4 flex justify-between items-center">
                        @if(isset($is_buy_now) && $is_buy_now)
                            <a href="javascript:history.back()" class="text-sm text-gray-500 hover:underline">← Back</a>
                        @else
                            <a href="{{ route('carts.index') }}" class="text-sm text-gray-500 hover:underline">← Back to Cart</a>
                        @endif
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Confirm
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>