<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Manage Orders
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
    {{-- 1. คอลัมน์ Order ID --}}
    <td class="px-6 py-4 font-medium">#{{ $order->order_id }}</td>

    {{-- 2. คอลัมน์ Customer --}}
    <td class="px-6 py-4 text-gray-600">{{ $order->user->name ?? '-' }}</td>

    {{-- 3. คอลัมน์ Amount --}}
    <td class="px-6 py-4 font-semibold">฿{{ number_format($order->total_amount, 2) }}</td>

    {{-- 4. คอลัมน์ Status --}}
    <td class="px-6 py-4">
    <div class="flex gap-2">
        @php
            // ปรับให้เป็นตัวพิมพ์เล็กทั้งหมดเพื่อความแม่นยำในการเช็ค
            $currentStatus = strtolower($order->status);
        @endphp

        {{-- 1. จาก Pending -> Processing (รับออเดอร์) --}}
        @if($currentStatus === 'pending')
            <form action="{{ route('admin.orders.updateStatus', $order->order_id) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="processing">
                <button type="submit" class="px-3 py-1 bg-blue-500 text-white rounded-lg text-xs hover:bg-blue-600">
                    Confirm Order
                </button>
            </form>
        @endif

        {{-- 2. จาก Processing -> Packing (เตรียมของเสร็จแล้ว) --}}
        @if($currentStatus === 'processing')
            <form action="{{ route('admin.orders.updateStatus', $order->order_id) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="packing">
                <button type="submit" class="px-3 py-1 bg-purple-500 text-white rounded-lg text-xs hover:bg-purple-600">
                    Pack Items
                </button>
            </form>
        @endif

        {{-- 3. จาก Packing -> Delivering (ส่งมอบให้ขนส่ง) --}}
        @if($currentStatus === 'packing')
            <form action="{{ route('admin.orders.updateStatus', $order->order_id) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="delivering">
                <button type="submit" class="px-3 py-1 bg-orange-500 text-white rounded-lg text-xs hover:bg-orange-600">
                    Ship
                </button>
            </form>
        @endif

        {{-- 4. จาก Delivering -> Complete (ลูกค้าได้รับของแล้ว) --}}
        @if($currentStatus === 'delivering')
            <form action="{{ route('admin.orders.updateStatus', $order->order_id) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="complete">
                <button type="submit" class="px-3 py-1 bg-green-500 text-white rounded-lg text-xs hover:bg-green-600">
                    Complete
                </button>
            </form>
        @endif

        {{-- ปุ่ม Cancel: ให้กดได้เฉพาะตอนที่ยังไม่ส่งของ (สถานะไม่เกิน packing) --}}
        @if(in_array($currentStatus, ['pending', 'processing', 'packing']))
            <form action="{{ route('admin.orders.updateStatus', $order->order_id) }}" method="POST" onsubmit="return confirm('Confirm cancellation?')">
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="cancelled">
                <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-lg text-xs hover:bg-red-600">
                    Cancel
                </button>
            </form>
        @endif
    </div>
</td>

    {{-- 5. คอลัมน์ Payment --}}
    <td class="px-6 py-4">
        {{ $order->payment_status ?? 'unpaid' }}
    </td>

    {{-- 6. คอลัมน์ Date --}}
    <td class="px-6 py-4 text-gray-400">
        {{ $order->created_at->format('d/m/Y') }}
    </td>

    {{-- 7. คอลัมน์ Actions (ปุ่มกด) --}}
    <td class="px-6 py-4">
        {{-- ใส่ Logic ปุ่ม @if($order->status === ...) ที่นี่ --}}
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