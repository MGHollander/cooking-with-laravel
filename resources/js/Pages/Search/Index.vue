<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
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
      <div class="relative flex w-full items-center rounded-lg shadow-lg">
        <img
          class="absolute inset-0 h-full w-full rounded-lg object-cover shadow-lg"
          src="https://picsum.photos/id/999/1000"
        />

        <form class="relative flex w-full p-4 sm:px-16 sm:py-8 md:px-32" @submit.prevent="form.get(route('search'))">
          <input
            v-model="form.q"
            class="w-full rounded-l-md border-2 border-gray-300 bg-white p-2 text-sm sm:px-6 sm:py-4 md:text-base lg:text-lg"
            placeholder="What would you like to eat today?"
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

      <div v-if="recipes.total === 0">
        <h1 class="mb-4 text-center text-2xl font-bold">
          Geen recepten gevonden met <span class="italic text-emerald-700">{{ q }}</span>
        </h1>

        <p class="text-center">Probeer een ander zoekwoord.</p>
      </div>

      <template v-if="recipes.total > 0">
        <h1 class="text-2xl font-bold">
          {{ recipes.total }} {{ recipeNounForm }}
          <template v-if="q">
            met <span class="italic text-emerald-700">{{ q }}</span>
          </template>
        </h1>

        <div class="grid grid-cols-12 place-items-stretch gap-4 sm:gap-6">
          <Link
            v-for="(recipe, index) in recipes.data"
            :key="recipe.id"
            :href="route('recipes.show', recipe.slug)"
            class="recipe-card md:col-span-4"
          >
            <div v-if="recipe.image" class="recipe-card-image">
              <img :src="recipe.image" alt="A picture of '{{ recipe.title }}'" />
            </div>

            <h2 class="recipe-card-title">{{ recipe.title }}</h2>
          </Link>
        </div>

        <div v-if="recipes.last_page > 1">
          <Pagination :links="recipes.links" class="flex flex-auto flex-wrap justify-center" />
        </div>
      </template>
    </div>
  </DefaultLayout>
</template>
