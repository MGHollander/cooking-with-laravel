<x-kocina.layout>
    <x-slot name="title">
        {{ $recipe["title"] }}
    </x-slot>

    <x-slot name="meta">
        <link rel="canonical" href="{{ route("recipes.show", ["slug" => $recipe["slug"]]) }}" />
    </x-slot>

    @push("scripts")
        {{-- TODO Think of a way to import the strike helper into recipe --}}
        <script src="{{ Vite::asset('resources/kocina/js/helpers/strike.js') }}"></script>
        <script src="{{ Vite::asset('resources/kocina/js/recipe.js') }}"></script>
    @endpush

    <div
        class="container recipe-page"
        x-data="recipe({{ Illuminate\Support\Js::from($recipe["ingredients"]) }}, parseInt({{ $recipe["servings"] }}) ?? 1)"
    >
        <div class="recipe-header">
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
                                <x-icon.plate />
                            </div>
                            Aantal porties
                        </dt>
                        <dd x-text="servingsText">
                            {{ $recipe["servings"] }} {{ $recipe["servings"] === 1 ? "portie" : "porties" }}
                        </dd>
                    </dl>

                    <dl>
                        <dt>
                            <div class="recipe-meta-icon">
                                <x-icon.gauge />
                            </div>
                            Moeilijk&shy;heid
                        </dt>
                        <dd>{{ $recipe["difficulty"] }}</dd>
                    </dl>

                    @if ($recipe["preparation_minutes"])
                        <dl>
                            <dt>
                                <div class="recipe-meta-icon">
                                    <x-icon.knife />
                                </div>
                                Voor&shy;be&shy;rei&shy;dings&shy;tijd
                            </dt>
                            <dd>{{ $recipe["preparation_minutes"] }} {{ $recipe["preparation_minutes"] === 1 ? "minuut" : "minuten" }}</dd>
                        </dl>
                    @endif

                    @if ($recipe["cooking_minutes"])
                        <dl>
                            <dt>
                                <div class="recipe-meta-icon">
                                    <x-icon.cooking-timer />
                                </div>
                                Berei&shy;dings&shy;tijd
                            </dt>
                            <dd>{{ $recipe["cooking_minutes"] }} {{ $recipe["cooking_minutes"] === 1 ? "minuut" : "minuten" }}</dd>
                        </dl>
                    @endif

                    <div class="recipe-author">
                        Toegevoegd door {{ $recipe['author'] }}
                    </div>
                </div>

            </div>
        </div>

        <div class="recipe-content-container">
            <div class="recipe-ingredient-container">
                <h2>Ingredi&euml;nten</h2>

                <div class="recipe-ingredient-controls">
                    <button
                        :disabled="servings <= 1"
                        aria-label="Verminder het aantal porties"
                        x-on:click="updateServings(servings - 1)"
                    >
                        <x-icon.min />
                    </button>

                    <p x-text="servingsText">
                        {{ $recipe["servings"] }} {{ $recipe["servings"] === 1 ? "portie" : "porties" }}
                    </p>

                    <button
                        aria-label="Verhoog het aantal porties"
                        x-on:click="updateServings(servings + 1)"
                    >
                        <x-icon.plus />
                    </button>

                    <button
                        aria-label="Terug naar het standaard aantal porties"
                        x-show="servings !== parseInt({{ $recipe["servings"] }})"
                        x-on:click="updateServings({{ $recipe["servings"] }})"
                        x-transition
                    >
                        <x-icon.rotate-left />
                    </button>
                </div>

                <template x-for="list in ingredientLists">
                    <div class="recipe-ingredient-list">
                        <template x-if="list.title">
                            <h3 x-text="list.title"></h3>
                        </template>

                        <ul>
                            <template x-for="ingredient in list.ingredients">
                                <li @click="strikeIngredient($event)">
                                    <span class="strike-animation">
                                        <template x-if="ingredient.amount">
                                            <span x-text="Math.round(ingredient.amount * 100) / 100 + '&nbsp;'"></span>
                                        </template>
                                        <template x-if="ingredient.unit">
                                            <span x-text="ingredient.unit + '&nbsp;'"></span>
                                        </template>
                                        <template x-if="ingredient.name_plural && ingredient.amount > 1">
                                            <span x-text="ingredient.name_plural"></span>
                                        </template>
                                        <template
                                            x-if="! ingredient.name_plural || (ingredient.amount > 0 && ingredient.amount <= 1)">
                                            <span x-text="ingredient.name"></span>
                                        </template>
                                        <template x-if="ingredient.info">
                                            <span x-text="ingredient.info"></span>
                                        </template>
                                    </span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>

            <div class="recipe-instructions-container space-y-4 sm:px-6 md:w-2/3 md:px-0">
                <h2 class="mb-4 text-xl font-bold md:mt-6 md:text-2xl">Instructies</h2>

                <div class="recipe-instructions">
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

            @if (count($recipe["tags"]) > 0)
                <div class="recipe-tags-container">
                    @foreach ($recipe["tags"] as $tag)
                        <div>{{ $tag }}</div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-kocina.layout>
