<template>
  <AuthenticatedLayout>
    <div class="max-w-screen-sm mx-auto">
      <h1>Test TTS Page</h1>

      <div class="my-4">
        <Label>Text to Speech</Label>
        <Textarea v-model="text" class="!bg-brand-maroon-900/5" />
      </div>

      <audio v-if="blobUrl" controls :src="blobUrl"></audio>

      <Button @click="play">Play</Button>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout/AuthenticatedLayout.vue";
import { ref } from "vue";
import { useTextToSpeech } from "@/composables/useTextToSpeech";

const text = ref("");

const { blobUrl, play } = useTextToSpeech(text);
</script>
<style scoped></style>
