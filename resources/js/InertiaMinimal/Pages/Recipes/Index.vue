<script setup>
import Pagination from "@/InertiaMinimal/Components/Pagination.vue";
import RecipeCard from "@/InertiaMinimal/Components/RecipeCard.vue";
import DefaultLayout from "@/InertiaMinimal/Layouts/Default.vue";

let props = defineProps({
  recipes: Object,
});
</script>

<template>
  <DefaultLayout>
    <h1 class="mb-4 text-2xl font-bold md:text-3xl">Recipes</h1>

    <template v-if="recipes.data.length">
      <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <RecipeCard v-for="recipe in recipes.data" :key="recipe.id" :recipe="recipe" />
      </div>

      <div class="mt-8 flex justify-center">
        <Pagination :links="recipes.links" />
      </div>
    </template>
    <template v-else>
      <p>Je hebt nog geen recepten opgeslagen.</p>
    </template>
  </DefaultLayout>
</template>
