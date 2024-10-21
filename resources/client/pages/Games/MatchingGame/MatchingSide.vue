<template>
  <div
    class="bg-brand-oatmeal-50 shadow-sm border border-brand-oatmeal-300 rounded-sm aspect-square overflow-y-auto scrollbar-thin scrollbar-thumb-black/10 scrollbar-track-transparent relative flex flex-col h-full w-full items-center justify-center p-1"
    :class="{
      'ring-2 ring-brand-teal-300 ring-offset-2': status === 'selected',
      'opacity-25 cursor-not-allowed': status === 'disabled',
      'cursor-pointer': status !== 'disabled',
    }"
  >
    <Transition name="fade">
      <aside
        v-if="status !== 'idle'"
        class="absolute inset-0 z-20 rounded-sm font-bold text-4xl px-4 py-3 flex items-center justify-center text-brand-oatmeal-50"
        :class="{
          'bg-brand-teal-500/25 ring-2 ring-brand-teal-300 ring-offset-2 shadow-md':
            status === 'selected',
          'bg-brand-orange-500/75 backdrop-blur-sm': status === 'mismatch',
          'bg-brand-teal-300/75 backdrop-blur-sm': status === 'match',
        }"
      >
        <span v-if="status === 'match'">
          <IconCheck />
          <span class="sr-only">Match</span>
        </span>
        <span v-else-if="status === 'mismatch'">
          <IconX />
          <span class="sr-only">Not a match. Try again</span>
        </span>
      </aside>
    </Transition>
    <template v-for="block in blocks" :key="block.id">
      <TextBlockView
        v-if="isTextBlock(block)"
        :block="block"
        class="text-xs sm:text-base !leading-[1] pointer-events-none"
      />
      <ImageBlockView
        v-else-if="isImageBlock(block)"
        :src="block.content"
        :alt="block.meta?.alt ?? ''"
        :withLightbox="false"
      />
      <VideoBlockView v-else-if="isVideoBlock(block)" :block="block" />
      <EmbedBlockView v-else-if="isEmbedBlock(block)" :block="block" />
      <SimpleAudioPlayer v-else-if="isAudioBlock(block)" :src="block.content" />
      <HintBlockView
        v-else-if="isHintBlock(block)"
        :modelValue="block.content"
        :meta="block.meta"
      />
      <MathBlockView v-else-if="isMathBlock(block)" :block="block" />
      <UnknownBlockView v-else :block="block" />
    </template>
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import {
  isImageBlock,
  isTextBlock,
  isVideoBlock,
  isAudioBlock,
  isEmbedBlock,
  isMathBlock,
  isHintBlock,
} from "@/lib/isBlockOfType";
import MathBlockView from "@/components/CardSideView/MathBlockView.vue";
import HintBlockView from "@/components/CardSideView/HintBlockView.vue";
import ImageBlockView from "@/components/CardSideView/ImageBlockView.vue";
import TextBlockView from "@/components/CardSideView/TextBlockView.vue";
import VideoBlockView from "@/components/CardSideView/VideoBlockView.vue";
import EmbedBlockView from "@/components/CardSideView/EmbedBlockView.vue";
import UnknownBlockView from "@/components/CardSideView/UnknownBlockView.vue";
import SimpleAudioPlayer from "@/components/SimpleAudioPlayer.vue";
import { IconX, IconCheck } from "@/components/icons";
import { MatchingCardSide } from "./matchingGameStore";

const props = defineProps<{
  blocks: T.ContentBlock[];
  label: T.CardSideName;
  status: MatchingCardSide["status"];
}>();
</script>
<style scoped></style>
