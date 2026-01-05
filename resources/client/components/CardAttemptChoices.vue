<template>
  <div
    class="flex gap-6 justify-center items-center text-2xl transition-colors"
  >
    <button
      :disabled="props.disabled"
      @click="handleAnswer(1)"
      class="flex items-center justify-center bg-brand-maroon-800/5 py-4 px-8 rounded-lg leading-none hover:bg-brand-maroon-800/10 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      ❌
    </button>
    <button
      :disabled="props.disabled"
      @click="handleAnswer(2)"
      class="flex items-center justify-center bg-brand-maroon-800/5 py-4 px-8 rounded-lg leading-none hover:bg-brand-maroon-800/10 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      🫤
    </button>
    <button
      :disabled="props.disabled"
      class="flex items-center justify-center bg-brand-maroon-800/5 py-4 px-8 rounded-lg leading-none hover:bg-brand-maroon-800/10 disabled:opacity-50 disabled:cursor-not-allowed"
      @click="handleAnswer(3)"
    >
      ✅
    </button>
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { useCreateCardAttemptMutation } from "@/queries/cardAttempts";

const props = withDefaults(
  defineProps<{
    card: T.Card;
    initialSideName: T.CardSideName;
    disabled?: boolean;
  }>(),
  {
    disabled: false,
  },
);

const emit = defineEmits<{
  (event: "answer", score: number): void;
}>();

const { mutate: createCardAttempt } = useCreateCardAttemptMutation();

function handleAnswer(score: number) {
  createCardAttempt(
    {
      cardId: props.card.id,
      promptSide: props.initialSideName,
      score,
    },
    {
      onSuccess: () => {
        emit("answer", score);
      },
    },
  );
}
</script>
<style scoped></style>
