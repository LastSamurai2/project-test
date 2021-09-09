<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn btn-primary btn-lg btn-multiple-state btn-shadow']) }}>
    {{ $slot }}
</button>
