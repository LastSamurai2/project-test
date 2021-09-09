@props(['value'])

<label {{ $attributes->merge(['class' => 'bv-no-focus-ring col-form-label pt-0']) }}>
    {{ $value ?? $slot }}
</label>
