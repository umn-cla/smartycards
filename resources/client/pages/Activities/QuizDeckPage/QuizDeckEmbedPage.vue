<template>
  <EmbedLayout>
    <ActivityPageHeader title="Practice Quiz" :subtitle="deck?.name">
      <LevelProgress
        :xp="deckStats?.current_user_xp ?? 0"
        class="w-full px-2"
      />
    </ActivityPageHeader>

    <div
      class="p-4 sm:p-8 pb-8 sm:pb-12 rounded-xl mx-auto max-w-screen-sm bg-brand-oatmeal-50"
    >
      <section class="flex flex-col gap-4" v-if="state.quizState === 'setup'">
        <h2 class="text-center font-bold text-xl">Set Up</h2>

        <aside
          class="flex gap-4 bg-amber-100 p-4 rounded-sm border-l-4 border-amber-500 text-amber-800 shadow-sm mb-4"
        >
          <div class="flex items-start justify-center">
            <IconExclamationTriangle class="size-6" />
          </div>

          <div class="flex flex-col gap-2">
            <h2 class="font-bold">Quiz mode uses AI</h2>
            <p>
              Like any AI system, quiz mode may not always get things right.
              Currently, only text from your cards is used for generating
              questions and choices. It will not use or generate images, audio,
              or video.
            </p>
          </div>
        </aside>

        <div class="flex flex-wrap gap-8 items-start mx-auto">
          <div>
            <Label for="starting-side-select">
              Prompt Side
              <HintTooltip>
                <p>
                  Side for generating the question. The opposite side is used
                  for the answer.
                </p>
              </HintTooltip>
            </Label>
            <Select v-model="state.cardSide" id="starting-side-select">
              <SelectTrigger class="w-28 bg-brand-maroon-800/5">
                <SelectValue placeholder="Starting side" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem value="front">Front</SelectItem>
                <SelectItem value="back">Back</SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div>
            <Label>Number of questions</Label>
            <div class="flex items-center gap-2">
              <input
                v-model="state.numberOfQuestions"
                type="range"
                :min="1"
                :max="10"
                class="w-28 block h-8"
              />
              <span>{{ state.numberOfQuestions }}</span>
            </div>
          </div>
        </div>

        <div class="flex justify-center">
          <Button @click="startQuiz">Start</Button>
        </div>
      </section>

      <section v-else-if="state.quizState === 'loading'">
        <h2 class="text-center font-bold text-xl">Loading</h2>

        <div class="flex flex-col items-center justify-center gap-4 my-8">
          <p>{{ waitMessage }}</p>
          <IconSpinner class="size-12 text-brand-maroon-800/50" />
        </div>
      </section>

      <section v-else-if="state.quizState === 'in-progress'">
        <h2 class="text-center font-bold text-xl">Quiz</h2>

        <Quiz v-if="state.quiz" :quiz="state.quiz" @end-quiz="handleEndQuiz" />
      </section>

      <section v-else-if="state.quizState === 'error'">
        <h2 class="text-center font-bold text-xl text-brand-maroon-500">
          Error
        </h2>

        <div class="flex flex-col gap-4 items-center">
          <p>
            Something went wrong creating the quiz. You can try again or with
            fewer questions. If the problem persists, email
            <a href="latistecharch@umn.edu">latistecharch@umn.edu</a>
          </p>
          <Button @click="state.quizState = 'setup'">Try Again</Button>
        </div>
      </section>

      <section v-else>
        <h2 class="text-center font-bold text-xl">Complete</h2>

        <div class="flex flex-col gap-4 items-center">
          <Tuple label="✅ Correct">
            <span>{{ state.correctCount }}</span>
          </Tuple>
          <Tuple label="❌ Incorrect">
            <span>{{ state.incorrectCount }}</span>
          </Tuple>

          <p>
            {{
              Math.round((state.correctCount / state.numberOfQuestions) * 100)
            }}%
          </p>

          <Button @click="startQuiz">Try Again</Button>
        </div>
      </section>
    </div>
  </EmbedLayout>
