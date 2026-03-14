<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('รายละเอียดการชำระเงิน') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow p-6 space-y-6">

                {{-- สถานะ --}}
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">หมายเลขการชำระเงิน</p>
                        <p class="font-bold text-2xl">#{{ $payment->payment_id }}</p>
                    </div>
                    <span @class([
                        'px-4 py-2 rounded-full text-sm font-semibold',
                        'bg-yellow-100 text-yellow-700' => $payment->status === 'pending',
                        'bg-green-100 text-green-700'  => $payment->status === 'paid',
                        'bg-red-100 text-red-700'      => $payment->status === 'failed',
                    ])>
                        @if($payment->status === 'pending') Pending
                        @elseif($payment->status === 'paid') Paid
                        @elseif($payment->status === 'failed') Failed
                        @else {{ $payment->status }}
                        @endif
                    </span>
                </div>

                {{-- ข้อมูลการชำระเงิน --}}
                <div class="border-t pt-4 space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>หมายเลขคำสั่งซื้อ</span>
                        <span class="font-medium">#{{ $payment->order->order_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>วิธีชำระเงิน</span>
                        <span class="font-medium">{{ ucfirst($payment->method) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>วันที่ชำระเงิน</span>
                        <span class="font-medium">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                {{-- รายการสินค้า --}}
                <div class="border-t pt-4 space-y-4">
                    <h3 class="font-semibold text-lg">รายการสินค้า</h3>
                    @foreach($payment->order->items as $item)
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
                        <span>฿{{ number_format($payment->amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>ค่าจัดส่ง</span>
                        <span>฿50.00</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-2 border-t">
                        <span>รวมทั้งหมด</span>
                        <span class="text-pink-500">฿{{ number_format($payment->amount, 2) }}</span>
                    </div>
                </div>

                {{-- ปุ่ม --}}
                <div class="border-t pt-4 flex justify-between items-center">
                    <a href="{{ route('orders.show', $payment->order->order_id) }}"
                       class="text-sm text-gray-500 hover:underline">
                        ← กลับไปคำสั่งซื้อ
                    </a>
                    <a href="{{ route('orders.index') }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        ดูคำสั่งซื้อทั้งหมด
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>