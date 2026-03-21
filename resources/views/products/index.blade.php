<x-app-layout class="{{ ($mode ?? 'online') === 'Secondhand/2nd hand' ? 'theme-brown' : '' }}">
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: var(--secondary);">
            {{ ($mode ?? 'online') === 'Secondhand/2nd hand' ? '♻️ 2nd Hand Market' : '📁 New Products' }}
        </h2>
    </x-slot>

    <div class="page-wrap">
        <div class="container">
            {{-- Description Card --}}
            <div class="card card-pad mb-8" style="border-left: 4px solid var(--secondary);">
                <p class="muted mb-0">
                    {{ ($mode ?? 'online') === 'Secondhand/2nd hand' ? 'Explore our curated selection of pre-loved treasures, from vintage finds to gently used gems. Shop sustainably and discover unique items with character.' : 'Browse our collection of new products, carefully curated for quality and style. Find the perfect item that suits your needs and preferences.' }}
                </p>
            </div>

            {{-- Mode Toggle --}}
            <div class="flex justify-center mb-10">
                <div class="inline-flex rounded-full p-1 backdrop-blur-sm" style="background: rgba(158, 131, 131, 0.08); border: 1px solid rgba(158, 131, 131, 0.1);">
                    <a href="{{ request()->fullUrlWithQuery(['mode' => 'Online']) }}"
                       class="px-6 py-2 rounded-full text-sm font-bold transition flex items-center gap-2"
                       style="{{ ($mode ?? 'online') !== 'Secondhand/2nd hand' ? 'background: var(--surface); color: var(--primary); box-shadow: 0 2px 8px rgba(0,0,0,0.1);' : 'color: var(--text); opacity: 0.6;' }}">
                        📁 New
                    </a>
                    <a href="{{ request()->fullUrlWithQuery(['mode' => 'Secondhand/2nd hand']) }}"
                       class="px-6 py-2 rounded-full text-sm font-bold transition flex items-center gap-2"
                       style="{{ ($mode ?? 'online') === 'Secondhand/2nd hand' ? 'background: var(--surface); color: var(--primary); box-shadow: 0 2px 8px rgba(0,0,0,0.1);' : 'color: var(--text); opacity: 0.6;' }}">
                        ♻️ 2nd Hand
                    </a>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="mb-8 card card-pad" style="border-top: 3px solid var(--secondary);">
                <form action="{{ route('products.index') }}" method="GET" class="flex gap-3 flex-wrap items-center">
                    <div class="flex-1 min-w-64 relative">
                        <input
                            type="text"
                            name="search"
                            placeholder="Search products..."
                            value="{{ $search ?? '' }}"
                            class="input w-full"
                        />
                        <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">🔍</span>
                    </div>

                    @if ($category ?? false)
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif

                    <button type="submit" class="btn-primary">
                        Search
                    </button>

                    @if ($search ?? false)
                        @php
                            $clearUrl = route('products.index');
                            if ($category ?? false) {
                                $clearUrl .= '?category=' . urlencode($category) . '&mode=' . urlencode($mode ?? 'online');
                            } else {
                                $clearUrl .= '?mode=' . urlencode($mode ?? 'online');
                            }
                        @endphp

                        <a href="{{ $clearUrl }}" class="btn" style="background: var(--pinkPage-neutral-2); color: var(--text);">
                            ✕ Clear
                        </a>
                    @endif
                </form>
            </div>

            {{-- Category Filter --}}
            @if ($categories->count() > 0)
                <div class="mb-10 card card-pad">
                    <h3 class="h2 mb-4 flex items-center gap-2">
                        <span>🏷️</span> Category
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <a
                            href="{{ route('products.index', ['mode' => $mode ?? 'online']) }}"
                            class="badge transition hover:shadow-md"
                            style="
                                background: {{ !($category ?? false) ? 'linear-gradient(135deg, var(--primary), var(--secondary))' : 'var(--pinkPage-neutral-2)' }};
                                color: {{ !($category ?? false) ? '#fff' : 'var(--text)' }};
                                padding: 0.5rem 1rem;
                                border-radius: 9999px;
                                border: 1px solid {{ !($category ?? false) ? 'transparent' : 'rgba(0,0,0,0.08)' }};
                            "
                        >
                            All
                        </a>

                        @foreach ($categories as $cat)
                            <a
                                href="{{ route('products.index', ['category' => $cat, 'mode' => $mode ?? 'online']) }}"
                                class="badge transition hover:shadow-md"
                                style="
                                    background: {{ (($category ?? false) === $cat) ? 'linear-gradient(135deg, var(--primary), var(--secondary))' : 'var(--pinkPage-neutral-2)' }};
                                    color: {{ (($category ?? false) === $cat) ? '#fff' : 'var(--text)' }};
                                    padding: 0.5rem 1rem;
                                    border-radius: 9999px;
                                    border: 1px solid {{ (($category ?? false) === $cat) ? 'transparent' : 'rgba(0,0,0,0.08)' }};
                                "
                            >
                                {{ $cat }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Product Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4 gap-6">
                @forelse ($products as $product)
                    <div class="card group overflow-hidden transition-all hover:shadow-lg hover:scale-105 duration-300"
                         style="border: 1px solid rgba(0,0,0,0.05);">
                        {{-- Image Container --}}
                        <div class="relative h-56 w-full overflow-hidden bg-gradient-to-br" style="background: linear-gradient(135deg, var(--pinkPage-neutral-2), rgba(0,0,0,0.02));">
                            @if ($product->image)
                                @php
                                    $imageUrl = str_starts_with($product->image, 'http')
                                        ? $product->image
                                        : route('product.photo', ['filename' => basename($product->image)]);
                                @endphp
                                <img src="{{ $imageUrl }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center flex-col gap-2" style="background: var(--pinkPage-neutral-2);">
                                    <span class="text-2xl">📸</span>
                                    <span class="muted text-xs">No Image</span>
                                </div>
                            @endif
                            
                            {{-- Stock Badge --}}
                            <div class="absolute top-3 right-3 text-xs px-3 py-1 rounded-full font-semibold backdrop-blur-sm"
                                 style="background: rgba(255,255,255,0.9); color: var(--text); border: 1px solid rgba(0,0,0,0.1);">
                                {{ $product->stock_number > 0 ? '✓ In Stock' : '✗ Out' }}
                            </div>
                        </div>

                        <div class="card-pad flex flex-col gap-3 flex-1">
                            {{-- Product Name --}}
                            <h3 class="font-bold text-lg truncate" style="color: var(--text);">
                                {{ $product->name }}
                            </h3>

                            {{-- Price & Stock --}}
                            <div class="flex items-center justify-between gap-2 pb-3" style="border-bottom: 1px solid rgba(0,0,0,0.06);">
                                <span class="font-extrabold text-2xl" style="color: var(--secondary);">
                                    ฿{{ number_format($product->price, 2) }}
                                </span>
                                <span class="text-xs px-2 py-1 rounded" style="background: var(--pinkPage-neutral-2); color: var(--text);">
                                    {{ $product->stock_number }} left
                                </span>
                            </div>

                            {{-- Tags --}}
                            @if($product->tags->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($product->tags as $tag)
                                        <span class="text-xs px-2.5 py-1 rounded-full font-semibold uppercase"
                                              style="background: var(--accent); color: var(--text); opacity: 0.9;">
                                            #{{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif

                            {{-- Action Button --}}
                            <div class="mt-auto pt-3">
                                @auth
                                    <a href="{{ route('products.show', $product->product_id) }}" class="btn-primary w-full flex items-center justify-center gap-2">
                                        👁️ View Details
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn w-full flex items-center justify-center gap-2" style="background: var(--neutral); color: #fff;">
                                        🔐 Login to Order
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-5xl mb-4">😅</div>
                        <p class="muted text-lg">
                            {{ $search ? 'No products match your search.' : 'No products available' }}
                        </p>
                        @if($search)
                            <p class="muted text-sm mt-2">Try adjusting your search or filters</p>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
