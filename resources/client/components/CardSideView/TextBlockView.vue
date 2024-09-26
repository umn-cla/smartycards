<template>
  <div>
    <div
      v-html="block.content"
      @click.stop
      ref="blockElRef"
      class="text-block-view"
      :class="{
        'text-center ': wordCount < 5,
      }"
    />
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { computed, ref } from "vue";
const props = defineProps<{
  block: T.TextContentBlock;
}>();

const wordCount = computed(() => props.block.content.split(/\s+/).length);
</script>
<style scoped></style>
