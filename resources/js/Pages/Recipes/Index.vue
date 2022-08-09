<script setup>
import DefaultLayout from "../../Layouts/Default.vue";

let props = defineProps({
    recipes: Object,
});
</script>

<template>
    <DefaultLayout>
        <ul v-for="recipe in recipes.data" :key="recipe.id">
            <li>{{ recipe.title }}</li>
        </ul>
    </DefaultLayout>
</template>
