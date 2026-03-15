<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Our Products</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900">

    <nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="text-xl font-bold text-pink-500">MY SHOP</div>
                <div class="flex gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 dark:text-gray-300">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-300">Login</a>
                        <a href="{{ route('register') }}" class="text-sm font-bold text-pink-500">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <header class="bg-white dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                รายการสินค้าทั้งหมด
            </h2>
        </div>
    </header>

    <main class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Bar -->
            <div class="mb-6">
                <form action="{{ route('products.index') }}" method="GET" class="flex gap-2 flex-wrap">
                    <input type="text" name="search" placeholder="Search products by name or description..."
                           value="{{ $search ?? '' }}"
                           class="flex-1 min-w-64 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @if ($category ?? false)
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Search
                    </button>
                    @if ($search ?? false)
                        @php
                            $clearUrl = route('products.index');
                            if ($category ?? false) {
                                $clearUrl .= '?category=' . urlencode($category);
                            }
                        @endphp
                        <a href="{{ $clearUrl }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                            Clear Search
                        </a>
                    @endif
                </form>
            </div>

            <!-- Category Filter --> 
            @if ($categories->count() > 0)
                <div class="mb-8">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">หมวดหมู่สินค้า</h3>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('products.index', ['search' => $search ?? '']) }}"
                           class="py-2 px-4 rounded-full transition {{ !($category ?? false) ? 'bg-pink-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                            ทั้งหมด
                        </a>
                        @foreach ($categories as $cat)
                            <a href="{{ route('products.index', ['category' => $cat, 'search' => $search ?? '']) }}"
                               class="py-2 px-4 rounded-full transition {{ ($category ?? false) === $cat ? 'bg-pink-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600' }}">
                                {{ $cat }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($products as $product)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-2xl overflow-hidden hover:shadow-xl transition duration-300">
                        
                        {{-- ส่วนแสดงรูปภาพ --}}
                        <div class="relative h-48 w-full overflow-hidden">
                            @if ($product->image)
                                @php
                                    $imageUrl = str_starts_with($product->image, 'http') 
                                        ? $product->image 
                                        : route('product.photo', ['filename' => basename($product->image)]);
                                @endphp
                                <img src="{{ $imageUrl }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-400">No Image</span>
                                </div>
                            @endif
                        </div>

                        <div class="p-5">
                            <h3 class="font-bold text-gray-900 dark:text-gray-100 truncate">{{ $product->name }}</h3>
                            <div class="flex justify-between items-center mt-3">
                                <span class="text-pink-600 font-extrabold text-lg">฿{{ number_format($product->price, 2) }}</span>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">คงเหลือ: {{ $product->stock_number }}</span>
                            </div>
                                <div class="flex flex-wrap gap-1 mt-2">
    @foreach($product->tags as $tag)
        <span class="text-[10px] bg-indigo-50 text-indigo-500 px-2 py-0.5 rounded-full border border-indigo-100 uppercase">
            #{{ $tag->name }}
        </span>
    @endforeach
</div>
                            {{-- ปุ่มสั่งซื้อ --}}
                            <div class="mt-4">
                                @auth
                                    <a href="{{ route('products.show', $product->product_id) }}" 
                                       class="block w-full text-center bg-pink-500 hover:bg-pink-600 text-white font-bold py-2 rounded-xl transition">
                                        ดูรายละเอียด / สั่งซื้อ
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" 
                                       class="block w-full text-center bg-gray-700 hover:bg-gray-800 text-white font-bold py-2 rounded-xl transition">
                                        เข้าสู่ระบบเพื่อซื้อ
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">{{ $search ? 'No products found matching your search.' : 'No products available' }}</p>
                @endforelse
            </div>
        </div>
    </main>
</body>
</html>