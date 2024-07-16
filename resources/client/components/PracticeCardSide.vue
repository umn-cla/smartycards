<template>
  <article
    class="p-4 flex flex-col justify-center items-center relative cursor-pointer rounded-lg bg-black/5 gap-4"
  >
    <header
      class="absolute bottom-3 right-4 z-10 uppercase text-xs opacity-50 leading-none"
      v-if="cardLabel"
    >
      {{ cardLabel }}
    </header>
    <div
      v-if="side.type === 'text'"
      v-html="side.content"
    />
    <img
      v-else-if="side.type === 'image'"
      :src="side.content"
      :alt="side.meta.alt ?? 'Card Image'"
      class="object-contain w-full h-full"
    />
    <EmbedVideo
      v-else-if="side.type === 'embed'"
      :src="side.content"
    />
    <audio
      v-else-if="side.type === 'audio'"
      :src="side.content"
      controls
    />
    <p v-else>Unsupported card type</p>
  </article>
</template>
<script setup lang="ts">
import * as T from '@/types';
import EmbedVideo from './EmbedVideo.vue';

defineProps<{
  side: T.CardSide;
  cardLabel?: string;
}>();
</script>
<style scoped></style>
