<x-app-layout>

<div class="max-w-4xl mx-auto py-8 px-4">

    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-0">

            {{-- Image --}}
            <div class="bg-gray-50 flex items-center justify-center p-6">
                @if($product->image)
    @if(str_starts_with($product->image, 'http'))
        {{-- If from the Internet --}}
        <img src="{{ $product->image }}" class="w-full max-h-80 object-contain rounded-xl">
    @else
        {{-- If from local device (calling via Route) --}}
        <img src="{{ route('product.photo', ['filename' => basename($product->image)]) }}" 
             class="w-full max-h-80 object-contain rounded-xl">
    @endif
@endif

            </div>

            {{-- Product Info --}}
            <div class="p-6 flex flex-col justify-between">

                {{-- tags --}}
                <div class="flex flex-wrap gap-2 text-xs mb-3">
                    @foreach($product->tags as $tag)
                        <span class="bg-pink-100 text-pink-600 px-3 py-1 rounded-full">#{{ $tag->name }}</span>
                    @endforeach
                </div>

                {{-- Name --}}
                <h1 class="text-xl font-bold text-gray-800">{{ $product->name }}</h1>

                {{-- rating --}}
                <div class="flex items-center mt-2 text-yellow-400 text-sm">
                    ⭐⭐⭐⭐☆
                    <span class="text-gray-400 ml-2">(45 Reviews)</span>
                </div>

                {{-- Price --}}
                <div class="mt-3 flex items-baseline gap-3">
                    <span class="text-2xl font-bold text-gray-800">฿{{ number_format($product->price, 2) }}</span>
                    <span class="text-gray-400 line-through text-sm">฿{{ number_format($product->price * 1.2, 2) }}</span>
                </div>

                {{-- description --}}
                <p class="mt-3 text-gray-500 text-sm leading-relaxed">{{ $product->description }}</p>

                {{-- stock --}}
                <p class="mt-2 text-xs text-gray-400">Remaining {{ $product->stock_number }}</p>

                {{-- quantity --}}
                <div class="flex items-center gap-3 mt-4">
                    <span class="text-sm text-gray-600">Amount:</span>
                    <div class="flex items-center border rounded-lg overflow-hidden">
                        <button onclick="changeQty(-1)" class="px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600">−</button>
                        <input type="text" id="quantity" value="1" min="1" max="{{ $product->stock_number }}"
                            class="w-10 text-center text-sm border-x focus:outline-none">
                        <button onclick="changeQty(1)" class="px-3 py-2 bg-gray-50 hover:bg-gray-100 text-gray-600">+</button>
                    </div>
                </div>

                {{-- Button --}}
                <div class="flex gap-2 mt-6">

                    {{-- Wishlist --}}
                    @auth
                    @if($inWishlist)
                    <form method="POST" action="{{ route('wishlist.destroy', \App\Models\Wishlist::where('user_id', Auth::id())->where('product_id', $product->product_id)->first()?->wishlist_id) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-12 h-12 border-2 border-yellow-400 rounded-xl text-yellow-400 hover:bg-yellow-50 transition text-lg">
                            ★
                        </button>
                    </form>
                    @else
                    <button id="wishlist-btn" onclick="toggleWishlist({{ $product->product_id }})"
                        class="w-12 h-12 border-2 border-gray-200 text-gray-400 rounded-xl hover:border-yellow-400 hover:text-yellow-400 transition text-lg">
                        ☆
                    </button>
                    @endif
                    @endauth

                    {{-- Add to cart --}}
                    <form method="POST" action="{{ route('carts.store') }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                        <input type="hidden" name="quantity" id="quantity-input" value="1">
                        <button type="submit" class="w-full h-12 border-2 border-pink-400 text-pink-500 rounded-xl hover:bg-pink-50 transition text-sm font-medium">
                            🛒 Add to Cart
                        </button>
                    </form>

                    {{-- Buy Now --}}
                    <form method="POST" action="{{ route('orders.storeNow') }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                        <input type="hidden" name="quantity" id="quantity-input-now" value="1">
                        <button type="submit" class="w-full h-12 bg-pink-400 hover:bg-pink-500 text-white rounded-xl transition text-sm font-medium">
                            ⚡ Buy Now
                        </button>
                    </form>

                </div>

            </div>
        </div>
    </div>
</div>

<script>
function changeQty(change) {
    const input = document.getElementById('quantity');
    const max = {{ $product->stock_number }};
    let value = parseInt(input.value) + change;
    if (value < 1) value = 1;
    if (value > max) value = max;
    input.value = value;
    document.getElementById('quantity-input').value = value;
    document.getElementById('quantity-input-now').value = value;
}

function toggleWishlist(productId) {
    const btn = document.getElementById('wishlist-btn');
    if (!btn) return;
    const isWishlisted = btn.innerText.trim() === '★';

    fetch('/wishlists/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.wishlisted) {
            btn.innerText = '★';
            btn.classList.add('border-yellow-400', 'text-yellow-400');
            btn.classList.remove('border-gray-200', 'text-gray-400');
        } else {
            btn.innerText = '☆';
            btn.classList.remove('border-yellow-400', 'text-yellow-400');
            btn.classList.add('border-gray-200', 'text-gray-400');
        }
    })
    .catch(err => console.error('error', err));
}
</script>

</x-app-layout>