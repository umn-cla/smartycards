<template>
  <QuillyEditor
    ref="editor"
    :modelValue="modelValue"
    @update:modelValue="$emit('update:modelValue', $event)"
    :options="options"
    class="bg-black/5 rounded-lg"
  />
</template>
<script setup lang="ts">
import { QuillyEditor } from 'vue-quilly';
import Quill from 'quill/quill'; // Core build
import { ref, onMounted } from 'vue';
import 'quill/dist/quill.core.css';
import 'quill/dist/quill.bubble.css';

defineProps<{
  modelValue: string;
}>();

defineEmits<{
  (event: 'update:modelValue', value: string): void;
}>();

const options = {
  theme: 'bubble',
  modules: {
    toolbar: [
      ['bold', 'italic', { list: 'ordered' }, { list: 'bullet' }, 'link', 'code-block'],
      ['clean'],
    ],
    keyboard: {
      bindings: {
        // disable tab key
        tab: {
          key: 'Tab',
          handler: () => true,
        },
      },
    },
  },
  placeholder: 'Write something...',
  readOnly: false,
};

const editor = ref<InstanceType<typeof QuillyEditor>>();

onMounted(() => {
  if (!editor.value) {
    throw new Error('Editor element not found');
  }

  const quill = editor.value.initialize(Quill);
});
</script>
<style scoped></style>
