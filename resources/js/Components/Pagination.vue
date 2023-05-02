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
            :class="{ 'text-emerald-600 hover:text-emerald-900 no-underline hover:underline transition-all' : link.url, 'text-gray-500' : ! link.url, 'font-bold' : link.active }"
            :href="link.url"
            class="p-2"
            v-html="link.label"
        />
    </div>
</template>
