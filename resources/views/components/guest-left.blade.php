<div class="position-relative image-side"><p class="text-white h2">{{ __('MAGIC IS IN THE DETAILS') }}</p>
    <p class="white mb-0">
        {{ $slot }}
        <br>
        {{ __('auth.is-registered') }}
        <a href="{{ route('login') }}" class="white">{{ __('auth.login') }}</a>.
    </p>
</div>



