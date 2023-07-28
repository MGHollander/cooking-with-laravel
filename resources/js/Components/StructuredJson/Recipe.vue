<script setup>
import { Head } from "@inertiajs/vue3";
import dayjs from "dayjs";
import duration from "dayjs/plugin/duration";

dayjs.extend(duration);

const props = defineProps({ recipe: Object });

const recipeData = {
  "@context": "https://schema.org/",
  "@type": "Recipe",
  name: props.recipe.title,
  datePublished: props.recipe.created_at,
  recipeYield: props.recipe.servings,
  recipeIngredient: props.recipe.structured_data.ingredients,
  recipeInstructions: props.recipe.structured_data.instructions,
};

if (props.recipe.image) {
  recipeData.image = [props.recipe.image];
}

if (props.recipe.structured_data.description) {
  // eslint-disable-next-line vue/no-setup-props-destructure
  recipeData.description = props.recipe.structured_data.description;
}

if (props.recipe.preparation_minutes && props.recipe.cooking_minutes) {
  recipeData.prepTime = transformMinutes(props.recipe.preparation_minutes);
  recipeData.cookTime = transformMinutes(props.recipe.cooking_minutes);
}

if (props.recipe.preparation_minutes || props.recipe.cooking_minutes) {
  recipeData.totalTime = transformMinutes(
    (props.recipe.preparation_minutes ?? 0) + (props.recipe.cooking_minutes ?? 0)
  );
}

if (props.recipe.structured_data.keywords) {
  // eslint-disable-next-line vue/no-setup-props-destructure
  recipeData.keywords = props.recipe.structured_data.keywords;
}

function transformMinutes(minutes) {
  let duration = { minutes: minutes % 60 };
  if (minutes > 59) {
    duration.hours = Math.floor(minutes / 60);
  }
  return dayjs.duration(duration).toISOString();
}
</script>

<template>
  <Head>
    <component :is="'script'" type="application/ld+json">
      {{ JSON.stringify(recipeData) }}
    </component>
  </Head>
</template>
