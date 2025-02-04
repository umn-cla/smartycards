<template>
  <div
    class="flex flex-col items-center justify-center h-full bg-brand-maroon-950 min-h-dvh"
  >
    <div class="mx-auto max-w-screen-lg">
      <div v-if="!embedcode" class="text-center">
        <p>Enter your embed code below to preview it.</p>
      </div>

      <div v-else v-html="embedcode" class="border border-neutral-900" />

      <Textarea v-model="embedcode" class="bg-brand-oatmeal-50" />
    </div>
  </div>
</template>
<script setup lang="ts">
import { Textarea } from "@/components/ui/textarea";
import { ref } from "vue";

const embedcode = ref(
  `<iframe src="https://localhost/decks/19/practice/embed" height="640" width="400"/>`,
);
</script>
<style scoped></style>
