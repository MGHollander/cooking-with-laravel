<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
  recipe: Object,
});
</script>
<template>
  <Link :key="recipe.id" :href="route('recipes.show', recipe.slug)" class="recipe-card md:col-span-4">
    <div v-if="recipe.image" class="recipe-card-image">
      <img :src="recipe.image" :alt="`A picture of '${recipe.title}'`" />
    </div>

    <h2 class="recipe-card-title">{{ recipe.title }}</h2>
  </Link>
</template>
