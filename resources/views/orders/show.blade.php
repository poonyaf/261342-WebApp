<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('รายละเอียดคำสั่งซื้อ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white rounded-2xl shadow p-6">

                {{-- header --}}
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <p class="text-sm text-gray-500">หมายเลขคำสั่งซื้อ</p>
                        <p class="font-bold text-2xl">#{{ $order->order_id }}</p>
                        <p class="text-sm text-gray-400">{{ $order->order_date->format('d M Y H:i') }}</p>
                    </div>
                    <span @class([
                        'px-4 py-2 rounded-full text-sm font-medium',
                        'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
                        'bg-blue-100 text-blue-700'     => $order->status === 'processing',
                        'bg-green-100 text-green-700'   => $order->status === 'delivered',
                        'bg-red-100 text-red-700'       => $order->status === 'cancelled',
                    ])>
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                {{-- order items --}}
                <div class="space-y-4 border-t pt-4">
                    @foreach ($order->items as $item)
                        <div class="flex items-center gap-4">
                            {{-- รูป --}}
                            @if ($item->product->image)
                                @if (str_starts_with($item->product->image, 'http'))
                                    <img src="{{ $item->product->image }}" class="w-16 h-16 object-cover rounded-lg">
                                @else
                                    <img src="{{ route('product.photo', ['filename' => basename($item->product->image)]) }}" 
     class="w-20 h-20 object-cover rounded-lg shadow-sm border border-gray-100">
                                @endif
                            @endif

                            {{-- ชื่อและราคา --}}
                            <div class="flex-1">
                                <p class="font-medium">{{ $item->product->name }}</p>
                                <p class="text-sm text-gray-500">฿{{ number_format($item->price_at_purchase, 2) }} x {{ $item->quantity }}</p>
                            </div>

                            {{-- รวม --}}
                            <p class="font-semibold">฿{{ number_format($item->price_at_purchase * $item->quantity, 2) }}</p>
                            </div>
                            
                        @endforeach
                </div>

                {{-- สรุปราคา --}}
                <div class="mt-6 pt-4 border-t space-y-2">
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>ยอดสินค้า</span>
                        <span>฿{{ number_format($order->items->sum(fn($i) => $i->price_at_purchase * $i->quantity), 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>ค่าจัดส่ง</span>
                        <span>฿50.00</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg pt-2 border-t">
                        <span>รวมทั้งหมด</span>
                        <span class="text-pink-500">฿{{ number_format($order->total_amount, 2) }}</span>
                    </div>

                    <div class="mt-4 p-4 rounded-2xl border border-gray-100 bg-gray-50">
                        <p class="text-xs tracking-wide text-gray-400 uppercase mb-1">Shipping Address</p>
                        <p class="font-medium text-gray-800">{{ $order->address }}</p>
                    </div>
                </div>

                {{-- ปุ่มกลับ --}}
<div class="mt-6 flex justify-between items-center">
    <a href="{{ route('orders.index') }}" class="text-sm text-gray-500 hover:underline">
        ← กลับไปคำสั่งซื้อทั้งหมด
    </a>

    {{-- ปุ่มแก้ไข --}}
    @if($order->status === 'pending')
        <a href="{{ route('orders.edit', $order->order_id) }}" 
           class="px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 text-sm">
            แก้ไขคำสั่งซื้อ
        </a>
    @endif
</div>

            </div>

        </div>
    </div>
</x-app-layout>