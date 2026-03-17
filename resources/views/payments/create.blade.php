<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Payment') }}
        </h2>
    </x-slot>

    <div class="page-wrap">
        <div class="container">
            <div class="card card-pad space-y-6">
                @php
                    $subtotal = $order->items->sum(fn($i) => $i->price_at_purchase * $i->quantity);
                    $shippingFee = $order->shipping_fee ?? 50; // fallback just in case old orders have null
                    $grandTotal = $order->total_amount;        // DB truth (should already include shipping)
                @endphp

                {{-- รายละเอียด order --}}
                <div>
                    <p class="text-sm text-gray-500">Order Number</p>
                    <p class="font-bold text-2xl">#{{ $order->order_id }}</p>
                    
                </div>

                {{-- Product List --}}
                <div class="border-t pt-4 space-y-4">
                    <h3 class="font-semibold text-lg">Product List</h3>
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-4">
                            @if(str_starts_with($item->product->image, 'http'))
                                <img src="{{ $item->product->image }}" class="w-16 h-16 object-cover rounded-lg">
                            @else
                                <img src="{{ route('product.photo', ['filename' => basename($item->product->image)]) }}" 
     class="w-20 h-20 object-cover rounded-lg shadow-sm border border-gray-100">
                            @endif
                            <div class="flex-1">
                                <p class="font-medium">{{ $item->product->name }}</p>
                                <p class="text-sm text-gray-500">฿{{ number_format($item->price_at_purchase, 2) }} x {{ $item->quantity }}</p>
                            </div>
                            <p class="font-semibold">฿{{ number_format($item->price_at_purchase * $item->quantity, 2) }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- Price Summary --}}
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Order Price</span>
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

               {{-- Payment Method --}}
                @if($order->status === 'pending' && !$order->isPaid())
                <form method="POST" action="{{ route('payments.store') }}" class="border-t pt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                    <input type="hidden" name="amount" value="{{ $grandTotal }}">

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Payment Method</label>
                        <select name="method" class="select">
                            <option value="promptpay">PromptPay</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="bank_transfer">Transfer</option>
                            <option value="cash_on_delivery">Cash on Delivery</option>
                        </select>
                        @error('method') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <a href="{{ route('orders.show', $order->order_id) }}"
                           class="text-sm text-gray-500 hover:underline">
                            ← Back
                        </a>
                        <button type="submit" class="btn-success">
                            Confirm
                        </button>
                    </div>
                </form>
                @endif

                {{-- Cancel Button --}}
                @if($order->status === 'pending')
                <form method="POST" action="{{ route('orders.cancel', $order->order_id) }}" class="flex justify-end mt-2">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn-danger">
                        Cancel
                    </button>
                </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>