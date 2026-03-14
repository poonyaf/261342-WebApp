<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('ชำระเงิน') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow p-6 space-y-6">

                {{-- รายละเอียด order --}}
                <div>
                    <p class="text-sm text-gray-500">หมายเลขคำสั่งซื้อ</p>
                    <p class="font-bold text-2xl">#{{ $order->order_id }}</p>
                    
                </div>

                {{-- รายการสินค้า --}}
                <div class="border-t pt-4 space-y-4">
                    <h3 class="font-semibold text-lg">รายการสินค้า</h3>
                    @foreach($order->items as $item)
                        <div class="flex items-center gap-4">
                            @if(str_starts_with($item->product->image, 'http'))
                                <img src="{{ $item->product->image }}" class="w-16 h-16 object-cover rounded-lg">
                            @else
                                <img src="{{ asset('storage/products/' . $item->product->image) }}" class="w-16 h-16 object-cover rounded-lg">
                            @endif
                            <div class="flex-1">
                                <p class="font-medium">{{ $item->product->name }}</p>
                                <p class="text-sm text-gray-500">฿{{ number_format($item->price_at_purchase, 2) }} x {{ $item->quantity }}</p>
                            </div>
                            <p class="font-semibold">฿{{ number_format($item->price_at_purchase * $item->quantity, 2) }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- สรุปราคา --}}
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>ยอดสินค้า</span>
                        <span>฿{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>ค่าจัดส่ง</span>
                        <span>฿50.00</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-2 border-t">
                        <span>รวมทั้งหมด</span>
                        <span class="text-pink-500">฿{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>

               {{-- form ชำระเงิน --}}
                @if($order->status === 'pending' && !$order->isPaid())
                <form method="POST" action="{{ route('payments.store') }}" class="border-t pt-4 space-y-4">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->order_id }}">
                    <input type="hidden" name="amount" value="{{ $order->total_amount }}">

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">วิธีชำระเงิน</label>
                        <select name="method"
                            class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300">
                            <option value="promptpay">PromptPay</option>
                            <option value="credit_card">บัตรเครดิต</option>
                            <option value="bank_transfer">โอนเงิน</option>
                            <option value="cash_on_delivery">เก็บเงินปลายทาง</option>
                        </select>
                        @error('method') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-between items-center pt-2">
                        <a href="{{ route('orders.show', $order->order_id) }}"
                           class="text-sm text-gray-500 hover:underline">
                            ← กลับไปคำสั่งซื้อ
                        </a>
                        <button type="submit"
                            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm">
                            ยืนยันชำระเงิน
                        </button>
                    </div>
                </form>
                @endif

                {{-- ปุ่มยกเลิก --}}
                @if($order->status === 'pending')
                <form method="POST" action="{{ route('orders.cancel', $order->order_id) }}" class="flex justify-end mt-2">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                        class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm">
                        ยกเลิก
                    </button>
                </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>