<script setup>
import { faEllipsisV, faMoon, faSun } from "@fortawesome/free-solid-svg-icons";
import { faUndo } from "@fortawesome/free-solid-svg-icons/faUndo";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { Head, router } from "@inertiajs/vue3";
import { useWakeLock } from "@vueuse/core";
import { computed, reactive, ref } from "vue";
import Button from "@/Components/Button.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import DefaultLayout from "@/Layouts/Default.vue";

const props = defineProps({
  recipe: Object,
});

const localRecipe = ref(props.recipe);
const defaultServings = localRecipe.value.servings;
const servings = ref(localRecipe.value.servings);
const servingsLabel = computed(() => (localRecipe.value.servings === 1 ? "portie" : "porties"));

function updateServings(amount) {
  for (let listKey in localRecipe.value.ingredients) {
    for (let key in localRecipe.value.ingredients[listKey].ingredients) {
      const ingredientAmount = parseFloat(props.recipe.ingredients[listKey].ingredients[key].amount);
      localRecipe.value.ingredients[listKey].ingredients[key].amount =
        (ingredientAmount / parseFloat(servings.value)) * amount;
    }
  }
  servings.value = amount;
}

function confirmDeletion() {
  if (confirm("Weet je zeker dat je dit recept wilt verwijderen?")) {
    router.delete(route("recipes.destroy", localRecipe.value.id), {
      method: "delete",
    });
  }
}

const wakeLock = reactive(useWakeLock());
const wakeLockButtonIcon = computed(() => (wakeLock.isActive ? faMoon : faSun));
const wakeLockButtonTitle = computed(() =>
  wakeLock.isActive ? "Scherm automatisch dimmer volgens systeem instellingen" : "Scherm niet automatisch dimmen"
);

function toggleWakeLock() {
  return wakeLock.isActive ? wakeLock.release() : wakeLock.request("screen");
}

const strikedIngredients = reactive(new Set());
const toggleStrike = (ingredient) => {
  if (strikedIngredients.has(ingredient)) {
    strikedIngredients.delete(ingredient);
  } else {
    strikedIngredients.add(ingredient);
  }
};
</script>

