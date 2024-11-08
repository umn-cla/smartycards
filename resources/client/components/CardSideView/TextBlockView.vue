<template>
  <div class="text-block-view flex items-center justify-center text-lg">
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
    <button @click="tts.play">
      <IconCirclePlay class="w-6 h-6" />
      <span class="sr-only">Play audio</span>
    </button>
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { computed, ref } from "vue";
import { cn } from "@/lib/utils";
import IconCirclePlay from "../icons/IconCirclePlay.vue";
import { useTextToSpeech } from "@/composables/useTextToSpeech";
import { stripHtml } from "@/lib/stripHtml";

const props = defineProps<{
  block: T.TextContentBlock;
  class?: T.CSSClass;
}>();

const wordCount = computed(() => props.block.content.split(/\s+/).length);

const text = computed(() => stripHtml(props.block.content));
const lang = computed(() => props.block.meta?.lang as string);

const tts = useTextToSpeech(text, lang);
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
