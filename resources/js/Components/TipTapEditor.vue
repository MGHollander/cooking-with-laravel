<script setup>
import { useEditor, EditorContent } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import Underline from "@tiptap/extension-underline";
import ListItem from "@tiptap/extension-list-item";
import { watch, ref, onMounted, onBeforeUnmount } from "vue";

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
    type: Object,
    default: () => ({
      bold: true,
      italic: true,
      underline: true,
      bulletList: true,
      orderedList: true,
    }),
  },
});

const emit = defineEmits(["update:modelValue"]);

const editorInitialized = ref(false);
const editorError = ref(false);
const fallbackTextarea = ref(null);

const getExtensions = () => {
  const extensions = [
    StarterKit.configure({
      heading: false,
      horizontalRule: false,
      blockquote: false,
      bulletList: props.toolbar.bulletList,
      orderedList: props.toolbar.orderedList,
      bold: props.toolbar.bold,
      italic: props.toolbar.italic,
    }),
  ];

  if (props.toolbar.underline) {
    extensions.push(Underline);
  }

  if (props.toolbar.bulletList || props.toolbar.orderedList) {
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

const isActive = (name, attributes = {}) => {
  return editor.value ? editor.value.isActive(name, attributes) : false;
};

const runCommand = (command) => {
  if (editor.value) {
    command(editor.value.chain().focus());
  }
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
      <div class="flex items-center gap-1 p-2 border-b border-gray-200 bg-gray-50 rounded-t-md">
        <template v-if="props.toolbar.bold">
          <button
            type="button"
            @click="runCommand((chain) => chain.toggleBold().run())"
            :class="{
              'bg-gray-200': isActive('bold'),
              'hover:bg-gray-200': !isActive('bold'),
            }"
            class="px-2 py-1 text-sm font-medium rounded transition-colors duration-200"
            title="Bold"
          >
            <strong>B</strong>
          </button>
        </template>

        <template v-if="props.toolbar.italic">
          <button
            type="button"
            @click="runCommand((chain) => chain.toggleItalic().run())"
            :class="{
              'bg-gray-200': isActive('italic'),
              'hover:bg-gray-200': !isActive('italic'),
            }"
            class="px-2 py-1 text-sm font-medium rounded transition-colors duration-200"
            title="Italic"
          >
            <em>I</em>
          </button>
        </template>

        <template v-if="props.toolbar.underline">
          <button
            type="button"
            @click="runCommand((chain) => chain.toggleUnderline().run())"
            :class="{
              'bg-gray-200': isActive('underline'),
              'hover:bg-gray-200': !isActive('underline'),
            }"
            class="px-2 py-1 text-sm font-medium rounded transition-colors duration-200"
            title="Underline"
          >
            <span style="text-decoration: underline">U</span>
          </button>
        </template>

        <template v-if="props.toolbar.bulletList || props.toolbar.orderedList">
          <div class="w-px h-4 bg-gray-300 mx-1"></div>
        </template>

        <template v-if="props.toolbar.bulletList">
          <button
            type="button"
            @click="runCommand((chain) => chain.toggleBulletList().run())"
            :class="{
              'bg-gray-200': isActive('bulletList'),
              'hover:bg-gray-200': !isActive('bulletList'),
            }"
            class="px-2 py-1 text-sm font-medium rounded transition-colors duration-200"
            title="Bullet List"
          >
            •
          </button>
        </template>

        <template v-if="props.toolbar.orderedList">
          <button
            type="button"
            @click="runCommand((chain) => chain.toggleOrderedList().run())"
            :class="{
              'bg-gray-200': isActive('orderedList'),
              'hover:bg-gray-200': !isActive('orderedList'),
            }"
            class="px-2 py-1 text-sm font-medium rounded transition-colors duration-200"
            title="Numbered List"
          >
            1.
          </button>
        </template>
      </div>

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
