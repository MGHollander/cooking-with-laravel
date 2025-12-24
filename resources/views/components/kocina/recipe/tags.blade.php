@props([
    'recipe'
])


<div {{ $attributes->class(['recipe-tags-container']) }}>
    <ul lang="{{ $recipe["locale"] }}">
        @foreach ($recipe["tags"] as $tag)
            <li>{{ $tag }}</li>
        @endforeach
    </ul>
</div>
