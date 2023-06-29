<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps(["href", "active"]);

// TODO Refactor classes. The duplicates can be defined in another way.
const classes = computed(() =>
  props.active
    ? "border-indigo-400 text-indigo-700 bg-indigo-50 focus:text-indigo-800 focus:bg-indigo-100 focus:border-indigo-700"
    : "border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300"
);
</script>

<template>
  <Link
    :href="href"
    :class="classes"
    class="block border-l-4 py-2 pl-3 pr-4 text-base font-medium no-underline transition duration-150 ease-in-out focus:outline-none"
  >
    <slot />
  </Link>
</template>
