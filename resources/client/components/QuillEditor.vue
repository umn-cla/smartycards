<template>
  <div class="">
    <QuillyEditor
      ref="editor"
      v-model="model"
      :options="options"
      class="bg-black/5 rounded-lg"
    />
  </div>
</template>
<script setup lang="ts">
import * as T from '@/types';
import { ref, onMounted } from 'vue';
import Quill from 'quill'; // Full build
import { QuillyEditor } from 'vue-quilly';

import 'quill/dist/quill.core.css'; // Required
// import 'quill/dist/quill.snow.css'; // For snow theme (optional)
import 'quill/dist/quill.bubble.css'; // For bubble theme (optional)

const model = defineModel<T.CardSide['content']>({ required: true });

const props = withDefaults(
  defineProps<{
    placeholder?: string;
  }>(),
  {
    placeholder: '',
  }
);

const options = {
  theme: 'bubble', // If you need Quill theme
  modules: {
    toolbar: [
      ['bold', 'italic'],
      ['link', 'formula'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      ['clean'], // remove formatting buttonp
    ],
  },
  placeholder: props.placeholder,
  readOnly: false,
};

const editor = ref<InstanceType<typeof QuillyEditor>>();

// Quill instance
let quill: Quill | null = null;

onMounted(() => {
  quill = editor.value?.initialize(Quill)!;
});
</script>
<style scoped></style>
