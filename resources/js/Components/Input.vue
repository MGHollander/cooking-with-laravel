<script setup>
import { onMounted, ref } from 'vue';

defineProps(['modelValue']);

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});
</script>

<template>
    <input
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
        class="
            border-gray-300 rounded-md
            focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
            shadow-sm
            transition ease-in-out duration-150
        "
    >
</template>
