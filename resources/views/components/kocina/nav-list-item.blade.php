<li {{ $attributes }}>
    <a href="{{ $route }}" @class(['active' => url()->current() === $route])>
        {{ $label }}
    </a>
</li>
