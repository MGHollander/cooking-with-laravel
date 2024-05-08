<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
  recipe: Object,
});
</script>
<template>
  <Link
    :key="recipe.id"
    :href="route('recipes.show', recipe.slug)"
    class="cursor-pointer rounded-lg bg-white no-underline shadow-lg hover:scale-105"
  >
    <div v-if="recipe.image" class="overflow-hidden rounded-t-lg">
      <img
        :src="recipe.image"
        :alt="`A picture of '${recipe.title}'`"
        class="h-40 w-full object-cover transition-all"
      />
    </div>

    <h2 class="mb-2 px-6 py-3 text-xl font-medium text-gray-900">{{ recipe.title }}</h2>
  </Link>
</template>
