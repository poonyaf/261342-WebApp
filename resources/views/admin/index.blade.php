<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                    <p class="text-3xl font-bold text-blue-500">{{ $totalUsers }}</p>
                    <p class="text-sm text-gray-500 mt-1">Users</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                    <p class="text-3xl font-bold text-green-500">{{ $totalProducts }}</p>
                    <p class="text-sm text-gray-500 mt-1">Products</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                    <p class="text-3xl font-bold text-yellow-500">{{ $totalOrders }}</p>
                    <p class="text-sm text-gray-500 mt-1">Orders</p>
                </div>
                <div class="bg-white rounded-2xl shadow-sm p-6 text-center">
                    <p class="text-3xl font-bold text-pink-500">฿{{ number_format($totalRevenue, 2) }}</p>
                    <p class="text-sm text-gray-500 mt-1">Revenue</p>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('admin.users') }}"
                   class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-md transition flex items-center gap-4">
                    <span class="text-3xl">👥</span>
                    <div>
                        <p class="font-semibold text-gray-800">Manage Users</p>
                        <p class="text-sm text-gray-500">View and manage all users</p>
                    </div>
                </a>
                <a href="{{ route('admin.products') }}"
                   class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-md transition flex items-center gap-4">
                    <span class="text-3xl">📦</span>
                    <div>
                        <p class="font-semibold text-gray-800">Manage Products</p>
                        <p class="text-sm text-gray-500">Add, edit, delete products</p>
                    </div>
                </a>
                <a href="{{ route('admin.orders') }}"
                   class="bg-white rounded-2xl shadow-sm p-6 hover:shadow-md transition flex items-center gap-4">
                    <span class="text-3xl">🛒</span>
                    <div>
                        <p class="font-semibold text-gray-800">Manage Orders</p>
                        <p class="text-sm text-gray-500">View and update order status</p>
                    </div>
                </a>
            </div>

        </div>
    </div>
</x-app-layout>