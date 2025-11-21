<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
  recipe: Object,
});

const recipeUrl = computed(() => {
  if (props.recipe.locale === 'nl') {
    return route('recipes.show.nl', props.recipe.slug);
  }
  return route('recipes.show.en', props.recipe.slug);
});
</script>
<template>
  <Link :key="recipe.id" :href="recipeUrl" class="recipe-card md:col-span-4">
    <div v-if="recipe.image" class="recipe-card-image">
      <img :src="recipe.image" :alt="$t('recipes.card_image_alt', { title: recipe.title })" />
    </div>

    <h2 class="recipe-card-title">{{ recipe.title }}</h2>
  </Link>
</template>
