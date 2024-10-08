<template>
  <div>
    <EmbedVideo
      v-if="isValidUrlComputed"
      :src="src"
      class="aspect-video rounded-lg overflow-clip"
    />
    <div
      v-else
      class="aspect-video bg-white/50 rounded-lg overflow-clip flex items-center justify-center text-neutral-400"
    >
      Invalid URL
    </div>
  </div>
</template>
<script setup lang="ts">
import { computed } from "vue";
import { isValidUrl } from "@/lib/utils";
import EmbedVideo from "@/components/EmbedVideo.vue";
import * as T from "@/types";

const props = defineProps<{
  block: T.VideoContentBlock;
}>();

const src = computed(() => props.block.content);

const isValidUrlComputed = computed(() => isValidUrl(src.value));
</script>
<style scoped></style>