<template>
  <Head :title="localRecipe.title" />

  <DefaultLayout>
    <div class="overflow-hidden bg-white sm:rounded-lg sm:shadow-lg">
      <div class="m-6 space-y-6 md:space-y-10 lg:m-10">
        <div>
          <div class="float-right flex gap-2">
            <Button
              v-if="wakeLock.isSupported"
              button-style="ghost"
              aria-label="Uitklapmenu voor acties op recept"
              class="w-10 !p-2.5"
              :title="wakeLockButtonTitle"
              @click="toggleWakeLock"
            >
              <FontAwesomeIcon :icon="wakeLockButtonIcon" class="mx-auto" />
            </Button>
            <Dropdown v-if="$page.props.auth.user" align="right" width="48">
              <template #trigger>
                <Button button-style="ghost" aria-label="Uitklapmenu voor acties op recept" class="w-10 !p-2.5">
                  <FontAwesomeIcon :icon="faEllipsisV" class="mx-auto" />
                </Button>
              </template>

              <template #content>
                <DropdownLink :href="route('recipes.edit', localRecipe.id)">Recept bewerken</DropdownLink>
                <DropdownLink href="#" @click="confirmDeletion">Verwijder</DropdownLink>
              </template>
            </Dropdown>
          </div>

          <h1 class="mb-4 text-2xl font-bold md:text-3xl">{{ localRecipe.title }}</h1>

          <div v-if="localRecipe.tags.length > 0" class="-mb-2 flex flex-wrap text-sm">
            <div v-for="item in localRecipe.tags" :key="item.id" class="mb-2 mr-2 rounded bg-gray-200 px-2">
              {{ item }}
            </div>
          </div>
        </div>

        <div v-if="localRecipe.summary" class="md:text-lg" v-html="localRecipe.summary" />
      </div>

      <div
        class="m-6 grid items-center space-y-6 md:space-y-0 lg:m-10"
        :class="{
          'md:grid-cols-2': localRecipe.image,
        }"
      >
        <div v-if="localRecipe.image">
          <img :src="localRecipe.image" class="mx-auto aspect-[4/3] w-full rounded-lg object-cover md:mx-0" />
        </div>

        <div>
          <div
            class="grid grid-cols-2 gap-4 text-center sm:max-md:grid-cols-4"
            :class="{
              'md:grid-cols-4': !localRecipe.image,
            }"
          >
            <div>
              <div class="mx-auto w-16 fill-orange-600">
                <svg viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M307.75 141c-85.1 0-154.84 73.58-154.84 163.8S222 468.6 307.75 468.6c85.1 0 154.2-73.58 154.2-163.8 0-90.86-69.11-163.8-154.2-163.8zm0 301.36c-71 0-129.25-62.06-129.25-138.2S236.09 166 307.75 166c71 0 128.61 62.06 128.61 138.2s-57.59 138.16-128.61 138.16z"
                  />
                  <path
                    d="M307.75 187.71c-60.14 0-109.41 52.47-109.41 116.45s49.26 116.45 109.41 116.45 109.41-52.47 109.41-116.45-49.27-116.45-109.41-116.45zm0 207.31c-46.07 0-83.82-40.95-83.82-90.86s37.75-90.86 83.82-90.86 83.82 40.95 83.82 90.86c0 50.55-37.76 90.84-83.82 90.84zM549 297.76l.64-160a13.36 13.36 0 0 0-5.12-10.24q-4.8-3.84-11.52-1.92c-1.92.64-46.07 14.08-46.07 92.78 0 37.11 2.56 64.62 6.4 79.34a23.65 23.65 0 0 0-3.84 13.44v139.52c0 13.44 10.88 24.31 23.67 24.31h14.72c13.44 0 23.67-10.88 23.67-24.31V310.56c1.25-4.48-.03-8.96-2.55-12.8zM524 166l-.64 119.65h-5.76c-1.92-8.32-4.48-28.15-4.48-67.82.01-25 5.13-41.64 10.88-51.83zm3.2 283.45h-10.87V311.84h10.88zm-400.53-323.8a12.83 12.83 0 0 0-12.8 12.8v46.71h-7v-46.72a12.8 12.8 0 0 0-25.59 0v46.71h-7.71v-46.71a12.8 12.8 0 0 0-25.59 0v59.5a3.85 3.85 0 0 0 .64 2.56v.64c0 .64.64 1.28.64 1.92v.64l20.47 34.55a25.13 25.13 0 0 0-7 17.92v194.51c0 13.44 10.88 24.31 23.67 24.31h14.72c13.44 0 23.67-10.88 23.67-24.31V255.53a25.66 25.66 0 0 0-7.68-17.92l19.83-33.27v-.64a2.35 2.35 0 0 0 .64-1.92v-.64c0-.64.64-1.92.64-2.56v-60.14c1.25-7.04-4.51-12.79-11.55-12.79zm-23 85.1L93.4 228l-10.23-17.26zM88.28 449.4v-190c1.92.64 3.2 1.28 5.12 1.28a18.68 18.68 0 0 0 5.76-1.28v190z"
                  />
                </svg>
              </div>
              <strong>Aantal porties</strong><br />
              {{ servings }} {{ servingsLabel }}
            </div>

            <div>
              <div class="mx-auto w-16 fill-orange-600">
                <svg viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M299.29 118.57c-120.29 0-217.86 97.61-217.86 217.86a215.64 215.64 0 0 0 48.81 137.22 13.8 13.8 0 0 0 19.76 2.12c5.66-5 7.07-14.15 2.12-19.81a187.92 187.92 0 0 1-20.51-31.83l45.27-18.39a14.12 14.12 0 1 0-10.61-26.17l-46 18.39c-5-14.15-8.49-29-9.2-44.56h48.1a14.15 14.15 0 0 0 0-28.29h-48.74a165.28 165.28 0 0 1 8.49-46l43.85 18.39c2.12.71 3.54 1.41 5.66 1.41a14 14 0 0 0 12.73-8.49 14.36 14.36 0 0 0-7.78-18.39l-43.85-18.39A204.84 204.84 0 0 1 155 214.77l32.54 32.54a13.68 13.68 0 0 0 19.81 0 13.68 13.68 0 0 0 0-19.81l-33.24-33.24A211.56 211.56 0 0 1 213 167.38l17.68 42.44c2.12 5.66 7.78 8.49 13.44 8.49 2.12 0 3.54 0 5.66-1.41a15.46 15.46 0 0 0 3.54-2.12c-1.41 12-2.83 30.42-2.83 58 0 33.24.71 74.27 3.54 86.29a49.32 49.32 0 0 0 48.1 38.2 54.6 54.6 0 0 0 10.61-1.41c26.88-5.66 43.85-31.83 38.2-58-2.12-12-18.39-49.51-31.83-79.22-12.73-28.29-21.22-45.27-27.59-55.17 2.12 2.12 5.66 2.83 8.49 2.83a14.19 14.19 0 0 0 14.15-14.15v-44.58a202.27 202.27 0 0 1 46.68 9.2l-17.68 42.44a14.36 14.36 0 0 0 7.78 18.39c2.12.71 3.54 1.41 5.66 1.41a14 14 0 0 0 12.73-8.49L387 168.08a182.4 182.4 0 0 1 38.2 26.17L392 227.5a13.68 13.68 0 0 0 0 19.81 13.68 13.68 0 0 0 19.81 0l32.54-32.54c9.9 12 19.1 25.46 25.46 39.61l-43.85 18.39a14.36 14.36 0 0 0-7.78 18.39c2.12 5.66 7.78 8.49 13.44 8.49 2.12 0 3.54 0 5.66-1.41l43.85-17.68a233 233 0 0 1 8.49 45.27h-48.1a14.15 14.15 0 0 0 0 28.29h48.1a199.17 199.17 0 0 1-9.9 45.27l-45.27-19.1a14.12 14.12 0 0 0-10.61 26.17l44.56 18.39a207.78 207.78 0 0 1-21.93 33.24 13.8 13.8 0 0 0 2.12 19.81c2.83 2.12 5.66 3.54 9.2 3.54a12.73 12.73 0 0 0 10.61-5 217.33 217.33 0 0 0 50.22-139.34c-.76-120.92-99.08-218.53-219.33-218.53zm24 226.35a20.66 20.66 0 0 1-16.27 24c-11.32 2.12-22.63-5-24.76-15.56-2.12-12-3.54-71.44-2.12-110.34 16.32 35.41 40.37 90.58 43.2 101.9zM271.71 188.6h-2.83c-5 .71-8.49 2.83-11.32 9.9l-17.68-42.44a196.33 196.33 0 0 1 46-9.2v46c0 1.41 0 2.83.71 3.54-7.1-7.8-10.59-7.8-14.88-7.8z"
                  />
                  <circle cx="300.24" cy="339.59" r="9.2" transform="rotate(-11.9 300.247 339.58)" />
                </svg>
              </div>
              <strong>Moeilijkheid</strong><br />
              {{ localRecipe.difficulty }}
            </div>

            <div v-if="localRecipe.preparation_minutes">
              <div class="mx-auto w-16 fill-emerald-700">
                <svg viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M540.13 125.81C530 105.45 504.5 85.08 479 85.08a41.47 41.47 0 0 0-9.46.73c-7.27 1.45-26.91 5.82-136.73 130.92h-.73c-8.73 0-13.82 0-145.46 138.19C122.66 420.37 60.11 488 59.38 488.74a14.55 14.55 0 0 0 8 24 122.6 122.6 0 0 0 24 2.18c69.09 0 152.73-53.82 210.92-98.91 39.27-30.55 113.46-96.73 115.64-114.19.73-6.55-2.18-13.82-9.46-23.27L487 181.81h3.64c14.55 0 34.91-3.64 47.28-19.64 8.08-9.45 8.76-23.27 2.21-36.36zM102.29 485.1c85.1-90.91 203.65-215.28 230.56-237.83 13.82 8.73 45.09 37.82 53.82 50.91C361.94 330.91 207 474.92 102.29 485.1zM515.4 144.72c-5.82 7.27-18.91 8-24.73 8a45.59 45.59 0 0 1-8-.73c-5.09-.73-10.18 1.45-13.82 5.09l-78.55 96.73a246.41 246.41 0 0 0-30.55-25.46c93.83-104.72 114.2-113.45 115.65-114.18 9.46-2.18 25.46 6.55 34.18 18.91 5.82 6.55 5.82 10.92 5.82 11.64z"
                  />
                  <circle cx="483.4" cy="135.26" r="9.46" />
                </svg>
              </div>
              <strong>Voorbereidingstijd</strong><br />
              {{ localRecipe.preparation_minutes }} minuten
            </div>

            <div v-if="localRecipe.cooking_minutes">
              <div class="mx-auto w-16 fill-emerald-700">
                <svg viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg">
                  <path
                    d="M299.32 388.92c-1.35 2.7-2 4.73-2.7 6.08-3.38 7.43-5.4 12.83-7.43 16.88h20.94c-2-3.38-4.05-8.1-6.75-14.18-1.38-3.38-2.7-6.08-4.06-8.78zM196.67 276.81a13.51 13.51 0 0 1 27 0v32.42h33.09v-32.42a13.51 13.51 0 1 1 27 0v32.42h32.42v-32.42a13.51 13.51 0 1 1 27 0v32.42h32.42v-32.42a13.51 13.51 0 0 1 27 0v32.42h33.09C424.26 212.66 352 118.11 299.32 118.11c-51.33 0-124.26 95.9-136.42 191.12h33.77z"
                    fill="none"
                  />
                  <path
                    d="M388.47 336.24H160.88c.68 110.75 71.59 150.6 138.44 150.6s137.77-39.17 137.09-150.6zm-47.95 93.2c-4.73 9.45-12.83 9.45-39.17 9.45h-8.1c-20.26 0-30.39 0-34.44-10.13-2.7-6.75-2-10.13 13.51-44.57 13.51-30.39 16.88-36.47 27-36.47s13.51 6.08 29.71 38.49c13.52 29.05 14.87 35.79 11.49 43.23z"
                    fill="none"
                  />
                  <path
                    d="M299.32 91.1c-75.64 0-164.78 132.37-164.78 243.8 0 108.73 64.83 179 165.46 179s165.46-70.23 165.46-179C464.78 220.76 377 91.1 299.32 91.1zm0 27c52.68 0 124.94 94.55 136.42 191.12h-33.09v-32.41a13.51 13.51 0 0 0-27 0v32.42h-32.43v-32.42a13.51 13.51 0 1 0-27 0v32.42h-32.43v-32.42a13.51 13.51 0 1 0-27 0v32.42h-33.1v-32.42a13.51 13.51 0 0 0-27 0v32.42h-33.78C175.06 214 248 118.11 299.32 118.11zm0 368.73c-66.86 0-137.77-39.84-138.44-150.6h275.54c.67 111.44-70.24 150.61-137.1 150.61z"
                  />
                  <path
                    d="M299.32 347.72c-10.13 0-13.51 6.08-27 36.47-15.54 34.44-16.22 37.81-13.52 44.57 4.05 10.13 14.18 10.13 34.44 10.13h8.1c26.34 0 34.44 0 39.17-9.45 3.38-7.43 2-14.18-11.48-43.22-16.2-32.42-19.58-38.5-29.71-38.5zm1.35 64.16h-11.48c2-4.05 4.05-9.45 7.43-16.88.68-1.35 1.35-3.38 2.7-6.08 1.35 2.7 2.7 5.4 4.05 8.78 2.7 6.08 4.73 10.81 6.75 14.18z"
                  />
                </svg>
              </div>

              <strong>Bereidingstijd</strong><br />
              {{ localRecipe.cooking_minutes }} minuten
            </div>
          </div>
        </div>
      </div>

      <div class="m-6 space-y-6 md:space-y-10 lg:m-10">
        <div class="space-y-6 md:flex md:items-start md:space-x-8 md:space-y-0">
          <div class="-mx-6 bg-gray-100 p-6 sm:mx-0 sm:rounded-lg md:w-1/3">
            <h2 class="mb-2 text-xl font-bold md:text-2xl">Ingrediënten</h2>

            <div class="-mx-2 mb-4 flex items-center justify-between rounded bg-gray-200 p-2">
              <div>{{ servings }} {{ servingsLabel }}</div>
              <div class="flex items-stretch gap-2">
                <button
                  v-if="servings !== defaultServings"
                  class="w-8 rounded text-xs text-gray-600 transition-all hover:bg-gray-500 hover:text-white"
                  aria-label="Terug naar het standaard aantal porties"
                  @click="updateServings(defaultServings)"
                >
                  <FontAwesomeIcon :icon="faUndo" />
                </button>

                <button
                  class="w-8 rounded border-2 border-gray-500 text-lg font-bold text-gray-600 transition-all hover:bg-gray-500 hover:text-white"
                  aria-label="Verhoog aantal porties"
                  @click="updateServings(parseInt(servings) + 1)"
                >
                  +
                </button>

                <button
                  :disabled="servings === 1"
                  class="w-8 rounded border-2 border-gray-500 text-lg font-bold text-gray-600 hover:bg-gray-500 hover:text-white disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:bg-transparent disabled:hover:text-gray-600"
                  aria-label="Verminder aantal porties"
                  @click="updateServings(parseInt(servings) - 1)"
                >
                  -
                </button>
              </div>
            </div>

            <div class="space-y-6">
              <button
                v-if="strikedIngredients.size > 0"
                class="float-right text-sm"
                @click="() => strikedIngredients.clear()"
              >
                <FontAwesomeIcon :icon="faUndo" />
              </button>
              <div v-for="(list, list_index) in localRecipe.ingredients" :key="`${list.title}-${list_index}`">
                <h3 v-if="list.title" class="mb-2 mt-8 text-lg font-bold">{{ list.title }}</h3>

                <ul class="m-0 space-y-1">
                  <li
                    v-for="(ingredient, ingredient_index) in list.ingredients"
                    :key="`${ingredient.name}-${ingredient_index}`"
                    class="flex flex-auto"
                  >
                    <span
                      class="cursor-pointer"
                      :class="{ 'text-gray-600 line-through': strikedIngredients.has(ingredient) }"
                      @click="toggleStrike(ingredient)"
                    >
                      <template v-if="ingredient.amount">
                        <!-- The span is a trick to prevent extra whitspace between the amount and the following text. -->
                        <!-- Multiplying and deviding the amount is a trick to round to 2 decimal places. -->
                        <span>{{ Math.round(ingredient.amount * 100) / 100 }}&nbsp;</span>
                      </template>
                      <template v-if="ingredient.unit">{{ ingredient.unit }}&nbsp;</template>
                      <template v-if="ingredient.name_plural && ingredient.amount > 1">
                        {{ ingredient.name_plural }}
                      </template>
                      <template v-else>
                        {{ ingredient.name }}
                      </template>
                      <template v-if="ingredient.info">{{ ingredient.info }}</template>
                    </span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="space-y-4 sm:px-6 md:w-2/3 md:px-0">
            <h2 class="mb-4 text-xl font-bold md:mt-6 md:text-2xl">Instructies</h2>

            <div class="recipe-instructions" v-html="localRecipe.instructions" />

            <p v-if="localRecipe.source_label || localRecipe.source_link">
              <strong>Bron: </strong>
              <template v-if="localRecipe.source_link">
                <a :href="localRecipe.source_link" target="_blank">
                  {{ localRecipe.source_label ?? localRecipe.source_link }}
                </a>
              </template>
              <template v-if="!localRecipe.source_link && localRecipe.source_label">
                {{ localRecipe.source_label }}
              </template>
            </p>
          </div>
        </div>
      </div>
    </div>
  </DefaultLayout>
</template>
