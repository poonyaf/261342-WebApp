@props(['active'])

@php
    $isActive = ($active ?? false);
@endphp

<a
    {{ $attributes->merge([
        'class' => 'block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium transition duration-150 ease-in-out focus:outline-none',
        'style' =>
            'color: var(--text);' .
            'border-color: ' . ($isActive ? 'var(--secondary)' : 'transparent') . ';' .
            'background: ' . ($isActive ? 'var(--pinkPage-neutral-2)' : 'transparent') . ';'
    ]) }}
    onmouseover="this.style.background='var(--pinkPage-neutral-2)'"
    onmouseout="this.style.background='{{ $isActive ? 'var(--pinkPage-neutral-2)' : 'transparent' }}'"
>
    {{ $slot }}
</a>