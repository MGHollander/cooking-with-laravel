<script setup>
import { useEditor, EditorContent } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import Underline from "@tiptap/extension-underline";
import ListItem from "@tiptap/extension-list-item";
import { watch, ref, onMounted, onBeforeUnmount } from "vue";
import TipTapToolbar from "@/Components/TipTapToolbar.vue";

const props = defineProps({
  modelValue: {
    type: String,
    default: "",
  },
  placeholder: {
    type: String,
    default: "Type here...",
  },
  rows: {
    type: Number,
    default: 4,
  },
  toolbar: {
    type: Array,
    default: () => ["bold", "italic", "underline", "|", "bulletList", "orderedList"],
  },
});

const emit = defineEmits(["update:modelValue"]);

const editorInitialized = ref(false);
const editorError = ref(false);
const fallbackTextarea = ref(null);

const getExtensions = () => {
  const hasHeading = props.toolbar.includes("heading");
  const hasBulletList = props.toolbar.includes("bulletList");
  const hasOrderedList = props.toolbar.includes("orderedList");
  const hasBold = props.toolbar.includes("bold");
  const hasItalic = props.toolbar.includes("italic");
  const hasUnderline = props.toolbar.includes("underline");

  const extensions = [
    StarterKit.configure({
      heading: hasHeading ? { levels: [3] } : false,
      horizontalRule: false,
      blockquote: false,
      bulletList: hasBulletList,
      orderedList: hasOrderedList,
      bold: hasBold,
      italic: hasItalic,
    }),
  ];

  if (hasUnderline) {
    extensions.push(Underline);
  }

  if (hasBulletList || hasOrderedList) {
    extensions.push(
      ListItem.configure({
        HTMLAttributes: {
          class: "list-item",
        },
      }),
    );
  }

  return extensions;
};

const editor = useEditor({
  content: props.modelValue,
  extensions: getExtensions(),
  editorProps: {
    attributes: {
      class: "prose prose-sm sm:prose lg:prose-lg xl:prose-2xl mx-auto focus:outline-none min-h-[100px] px-3 py-2",
    },
  },
  onTransaction: () => {
    // Force re-render so `editor.isActive` works as expected
  },
  onCreate: ({ editor }) => {
    editorInitialized.value = true;
  },
  onUpdate: ({ editor }) => {
    const html = editor.getHTML();
    emit("update:modelValue", html);
  },
  onDestroy: () => {
    editorInitialized.value = false;
  },
});

// Watch for prop changes
watch(
  () => props.modelValue,
  (newValue) => {
    if (editor.value && editor.value.getHTML() !== newValue) {
      editor.value.commands.setContent(newValue, false);
    }
  },
);

// Error handling
onMounted(() => {
  setTimeout(() => {
    if (!editorInitialized.value) {
      editorError.value = true;
      console.warn("TipTap editor failed to initialize, falling back to textarea");
    }
  }, 1000); // Give editor 1 second to initialize
});

onBeforeUnmount(() => {
  if (editor.value) {
    editor.value.destroy();
  }
});

// Fallback textarea handlers
const handleTextareaInput = (event) => {
  emit("update:modelValue", event.target.value);
};
</script>

<template>
  <div>
    <!-- TipTap Editor -->
    <div
      v-if="!editorError && editor"
      class="border border-gray-300 rounded-md shadow-sm focus-within:border-indigo-300 focus-within:ring focus-within:ring-indigo-200 focus-within:ring-opacity-50"
    >
      <!-- Toolbar -->
      <TipTapToolbar :toolbar="props.toolbar" :editor="editor" />

      <!-- Editor Content -->
      <EditorContent :editor="editor" class="min-h-[100px] max-h-[400px] overflow-y-auto" />
    </div>

    <!-- Fallback Textarea -->
    <textarea
      v-else
      ref="fallbackTextarea"
      :value="modelValue"
      :placeholder="placeholder"
      :rows="rows"
      class="block w-full rounded-md border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
      @input="handleTextareaInput"
    />

    <!-- Error message -->
    <p v-if="editorError" class="mt-1 text-xs text-yellow-600">
      Rich text editor niet beschikbaar, gebruik van gewone tekstbox.
    </p>
  </div>
</template>
