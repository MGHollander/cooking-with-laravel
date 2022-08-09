<script setup>
import { Link } from '@inertiajs/inertia-vue3';

let props = defineProps({
    active: Boolean,
});
</script>

<template>
    <Link
        class="
            block w-full px-4 py-2
            text-left text-sm leading-5 text-gray-700
            border-l-2 border-transparent
            hover:bg-gray-100
            focus:outline-none focus:bg-gray-100
            transition duration-150 ease-in-out
        "
        :class="{' border-indigo-400 bg-indigo-50 text-indigo-700': active}"
    >
        <slot />
    </Link>
</template>
