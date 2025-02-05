<template>
  <div
    class="flex flex-col items-center justify-center h-full bg-brand-maroon-950 min-h-dvh"
  >
    <div class="mx-auto max-w-screen-sm w-full">
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
  `<iframe src="https://localhost/decks/20/invite?fromUserId=4&redirectTo=%2Fdecks%2F20%2Fpractice%2Fembed&role=viewer&token=rBVKBLw7koo3WunJwttULg9yda0Id7oY&signature=fca4a1ca74e079477b951e52264a1048a9e29535349d394bf06538d855b4ec6f" width="100%" height="640px" frameborder="0" allowfullscreen></iframe>`,
);
</script>
<style scoped></style>
