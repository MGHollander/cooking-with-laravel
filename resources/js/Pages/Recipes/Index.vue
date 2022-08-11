<script setup>
import { Link } from '@inertiajs/inertia-vue3';
import DefaultLayout from '@/Layouts/Default.vue';
import Button from '@/Components/Button.vue';

let props = defineProps({
    recipes: Object,
});
</script>

<template>
    <DefaultLayout>
        <div class="space-y-6 sm:space-y-12 px-4 sm:px-0">
            <div class="flex relative w-full rounded-lg shadow-lg items-center">
                <img src="https://picsum.photos/id/999/1000" class="absolute inset-0 object-cover rounded-lg shadow-lg w-full h-full" />

                <div class="px-4 py-8 sm:p-16 md:p-32 w-full flex relative">
                    <input type="search" class="w-full p-2 sm:py-4 sm:px-6 bg-white border-2 border-gray-300 rounded-l-md text-sm md:text-base lg:text-lg" placeholder="What would you like to eat today?" />
                    <button class="p-2 sm:py-4 sm:px-6 bg-emerald-700 border-2 border-emerald-700 border-l-0 rounded-r-md text-white text-sm md:text-base lg:text-lg">Search</button>
                </div>
            </div>

            <div class="grid grid-cols-12 gap-4 sm:gap-6 place-items-stretch">
                <Link
                    :href="route('recipes.show', recipe.slug)"
                    v-for="(recipe, index) in recipes.data"
                    :key="recipe.id"
                    class="flex flex-col rounded-lg shadow-lg bg-white col-span-12 cursor-pointer"
                    :class="{
                        'md:col-span-6': index < 2,
                        'md:col-span-4': index >= 2,
                        'lg:col-span-3': index >= 5,
                    }"
                >
                    <img class="rounded-t-lg w-full h-40 object-cover" :src="recipe.image" alt=""/>

                    <h2 class="flex-1 text-gray-900 text-xl font-medium mb-2 pt-6 px-6">{{ recipe.title }}</h2>

                    <Button class="mx-6 mb-6 bg-emerald-700 self-start">See recipe</Button>
                </Link>
            </div>
        </div>
    </DefaultLayout>
</template>
