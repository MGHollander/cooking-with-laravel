@props([
    'recipe'
])

<div {{ $attributes->class(['recipe-instructions-container space-y-4 sm:px-6 md:w-2/3 md:px-0']) }}>
    <h2 class="mb-4 text-xl font-bold md:mt-6 md:text-2xl">Instructies</h2>

    <div class="recipe-instructions" style="--step-text: 'Stap'">
        {!! $recipe["instructions"] !!}
    </div>

    @if ($recipe["source_label"] || $recipe["source_link"])
        <p>
            <strong>Bron:</strong>
            @if ($recipe["source_link"])
                <a href="{{ $recipe["source_link"] }}" target="_blank">
                    {{ $recipe["source_label"] ?? $recipe["source_link"] }}
                </a>
            @elseif ($recipe["source_label"])
                {{ $recipe["source_label"] }}
            @endif
        </p>
    @endif
</div>