</template>
<script setup lang="ts">
import EmbedLayout from "@/layouts/EmbedLayout.vue";
import { Button } from "@/components/ui/button";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Label } from "@/components/ui/label";
import { computed, reactive, ref, watch } from "vue";
import { useRoute } from "vue-router";
import { useDeckByIdQuery } from "@/queries/decks";
import Quiz from "./Quiz.vue";
import * as api from "@/api";
import * as T from "@/types";
import Tuple from "@/components/Tuple.vue";
import IconSpinner from "@/components/icons/IconSpinner.vue";
import HintTooltip from "@/components/HintTooltip.vue";
import { useCreateDeckActivityEventMutation } from "@/queries/deckActivityEvents/useCreateDeckActivityEventMutation";
import { useDeckStatsQuery } from "@/queries/decks/useDeckStatsQuery";
import LevelProgress from "@/components/LevelProgress.vue";
import { IconExclamationTriangle } from "@/components/icons";
import ActivityPageHeader from "../ActivityPageHeader.vue";

const props = defineProps<{
  deckId: number;
}>();

const route = useRoute();

const state = reactive({
  quizState: "setup" as
    | "setup"
    | "loading"
    | "in-progress"
    | "complete"
    | "error",
  cardSide: "front" as T.CardSideName,
  numberOfQuestions: 5,
  quiz: null as null | T.Quiz,
  activeQuestionIndex: 0,
  correctCount: 0,
  incorrectCount: 0,
});

const deckIdRef = computed(() => props.deckId);

const { data: deck, isLoading: isDeckLoading } = useDeckByIdQuery(deckIdRef);

async function startQuiz() {
  state.quizState = "loading";

  try {
    state.quiz = await api.createQuizForDeck(
      deckIdRef.value,
      {
        cardSide: state.cardSide,
        numberOfQuestions: state.numberOfQuestions,
      },
      {
        skipErrorNotifications: true,
      },
    );
  } catch (error) {
    state.quizState = "error";
    return;
  }

  state.quizState = "in-progress";
}

const { data: deckStats } = useDeckStatsQuery(deckIdRef);
const { mutate: createActivityEvent } = useCreateDeckActivityEventMutation();

// Extract LTI launch ID from URL if present
const ltiLaunchId = computed(() => {
  const launchId = route.query.lti_launch_id;
  return typeof launchId === "string" ? launchId : null;
});

async function handleEndQuiz(payload: {
  correctCount: number;
  incorrectCount: number;
}) {
  await createActivityEvent({
    deckId: deckIdRef.value,
    activityType: T.ActivityTypeName.QUIZ,
    correctCount: payload.correctCount,
    totalCount: payload.correctCount + payload.incorrectCount,
    ltiLaunchId: ltiLaunchId.value,
  });

  state.quizState = "complete";
  state.correctCount = payload.correctCount;
  state.incorrectCount = payload.incorrectCount;
}

// shuffle through some messages while users are waiting
const messages = [
  "Loading...",
  "Shuffling cards...",
  "Reticulating splines...",
  "Contacting the mothership...",
  "Fetching coffee...",
  "Finding my glasses...",
  "Making small talk...",
  "Rocking vending machine...",
  "Releasing the Kraken...",
  "Phoning Dr. Cunningham...",
  "Adding tots to hotdish...",
  "Loading rabbits into hats...",
  "Hailing Campus Connector...",
  "Balancing beeps with boops...",
  "Encouraging electrons...",
  "Polishing gopher teeth...",
  "Scraping the windshield...",
  "Cutting the last piece in half...",
];

const waitMessage = ref("Loading...");

watch(
  () => state.quizState,
  (newState) => {
    let timeout = null as null | ReturnType<typeof setTimeout>;
    if (newState !== "loading") {
      if (timeout) {
        clearTimeout(timeout);
      }
      return;
    }

    const randomMessages = ["Loading..."] as string[];

    // start the wait message shuffle
    function advanceWaitMessage() {
      if (!randomMessages.length) {
        randomMessages.push(...messages.toSorted(() => Math.random() - 0.5));
      }

      waitMessage.value = randomMessages.pop() ?? "...";
      timeout = setTimeout(advanceWaitMessage, 2000);
    }

    advanceWaitMessage();
  },
);
</script>
<style scoped></style>
