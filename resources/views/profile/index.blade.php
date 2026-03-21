<x-app-layout>
    <div class="py-8" x-data="{ showSellerModal: false }">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- 🌟 กล่องแจ้งเตือน Success / Error --}}
            @if(session('success'))
                <div class="bg-green-100 border border-green-200 text-green-700 px-6 py-4 rounded-2xl mb-6 font-bold flex items-center gap-2">
                    <span>✅</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-6 font-bold flex items-center gap-2">
                    <span>⚠️</span> {{ session('error') }}
                </div>
            @endif

            {{-- ปุ่มกลับ --}}
            <div>
                <a href="{{ route('products.index') }}" class="text-sm font-medium flex items-center gap-2" style="color: var(--secondary);">
                    <span>←</span> back
                </a>
            </div>

            {{-- Profile Card (ส่วนบน) --}}
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-pink-50 flex flex-col sm:flex-row justify-between items-center sm:items-start gap-4 relative">
                
                {{-- Avatar & Info --}}
                <div class="flex items-center gap-4">
                    <div class="relative">
                        @if(auth()->user()->image)
    <img src="{{ auth()->user()->image }}" class="w-16 h-16 rounded-full object-cover shadow-inner">
@else
    <div class="w-16 h-16 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-inner"
         style="background: linear-gradient(135deg, var(--accent), var(--secondary));">
        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
    </div>
@endif
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">Welcome back, {{ auth()->user()->name }}! 👋</h3>
                        <p class="text-sm text-gray-500 mb-2">{{ auth()->user()->email }}</p>
                        
                        {{-- ปุ่มแก้ไขโปรไฟล์ (ลิงก์ไปหน้า edit.blade.php เดิม) --}}
                        <a href="{{ route('profile.edit') }}" 
                           class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold border transition"
                           style="border-color: var(--secondary); color: var(--secondary);">
                            ✎ Edit Profile
                        </a>
                    </div>
                </div>

                {{-- Toggle Become Seller --}}
                <div class="bg-pink-50 rounded-2xl p-3 flex items-center gap-3 sm:mt-0 mt-4 w-full sm:w-auto justify-between">
                    <span class="text-sm font-semibold text-pink-700 flex items-center gap-2">
                        🏪 Want to be Seller?
                    </span>

                    @if(auth()->user()->role === 'admin')
    {{-- Admin --}}
    <a href="{{ route('admin.index') }}" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" style="background: var(--secondary);">
        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition translate-x-6"></span>
    </a>
@elseif(auth()->user()->role === 'seller')
    {{-- Seller อยู่แล้ว --}}
    <a href="{{ route('seller.index') }}" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors" style="background: var(--secondary);">
        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition translate-x-6"></span>
    </a>
@else
    {{-- Customer ปกติ --}}
    <button @click="showSellerModal = true" type="button" class="relative inline-flex h-6 w-11 items-center rounded-full bg-gray-300 transition-colors">
        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition translate-x-1"></span>
    </button>
@endif
                </div>
            </div>

            {{-- Menu Grid (4 กล่อง) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                {{-- 1. สินค้าที่ชอบ --}}
                <a href="{{ route('wishlist.index') }}" class="bg-white rounded-3xl p-5 shadow-sm border border-pink-50 hover:shadow-md transition text-center flex flex-col items-center justify-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-pink-500 bg-pink-50 text-xl">⭐</div>
                    <div>
                        <p class="font-bold text-gray-800">Wish List</p>
                        <p class="text-xs text-gray-500">Your Favorite</p>
                    </div>
                </a>

                {{-- 2. ตะกร้าของฉัน --}}
                <a href="{{ route('carts.index') }}" class="bg-white rounded-3xl p-5 shadow-sm border border-pink-50 hover:shadow-md transition text-center flex flex-col items-center justify-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-gray-600 bg-gray-100 text-xl">🛒</div>
                    <div>
                        <p class="font-bold text-gray-800">My cart</p>
                        <p class="text-xs text-gray-500">Products in cart</p>
                    </div>
                </a>
{{-- 3. Payment --}}
<a href="{{ route('payments.index') }}" class="bg-white rounded-3xl p-5 shadow-sm border border-pink-50 hover:shadow-md transition text-center flex flex-col items-center justify-center gap-2">
    <div class="w-10 h-10 rounded-full flex items-center justify-center text-pink-500 bg-pink-50 text-xl">💳</div>
    <div>
        <p class="font-bold text-gray-800">Payment</p>
        <p class="text-xs text-gray-500">My Payments</p>
    </div>
</a>
                {{-- 4. ประวัติคำสั่งซื้อ --}}
                <a href="{{ route('orders.index') }}" class="bg-white rounded-3xl p-5 shadow-sm border border-pink-50 hover:shadow-md transition text-center flex flex-col items-center justify-center gap-2">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-green-500 bg-green-50 text-xl">📦</div>
                    <div>
                        <p class="font-bold text-gray-800">Order History</p>
                        <p class="text-xs text-gray-500">Order and Status</p>
                    </div>
                </a>

            </div>

            {{-- Logout Button --}}
            <form method="POST" action="{{ route('logout') }}" class="mt-6">
                @csrf
                <button type="submit" class="w-full bg-white border border-red-400 text-red-500 font-bold py-3 rounded-full hover:bg-red-50 transition flex justify-center items-center gap-2">
                    <span>[→</span> Log out
                </button>
            </form>

        </div>

        {{-- 🌟 sent request modal (สำหรับ Customer) --}}
        <div x-show="showSellerModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                {{-- Background overlay --}}
                <div x-show="showSellerModal" x-transition.opacity class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" @click="showSellerModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal Panel --}}
                <div x-show="showSellerModal" 
                     x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-pink-100 mb-4 text-2xl">
                            🏪
                        </div>
                        <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-title">
                           Request to Become a Seller
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Would you like to send a request to the admin to become a seller?
                            </p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-4 sm:px-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                        
                        {{-- ปุ่มยกเลิก --}}
                        <button type="button" @click="showSellerModal = false" 
                                class="w-full sm:w-1/2 inline-flex justify-center items-center rounded-full border border-gray-300 shadow-sm px-6 py-2.5 bg-white text-base font-bold text-gray-700 hover:bg-gray-50 transition sm:text-sm">
                            Cancel
                        </button>

                        <a href="{{ route('seller.form.create') }}" 
                           class="w-full sm:w-1/2 inline-flex justify-center items-center rounded-full border border-transparent shadow-sm px-6 py-2.5 text-base font-bold text-white hover:opacity-90 transition sm:text-sm m-0" 
                           style="background: var(--primary);">
                            Enter Store Details
                        </a>
                        
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>