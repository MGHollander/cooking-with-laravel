<script setup>
import {Link, useForm} from '@inertiajs/vue3';
import DefaultLayout from '@/Layouts/Default.vue';
import Button from '@/Components/Button.vue';
import Pagination from '@/Components/Pagination.vue';

let props = defineProps({
    recipes: Object,
    q: String,
});

const form = useForm({
    q: props.q ?? '',
});
</script>

<template>
    <DefaultLayout>
        <div class="space-y-6 sm:space-y-12 px-4 sm:px-0">
            <div class="flex relative w-full rounded-lg shadow-lg items-center">
                <img class="absolute inset-0 object-cover rounded-lg shadow-lg w-full h-full"
                     src="https://picsum.photos/id/999/1000"/>

                <form @submit.prevent="form.get(route('search'))"
                      class="px-4 py-8 sm:p-16 md:p-32 w-full flex relative">
                    <input
                        class="w-full p-2 sm:py-4 sm:px-6 bg-white border-2 border-gray-300 rounded-l-md text-sm md:text-base lg:text-lg"
                        placeholder="Waar heb je zin in?"
                        type="search"
                        v-model="form.q"
                    />
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="p-2 sm:py-4 sm:px-6 bg-emerald-700 border-2 border-emerald-700 border-l-0 rounded-r-md text-white text-sm md:text-base lg:text-lg"
                    >
                        Zoeken
                    </button>
                </form>
            </div>

            <h1 class="sr-only">Recepten</h1>

            <div class="grid grid-cols-12 gap-4 sm:gap-6 place-items-stretch">
                <Link
                    v-for="(recipe, index) in recipes.data"
                    :key="recipe.id"
                    :class="{
                        'md:col-span-6': index < 2,
                        'md:col-span-4': index >= 2,
                        'lg:col-span-3': index >= 5,
                    }"
                    :href="route('recipes.show', recipe.slug)"
                    class="recipe-card"
                >
                    <div v-if="recipe.image" class="recipe-card-image">
                        <img :src="recipe.image" :alt="`Afbeelding van '${recipe.title}'`"/>
                    </div>

                    <h2 class="recipe-card-title">{{ recipe.title }}</h2>
                </Link>
            </div>

            <div v-if="recipes.last_page > 1">
                <Pagination :links="recipes.links" class="flex flex-auto flex-wrap justify-center"/>
            </div>
        </div>
    </DefaultLayout>
</template>
