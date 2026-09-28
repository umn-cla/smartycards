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
        :sideName="activeQuestion.sourceCardSide"
        class="mb-4"
      />
      <Markdown :content="activeQuestion.prompt" class="mb-4" />

      <RadioGroup
        :modelValue="selectedChoiceIndex?.toString()"
        @update:modelValue="selectChoiceByRadioValue"
        :disabled="isShowingResult"
        class="pl-4"
      >
        <Label
          class="flex items-center p-4 bg-brand-maroon-950/5 rounded-md transition"
          v-for="(choice, index) in activeQuestion.choices"
          :key="index"
          :for="getQuestionChoiceId(progress.questionIndex, index)"
          :class="{
            '!bg-brand-teal-300/10 rounded-md border border-brand-teal-500/50 !text-brand-teal-700':
              isShowingResult && isChoiceIndexCorrect(index),
            'hover:bg-brand-gold-500/50 cursor-pointer': !isShowingResult,
          }"
        >
          <RadioGroupItem
            :id="getQuestionChoiceId(progress.questionIndex, index)"
            :value="index.toString()"
            class="mr-2"
            :class="{
              'border-brand-teal-700':
                isShowingResult && isChoiceIndexCorrect(index),
            }"
          />
          <div class="flex w-full items-center justify-between gap-4">
            <Markdown :content="choice" />
            <!-- <span>{{ choice }}</span> -->
            <span v-if="isShowingResult && isChoiceIndexCorrect(index)"
              >✅</span
            >
            <span v-else-if="isShowingResult && selectedChoiceIndex === index"
              >❌</span
            >
          </div>
        </Label>
      </RadioGroup>

      <footer v-if="!isShowingResult" class="mt-8">
        <Button
          :disabled="selectedChoiceIndex === null"
          @click="checkSelectedAnswer"
        >
          Check answer
        </Button>
      </footer>

      <footer v-else>
        <div
          class="my-8 p-4 rounded-md"
          :class="{
            'bg-brand-teal-300/10': isAnswerCorrect,
            'bg-brand-orange-500/10': !isAnswerCorrect,
          }"
        >
          <p v-if="isAnswerCorrect" class="text-brand-teal-500">✅ Correct!</p>
          <p v-else class="text-brand-orange-500">❌ Incorrect</p>
        </div>

        <Button
          v-if="progress.questionIndex === totalQuestions - 1"
          ref="nextOrFinishButton"
          @click="
            $emit('end-quiz', {
              correctCount: progress.correctCount,
              incorrectCount: progress.incorrectCount,
            })
          "
        >
          Finish
        </Button>

        <Button v-else ref="nextOrFinishButton" @click="showNextQuestion">
          Next
        </Button>
      </footer>
    </div>
  </div>
</template>
<script setup lang="ts">
import * as T from "@/types";
import { computed, nextTick, ref, useTemplateRef } from "vue";
import { useAnnouncer } from "@vue-a11y/announcer";
import { Label } from "@/components/ui/label";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Button } from "@/components/ui/button";
import CardSideView from "@/components/CardSideView/CardSideView.vue";
import Markdown from "@/components/Markdown.vue";
import { makeContentBlock } from "@/lib/makeContentBlock";
import { isMathBlock, isTextBlock } from "@/lib/isBlockOfType";
import {
  checkAnswer,
  describeAnswerResult,
  goToNextQuestion,
  selectChoice,
  startQuizProgress,
  type QuizProgress,
} from "./quizProgress";

const props = defineProps<{
  quiz: T.Quiz;
}>();

defineEmits<{
  (
    eventName: "end-quiz",
    payload: {
      correctCount: number;
      incorrectCount: number;
    },
  );
}>();

const progress = ref<QuizProgress>(startQuizProgress());
const announcer = useAnnouncer();
const nextOrFinishButton =
  useTemplateRef<InstanceType<typeof Button>>("nextOrFinishButton");

const isShowingResult = computed(
  () => progress.value.answer.status === "showingResult",
);
const selectedChoiceIndex = computed(
  () => progress.value.answer.selectedChoiceIndex,
);
const questionNumber = computed(() => progress.value.questionIndex + 1);
const totalQuestions = computed(() => props.quiz.questions.length);
const activeQuestion = computed(
  () => props.quiz.questions[progress.value.questionIndex],
);

function createImageBlocksFromTextBlock(text: string): T.ImageContentBlock[] {
  // parseHTML for image tags
  const parser = new DOMParser();
  const doc = parser.parseFromString(text, "text/html");
  const imgElements = doc.getElementsByTagName("img");
  return [...imgElements]
    .map((imgEl) => {
      // Only allow http, https, or relative paths for src
      if (!/^(https?:\/\/|\/)/i.test(imgEl.src)) return null;
      const block = makeContentBlock("image") as T.ImageContentBlock;
      block.content = imgEl.src;
      block.meta = { alt: imgEl.alt || "" };
      return block;
    })
    .filter((block): block is T.ImageContentBlock => block !== null);
}

const activeQuestionPromptMedia = computed((): T.ContentBlock[] => {
  const card = activeQuestion.value.sourceCard;
  const side = activeQuestion.value.sourceCardSide;
  const contentBlocks = card[side];
  return contentBlocks.reduce((acc: T.ContentBlock[], block) => {
    // skip math blocks
    if (isMathBlock(block)) {
      return acc;
    }

    // extract images from text block and
    // add them as separate image blocks
    if (isTextBlock(block)) {
      const imageBlocks = createImageBlocksFromTextBlock(block.content);
      return [...acc, ...imageBlocks];
    }

    // otherwise just add the block as is
    return [...acc, block];
  }, []);
});
function isChoiceIndexCorrect(choiceIndex?: number): boolean {
  return activeQuestion.value.correctChoiceIndex === choiceIndex;
}

const isAnswerCorrect = computed((): boolean => {
  const { answer } = progress.value;
  return answer.status === "showingResult" && answer.isCorrect;
});

function getQuestionChoiceId(questionIndex: number, choiceIndex: number) {
  return `quiz-q${questionIndex}-choice${choiceIndex}`;
}

function selectChoiceByRadioValue(radioValue: string): void {
  progress.value = selectChoice(progress.value, Number.parseInt(radioValue));
}

function focusButton(button: InstanceType<typeof Button> | null): void {
  const buttonElement: unknown = button?.$el;
  if (buttonElement instanceof HTMLElement) {
    buttonElement.focus();
  }
}

async function checkSelectedAnswer(): Promise<void> {
  const progressWithResult = checkAnswer(progress.value, activeQuestion.value);
  progress.value = progressWithResult;
  if (progressWithResult.answer.status !== "showingResult") {
    return;
  }

  announcer.polite(
    describeAnswerResult(progressWithResult.answer, activeQuestion.value),
  );
  await nextTick();
  focusButton(nextOrFinishButton.value);
}

function showNextQuestion(): void {
  progress.value = goToNextQuestion(progress.value);
}
</script>
<style scoped></style>
