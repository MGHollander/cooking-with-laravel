<script setup>
import { Link } from "@inertiajs/vue3";

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
      :class="{
        'text-emerald-600 no-underline transition-all hover:text-emerald-900 hover:underline': link.url,
        'text-gray-500': !link.url,
        'font-bold': link.active,
      }"
      :href="link.url"
      class="p-2"
      v-html="link.label"
    />
  </div>
</template>
