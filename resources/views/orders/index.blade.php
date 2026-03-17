<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: var(--secondary);">
            {{ __('My Order') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <p class="mb-4 text-green-600">{{ session('success') }}</p>
            @endif

            @forelse ($orders as $order)
                <div class="bg-white rounded-2xl shadow p-6">

                    {{-- header --}}
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="text-sm text-gray-500">Order Number</p>
                            <p class="font-bold text-lg">#{{ $order->order_id }}</p>
                        </div>
                        <span @class([
                            'px-3 py-1 rounded-full text-sm font-medium',
                            'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
                            'bg-blue-100 text-blue-700'   => $order->status === 'processing',
                            'bg-green-100 text-green-700' => $order->status === 'delivered',
                            'bg-red-100 text-red-700'     => $order->status === 'cancelled',
                        ])>
                            {{ ucfirst($order->status) }}
                        </span>
                    </div>

                    {{-- order items --}}
                    <div class="space-y-3">
                        @foreach ($order->items as $item)
                            <div class="flex items-center gap-4">
                                {{-- รูป --}}
                                @if ($item->product->image)
                                    @if (str_starts_with($item->product->image, 'http'))
                                        <img src="{{ $item->product->image }}" class="w-14 h-14 object-cover rounded-lg">
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
                                <p class="font-medium">฿{{ number_format($item->price_at_purchase * $item->quantity, 2) }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- สรุปราคา --}}
                    <div class="mt-4 pt-4 border-t space-y-1">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Price</span>
                            <span>฿{{ number_format($order->items->sum(fn($i) => $i->price_at_purchase * $i->quantity), 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Shipping Fee</span>
                            <span>฿50.00</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg pt-2 border-t">
                            <span>Total</span>
                            <span class="text-pink-500">฿{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>

                    {{-- ปุ่ม --}}
                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('orders.show', $order->order_id) }}"
                           class="text-sm text-blue-600 hover:underline">
                            For more information →
                        </a>
                    </div>

                </div>
            @empty
                <p class="text-gray-500 text-center">You haven't ordered yet. Let's go grab something!</p>
            @endforelse

        </div>
    </div>
</x-app-layout>