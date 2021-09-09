<x-guest-layout>
    <x-jet-authentication-card>

        <x-guest-left is-guest={{true}}>
            {{ __('Please use this form to create new password for your account.') }}
        </x-guest-left>

        <div class="form-side">
            <x-jet-validation-errors class="mb-4" />

            <div class="mb-3">
                <a href="/" class="active">
                    <x-jet-authentication-card-logo />
                </a>
            </div>
            <h6 class="mb-5">{{ __('Forgot password') }}</h6>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">


                <fieldset class="form-group has-float-label mb-4">
                    <x-jet-label for="email" value="{{ __('Email') }}" />
                    <x-jet-input id="email" class="form-control" type="email" name="email" :value="old('email', $request->email)" required autofocus />
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
                    <x-jet-button>
                        {{ __('Reset Password') }}
                    </x-jet-button>
                </div>
            </form>
        </div>
    </x-jet-authentication-card>
</x-guest-layout>
