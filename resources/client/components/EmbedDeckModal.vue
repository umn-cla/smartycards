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
    <div v-for="mode in embedModes" :key="mode" class="my-4">
      <div class="flex gap-4 items-center justify-between mb-1">
        <Label for="embed-practice"> {{ capitalize(mode) }} Mode </Label>
        <Button
          @click="handleCopy(mode)"
          variant="ghost"
          class="flex gap-1 text-xs py-1"
        >
          <IconCopy v-if="!isEmbedCopied[mode]" class="size-4" />
          <IconCheck v-else class="size-4" />
          <span>{{ isEmbedCopied[mode] ? "Copied" : "Copy" }}</span>
        </Button>
      </div>
      <Textarea
        readonly
        :modelValue="embedCodes[mode]"
        class="h-20 bg-brand-maroon-900/5 border-none"
      />
    </div>
  </Modal>
</template>
<script setup lang="ts">
import { capitalize, computed, ref, reactive, toRef } from "vue";
import * as T from "@/types";
import Modal from "@/components/Modal.vue";
import { Label } from "./ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Button } from "@/components/ui/button";
import { IconCopy, IconCheck } from "@/components/icons";
import { useClipboard } from "@vueuse/core";
import { useDeckShareLinkQuery } from "@/queries/deckMemberships";

const props = defineProps<{
  deck: T.Deck;
  isOpen: boolean;
}>();

defineEmits<{
  (eventName: "update:isOpen", isOpen: boolean): void;
}>();

type EmbedMode = "practice" | "matching" | "quiz";

const embedModes: EmbedMode[] = ["practice", "matching", "quiz"];

type PracticeEmbedCodes = {
  [key in EmbedMode]: string;
};

const { data: shareThenPracticeUrl } = useDeckShareLinkQuery(
  toRef(props.deck.id),
  "view",
  `/decks/${props.deck.id}/practice/embed`,
);
const { data: shareThenQuizUrl } = useDeckShareLinkQuery(
  toRef(props.deck.id),
  "view",
  `/decks/${props.deck.id}/quiz/embed`,
);

const { data: shareThenMatchingUrl } = useDeckShareLinkQuery(
  toRef(props.deck.id),
  "view",
  `/decks/${props.deck.id}/matching/embed`,
);

const getIframeForEmbedMode = (mode: EmbedMode) => {
  const src = {
    practice: shareThenPracticeUrl.value,
    matching: shareThenMatchingUrl.value,
    quiz: shareThenQuizUrl.value,
  };

  return `<iframe src="${src[mode]}" width="100%" height="640px" frameborder="0" allowfullscreen></iframe>`;
};

const embedCodes = computed((): PracticeEmbedCodes => {
  return {
    practice: getIframeForEmbedMode("practice"),
    matching: getIframeForEmbedMode("matching"),
    quiz: getIframeForEmbedMode("quiz"),
  };
});

const { copy } = useClipboard();

const isEmbedCopied = reactive<Record<EmbedMode, boolean>>({
  practice: false,
  matching: false,
  quiz: false,
});

function handleCopy(mode: EmbedMode) {
  const embedCodeForMode = embedCodes.value[mode];
  copy(embedCodeForMode);
  isEmbedCopied[mode] = true;

  setTimeout(() => {
    isEmbedCopied[mode] = false;
  }, 2000);
}
</script>
<style scoped></style>
