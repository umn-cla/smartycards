<template>
  <div
    class="rounded-lg p-2 flex flex-col gap-4 border border-black/20"
    :data-cy="`card-side-view--${label}`"
  >
    <h2 class="text-xs uppercase text-center text-black/30" v-if="showLabel">
      {{ props.label }}
    </h2>
    <slot name="prepend" />
    <div
      class="flex flex-col gap-4 my-auto overflow-y-auto scrollbar-thin scrollbar-thumb-black/10 scrollbar-track-transparent py-1"
    >
      <CardSideContextProvider :deck="deck ?? null" :cardSideName="sideName">
        <template v-for="block in contentBlocks" :key="block.id">
          <TextBlockView v-if="isTextBlock(block)" :block="block" />
          <ImageBlockView
            v-else-if="isImageBlock(block)"
            :src="block.content"
            :alt="block.meta?.alt ?? ''"
          />
          <VideoBlockView v-else-if="isVideoBlock(block)" :block="block" />
          <EmbedBlockView v-else-if="isEmbedBlock(block)" :block="block" />
          <AudioBlockView v-else-if="isAudioBlock(block)" :block="block" />
          <HintBlockView
            v-else-if="isHintBlock(block)"
            :modelValue="block.content"
            :meta="block.meta"
          />
          <MathBlockView
            v-else-if="isMathBlock(block)"
            :block="block"
            class="mx-auto"
          />
          <UnknownBlockView v-else :block="block" />
        </template>
      </CardSideContextProvider>
    </div>
    <slot name="append" />
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { computed } from "vue";
import HintBlockView from "./HintBlockView.vue";
import ImageBlockView from "@/components/CardSideView/ImageBlockView.vue";
import TextBlockView from "./TextBlockView.vue";
import VideoBlockView from "./VideoBlockView.vue";
import MathBlockView from "./MathBlockView.vue";
import EmbedBlockView from "./EmbedBlockView.vue";
import AudioBlockView from "./AudioBlockView.vue";
import {
  isImageBlock,
  isTextBlock,
  isVideoBlock,
  isAudioBlock,
  isEmbedBlock,
  isMathBlock,
  isHintBlock,
} from "@/lib/isBlockOfType";
import UnknownBlockView from "./UnknownBlockView.vue";
import CardSideContextProvider from "@/components/CardSideContextProvider.vue";

const props = withDefaults(
  defineProps<{
    label?: string;
    side: T.CardSide;
    showLabel?: boolean;
    sideName: T.CardSideName;
    deck?: T.Deck | null;
  }>(),
  {},
);

const contentBlocks = computed(() => props.side);
</script>
<style></style>
