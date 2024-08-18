<template>
  <iframe
    v-if="isValidUrl && embedUrl"
    :src="embedUrl"
    class="w-full object-cover h-full rounded-lg"
    frameborder="0"
    allowfullscreen
    referrerpolicy="strict-origin-when-cross-origin"
  ></iframe>
  <div v-else class="text-red-600 bg-neutral-100 w-full p-4 rounded-md">
    No embed
  </div>
</template>
<script setup lang="ts">
import { computed } from "vue";
import { convertYouTubeShareLinkToEmbedLink } from "@/utils/convertYouTubeShareLinkToEmbedLink";

const props = defineProps<{
  src: string;
}>();

const isValidUrl = computed(() => {
  try {
    new URL(props.src);
    return true;
  } catch {
    return false;
  }
});

const embedUrl = computed(() => {
  if (!isValidUrl.value) return null;

  return convertYouTubeShareLinkToEmbedLink(props.src) ?? props.src;
});
</script>
<style scoped></style>
