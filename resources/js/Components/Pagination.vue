<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array,
});
</script>

<template>
    <div>
        <Component
            :is="link.url ? Link : 'span'"
            v-for="(link, index) in links"
            :key="index"
            :href="link.url"
            v-html="link.label"
            class="p-2"
            :class="{ 'text-indigo-600 hover:text-indigo-900 no-underline hover:underline' : link.url, 'text-gray-500' : ! link.url, 'font-bold' : link.active }"
        />
    </div>
</template>
