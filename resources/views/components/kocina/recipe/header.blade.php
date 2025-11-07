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
                    {{ __('recipes.show.servings_title') }}
                </dt>
                <dd x-text="servingsText">
                    {{ $recipe["servings"] }} {{ trans_choice('recipes.show.servings', $recipe["servings"]) }}
                </dd>
            </dl>

            @if ($recipe["preparation_minutes"])
                <dl>
                    <dt>
                        <div class="recipe-meta-icon">
                            <x-icon.knife-thin />
                        </div>
                        {!! __('recipes.show.preparation') !!}
                    </dt>
                    <dd>{{ $recipe["preparation_minutes"] }} {{ $recipe["preparation_minutes"] === 1 ? __('recipes.show.minute') : __('recipes.show.minutes') }}</dd>
                </dl>
            @endif

            @if ($recipe["cooking_minutes"])
                <dl>
                    <dt>
                        <div class="recipe-meta-icon">
                            <x-icon.cooking-pot />
                        </div>
                        {!! __('recipes.show.cooking') !!}
                    </dt>
                    <dd>{{ $recipe["cooking_minutes"] }} {{ $recipe["cooking_minutes"] === 1 ? __('recipes.show.minute') : __('recipes.show.minutes') }}</dd>
                </dl>
            @endif
        </div>

        <div class="recipe-author">
            {{ __('recipes.show.added_by') }} {{ $recipe['author'] }}
        </div>

        @if ($recipe['user_id'] === auth()->id())
            <div class="recipe-management">
                <a href="{{ route('recipes.edit', $recipe['id']) }}" class="button button-primary button-small">
                    {{ __('recipes.show.edit') }}
                </a>
            </div>
        @endif
    </div>
</div>
