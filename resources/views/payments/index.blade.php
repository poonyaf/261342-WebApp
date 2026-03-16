<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: var(--secondary);">
            {{ __('Payments') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Unpaid Orders --}}
            @php 
            $unpaid = $unpaidOrders->where('status', 'pending'); 
            @endphp
            
            @if($unpaid->isNotEmpty())
            <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Unpaid Orders</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($unpaid as $order)
                    <div class="bg-white shadow sm:rounded-lg overflow-hidden border-l-4 border-yellow-400">
                        <div class="p-4">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900">Order #{{ $order->order_id }}</h3>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                    Unpaid
                                </span>
                            </div>
                            <p class="text-green-600 font-bold mt-1">฿{{ number_format($order->total_amount, 2) }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $order->order_date->format('d/m/Y H:i') }}</p>
                            <a href="{{ route('payments.create', $order->order_id) }}"
                               class="block mt-3 text-center text-sm bg-green-500 text-white rounded-lg py-2 hover:bg-green-600">
                                Pay Now
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Payment History --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Payment History</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @forelse ($payments as $payment)
                    <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                        <div class="p-4">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900">Payment #{{ $payment->payment_id }}</h3>
                                <span @class([
                                    'px-2 py-1 rounded-full text-xs font-semibold',
                                    'bg-green-100 text-green-700'   => $payment->status === 'paid',
                                    'bg-yellow-100 text-yellow-700' => $payment->status === 'pending',
                                    'bg-red-100 text-red-700'       => $payment->status === 'failed',
                                ])>
                                    @switch($payment->status)
                                        @case('paid') Paid ✓ @break
                                        @case('pending') Pending @break
                                        @case('failed') Failed @break
                                        @default {{ $payment->status }}
                                    @endswitch
                                </span>
                            </div>
                            <p class="text-green-600 font-bold mt-1">฿{{ number_format($payment->amount, 2) }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y H:i') }}</p>
                            <a href="{{ route('payments.show', $payment->payment_id) }}"
                               class="block mt-3 text-center text-sm bg-blue-600 text-white rounded-lg py-2 hover:bg-blue-700">
                                View Details
                            </a>
                        </div>
                    </div>
                    @empty
                        <p class="text-gray-500 col-span-4">No payment history found.</p>
                    @endforelse
                </div>
            </div>

            {{-- Cancelled Orders --}}
            @php $cancelled = $unpaidOrders->where('status', 'cancelled'); @endphp
            @if($cancelled->isNotEmpty())
            <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-4">Cancelled Orders</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach($cancelled as $order)
                    <div class="bg-white shadow sm:rounded-lg overflow-hidden border-l-4 border-red-400">
                        <div class="p-4">
                            <div class="flex justify-between items-center">
                                <h3 class="font-semibold text-gray-900">Order #{{ $order->order_id }}</h3>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                    Cancelled
                                </span>
                            </div>
                            <p class="text-green-600 font-bold mt-1">฿{{ number_format($order->total_amount, 2) }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $order->order_date->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>
    </div>

</x-app-layout>