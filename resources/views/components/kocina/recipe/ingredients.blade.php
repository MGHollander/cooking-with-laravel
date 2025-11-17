@props([
    'recipe'
])

<div {{ $attributes->class(['recipe-ingredients-container']) }}>
    <h2>{!! __('recipes.show.ingredients') !!}</h2>

    <div class="recipe-ingredients-controls">
        <button
            class="button button-icon button-outline"
            :disabled="servings <= 1"
            aria-label="{{ __('recipes.show.decrease_servings') }}"
            @click="updateServings(servings - 1)"
        >
            <x-icon.min />
        </button>

        <p x-text="servingsText">
            {{ $recipe["servings"] }} {{ trans_choice('recipes.show.servings', $recipe["servings"]) }}
        </p>

        <button
            class="button button-icon button-outline"
            aria-label="{{ __('recipes.show.increase_servings') }}"
            @click="updateServings(servings + 1)"
        >
            <x-icon.plus />
        </button>

        <button
            class="button button-icon button-outline"
            aria-label="{{ __('recipes.show.reset_servings') }}"
            @click="updateServings({{ $recipe["servings"] }})"
            x-show="servings !== parseInt({{ $recipe["servings"] }})"
            x-transition
        >
            <x-icon.rotate-left />
        </button>
    </div>

    <div class="recipe-ingredients-list-container">
        <button
            class="button button-outline button-icon recipe-ingredients-list-reset"
            aria-label="{{ __('recipes.show.reset_striked') }}"
            @click="strikedIngredientsList.clear()"
            x-show="strikedIngredientsList.size > 0"
            x-transition
        >
            <x-icon.rotate-left />
        </button>

        <template x-for="list in ingredientLists">
            <div class="recipe-ingredients">
                <template x-if="list.title">
                    <h3 x-text="list.title"></h3>
                </template>

                <ul>
                    <template x-for="ingredient in list.ingredients">
                        <li @click="strikeIngredient(ingredient)">
                            <span
                                class="recipe-ingredient"
                                :class="{ 'striked' : strikedIngredientsList.has(ingredient) }"
                            >
                                <template x-if="ingredient.amount">
                                    <span x-text="Math.round(ingredient.amount * 100) / 100"></span>
                                </template>
                                <template x-if="ingredient.unit">
                                    <span x-text="ingredient.unit"></span>
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
</div>
