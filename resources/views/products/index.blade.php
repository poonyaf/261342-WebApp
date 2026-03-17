<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: var(--text);">
            Products
        </h2>
    </x-slot>

    <div class="page-wrap">
        <div class="container">
            {{-- Search Bar --}}
            <div class="mb-6 card card-pad">
                <form action="{{ route('products.index') }}" method="GET" class="flex gap-2 flex-wrap items-center">
                    <input
                        type="text"
                        name="search"
                        placeholder="Search products by name or description..."
                        value="{{ $search ?? '' }}"
                        class="input flex-1 min-w-64"
                    />

                    @if ($category ?? false)
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif

                    <button type="submit" class="btn-primary ">
                        Search
                    </button>

                    @if ($search ?? false)
                        @php
                            $clearUrl = route('products.index');
                            if ($category ?? false) {
                                $clearUrl .= '?category=' . urlencode($category);
                            }
                        @endphp

                        <a href="{{ $clearUrl }}" class="btn" style="background: var(--pinkPage-neutral-2); color: var(--text);">
                            Clear Search
                        </a>
                    @endif
                </form>
            </div>

            {{-- Category Filter --}}
            @if ($categories->count() > 0)
                <div class="mb-8 card card-pad">
                    <h3 class="h2 mb-3">Category</h3>
                    <div class="flex flex-wrap gap-2">
                        <a
                            href="{{ route('products.index', ['search' => $search ?? '']) }}"
                            class="badge"
                            style="
                                background: {{ !($category ?? false) ? 'var(--secondary)' : 'var(--pinkPage-neutral-2)' }};
                                color: {{ !($category ?? false) ? '#fff' : 'var(--text)' }};
                                padding: 0.5rem 1rem;
                                border-radius: 9999px;
                            "
                        >
                            All
                        </a>

                        @foreach ($categories as $cat)
                            <a
                                href="{{ route('products.index', ['category' => $cat, 'search' => $search ?? '']) }}"
                                class="badge"
                                style="
                                    background: {{ (($category ?? false) === $cat) ? 'var(--secondary)' : 'var(--pinkPage-neutral-2)' }};
                                    color: {{ (($category ?? false) === $cat) ? '#fff' : 'var(--text)' }};
                                    padding: 0.5rem 1rem;
                                    border-radius: 9999px;
                                "
                            >
                                {{ $cat }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Product Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
                @forelse ($products as $product)
                    <div class="card overflow-hidden">
                        {{-- Image --}}
                        <div class="relative h-48 w-full overflow-hidden">
                            @if ($product->image)
                                @php
                                    $imageUrl = str_starts_with($product->image, 'http')
                                        ? $product->image
                                        : route('product.photo', ['filename' => basename($product->image)]);
                                @endphp
                                <img src="{{ $imageUrl }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center" style="background: var(--pinkPage-neutral-2);">
                                    <span class="muted">No Image</span>
                                </div>
                            @endif
                        </div>

                        <div class="card-pad">
                            <h3 class="font-bold truncate" style="color: var(--text);">
                                {{ $product->name }}
                            </h3>

                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <span class="font-extrabold text-lg" style="color: var(--secondary);">
                                ฿{{ number_format($product->price, 2) }}
                                </span>

                                <span class="text-xs px-2 py-1 rounded whitespace-nowrap"
                                    style="background: var(--pinkPage-neutral-2); color: var(--text);">
                                Remaining: {{ $product->stock_number }}
                                </span>
                            </div>

                            {{-- Tags (remove indigo) --}}
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($product->tags as $tag)
                                    <span
                                        class="text-[10px] px-2 py-0.5 rounded-full uppercase"
                                        style="background: var(--pinkPage-neutral-2); color: var(--neutral); border: 1px solid rgba(0,0,0,0.06);"
                                    >
                                        #{{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>

                            {{-- Action button --}}
                            <div class="mt-4">
                                @auth
                                    <a href="{{ route('products.show', $product->product_id) }}" class="btn-primary w-full">
                                        View / Order
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn w-full" style="background: var(--neutral); color: #fff;">
                                        Please login to order
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="muted">
                        {{ $search ? 'No products found matching your search.' : 'No products available' }}
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
