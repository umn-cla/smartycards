<template>
  <div class="min-h-dvh bg-neutral-100 grid sm:grid-cols-2">
    <div class="bg-neutral-100 p-4">
      <h1>TestEditorPage</h1>
      <BlockEditor v-model="blocks" />
    </div>
    <div class="bg-neutral-200 font-mono p-4 overflow-x-scroll">
      <pre class="text-xs">{{ JSON.stringify(blocks, null, 2) }}</pre>
    </div>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue';
import { BlockEditor } from '@/components/BlockEditor';
import type { ContentBlock } from '@/types';

const blocks = ref<ContentBlock[]>([]);
</script>
<style scoped></style>
