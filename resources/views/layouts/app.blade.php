<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body {{ $attributes->merge(['class' => 'font-sans antialiased transition-colors duration-300']) }}>

    <div class="min-h-screen">
        {{-- NAVBAR --}}
        @include('layouts.navigation')

        {{-- HEADER --}}
        @isset($header)
            <header>
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h2 class="font-semibold text-xl leading-tight" style="color: var(--text);">
                        {{ $header }}
                    </h2>
                </div>
            </header>
        @endisset

        {{-- CONTENT --}}
        <main class="min-h-screen">
            {{ $slot }}
        </main>
    {{-- FOOTER --}}
<footer style="background: var(--primary); padding: 32px 0; text-align: center;">
    <div style="display:flex; flex-direction:column; align-items:center; gap:8px;">
        <img src="{{ asset('images/FontLogo.png') }}" 
             style="height:25px; width:auto; object-fit:contain;">
        <p style="font-size:0.85rem; color: var(--pinkPage-secondary); opacity:0.8;">
            Great styles, stellar prices
        </p>
        <p style="font-size:0.8rem; color: var(--pinkPage-secondary); opacity:0.5;">
            © {{ date('Y') }} Stellar. All rights reserved.
        </p>
    </div>
</footer>
    </div>

</body>
</html>
