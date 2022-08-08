<script setup>
import { Link } from '@inertiajs/inertia-vue3';

defineProps({
    href: {
        type: String,
        default: null,
    },
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <component
        :is="href ? Link : 'button'"
        :href="href ?? null"
        :type="href ? null : type"
        class="
            inline-flex items-center
            px-4 py-2
            bg-gray-800 hover:bg-gray-700 active:bg-gray-900
            border border-transparent rounded-md
            focus:ring focus:ring-indigo-200
            focus-visible:ring focus-visible:ring-indigo-200 focus-visible:outline-none
            font-semibold text-xs text-white
            transition ease-in-out duration-150
            disabled:opacity-25"
    >
        <slot />
    </component>
</template>
