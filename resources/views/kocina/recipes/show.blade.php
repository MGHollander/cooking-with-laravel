<x-kocina.layout>
    <x-slot name="title">
        {{ $recipe["title"] }}
    </x-slot>

    <x-slot name="meta">
        <link rel="canonical" href="{{ route("recipes.show", ["slug" => $recipe["slug"]]) }}" />
    </x-slot>

    @push("scripts")
        <script src="{{ Vite::asset('resources/kocina/js/recipe.js') }}"></script>
    @endpush

    <div
        class="container"
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

        @if (count($recipe["tags"]) > 0)
            <div class="!mt-4 flex flex-wrap text-sm">
                @foreach ($recipe["tags"] as $tag)
                    <div class="mb-2 mr-2 rounded bg-gray-200 px-2">
                        {{ $tag }}
                    </div>
                @endforeach
            </div>
        @endif


        <div class="space-y-6 md:flex md:items-start md:space-x-8 md:space-y-0">
            <div class="-mx-6 bg-gray-100 p-6 sm:mx-0 sm:rounded-lg md:w-1/3">
                <h2 class="mb-2 text-xl font-bold md:text-2xl">Ingrediënten</h2>

                <div class="-mx-2 mb-4 flex items-center justify-between rounded bg-gray-200 p-2">
                    <p x-text="servingsText">
                        {{ $recipe["servings"] }} {{ $recipe["servings"] === 1 ? "portie" : "porties" }}
                    </p>

                    <div class="space-x-2">
                        <button
                            class="inline-block w-8 rounded border-2 border-gray-500 text-lg font-bold text-gray-600 transition-all hover:bg-gray-500 hover:text-white"
                            aria-label="Verhoog het aantal porties"
                            x-on:click="incrementServings"
                        >
                            +
                        </button>

                        <button
                            :disabled="servings <= 1"
                            class="inline-block w-8 rounded border-2 border-gray-500 text-lg font-bold text-gray-600 hover:bg-gray-500 hover:text-white disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-transparent disabled:hover:text-gray-600"
                            aria-label="Verminder het aantal porties"
                            x-on:click="decrementServings"
                        >
                            -
                        </button>
                    </div>
                </div>
                <template x-for="list in ingredientLists">
                    <div>
                        <h3 x-text="list.title" class="mb-2 mt-8 text-lg font-bold"></h3>

                        <ul class="m-0 space-y-1">
                            <template x-for="ingredient in list.ingredients">
                                <li class="flex flex-auto">
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
                                </li>
                            </template>
                        </ul>
                    </div>
                </template>
            </div>

            <div class="space-y-4 sm:px-6 md:w-2/3 md:px-0">
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
        </div>
    </div>
</x-kocina.layout>
