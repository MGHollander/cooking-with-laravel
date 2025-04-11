<script setup>
import { onMounted, ref } from "vue";

defineProps({ modelValue: String });
defineEmits(["update:modelValue"]);

const input = ref(null);

onMounted(() => {
  if (input.value.hasAttribute("autofocus")) {
    input.value.focus();
  }
});
</script>

<template>
  <textarea
    ref="input"
    :value="modelValue"
    class="rounded-md border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
    @input="$emit('update:modelValue', $event.target.value)"
  />
</template>
