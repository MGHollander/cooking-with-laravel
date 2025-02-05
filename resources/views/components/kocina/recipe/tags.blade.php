@props([
    'recipe'
])


<div {{ $attributes->class(['recipe-tags-container']) }}>
    <ul>
        @foreach ($recipe["tags"] as $tag)
            <li>{{ $tag }}</li>
        @endforeach
    </ul>
</div>
