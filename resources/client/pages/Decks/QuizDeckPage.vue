<template>
  <AuthenticatedLayout>
    <header
      class="flex mb-6 justify-between border border-black/10 p-2 sm:p-4 rounded-xl mx-auto max-w-screen-sm gap-2"
      v-if="deck"
    >
      <div class="flex gap-2 sm:gap-4 items-baseline">
        <h1 class="font-bold text-brand-maroon-800 text-lg sm:text-xl">
          {{ deck?.name }}
        </h1>
        <h2
          class="text-brand-maroon-800/50 text-sm sm:text-base"
          v-if="deck.description"
        >
          {{ deck?.description }}
        </h2>
      </div>
      <Button asChild variant="secondary">
        <RouterLink
          :to="{ name: 'decks.show', params: { deckId: props.deckId } }"
          class="flex gap-2 items-center"
        >
          End Quiz
        </RouterLink>
      </Button>
    </header>

    <section
      class="p-2 sm:p-4 rounded-xl mx-auto max-w-screen-sm bg-brand-oatmeal-50 flex flex-col gap-4"
      v-if="state.quizState === 'setup'"
    >
      <h2 class="text-center font-bold text-xl">Set Up</h2>
      <div class="flex flex-wrap gap-8 items-start mx-auto">
        <div>
          <Label for="starting-side-select">Start Side</Label>
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
              :min="3"
              :max="10"
              class="w-28 block h-8"
            />
            <span>{{ state.numberOfQuestions }}</span>
          </div>
        </div>
      </div>

      <div class="flex justify-center">
        <Button @click="startQuiz">Start Quiz</Button>
      </div>
    </section>

    <section v-else-if="state.quizState === 'loading'">
      <h2 class="text-center font-bold text-xl">Loading</h2>
    </section>

    <section v-else-if="state.quizState === 'in-progress'">
      <h2 class="text-center font-bold text-xl">Quiz</h2>

      {{ state.quiz }}
    </section>

    <section v-else>
      <h2 class="text-center font-bold text-xl">Complete</h2>
    </section>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout/AuthenticatedLayout.vue";
import { Button } from "@/components/ui/button";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { Label } from "@/components/ui/label";
import { computed, reactive } from "vue";
import { useDeckByIdQuery } from "@/queries/decks";
import * as api from "@/api";
import * as T from "@/types";

const props = defineProps<{
  deckId: number;
}>();

const state = reactive({
  quizState: "setup" as "setup" | "loading" | "in-progress" | "complete",
  cardSide: "front" as T.CardSideName,
  numberOfQuestions: 3,
  quiz: null as null | T.Quiz,
});

const deckIdRef = computed(() => props.deckId);

const { data: deck, isLoading: isDeckLoading } = useDeckByIdQuery(deckIdRef);

async function startQuiz() {
  state.quizState = "loading";

  state.quiz = await api.createQuizForDeck(deckIdRef.value, {
    cardSide: state.cardSide,
    numberOfQuestions: state.numberOfQuestions,
  });

  state.quizState = "in-progress";
}
</script>
<style scoped></style>
