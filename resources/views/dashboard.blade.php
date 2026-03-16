<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight" style="color: var(--secondary);">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="page-wrap">
        <div class="container">
            <div class="card card-pad">
                <div class="text-base font-medium" style="color: var(--text);">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
