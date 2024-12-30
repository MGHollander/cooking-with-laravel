@props([
    'recipe'
])

<div {{ $attributes->class(['recipe-ingredients-container']) }}>
    <h2>Ingredi&euml;nten</h2>

    <div class="recipe-ingredients-controls">
        <button
            class="button button-outline"
            :disabled="servings <= 1"
            aria-label="Verminder het aantal porties"
            @click="updateServings(servings - 1)"
        >
            <x-icon.min />
        </button>

        <p x-text="servingsText">
            {{ $recipe["servings"] }} {{ $recipe["servings"] === 1 ? "portie" : "porties" }}
        </p>

        <button
            class="button button-outline"
            aria-label="Verhoog het aantal porties"
            @click="updateServings(servings + 1)"
        >
            <x-icon.plus />
        </button>

        <button
            class="button button-outline"
            aria-label="Terug naar het standaard aantal porties"
            @click="updateServings({{ $recipe["servings"] }})"
            x-show="servings !== parseInt({{ $recipe["servings"] }})"
            x-transition
        >
            <x-icon.rotate-left />
        </button>
    </div>

    <div class="recipe-ingredients-list-container">
        <button
            class="button button-outline recipe-ingredients-list-reset"
            aria-label="Reset de afgestreepte ingredi&euml;nten"
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
                                class="strike-animation"
                                :class="{ 'striked' : strikedIngredientsList.has(ingredient) }"
                            >
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
</div>
