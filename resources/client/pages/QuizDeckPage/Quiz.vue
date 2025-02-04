<template>
  <div class="quiz">
    <h1 class="text-center mb-8">
      Question {{ questionNumber }} of
      {{ totalQuestions }}
    </h1>

    <div class="w-80 max-w-full mx-auto">
      <CardSideView
        v-if="activeQuestionPromptMedia.length"
        :side="activeQuestionPromptMedia"
        class="mb-4"
      />
      <!-- <p class="mb-4">{{ activeQuestion.prompt }}</p> -->
      <Markdown :content="activeQuestion.prompt" class="mb-4" />

      <RadioGroup
        :modelValue="state.activeChoiceIndex?.toString()"
        @update:modelValue="handleUpdateRadioGroup"
        :disabled="isChoiceMade"
        class="pl-4"
      >
        <Label
          class="flex items-center p-4 bg-brand-maroon-950/5 rounded-md transition"
          v-for="(choice, index) in activeQuestion.choices"
          :key="index"
          :for="getQuestionChoiceId(state.activeQuestionIndex, index)"
          :class="{
            '!bg-brand-teal-300/10 rounded-md border border-brand-teal-500/50 !text-brand-teal-700':
              isChoiceMade && isChoiceIndexCorrect(index),
            'hover:bg-brand-gold-500/50 cursor-pointer': !isChoiceMade,
          }"
        >
          <RadioGroupItem
            :id="getQuestionChoiceId(state.activeQuestionIndex, index)"
            :value="index.toString()"
            class="mr-2"
            :class="{
              'border-brand-teal-700':
                isChoiceMade && isChoiceIndexCorrect(index),
            }"
          />
          <div class="flex w-full items-center justify-between gap-4">
            <Markdown :content="choice" />
            <!-- <span>{{ choice }}</span> -->
            <span v-if="isChoiceMade && isChoiceIndexCorrect(index)">✅</span>
            <span v-else-if="isChoiceMade && state.activeChoiceIndex === index"
              >❌</span
            >
          </div>
        </Label>
      </RadioGroup>

      <footer v-if="isChoiceMade">
        <div
          class="my-8 p-4 rounded-md"
          :class="{
            'bg-brand-teal-300/10': isActiveChoiceCorrect,
            'bg-brand-orange-500/10': !isActiveChoiceCorrect,
          }"
        >
          <p v-if="isActiveChoiceCorrect" class="text-brand-teal-500">
            ✅ Correct!
          </p>
          <p v-else class="text-brand-orange-500">❌ Incorrect</p>
        </div>

        <Button
          v-if="state.activeQuestionIndex === totalQuestions - 1"
          @click="
            $emit('end-quiz', {
              correctCount: state.correctCount,
              incorrectCount: state.incorrectCount,
            })
          "
        >
          Finish
        </Button>

        <Button v-else @click="handleNextQuestion"> Next </Button>
      </footer>
    </div>
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { reactive, computed, watch } from "vue";
import { Label } from "@/components/ui/label";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Button } from "@/components/ui/button";
import CardSideView from "@/components/CardSideView/CardSideView.vue";
import Markdown from "@/components/Markdown.vue";

const props = defineProps<{
  quiz: T.Quiz;
}>();

const emit = defineEmits<{
  (
    eventName: "end-quiz",
    payload: {
      correctCount: number;
      incorrectCount: number;
    },
  );
}>();

const state = reactive({
  activeQuestionIndex: 0,
  activeChoiceIndex: undefined as undefined | number,
  correctCount: 0,
  incorrectCount: 0,
});

const isChoiceMade = computed(() => state.activeChoiceIndex !== undefined);
const questionNumber = computed(() => state.activeQuestionIndex + 1);
const totalQuestions = computed(() => props.quiz.questions.length);
const activeQuestion = computed(
  () => props.quiz.questions[state.activeQuestionIndex],
);

const activeQuestionPromptMedia = computed((): T.ContentBlock[] => {
  const card = activeQuestion.value.sourceCard;
  const side = activeQuestion.value.sourceCardSide;
  const contentBlocks = card[side];
  const nonMarkdownableBlocks = contentBlocks.filter(
    (block) => !["text", "math"].includes(block.type),
  );

  return nonMarkdownableBlocks;
});
function isChoiceIndexCorrect(choiceIndex?: number): boolean {
  return activeQuestion.value.correctChoiceIndex === choiceIndex;
}

const isActiveChoiceCorrect = computed((): boolean => {
  return isChoiceMade.value && isChoiceIndexCorrect(state.activeChoiceIndex);
});

function getQuestionChoiceId(questionIndex: number, choiceIndex: number) {
  return `quiz-q${questionIndex}-choice${choiceIndex}`;
}

function handleUpdateRadioGroup(str: string) {
  state.activeChoiceIndex = Number.parseInt(str);
}

watch([() => state.activeQuestionIndex, () => state.activeChoiceIndex], () => {
  if (!isChoiceMade.value) return;

  if (isActiveChoiceCorrect.value) {
    state.correctCount += 1;
    return;
  }
  state.incorrectCount += 1;
});

function handleNextQuestion() {
  state.activeQuestionIndex += 1;
  state.activeChoiceIndex = undefined;
}
</script>
<style scoped></style>
