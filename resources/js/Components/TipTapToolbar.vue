<script setup>
import TipTapToolbarButton from "@/Components/TipTapToolbarButton.vue";

const props = defineProps({
  toolbar: {
    type: Array,
    default: () => ["bold", "italic", "underline", "|", "bulletList", "orderedList"],
  },
  editor: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <div class="flex items-center gap-1 p-2 border-b border-gray-200 bg-gray-50 rounded-t-md">
    <template v-for="(item, index) in props.toolbar" :key="index">
      <!-- Separator -->
      <div v-if="item === '|'" class="w-px h-4 bg-gray-300 mx-1"></div>

      <!-- Toolbar Button -->
      <TipTapToolbarButton v-else :type="item" :editor="props.editor" />
    </template>
  </div>
</template>
