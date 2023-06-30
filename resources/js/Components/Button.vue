<script setup>
import { Link } from "@inertiajs/vue3";

defineProps({
  href: {
    type: String,
    default: null,
  },
  type: {
    type: String,
    default: "button",
  },
  buttonStyle: {
    type: String,
  },
});
</script>

<template>
  <component
    :is="href ? Link : 'button'"
    :href="href ?? null"
    :type="href ? null : type"
    class="inline-flex items-center rounded-md border border-transparent px-4 py-2 font-semibold no-underline transition duration-150 ease-in-out focus:ring focus:ring-indigo-200 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-200 disabled:opacity-25"
    :class="{
      'bg-gray-800 hover:bg-gray-700 active:bg-gray-900': !buttonStyle,
      'bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700': buttonStyle === 'primary',
      'bg-green-600 hover:bg-green-500 active:bg-green-700': buttonStyle === 'success',
      'bg-red-600 hover:bg-red-500 active:bg-red-700': buttonStyle === 'danger',
      'bg-yellow-600 hover:bg-yellow-500 active:bg-yellow-700': buttonStyle === 'warning',
      'bg-gray-600 hover:bg-gray-500 active:bg-gray-700': buttonStyle === 'secondary',
      'bg-blue-600 hover:bg-blue-500 active:bg-blue-700': buttonStyle === 'info',
      'bg-white hover:bg-blue-500 active:bg-blue-700': buttonStyle === 'info',
      'text-white hover:text-white': buttonStyle !== 'ghost',
      'bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700': buttonStyle === 'ghost',
    }"
  >
    <slot />
  </component>
</template>
