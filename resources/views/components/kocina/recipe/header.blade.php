@props([
    'recipe'
])

<div {{ $attributes->class(['recipe-header']) }}>
    @if ($recipe["image"])
        <img
            src="{{ $recipe["image"] }}"
            class="recipe-image"
            alt="{{ $recipe["title"] }}"
        />
    @endif

    <div class="recipe-header-body">
        <h1 class="recipe-title">{{ $recipe["title"] }}</h1>

        <div class="recipe-summary">
            {!! $recipe["summary"] !!}
        </div>

        <div class="recipe-meta">
            <dl>
                <dt>
                    <div class="recipe-meta-icon">
                        <x-icon.cutlery />
                    </div>
                    Aantal porties
                </dt>
                <dd x-text="servingsText">
                    {{ $recipe["servings"] }} {{ $recipe["servings"] === 1 ? "portie" : "porties" }}
                </dd>
            </dl>

            @if ($recipe["preparation_minutes"])
                <dl>
                    <dt>
                        <div class="recipe-meta-icon">
                            <x-icon.knife-thin />
                        </div>
                        Voor&shy;be&shy;rei&shy;ding
                    </dt>
                    <dd>{{ $recipe["preparation_minutes"] }} {{ $recipe["preparation_minutes"] === 1 ? "minuut" : "minuten" }}</dd>
                </dl>
            @endif

            @if ($recipe["cooking_minutes"])
                <dl>
                    <dt>
                        <div class="recipe-meta-icon">
                            <x-icon.cooking-pot />
                        </div>
                        Be&shy;rei&shy;ding
                    </dt>
                    <dd>{{ $recipe["cooking_minutes"] }} {{ $recipe["cooking_minutes"] === 1 ? "minuut" : "minuten" }}</dd>
                </dl>
            @endif
        </div>

        <div class="recipe-author">
            Toegevoegd door {{ $recipe['author'] }}
        </div>

        @if ($recipe['user_id'] === auth()->id())
            <div class="recipe-management">
                <a href="{{ route('recipes.edit', $recipe['id']) }}" class="button button-primary button-small">
                    Bewerk recept
                </a>
            </div>
        @endif
    </div>
</div>
