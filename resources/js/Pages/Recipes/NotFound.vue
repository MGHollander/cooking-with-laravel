<script setup>
import { Head } from "@inertiajs/vue3";
import { computed } from "vue";
import Pagination from "@/Components/Pagination.vue";
import RecipeCard from "@/Components/RecipeCard.vue";
import SearchBlock from "@/Components/SearchBlock.vue";
import DefaultLayout from "@/Layouts/Default.vue";

const props = defineProps({
  recipes: Object,
  q: String,
});

const recipeWord = computed(() =>
  props.recipes.total === 1 ? $t('recipes.not_found.recipe') : $t('recipes.not_found.recipes')
);
</script>

<template>
  <Head :title="$t('recipes.not_found.title')" />

  <DefaultLayout>
    <div class="px-4 sm:px-0">
      <h1 class="mb-4 text-center text-2xl font-bold">{{ $t('recipes.not_found.title') }}</h1>

      <template v-if="recipes.total === 0">
        <p class="text-center">{{ $t('recipes.not_found.description') }}</p>
        <SearchBlock size="small" class="mt-6 sm:mt-12" />
      </template>

      <template v-else>
        <p class="text-center">
          {{ $t('recipes.not_found.found', { count: recipes.total, recipe: recipeWord, words: q }) }}
        </p>

        <p class="text-center">{{ $t('recipes.not_found.hopefully') }}</p>

        <div class="mt-6 grid grid-cols-12 place-items-stretch gap-4 sm:mt-12 sm:gap-6">
          <RecipeCard v-for="recipe in recipes.data" :key="recipe.id" :recipe="recipe" />
        </div>

        <div v-if="recipes.last_page > 1" class="mt-6">
          <Pagination :links="recipes.links" class="flex flex-auto flex-wrap justify-center" />
        </div>
      </template>
    </div>
  </DefaultLayout>
</template>
