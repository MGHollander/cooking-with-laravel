<script setup>
import { useForm } from "@inertiajs/vue3";
import Pagination from "@/Components/Pagination.vue";
import RecipeCard from "@/Components/RecipeCard.vue";
import SearchBlock from "@/Components/SearchBlock.vue";
import DefaultLayout from "@/Layouts/Default.vue";

let props = defineProps({
  recipes: Object,
  q: String,
});

const form = useForm({
  q: props.q ?? "",
});
</script>

<template>
  <DefaultLayout>
    <div class="space-y-6 px-4 sm:space-y-12 sm:px-0">
      <SearchBlock />

      <h1 class="sr-only">Recepten</h1>

      <div class="grid grid-cols-12 place-items-stretch gap-4 sm:gap-6">
        <RecipeCard
          v-for="(recipe, index) in recipes.data"
          :key="recipe.id"
          :recipe="recipe"
          :class="{
            'md:col-span-6': index < 2,
            'md:col-span-4': index >= 2,
            'lg:col-span-3': index >= 5,
          }"
        />
      </div>

      <div v-if="recipes.last_page > 1">
        <Pagination :links="recipes.links" class="flex flex-auto flex-wrap justify-center" />
      </div>
    </div>
  </DefaultLayout>
</template>
