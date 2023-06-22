<script setup>
import {Head, Link, useForm} from '@inertiajs/vue3';
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

const recipeNounForm = (props.recipes.total === 1) ? 'recept' : 'recepten';
const title = (props.q !== '' && props.recipes.total > 0) ?
    `${props.recipes.total} ${recipeNounForm} met '${props.q}'`
    : ((props.q && props.recipes.total === 0) ? `Geen recepten met '${props.q}'` : 'Zoek een recept');
</script>

<template>
    <Head :title="title"/>

    <DefaultLayout>
        <div class="space-y-6 sm:space-y-12 px-4 sm:px-0">
            <div class="flex relative w-full rounded-lg shadow-lg items-center">
                <img class="absolute inset-0 object-cover rounded-lg shadow-lg w-full h-full"
                     src="https://picsum.photos/id/999/1000"/>

                <form @submit.prevent="form.get(route('search'))"
                      class="p-4 sm:px-16 sm:py-8 md:px-32 w-full flex relative">
                    <input
                        class="w-full p-2 sm:py-4 sm:px-6 bg-white border-2 border-gray-300 rounded-l-md text-sm md:text-base lg:text-lg"
                        placeholder="What would you like to eat today?"
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

            <div v-if="recipes.total === 0">
                <h1 class="text-2xl  font-bold text-center mb-4">
                    Geen recepten gevonden met <span class="text-emerald-700 italic">{{ q }}</span>
                </h1>

                <p class="text-center">Probeer een ander zoekwoord.</p>
            </div>

            <template v-if="recipes.total > 0">
                <h1 class="text-2xl font-bold">
                    {{ recipes.total }} {{ recipeNounForm }}
                    <template v-if="q">
                        met <span class="text-emerald-700 italic">{{ q }}</span>
                    </template>
                </h1>

                <div class="grid grid-cols-12 gap-4 sm:gap-6 place-items-stretch">
                    <Link
                        v-for="(recipe, index) in recipes.data"
                        :key="recipe.id"
                        :href="route('recipes.show', recipe.slug)"
                        class="recipe-card md:col-span-4"
                    >
                        <div v-if="recipe.image" class="recipe-card-image">
                            <img :src="recipe.image" alt="A picture of '{{ recipe.title }}'"/>
                        </div>

                        <h2 class="recipe-card-title">{{ recipe.title }}</h2>
                    </Link>
                </div>

                <div v-if="recipes.last_page > 1">
                    <Pagination :links="recipes.links" class="flex flex-auto flex-wrap justify-center"/>
                </div>
            </template>
        </div>
    </DefaultLayout>
</template>
