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
    <HintTooltip>
      <dl
        class="grid grid-cols-[1rem_1fr] gap-y-2 gap-x-4 p-2 items-baseline text-base leading-tight"
      >
        <dt>❌</dt>
        <dd>
          <p class="m-0 font-semibold text-white/80">Wrong or didn't know</p>
          <small class="text-white/60"> Card returns soon </small>
        </dd>
        <dt>🫤</dt>
        <dd>
          <p class="m-0 font-semibold text-white/80">
            Partially incorrect or difficult
          </p>
          <small class="text-white/60">Card returns later</small>
        </dd>
        <dt>✅</dt>
        <dd>
          <p class="m-0 font-semibold text-white/80">Correct and easy</p>
          <small class="text-white/60"> Card removed from this session </small>
        </dd>
      </dl>
    </HintTooltip>
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { useCreateCardAttemptMutation } from "@/queries/cardAttempts";
import HintTooltip from "@/components/HintTooltip.vue";

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
