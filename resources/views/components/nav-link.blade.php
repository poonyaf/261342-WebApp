@props(['active'])

@php
    $isActive = ($active ?? false);

    // Keep layout/spacing from Breeze, but remove indigo/gray color classes.
    $classes = 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out focus:outline-none';
@endphp

<a
    {{ $attributes->merge(['class' => $classes]) }}
    style="
        color: {{ $isActive ? 'rgba(255,255,255,1)' : 'rgba(255,255,255,0.85)' }};
        border-color: {{ $isActive ? 'var(--secondary)' : 'transparent' }};
    "
    onmouseover="this.style.borderColor='var(--secondary)'; this.style.color='rgba(255,255,255,1)';"
    onmouseout="this.style.borderColor='{{ $isActive ? 'var(--secondary)' : 'transparent' }}'; this.style.color='{{ $isActive ? 'rgba(255,255,255,1)' : 'rgba(255,255,255,0.85)' }}';"
>
    {{ $slot }}
</a>