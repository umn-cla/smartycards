<template>
  <div class="text-block-view flex items-center justify-center">
    <div
      v-html="block.content"
      @click.stop
      ref="blockElRef"
      :class="
        cn(
          'font-serif leading-5',
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
<style type="post-css">
/**
 * Style fixes for lists:
 * 1. quill makes all lists `ol and adds a `data-list` attr depending
 * 2. also, tailwind strips styles
 */
.text-block-view :is(ol, ul) {
  padding-left: 1.5em;
}

.text-block-view [data-list="bullet"] {
  list-style-type: disc;
}

.text-block-view [data-list="ordered"] {
  list-style-type: decimal;
}

.text-block-view a {
  @apply text-brand-teal-500 underline;
}
</style>
