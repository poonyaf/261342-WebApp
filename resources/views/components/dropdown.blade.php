@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    default => $width,
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute z-50 mt-2 {{ $width }} {{ $alignmentClasses }}"
         style="display: none;"
         @click="open = false">

        <div
            class="{{ $contentClasses }}"
            style="
                background: var(--surface);
                border-radius: var(--radius-xl);
                border: 1px solid rgba(0,0,0,0.08);
                box-shadow: 0 12px 30px rgba(0,0,0,0.12);
                padding: 0.25rem;
            "
        >
            {{ $content }}
        </div>
    </div>
</div>