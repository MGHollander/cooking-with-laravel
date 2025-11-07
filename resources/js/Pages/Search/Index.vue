<script setup>
import { Head, useForm } from "@inertiajs/vue3";
import { computed } from "vue";
import Pagination from "@/Components/Pagination.vue";
import RecipeCard from "@/Components/RecipeCard.vue";
import SearchBlock from "@/Components/SearchBlock.vue";
import DefaultLayout from "@/Layouts/Default.vue";

const props = defineProps({
  recipes: Object,
  q: String,
});

const form = useForm({
  q: props.q ?? "",
});

const recipeNounForm = computed(() =>
  props.recipes.total === 1 ? $t('search.recipe') : $t('search.recipes')
);

const title = computed(() => {
  if (props.q !== "" && props.recipes.total > 0) {
    return $t('search.results_with', { count: props.recipes.total, recipe: recipeNounForm.value, query: props.q });
  } else if (props.q && props.recipes.total === 0) {
    return `${$t('search.no_results')} '${props.q}'`;
  }
  return $t('search.title');
});
</script>

<template>
  <Head :title="title" />

  <DefaultLayout>
    <div class="space-y-6 px-4 sm:space-y-12 sm:px-0">
      <SearchBlock size="small" :q="q" />

      <div v-if="recipes.total === 0">
        <h1 class="mb-4 text-center text-2xl font-bold">
          {{ $t('search.no_results') }} <span class="italic text-emerald-700">{{ q }}</span>
        </h1>

        <p class="text-center">{{ $t('search.try_another') }}</p>
      </div>

      <template v-else>
        <h1 class="text-2xl font-bold">
          {{ recipes.total }} {{ recipeNounForm }}
          <template v-if="q">
            {{ $t('search.found_with') }} <span class="italic text-emerald-700">{{ q }}</span>
          </template>
        </h1>

        <div class="grid grid-cols-12 place-items-stretch gap-4 sm:gap-6">
          <RecipeCard v-for="recipe in recipes.data" :key="recipe.id" :recipe="recipe" />
        </div>

        <div v-if="recipes.last_page > 1">
          <Pagination :links="recipes.links" class="flex flex-auto flex-wrap justify-center" />
        </div>
      </template>
    </div>
  </DefaultLayout>
</template>
