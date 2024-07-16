<template>
  <div>
    <div>
      <Label :for="id">Text</Label>
    </div>
    <QuillEditor
      :id="id"
      :modelValue="modelValue"
      @update:modelValue="(value) => $emit('update:modelValue', value)"
      placeholder="Add your text..."
    />
    <small class="text-neutral-400 block my-1"> Highlight text to format.</small>
  </div>
</template>
<script setup lang="ts">
import * as T from '@/types';
import QuillEditor from '@/components/QuillEditor.vue';
import { Label } from '../ui/label';

defineProps<{
  id: string;
  modelValue: T.CardSide['content'];
}>();

defineEmits<{
  (event: 'update:modelValue', value: T.CardSide['content']): void;
}>();
</script>
<style>
.ql-editor {
  min-height: 5rem;
}
</style>
