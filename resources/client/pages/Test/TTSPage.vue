<template>
  <AuthenticatedLayout>
    <div class="max-w-screen-sm mx-auto">
      <h1>Test TTS Page</h1>

      <div class="my-4">
        <Label>Text to Speech</Label>
        <Textarea v-model="text" class="!bg-brand-maroon-900/5" />
      </div>

      <audio v-if="blobUrl" ref="audioEl" controls :src="blobUrl"></audio>

      <Button @click="playAI">Play</Button>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import axios from "@/api/axios";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout/AuthenticatedLayout.vue";
import { ref, onUnmounted, nextTick, watch } from "vue";

const text = ref("");

const blobUrl = ref<string | null>(null);
const audioEl = ref<HTMLAudioElement | null>(null);

async function getAudioUrl(text: string, lang = "en-US"): Promise<string> {
  const res = await axios.post<Blob>(
    "tts",
    {
      text,
      lang,
    },
    {
      responseType: "blob",
    },
  );

  return URL.createObjectURL(res.data);
}

async function playAI() {
  // if we already have a blob url, play it
  if (blobUrl.value) {
    audioEl.value?.play();
    return;
  }

  blobUrl.value = await getAudioUrl(text.value);

  nextTick(() => {
    audioEl.value?.play();
  });
}

watch(text, () => {
  if (blobUrl.value) {
    URL.revokeObjectURL(blobUrl.value);
    blobUrl.value = null;
  }
});

function cleanup() {
  if (blobUrl.value) {
    URL.revokeObjectURL(blobUrl.value);
  }

  if (audioEl.value) {
    audioEl.value.pause();
    audioEl.value = null;
  }
}

onUnmounted(cleanup);
</script>
<style scoped></style>
