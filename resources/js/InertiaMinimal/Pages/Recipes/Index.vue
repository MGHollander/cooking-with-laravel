<script setup>
import debounce from "lodash/debounce";
import { ref, watch } from "vue";
import { Head, Link, router } from "@inertiajs/vue3";
import Pagination from "@/InertiaMinimal/Components/Pagination.vue";
import RecipeCard from "@/InertiaMinimal/Components/RecipeCard.vue";
import DefaultLayout from "@/InertiaMinimal/Layouts/Default.vue";

const search = ref(route().params.search ?? "");
let props = defineProps({
  recipes: Object,
});

watch(
  search,
  debounce((value) => {
    router.get(route("inertia-minimal.home", { search: value }), {}, { preserveState: true });
  }, 500),
);
</script>

<template>
  <Head title="lala">
    <link rel="canonical" :href="route('home')" />
  </Head>

  <DefaultLayout>
    <div class="mb-4 flex items-center justify-between">
      <h1 class="text-2xl font-bold md:text-3xl">
        <template v-if="search && recipes.total > 0"> Found {{ recipes.total }} recipes for "{{ search }}"</template>
        <template v-else-if="search && recipes.data.length === 0">No recipes found</template>
        <template v-else>Recipes</template>
      </h1>
      <form class="flex items-center space-x-2">
        <label>Search:</label>
        <input type="search" v-model="search" class="rounded border border-slate-300 px-3 py-2" />
      </form>
    </div>

    <template v-if="recipes.data.length">
      <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <RecipeCard v-for="recipe in recipes.data" :key="recipe.id" :recipe="recipe" />
      </div>

      <div v-if="recipes.last_page > 1" class="mt-8 flex justify-center">
        <Pagination :links="recipes.links" />
      </div>
    </template>
    <template v-else-if="search && recipes.data.length === 0">
      <p>No recipes found for "{{ search }}".</p>
    </template>
    <template v-else>
      <p>No recipes found.</p>
    </template>
  </DefaultLayout>
</template>
