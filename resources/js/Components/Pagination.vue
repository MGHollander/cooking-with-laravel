<script setup>
import { Link } from '@inertiajs/inertia-vue3';

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
            :class="{ 'text-gray-500' : ! link.url, 'font-bold' : link.active }"
        />
    </div>
</template>
