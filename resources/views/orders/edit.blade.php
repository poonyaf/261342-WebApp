<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('แก้ไขคำสั่งซื้อ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow p-6 space-y-6">

                {{-- header --}}
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-500">หมายเลขคำสั่งซื้อ</p>
                        <p class="font-bold text-2xl">#{{ $order->order_id }}</p>
                        <p class="text-sm text-gray-400">{{ $order->order_date->format('d M Y H:i') }}</p>
                    </div>
                    <span @class([
                        'px-4 py-2 rounded-full text-sm font-medium',
                        'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
                        'bg-blue-100 text-blue-700'     => $order->status === 'processing',
                        'bg-purple-100 text-purple-700' => $order->status === 'packing',
                        'bg-orange-100 text-orange-700' => $order->status === 'delivering',
                        'bg-green-100 text-green-700'   => $order->status === 'complete',
                        'bg-red-100 text-red-700'       => $order->status === 'cancelled',
                    ])>
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

                <form action="{{ route('orders.update', $order->order_id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    {{-- ที่อยู่จัดส่ง --}}
                    @php
                        $canEditAddress = $order->status === 'pending'
                            && $order->order_date->diffInHours(now()) < 24;
                    @endphp

                    <div class="border-t pt-4">
                        <h3 class="font-semibold text-lg mb-3">ที่อยู่จัดส่ง</h3>
                        @if($canEditAddress)
                            <textarea name="address" rows="3"
                                class="w-full border rounded-lg p-3 text-sm focus:outline-none focus:ring-2 focus:ring-pink-300"
                                placeholder="กรอกที่อยู่จัดส่ง">{{ old('address', $order->address ?? Auth::user()->address) }}</textarea>
                        @else
                            <p class="text-gray-600 text-sm">{{ $order->address ?? Auth::user()->address ?? 'ไม่มีที่อยู่' }}</p>
                            @if($order->status === 'pending')
                                <p class="text-red-400 text-xs mt-1">หมดเวลาแก้ไขที่อยู่แล้ว (เกิน 24 ชม.)</p>
                            @endif
                        @endif
                    </div>

                    {{-- รายการสินค้า --}}
                    <div class="border-t pt-4">
                        <h3 class="font-semibold text-lg mb-3">รายการสินค้า</h3>
                        <div class="space-y-3">
                            @foreach($order->items as $index => $item)
                                <div class="flex items-center gap-3">
                                    @if($order->status === 'pending')
                                        <select name="products[{{ $index }}][product_id]"
                                            class="flex-1 border rounded-lg p-2 text-sm">
                                            @foreach($products as $product)
                                                <option value="{{ $product->product_id }}"
                                                    {{ $item->product_id == $product->product_id ? 'selected' : '' }}>
                                                    {{ $product->name }} (฿{{ number_format($product->price, 2) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="number" name="products[{{ $index }}][quantity]"
                                            value="{{ $item->quantity }}" min="1"
                                            class="w-20 border rounded-lg p-2 text-sm text-center">
                                    @else
                                        @if($item->product->image)
                                            @if(str_starts_with($item->product->image, 'http'))
                                                <img src="{{ $item->product->image }}" class="w-16 h-16 object-cover rounded-lg">
                                            @else
                                                <img src="{{ route('product.photo', ['filename' => basename($item->product->image)]) }}" 
     class="w-20 h-20 object-cover rounded-lg shadow-sm border border-gray-100">
                                            @endif
                                        @endif
                                        <div class="flex-1">
                                            <p class="font-medium">{{ $item->product->name }}</p>
                                            <p class="text-sm text-gray-500">฿{{ number_format($item->price_at_purchase, 2) }} x {{ $item->quantity }}</p>
                                        </div>
                                        <p class="font-semibold">฿{{ number_format($item->price_at_purchase * $item->quantity, 2) }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- สรุปราคา --}}
                    <div class="border-t pt-4 space-y-2">
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
                    </div>

                    {{-- ปุ่ม --}}
                    <div class="border-t pt-4 flex justify-between items-center">
                        <a href="{{ route('orders.index') }}" class="text-sm text-gray-500 hover:underline">
                            ← กลับไปคำสั่งซื้อทั้งหมด
                        </a>
                        <div class="flex gap-2">
                            @if($order->status === 'pending')
                                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm">
                                    บันทึก
                                </button>
                            @endif
                            @if($order->status === 'pending' && !$order->isPaid())
                                <a href="{{ route('orders.pay', $order->order_id) }}"
                                   onclick="event.preventDefault(); document.getElementById('pay-form').submit();"
                                   class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm">
                                    ยืนยันชำระเงิน
                                </a>
                            @endif
                            @if($order->status === 'pending')
                                <a href="{{ route('orders.cancel', $order->order_id) }}"
                                   onclick="event.preventDefault(); document.getElementById('cancel-form').submit();"
                                   class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm">
                                    ยกเลิก
                                </a>
                            @endif
                        </div>
                    </div>
                </form>

                {{-- hidden forms สำหรับ pay และ cancel --}}
                @if($order->status === 'pending')
                    <form id="pay-form" action="{{ route('orders.pay', $order->order_id) }}" method="POST" class="hidden">
                        @csrf @method('PATCH')
                    </form>
                    <form id="cancel-form" action="{{ route('orders.cancel', $order->order_id) }}" method="POST" class="hidden">
                        @csrf @method('PATCH')
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>