<template>
  <QuillyEditor
    ref="editor"
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    :options="options"
    class="bg-brand-maroon-800/5 rounded-sm"
  />
</template>
<script setup lang="ts">
import { QuillyEditor } from "vue-quilly";
import Quill from "quill/quill"; // Core build
import { ref, onMounted, computed } from "vue";
import "quill-paste-smart";
import "quill/dist/quill.core.css";
import "quill/dist/quill.bubble.css";

defineProps<{
  modelValue: string;
}>();

defineEmits<{
  (event: "update:modelValue", value: string): void;
}>();

const editor = ref<InstanceType<typeof QuillyEditor>>();
let quill: Quill | null = null;

const options = computed(() => ({
  theme: "bubble",
  // bounds: editor.value ? editor.value.$el : null,
  modules: {
    toolbar: [
      [
        "bold",
        "italic",
        { list: "ordered" },
        { list: "bullet" },
        "link",
        "code-block",
        "formula",
      ],
      ["clean"],
    ],
    keyboard: {
      bindings: {
        // disable tab key
        tab: {
          key: "Tab",
          handler: () => true,
        },
        clearFormatting: {
          key: "\\",
          shortKey: true,
          handler(range, context) {
            if (!quill) {
              return;
            }
            quill.removeFormat(range.index, range.length, Quill.sources.USER);
          },
        },
      },
    },
  },
  placeholder: "Write something...",
  readOnly: false,
}));

onMounted(() => {
  if (!editor.value) {
    throw new Error("Editor element not found");
  }

  quill = editor.value.initialize(Quill);
});
</script>
<style scoped></style>
<style type="postcss">
.ql-editor {
  /* prevent clipping link tooltips in bubble theme */
  overflow: visible;
  @apply font-serif text-base leading-5;
}
</style>
