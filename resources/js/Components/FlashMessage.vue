<script setup>
const props = defineProps({
  type: {
    type: String,
    required: true,
    validator: (value) => ['error', 'success', 'warning'].includes(value)
  },
  message: {
    type: String,
    required: true
  },
  fullWidth: {
    type: Boolean,
    default: false
  }
});
</script>

<template>
  <div
    class="flex justify-between rounded-lg py-4"
    :class="{
      'bg-red-100 text-red-700': type === 'error',
      'bg-emerald-100 text-emerald-800': type === 'success',
      'bg-yellow-100 text-yellow-700': type === 'warning',
    }"
  >
    <div class="px-4" :class="{ 'mx-auto w-full max-w-7xl sm:px-6 lg:px-8': fullWidth }">
      <p v-html="message" />
    </div>
  </div>
</template>
