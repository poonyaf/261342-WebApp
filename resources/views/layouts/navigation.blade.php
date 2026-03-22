<nav x-data="{ open: false }" class="sticky top-0 z-50 transition-colors duration-300 bg-secondary">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            {{-- LOGO --}}
            <div class="flex items-center gap-8">
                <a href="{{ route('products.index') }}" class="flex items-center gap-2 shrink-0">
                    <div style="width:42px; height:42px; border-radius:50%; overflow:hidden; border: 2px solid rgba(255,255,255,0.6); flex-shrink:0;">
                        <img src="{{ asset('images/stellar.jpg') }}" alt="Stellar Logo" style="width:100%; height:100%; object-fit:cover;">
                    </div>
                </a>

                {{-- DESKTOP MENU --}}
                <div class="hidden sm:flex items-center gap-1">

                    {{-- Admin --}}
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.index') }}"
                               style="display:flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:14px; font-weight:500; text-decoration:none; transition: all 0.2s;
                               {{ request()->routeIs('admin.*') ? 'background:rgba(255,255,255,0.25); color:white;' : 'color:white;' }}">
                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1Z"/></svg>
                                Admin
                            </a>
                        @endif
                    @endauth
                    {{-- Seller --}}
@auth
    @if(auth()->user()->role === 'seller')
        <a href="{{ route('seller.index') }}"
           style="display:flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:14px; font-weight:500; text-decoration:none; transition: all 0.2s;
           {{ request()->routeIs('seller.*') ? 'background:rgba(255,255,255,0.25); color:white;' : 'color:white;' }}">
            My Shop
        </a>
    @endif
@endauth

                    {{-- Products --}}
                    <a href="{{ route('products.index') }}"
                       style="display:flex; align-items:center; gap:6px; padding:8px 14px; border-radius:10px; font-size:14px; font-weight:500; text-decoration:none; transition: all 0.2s;
                       {{ request()->routeIs('products.index') ? 'background:rgba(255,255,255,0.25); color:white;' : 'color:white;' }}">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                        Products
                    </a>

                   

                </div>
            </div>

            {{-- RIGHT SIDE: Cart + Profile --}}
            <div class="hidden sm:flex items-center gap-3">

                {{-- Cart Badge --}}
                <a href="{{ route('carts.index') }}" style="position:relative; display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:10px; text-decoration:none; transition:all 0.2s;
                {{ request()->routeIs('carts.*') ? 'background:rgba(255,255,255,0.25);' : '' }}">
                    <svg width="20" height="20" fill="none" stroke="#db2777" stroke-width="2" viewBox="0 0 24 24"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    @auth
                        @php
                            $cartCount = auth()->user()->cart?->items->sum('quantity') ?? 0;
                        @endphp
                        @if($cartCount > 0)
                            <span style="position:absolute; top:4px; right:4px; background:#db2777; color:white; font-size:10px; font-weight:600; width:16px; height:16px; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                {{ $cartCount > 9 ? '9+' : $cartCount }}
                            </span>
                        @endif
                    @endauth
                </a>
                {{-- Wishlist --}}
<a href="{{ route('wishlist.index') }}" style="display:flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:10px; text-decoration:none; transition:all 0.2s;
{{ request()->routeIs('wishlist.*') ? 'background:rgba(255,255,255,0.25);' : '' }}">
    <svg width="20" height="20" fill="#facc15" stroke="#facc15" stroke-width="1.5" viewBox="0 0 24 24"
     style="filter: drop-shadow(0 0 3px #facc15);">
    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
</svg>
</a>

                {{-- Profile Dropdown --}}
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button style="display:flex; align-items:center; gap:8px; padding:6px 10px; border-radius:10px; border:none; background:rgba(255,255,255,0.15); cursor:pointer; transition:all 0.2s;">
                            @auth
                              
                                <div style="width:32px; height:32px; border-radius:50%; overflow:hidden; flex-shrink:0;">
    @if(auth()->user()->image)
        <img src="{{ auth()->user()->image }}"
             style="width:100%; height:100%; object-fit:cover;">
    @else
        {{-- fallback to first letter if no photo --}}
        <div style="width:100%; height:100%; background:linear-gradient(135deg,#f9a8d4,#db2777); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; color:white;">
            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
        </div>
    @endif
</div>
                                <span style="font-size:14px; font-weight:500; color:white;">{{ auth()->user()->name }}</span>
                            @endauth
                            <svg width="14" height="14" fill="none" stroke="#db2777" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                    </x-slot>
                    
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.index')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" class="dropdown-danger"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- Hamburger (mobile) --}}
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" style="padding:8px; border-radius:8px; border:none; background:transparent; cursor:pointer; color:white;">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MOBILE MENU --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden" style="border-top: 1px solid rgba(255,182,193,0.3); padding: 12px 16px;">
        @auth
            <div style="display:flex; align-items:center; gap:10px; padding:10px 0; margin-bottom:8px; border-bottom: 1px solid rgba(255,182,193,0.2);">
                <div style="width:36px; height:36px; border-radius:50%; background:linear-gradient(135deg,#f9a8d4,#db2777); display:flex; align-items:center; justify-content:center; font-size:14px; font-weight:600; color:white;">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <p style="font-size:14px; font-weight:500; color:white; margin:0;">{{ auth()->user()->name }}</p>
                    <p style="font-size:12px; color:#c084a0; margin:0;">{{ auth()->user()->email }}</p>
                </div>
            </div>
        @endauth

        <!-- remove dashboard blade -->
        <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.index')">🛍️ Products</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">📄 Orders</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('payments.index')" :active="request()->routeIs('payments.*')">💳 Payments</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('carts.index')" :active="request()->routeIs('carts.*')">🛒 Cart</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('wishlist.index')" :active="request()->routeIs('wishlist.*')">🤍 Wishlist</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.edit')">👤 Profile</x-responsive-nav-link>
        <x-responsive-nav-link :href="route('profile.index')" :active="request()->routeIs('profile.index')">👤 Profile</x-responsive-nav-link>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" style="color:white;">
                🚪 Log Out
            </x-responsive-nav-link>
        </form>
    </div>
</nav>