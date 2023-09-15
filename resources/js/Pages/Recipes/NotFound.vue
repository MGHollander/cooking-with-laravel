<script setup>
import { Head } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import RecipeCard from "@/Components/RecipeCard.vue";
import SearchBlock from "@/Components/SearchBlock.vue";
import DefaultLayout from "@/Layouts/Default.vue";

defineProps({
  recipes: Object,
  q: String,
});
</script>

<template>
  <Head title="Pagina niet gevonden" />

  <DefaultLayout>
    <div class="px-4 sm:px-0">
      <h1 class="mb-4 text-center text-2xl font-bold">Helaas, dit recept bestaat (nog) niet!</h1>

      <template v-if="recipes.total === 0">
        <p class="text-center">Je kan het onderstaande formulier gebruiken om door alle recepten te zoeken.</p>
        <SearchBlock size="small" class="mt-6 sm:mt-12" />
      </template>

      <template v-else>
        <p class="text-center">
          Met behulp van de woorden uit de link heb ik <strong>{{ recipes.total }} recepten</strong> gevonden die
          mogelijk toch interessant zijn. Hiervoor heb ik de volgende woorden gebruikt: <strong>{{ q }}</strong>
        </p>

        <p class="text-center">Hopelijk zit er iets tussen!</p>

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
