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
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0"
        style="background: var(--bg); color: var(--text);">
            <div>
                <!-- รอโลโก้ -->
             <a href="{{ route('dashboard') }}" class="flex flex-col items-center mb-4" style="gap: 4px;">
    <div style="
        width: 110px; 
        height: 110px; 
        border-radius: 50%; 
        overflow: hidden; 
        flex-shrink: 0;
        border: 3px solid rgba(255,255,255,0.8);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    ">
        <img
            src="{{ asset('images/stellar.jpg') }}"
            alt="Stellar Logo"
            style="width:100%; height:100%; object-fit:cover;"
        />
    </div>
    
    
</a>
            </div>

            <div class="animated-border shadow">
                <div class="w-full sm:max-w-2xl mt-6 px-6 py-4 overflow-hidden sm:rounded-lg">
                <!-- <div class="w-full max-w-md sm:max-w-lg lg:max-w-xl mx-auto"> -->
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
