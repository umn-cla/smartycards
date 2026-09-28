<template>
  <div class="quiz">
    <h3
      ref="questionHeading"
      tabindex="-1"
      class="text-center mb-8 focus:outline-none"
    >
      Question {{ questionNumber }} of
      {{ totalQuestions }}
    </h3>

    <div class="w-80 max-w-full mx-auto">
      <CardSideView
        v-if="activeQuestionPromptMedia.length"
        :side="activeQuestionPromptMedia"
        :sideName="activeQuestion.sourceCardSide"
        class="mb-4"
      />
      <Markdown
        :id="activeQuestionPromptId"
        :content="activeQuestion.prompt"
        class="mb-4"
      />

      <RadioGroup
        :modelValue="selectedChoiceIndex?.toString()"
        @update:modelValue="selectChoiceByRadioValue"
        :disabled="isShowingResult"
        :aria-labelledby="activeQuestionPromptId"
        class="pl-4"
      >
        <!-- Keep the question index in :key. An index-only
          key reuses the radios, and radix-vue sets their
          aria-label to "0", "1", "2" instead of the
          choice text. -->
        <Label
          class="flex items-center p-4 bg-brand-maroon-950/5 rounded-md transition"
          v-for="(choice, index) in activeQuestion.choices"
          :key="`${progress.questionIndex}-${index}`"
          :for="getQuestionChoiceId(progress.questionIndex, index)"
          :class="{
            '!bg-brand-teal-300/10 rounded-md border border-brand-teal-500/50 !text-brand-teal-700':
              isRevealedCorrectChoice(index),
            'hover:bg-brand-gold-500/50 cursor-pointer': !isShowingResult,
          }"
        >
          <RadioGroupItem
            :id="getQuestionChoiceId(progress.questionIndex, index)"
            :value="index.toString()"
            class="mr-2"
            :class="{
              'border-brand-teal-700': isRevealedCorrectChoice(index),
            }"
          />
          <div class="flex w-full items-center justify-between gap-4">
            <Markdown :content="choice" />
            <!-- <span>{{ choice }}</span> -->
            <span v-if="isRevealedCorrectChoice(index)">✅</span>
            <span v-else-if="isRevealedWrongSelection(index)">❌</span>
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
          v-if="isLastQuestion(progress, totalQuestions)"
          ref="nextOrFinishButton"
          @click="finishQuiz"
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
import { unrefElement } from "@vueuse/core";
import { useAnnouncer } from "@vue-a11y/announcer";
import { Label } from "@/components/ui/label";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Button } from "@/components/ui/button";
import CardSideView from "@/components/CardSideView/CardSideView.vue";
import Markdown from "@/components/Markdown.vue";
import { makeContentBlock } from "@/lib/makeContentBlock";
import { isMathBlock, isTextBlock } from "@/lib/isBlockOfType";
import {
  gradeAnswer,
  describeAnswerResult,
  describeQuizScore,
  goToNextQuestion,
  isLastQuestion,
  selectChoice,
  createQuizProgress,
  type QuizProgress,
} from "./quizProgress";

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

const progress = ref<QuizProgress>(createQuizProgress());
const announcer = useAnnouncer();
const nextOrFinishButton =
  useTemplateRef<InstanceType<typeof Button>>("nextOrFinishButton");
const questionHeading = useTemplateRef<HTMLHeadingElement>("questionHeading");

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
const activeQuestionPromptId = computed(
  () => `quiz-q${progress.value.questionIndex}-prompt`,
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
function isChoiceIndexCorrect(choiceIndex: number): boolean {
  return activeQuestion.value.correctChoiceIndex === choiceIndex;
}

function isRevealedCorrectChoice(choiceIndex: number): boolean {
  return isShowingResult.value && isChoiceIndexCorrect(choiceIndex);
}

function isRevealedWrongSelection(choiceIndex: number): boolean {
  return (
    isShowingResult.value &&
    selectedChoiceIndex.value === choiceIndex &&
    !isChoiceIndexCorrect(choiceIndex)
  );
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

async function checkSelectedAnswer(): Promise<void> {
  const checkedProgress = gradeAnswer(progress.value, activeQuestion.value);
  progress.value = checkedProgress;
  if (checkedProgress.answer.status !== "showingResult") {
    return;
  }

  announcer.polite(
    describeAnswerResult(checkedProgress.answer, activeQuestion.value),
  );
  await nextTick();
  unrefElement(nextOrFinishButton)?.focus();
}

async function showNextQuestion(): Promise<void> {
  progress.value = goToNextQuestion(progress.value, totalQuestions.value);
  await nextTick();
  questionHeading.value?.focus();
}

function finishQuiz(): void {
  announcer.polite(describeQuizScore(progress.value));
  emit("end-quiz", {
    correctCount: progress.value.correctCount,
    incorrectCount: progress.value.incorrectCount,
  });
}
</script>
<style scoped></style>
