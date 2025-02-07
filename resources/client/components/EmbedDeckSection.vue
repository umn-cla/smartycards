<template>
  <div v-for="mode in embedModes" :key="mode" class="my-4">
    <div class="flex gap-4 items-center justify-between mb-1">
      <Label :for="`embed-${mode}`" class="font-bold text-brand-maroon-900/75">
        {{ capitalize(mode) }} Mode
      </Label>
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
      :id="`embed-${mode}`"
      :modelValue="embedCodes[mode]"
      class="h-20 bg-brand-maroon-900/5 border-none font-mono text-xs"
    />
  </div>
</template>
<script setup lang="ts">
import { capitalize, computed, reactive, toRef } from "vue";
import * as T from "@/types";
import { Label } from "./ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Button } from "@/components/ui/button";
import { IconCopy, IconCheck } from "@/components/icons";
import { useClipboard } from "@vueuse/core";
import { useDeckShareLinkQuery } from "@/queries/deckMemberships";

const props = defineProps<{
  deck: T.Deck;
}>();

defineEmits<{
  (eventName: "update:isOpen", isOpen: boolean): void;
}>();

type EmbedMode = "practice" | "matching" | "quiz";

const embedModes: EmbedMode[] = ["practice", "quiz", "matching"];

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
  `/decks/${props.deck.id}/games/matching/embed`,
);

const getIframeForEmbedMode = (mode: EmbedMode) => {
  const src = {
    practice: shareThenPracticeUrl.value,
    quiz: shareThenQuizUrl.value,
    matching: shareThenMatchingUrl.value,
  };

  return `<iframe src="${src[mode]}" width="100%" height="640px" frameborder="0" allowfullscreen></iframe>`;
};

const embedCodes = computed((): PracticeEmbedCodes => {
  return {
    practice: getIframeForEmbedMode("practice"),
    quiz: getIframeForEmbedMode("quiz"),
    matching: getIframeForEmbedMode("matching"),
  };
});

const { copy } = useClipboard();

const isEmbedCopied = reactive<Record<EmbedMode, boolean>>({
  practice: false,
  quiz: false,
  matching: false,
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
