<template>
  <div class="">
    <p>Unknown block type: {{ block.type }}</p>
    <pre>{{ block.content }}</pre>
  </div>
</template>
<script setup lang="ts">
import { ContentBlock } from "@/types";

defineProps<{
  block: ContentBlock;
}>();
</script>
<style scoped></style>
