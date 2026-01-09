<script setup>
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const errors = computed(() => usePage().props.errors);

const hasErrors = computed(() => Object.keys(errors.value).length > 0);
</script>

<template>
  <div v-if="hasErrors" class="bg-red-100 text-red-700">
    <div class="font-medium text-red-600">{{ $t('app.validation_errors_title') }}</div>

    <ul class="mb-0 mt-3 list-inside list-disc text-sm text-red-600">
      <li v-for="(error, key) in errors" :key="key">{{ error }}</li>
    </ul>
  </div>
</template>
