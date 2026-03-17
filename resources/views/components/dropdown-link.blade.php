<a
    {{ $attributes->merge([
        'class' => 'block w-full px-4 py-2 text-start text-sm leading-5 transition duration-150 ease-in-out focus:outline-none',
        'style' => 'color: var(--text); border-radius: var(--radius-lg);'
    ]) }}
    onmouseover="this.style.background='var(--pinkPage-neutral-2)'"
    onmouseout="this.style.background='transparent'"
    onfocus="this.style.background='var(--pinkPage-neutral-2)'"
    onblur="this.style.background='transparent'"
>
    {{ $slot }}
</a>