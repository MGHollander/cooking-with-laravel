<script setup>
import { Link, useForm } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import Pagination from "@/Components/Pagination.vue";
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
      <div class="relative flex w-full items-center rounded-lg shadow-lg">
        <img
          class="absolute inset-0 h-full w-full rounded-lg object-cover shadow-lg"
          src="https://picsum.photos/id/999/1000"
        />

        <form class="relative flex w-full px-4 py-8 sm:p-16 md:p-32" @submit.prevent="form.get(route('search'))">
          <input
            v-model="form.q"
            class="w-full rounded-l-md border-2 border-gray-300 bg-white p-2 text-sm sm:px-6 sm:py-4 md:text-base lg:text-lg"
            placeholder="Waar heb je zin in?"
            type="search"
          />
          <button
            type="submit"
            :disabled="form.processing"
            class="rounded-r-md border-2 border-l-0 border-emerald-700 bg-emerald-700 p-2 text-sm text-white sm:px-6 sm:py-4 md:text-base lg:text-lg"
          >
            Zoeken
          </button>
        </form>
      </div>

      <h1 class="sr-only">Recepten</h1>

      <div class="grid grid-cols-12 place-items-stretch gap-4 sm:gap-6">
        <Link
          v-for="(recipe, index) in recipes.data"
          :key="recipe.id"
          :href="route('recipes.show', recipe.slug)"
          :class="{
            'md:col-span-6': index < 2,
            'md:col-span-4': index >= 2,
            'lg:col-span-3': index >= 5,
          }"
          class="recipe-card"
        >
          <div v-if="recipe.image" class="recipe-card-image">
            <img :src="recipe.image" :alt="`Afbeelding van '${recipe.title}'`" />
          </div>

          <h2 class="recipe-card-title">{{ recipe.title }}</h2>
        </Link>
      </div>

      <div v-if="recipes.last_page > 1">
        <Pagination :links="recipes.links" class="flex flex-auto flex-wrap justify-center" />
      </div>
    </div>
  </DefaultLayout>
</template>
