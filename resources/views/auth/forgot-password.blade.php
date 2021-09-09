<x-guest-layout>
    <x-jet-authentication-card>

        <x-guest-left is-guest={{true}}>
            {{ __('auth.forgot-password') }}?<br>
            {{ __('auth.forgot-password-text') }}
        </x-guest-left>

        <div class="form-side">
            <x-jet-validation-errors class="mb-4" />

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-3">
                <a href="/" class="active">
                    <x-jet-authentication-card-logo />
                </a>
            </div>
            <h6 class="mb-5">{{ __('auth.forgot-password') }}</h6>

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <fieldset class="form-group has-float-label mb-4">
                    <x-jet-label for="email" value="{{ __('Email') }}" />
                    <x-jet-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus />
                </fieldset>

                <div class="flex items-center justify-end mt-4">
                    <x-jet-button>
                        {{ __('auth.send-reset-link-button') }}
                    </x-jet-button>
                </div>
            </form>

        </div>
    </x-jet-authentication-card>
</x-guest-layout>
