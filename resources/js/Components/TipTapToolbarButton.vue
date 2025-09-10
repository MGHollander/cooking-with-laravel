<script setup>
const props = defineProps({
  type: {
    type: String,
    required: true,
  },
  editor: {
    type: Object,
    required: true,
  },
});

const isActive = (name, attributes = {}) => {
  return props.editor ? props.editor.isActive(name, attributes) : false;
};

const runCommand = (command) => {
  if (props.editor) {
    command(props.editor.chain().focus());
  }
};

const getButtonConfig = (type) => {
  const configs = {
    bold: {
      action: (chain) => chain.toggleBold().run(),
      isActive: () => isActive("bold"),
      title: "Bold",
      content: "B",
      contentClass: "font-bold",
    },
    italic: {
      action: (chain) => chain.toggleItalic().run(),
      isActive: () => isActive("italic"),
      title: "Italic",
      content: "I",
      contentClass: "italic",
    },
    underline: {
      action: (chain) => chain.toggleUnderline().run(),
      isActive: () => isActive("underline"),
      title: "Underline",
      content: "U",
      contentClass: "underline",
    },
    heading: {
      action: (chain) => chain.toggleHeading({ level: 3 }).run(),
      isActive: () => isActive("heading", { level: 3 }),
      title: "Heading 3",
      content: "H3",
      contentClass: "",
    },
    bulletList: {
      action: (chain) => chain.toggleBulletList().run(),
      isActive: () => isActive("bulletList"),
      title: "Bullet List",
      content: "•",
      contentClass: "",
    },
    orderedList: {
      action: (chain) => chain.toggleOrderedList().run(),
      isActive: () => isActive("orderedList"),
      title: "Numbered List",
      content: "1.",
      contentClass: "",
    },
  };

  return configs[type] || null;
};

const config = getButtonConfig(props.type);

const handleClick = () => {
  if (config) {
    runCommand(config.action);
  }
};
</script>

<template>
  <button
    v-if="config"
    type="button"
    @click="handleClick"
    :class="{
      'bg-gray-200': config.isActive(),
      'hover:bg-gray-200': !config.isActive(),
    }"
    class="px-2 py-1 text-sm font-medium rounded transition-colors duration-200"
    :title="config.title"
  >
    <span :class="config.contentClass">{{ config.content }}</span>
  </button>
</template>
