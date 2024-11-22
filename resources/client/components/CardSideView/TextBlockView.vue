<template>
  <div
    class="text-block-view flex flex-col items-center justify-center text-lg gap-1"
  >
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
    <div
      class="flex items-center justify-center"
      v-if="featureFlags?.text_to_speech"
    >
      <SimpleTTSPlayer
        :text="block.content"
        :selectedLanguage="block.meta?.lang ?? null"
        v-if="charCount < MAX_TTS_CHARS"
      />
    </div>
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { computed } from "vue";
import { cn } from "@/lib/utils";
import SimpleTTSPlayer from "@/components/SimpleTTSPlayer.vue";
import { MAX_TTS_CHARS } from "@/constants";
import { useAllFeatureFlagsQuery } from "@/queries/featureFlags";

const props = defineProps<{
  block: T.TextContentBlock;
  class?: T.CSSClass;
}>();

const wordCount = computed(() => props.block.content.split(/\s+/).length);
const charCount = computed(() => props.block.content.length);

const { data: featureFlags } = useAllFeatureFlagsQuery();
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
