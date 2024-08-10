<template>
  <div class="flex overflow-clip border-b border-white/75">
    <button class="cursor-move bg-black/5 flex items-start px-1 py-3">
      <Icons.IconDragHandle class="size-4" />
      <span class="sr-only">Drag to reorder</span>
    </button>
    <component
      :is="getComponentForType(block.type)"
      class="flex-grow"
      :modelValue="block.content"
      @update:modelValue="handleUpdateBlockContent(block.id, $event)"
      :meta="block.meta"
      @update:meta="handleUpdateBlockMeta(block.id, $event)"
    />
    <button
      class="cursor-pointer flex items-start px-2 py-3"
      @click="removeBlock(block.id)"
    >
      <Icons.IconX class="size-4" />
      <span class="sr-only">Remove block</span>
    </button>
  </div>
</template>
<script setup lang="ts">
import * as Icons from '@/components/icons';
import * as T from '@/types';

defineProps<{
  block: T.ContentBlock;
}>();
</script>
<style scoped></style>
