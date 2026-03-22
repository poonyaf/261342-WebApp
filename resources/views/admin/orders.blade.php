<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Manage Orders
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Search Bar --}}
            <form method="GET" action="{{ route('admin.orders') }}">
                <div class="bg-white rounded-2xl shadow-sm border border-pink-100 p-4 flex gap-3 items-center">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by customer name or order ID..."
                        class="flex-1 border-gray-200 rounded-xl p-3 text-sm focus:ring-pink-500 focus:border-pink-500">
                    <button type="submit" class="px-6 py-3 bg-pink-400 hover:bg-pink-500 text-white rounded-xl text-sm font-medium">
                        Search
                    </button>
                </div>
            </form>

            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-3 text-left">Order ID</th>
                            <th class="px-6 py-3 text-left">Customer</th>
                            <th class="px-6 py-3 text-left">Amount</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-left">Payment</th>
                            <th class="px-6 py-3 text-left">Date</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium">#{{ $order->order_id }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $order->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 font-semibold">฿{{ number_format($order->total_amount, 2) }}</td>

                            <td class="px-6 py-4">
                                <div class="flex gap-2">
                                    @php $currentStatus = strtolower($order->status); @endphp

                                    @if($currentStatus === 'pending')
                                        <form action="{{ route('admin.orders.updateStatus', $order->order_id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="processing">
                                            <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded-lg text-xs hover:bg-blue-600">
                                                Confirm Order
                                            </button>
                                        </form>
                                    @endif

                                    @if($currentStatus === 'processing')
                                        <form action="{{ route('admin.orders.updateStatus', $order->order_id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="packing">
                                            <button type="submit" class="px-3 py-1 bg-purple-500 text-white rounded-lg text-xs hover:bg-purple-600">
                                                Pack Items
                                            </button>
                                        </form>
                                    @endif

                                    @if($currentStatus === 'packing')
                                        <form action="{{ route('admin.orders.updateStatus', $order->order_id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="delivering">
                                            <button type="submit" class="px-3 py-1 bg-orange-500 text-white rounded-lg text-xs hover:bg-orange-600">
                                                Ship
                                            </button>
                                        </form>
                                    @endif

                                    @if($currentStatus === 'delivering')
                                        <form action="{{ route('admin.orders.updateStatus', $order->order_id) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="complete">
                                            <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded-lg text-xs hover:bg-green-600">
                                                Complete
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($currentStatus, ['pending', 'processing', 'packing']))
                                        <form action="{{ route('admin.orders.updateStatus', $order->order_id) }}" method="POST" onsubmit="return confirm('Confirm cancellation?')">
                                            @csrf @method('PATCH')
                                            <input type="hidden" name="status" value="cancelled">
                                            <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-lg text-xs hover:bg-red-600">
                                                Cancel
                                            </button>
                                        </form>
                                    @endif

                                    @if(in_array($currentStatus, ['complete', 'cancelled', 'delivering']))
                                        <span class="text-xs text-gray-400 italic">{{ ucfirst($currentStatus) }}</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4">{{ $order->payment_status ?? 'unpaid' }}</td>
                            <td class="px-6 py-4 text-gray-400">{{ $order->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">
                                @if($order->status === 'packing')
                                    <form action="{{ route('admin.orders.updateStatus', $order->order_id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="delivering">
                                        <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded">Ship</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>