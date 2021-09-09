<x-guest-layout>
    <x-jet-authentication-card>

        <x-guest-left is-guest={{false}}>
            {{ __('auth.register-title') }}
        </x-guest-left>

        <div class="form-side">
            <x-jet-validation-errors class="mb-4" />

            <div class="mb-3">
                <a href="/" class="active">
                    <x-jet-authentication-card-logo />
                </a>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf


                <fieldset class="form-group has-float-label mb-4">
                    <x-jet-label for="name" value="{{ __('Name') }}" />
                    <x-jet-input id="name" class="form-control" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                </fieldset>

                <fieldset class="form-group has-float-label mb-4">
                        <x-jet-label for="email" value="{{ __('Email') }}" />
                        <x-jet-input id="email" class="form-control" type="email" name="email" :value="old('email')" required />
                </fieldset>

                <fieldset class="form-group has-float-label mb-4">
                    <x-jet-label for="password" value="{{ __('Password') }}" />
                    <x-jet-input id="password" class="form-control" type="password" name="password" required autocomplete="new-password" />
                </fieldset>

                <fieldset class="form-group has-float-label mb-4">
                <x-jet-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-jet-input id="password_confirmation" class="form-control" type="password" name="password_confirmation" required autocomplete="new-password" />
                </fieldset>

                <div class="d-flex justify-content-between align-items-center">
                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                        {{ __('auth.already-registered?') }}
                    </a>

                    <x-jet-button class="ml-4">
                        {{ __('auth.register-button') }}
                    </x-jet-button>
                </div>
            </form>
        </div>
    </x-jet-authentication-card>
</x-guest-layout>
