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
      <div class="flex flex-col gap-4 text-base p-2 leading-tight">
        <div class="flex gap-2 align-baseline">
          <div>❌</div>
          <div>
            <p class="text-white/90 font-medium mb-0">Wrong or didn't know</p>
            <small class="text-white/50 text-xs"> Card returns soon </small>
          </div>
        </div>
        <div class="flex gap-2 align-baseline">
          <div>🫤</div>
          <div>
            <p class="text-white/90 font-medium mb-0">
              Partially incorrect or difficult
            </p>
            <small class="text-white/50 text-xs">Card returns later</small>
          </div>
        </div>
        <div class="flex gap-2 text-base align-baseline leading-tight">
          <div>✅</div>
          <div>
            <p class="text-white/90 font-medium mb-0">Correct and easy</p>
            <small class="text-white/50 text-xs">
              Card removed from this session
            </small>
          </div>
        </div>
      </div>
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
