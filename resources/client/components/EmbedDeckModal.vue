<template>
  <Modal
    title="Embed Activity"
    submitButtonLabel="Close"
    triggerButtonVariant="none"
    @submit="$emit('update:isOpen', false)"
    :open="isOpen"
    @update:open="$emit('update:isOpen', $event)"
  >
    <p>Copy the following code to embed this deck on your website.</p>
    <div v-for="mode in PracticeMode" :key="mode" class="my-4">
      <div class="flex gap-4 items-center justify-between">
        <Label for="embed-practice"> {{ capitalize(mode) }} Mode </Label>
        <Button
          @click="handleCopy(mode)"
          variant="ghost"
          class="flex gap-1 text-xs py-2"
        >
          <IconCopy v-if="!isCopied" class="size-4" />
          <IconCheck v-else class="size-4" />
          <span>{{ isCopied ? "Copied" : "Copy" }}</span>
        </Button>
      </div>
      <Textarea
        readonly
        :modelValue="embedCodes.practice"
        class="h-20 bg-brand-maroon-900/5 border-none"
      />
    </div>
  </Modal>
</template>
<script setup lang="ts">
import { capitalize, computed, ref } from "vue";
import * as T from "@/types";
import Modal from "@/components/Modal.vue";
import { Label } from "./ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Button } from "@/components/ui/button";
import { IconCopy, IconCheck } from "@/components/icons";
import { useClipboard } from "@vueuse/core";

const props = defineProps<{
  deck: T.Deck;
  isOpen: boolean;
}>();

defineEmits<{
  (eventName: "update:isOpen", isOpen: boolean): void;
}>();

const deckUrl = computed(
  () => `${window.location.origin}/decks/${props.deck.id}`,
);

enum PracticeMode {
  PRACTICE = "practice",
  MATCHING = "matching",
  QUIZ = "quiz",
}

type PracticeEmbedCodes = {
  [key in PracticeMode]: string;
};

const createModeEmbedCode = (mode: string) =>
  `<iframe src="${deckUrl.value}/${mode}/embed" width="100%" height="640px" frameborder="0" allowfullscreen></iframe>`;

const embedCodes = computed((): PracticeEmbedCodes => {
  return {
    practice: createModeEmbedCode("practice"),
    matching: createModeEmbedCode("matching"),
    quiz: createModeEmbedCode("quiz"),
  };
});

const isCopied = ref(false);
const { copy } = useClipboard();

function handleCopy(mode: "practice" | "matching" | "quiz") {
  const embedCodeForMode = embedCodes.value[mode];
  copy(embedCodeForMode);
  isCopied.value = true;

  setTimeout(() => {
    isCopied.value = false;
  }, 2000);
}
</script>
<style scoped></style>
