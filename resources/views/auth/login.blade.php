<x-guest-layout>
    <x-jet-authentication-card>

        <x-guest-left is-guest={{true}}>
            {{ __('auth.side-info') }}
        </x-guest-left>

        <div class="form-side">

            <x-jet-validation-errors class="mb-4" />

            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <div class="mb-5">
                <a href="/" class="active">
                    <x-jet-authentication-card-logo />
                </a>
            </div>
            <h6 class="mb-4">{{ __('auth.login-title') }}</h6>
            <form method="POST" action="{{ route('login') }}" class="av-tooltip tooltip-label-bottom">
                @csrf
                <fieldset class="form-group has-float-label mb-4">
                    <x-jet-label for="email" value="{{ __('Email') }}" />
                    <x-jet-input id="email" class="form-control" type="email" name="email" :value="old('email')" required autofocus />
                </fieldset>

                <fieldset class="form-group has-float-label mb-4">
                    <x-jet-label for="password" value="{{ __('Password') }}" />
                    <x-jet-input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" />
                </fieldset>

                <label for="remember_me" class="flex items-center">
                    <input id="remember_me" type="checkbox" class="form-checkbox" name="remember">
                    <span class="ml-2 text-sm text-gray-600">{{ __('auth.remember') }}</span>
                </label>
                <div class="d-flex justify-content-between align-items-center">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="">{{ __('auth.forgot-password') }}?</a>
                    @endif

                    <x-jet-button>
                        {{ __('auth.login-button') }}
                    </x-jet-button>
                </div>
            </form>

        </div>
    </x-jet-authentication-card>
</x-guest-layout>
