<script setup>
import { Head } from "@inertiajs/vue3";

defineProps({
  title: {
    type: String,
  },
  url: {
    type: String,
  },
  image: {
    type: String,
  },
});
</script>

<template>
  <Head>
    <meta v-if="title" property="og:title" :content="title" />
    <meta v-if="url" property="og:url" :content="url" />
    <meta v-if="image" property="og:image" :content="image" />
  </Head>
</template>
