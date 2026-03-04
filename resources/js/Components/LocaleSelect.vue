<script setup>
import { computed } from "vue";

const props = defineProps({
  modelValue: {
    type: String,
    required: true,
  },
  languages: {
    type: Object,
    required: true,
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:modelValue"]);

const sortedLanguages = computed(() => {
  if (!props.languages) return [];

  const popular = ["en", "nl"];
  const popularLanguages = popular
    .map((code) => ({ code, name: props.languages[code] }))
    .filter((l) => l.name);
  const otherLanguages = Object.entries(props.languages)
    .filter(([code]) => !popular.includes(code))
    .map(([code, name]) => ({ code, name }))
    .sort((a, b) => a.name.localeCompare(b.name));

  return [...popularLanguages, ...otherLanguages];
});

function onChange(event) {
  emit("update:modelValue", event.target.value);
}
</script>

<template>
    <select
      :value="modelValue"
      :disabled="disabled"
      class="block w-full rounded-md border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
      @change="onChange"
    >
      <option v-for="lang in sortedLanguages" :key="lang.code" :value="lang.code">
        {{ lang.name }}
      </option>
    </select>
</template>
