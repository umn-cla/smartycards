<template>
  <div class="flex items-center justify-center">
    <div
      v-html="block.content"
      @click.stop
      ref="blockElRef"
      :class="
        cn(
          'text-block-view font-serif leading-5',
          {
            'text-center': wordCount < 5,
          },
          props.class,
        )
      "
    />
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { computed, ref } from "vue";
import { cn } from "@/lib/utils";

const props = defineProps<{
  block: T.TextContentBlock;
  class?: T.CSSClass;
}>();

const wordCount = computed(() => props.block.content.split(/\s+/).length);
</script>
<style></style>
