<script setup>
import { Head, useForm } from "@inertiajs/vue3";
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

const recipeNounForm = props.recipes.total === 1 ? "recept" : "recepten";
const title =
  props.q !== "" && props.recipes.total > 0
    ? `${props.recipes.total} ${recipeNounForm} met '${props.q}'`
    : props.q && props.recipes.total === 0
    ? `Geen recepten met '${props.q}'`
    : "Zoek een recept";
</script>

<template>
  <Head :title="title" />

  <DefaultLayout>
    <div class="space-y-6 px-4 sm:space-y-12 sm:px-0">
      <SearchBlock size="small" />

      <div v-if="recipes.total === 0">
        <h1 class="mb-4 text-center text-2xl font-bold">
          Geen recepten gevonden met <span class="italic text-emerald-700">{{ q }}</span>
        </h1>

        <p class="text-center">Probeer een ander zoekwoord.</p>
      </div>

      <template v-else>
        <h1 class="text-2xl font-bold">
          {{ recipes.total }} {{ recipeNounForm }}
          <template v-if="q">
            met <span class="italic text-emerald-700">{{ q }}</span>
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
