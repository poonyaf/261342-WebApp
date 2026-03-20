<section>
<header>
    <h2 class="text-lg font-medium" style="color: var(--secondary)";>
        {{ __('Profile Photo') }}
    </h2>

    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
        {{ __('Update your profile photo.') }}
    </p>
</header>

<form method="post" action="{{ route('profile.photo.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
    @csrf
    @method('PATCH')

    <div class="mt-2" x-data="{ fileName: '' }">
        <input
            id="photo"
            name="photo"
            type="file"
            class="hidden"
            accept="image/*"
            @change="fileName = $event.target.files?.[0]?.name ?? ''"
        >

        <div class="flex items-center gap-3 flex-wrap">
            <label for="photo" class="file-btn-animated-clip">
                Choose photo
            </label>

            <span class="text-sm" style="color: var(--neutral);"
                  x-text="fileName || 'No file chosen'"></span>
        </div>
    </div>

    <div class="flex items-center gap-4">
        <x-primary-button>{{ __('Save') }}</x-primary-button>

        @if (session('status') === 'profile-photo-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm font-semibold px-3 py-1 inline-block"
                style="
                    color: var(--secondary);
                    background: var(--pinkPage-neutral-2);
                    border: 1px solid rgba(0,0,0,0.06);
                    border-radius: 9999px;
                "
            >
                {{ __('Saved.') }}
            </p>
        @endif
    </div>
    
</form>

@if ($user->image)
    <div class="mt-6">
        <x-input-label for="current_photo" :value="__('Current Profile Photo')" />
        <div class="mt-1">
            <img src="{{ route('user.photo', ['filename' => $user->image]) }}"
                 alt="Profile Photo"
                 class="w-32 h-32 object-cover rounded-full" />
        </div>
    </div>
@endif
</section>