<template>
  <AuthenticatedLayout>
    <div v-if="deck" class="max-w-screen-lg mx-auto">
      <PageHeader
        title="Summary Report"
        :subtitle="deck?.name"
        :backLabel="deck?.name"
        :backTo="{ name: 'decks.show', params: { deckId } }"
        class="mb-8"
      >
        <div class="flex justify-end gap-4">
          <Tuple label="Total Cards">
            {{ deck?.cards_count }}
          </Tuple>
          <Tuple label="Members">
            {{ deck?.memberships_count }}
          </Tuple>
        </div>
      </PageHeader>

      <div class="mb-8">
        <h2 class="text-2xl font-bold mb-4">Cards</h2>
        <div>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Front</TableHead>
                <TableHead class="text-center">Back</TableHead>
                <TableHead class="text-center">Avg Score</TableHead>
                <TableHead class="text-center">Attempts</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="card in cardStats" :key="card.id">
                <TableCell>
                  <MatchingSide
                    :blocks="card.front"
                    label="front"
                    status="idle"
                  />
                </TableCell>
                <TableCell>
                  <MatchingSide
                    :blocks="card.front"
                    label="front"
                    status="idle"
                  />
                </TableCell>
                <TableCell class="text-center">
                  <Badge
                    variant="outline"
                    class="text-sm"
                    :class="{
                      'border-teal-300 bg-teal-100 text-teal-700':
                        card.avgScore >= 2.5,
                      'border-amber-400 bg-amber-100 text-amber-700':
                        card.avgScore >= 2.0 && card.avgScore < 2.5,
                      'border-orange-400 bg-orange-100 text-orange-700':
                        card.avgScore >= 1.5 && card.avgScore < 2.0,
                      'border-red-400 bg-red-100 text-red-700':
                        card.avgScore < 1.5,
                    }"
                  >
                    {{ card.avgScore.toFixed(2) }}
                  </Badge>
                </TableCell>
                <TableCell class="text-center">
                  {{ card.attempts }}
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </div>

      <div class="mb-8">
        <h2 class="text-2xl font-bold mb-4">Participation</h2>
        <div class="bg-brand-oatmeal-50 px-4 py-2 rounded-md">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead class="text-center">Practiced All</TableHead>
                <TableHead class="text-center">Quiz</TableHead>
                <TableHead class="text-center">Matching Game</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="stats in memberStats" :key="stats.email">
                <TableCell>
                  <p class="font-medium">{{ stats.name }}</p>
                  <p class="text-brand-maroon-900/50">{{ stats.email }}</p>
                </TableCell>
                <TableCell class="text-center">
                  <Boolean :modelValue="stats.hasPracticedAllCards" />
                </TableCell>
                <TableCell class="text-center">
                  <Boolean :modelValue="stats.hasUsedQuiz" />
                </TableCell>
                <TableCell class="text-center">
                  <Boolean :modelValue="stats.hasUsedMatching" />
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import PageHeader from "@/components/PageHeader.vue";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout/AuthenticatedLayout.vue";
import { useDeckByIdQuery } from "@/queries/decks";
import { computed, ref } from "vue";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import Tuple from "@/components/Tuple.vue";
import Boolean from "@/components/Boolean.vue";
import MatchingSide from "@/pages/Games/MatchingGame/MatchingSide.vue";
import { Badge } from "@/components/ui/badge";
import { useDeckSummaryReportQuery } from "@/queries/decks/useDeckSummaryReportQuery";

const props = defineProps<{
  deckId: number;
}>();

const memberStats = ref([
  {
    name: "James Johnson",
    email: "johnsojr@umn.edu",
    hasPracticedAllCards: true,
    hasUsedQuiz: false,
    hasUsedMatching: true,
  },
  {
    name: "James Johnson",
    email: "johnsojr@umn.edu",
    hasPracticedAllCards: false,
    hasUsedQuiz: false,
    hasUsedMatching: true,
  },
  {
    name: "James Johnson",
    email: "johnsojr@umn.edu",
    hasPracticedAllCards: false,
    hasUsedQuiz: false,
    hasUsedMatching: false,
  },
]);

function randomInt(min: number, max: number) {
  return Math.floor(Math.random() * (max - min + 1)) + min;
}

function randomFloat(min: number, max: number) {
  return Math.random() * (max - min) + min;
}

const cardStats = computed(() => {
  return deck.value?.cards.map((card) => {
    return {
      ...card,
      avgScore: randomFloat(1, 3),
      attempts: randomInt(1, 10),
    };
  });
});

const deckIdRef = computed(() => props.deckId);
const { data: deck } = useDeckByIdQuery(deckIdRef);
const { data: report } = useDeckSummaryReportQuery(deckIdRef);
</script>
<style scoped></style>
