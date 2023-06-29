<script setup>
import { Link } from "@inertiajs/vue3";

let props = defineProps({
  active: Boolean,
});
</script>

<template>
  <Link
    class="block w-full border-l-2 border-transparent px-4 py-2 text-left text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
    :class="{ ' border-indigo-400 bg-indigo-50 text-indigo-700': active }"
  >
    <slot />
  </Link>
</template>
