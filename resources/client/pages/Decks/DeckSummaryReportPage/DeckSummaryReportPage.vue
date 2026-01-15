<template>
  <AuthenticatedLayout>
    <div v-if="deck && report" class="max-w-screen-lg mx-auto">
      <DeckContextProvider :deck="deck">
        <PageHeader
          title="Summary Report"
          :subtitle="deck?.name"
          :backLabel="deck?.name"
          :backTo="{ name: 'decks.show', params: { deckId } }"
          class="mb-8"
        >
          <div class="flex justify-end gap-4">
            <Tuple label="Total Cards">
              {{ report.cards_count }}
            </Tuple>
            <Tuple label="Members">
              {{ report.memberships_count }}
            </Tuple>
          </div>
        </PageHeader>

        <div class="mb-8">
          <div class="flex items-center justify-between mb-4">
            <h2 class="text-2xl font-bold">Cards</h2>
          </div>
          <div>
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Front</TableHead>
                  <TableHead class="text-center">Back</TableHead>
                  <TableHead class="text-center">Avg Score</TableHead>
                  <TableHead class="text-center">Attempts</TableHead>
                  <TableHead class="text-center">Created</TableHead>
                  <TableHead class="text-center">Edited</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="card in cards" :key="card.id">
                  <TableCell class="w-36">
                    <MatchingSide
                      class="size-32"
                      :blocks="card.front"
                      label="front"
                      status="idle"
                    />
                  </TableCell>
                  <TableCell class="w-36">
                    <MatchingSide
                      class="size-32"
                      :blocks="card.back"
                      label="back"
                      status="idle"
                    />
                  </TableCell>
                  <TableCell class="text-center">
                    <Badge
                      v-if="card.avg_score"
                      variant="outline"
                      class="text-sm"
                      :class="{
                        'border-green-200 bg-green-100 text-green-700':
                          card.avg_score >= 2.5,
                        'border-amber-300 bg-amber-100 text-amber-700':
                          card.avg_score >= 2.0 && card.avg_score < 2.5,
                        'border-orange-200 bg-orange-100 text-orange-700':
                          card.avg_score >= 1.5 && card.avg_score < 2.0,
                        'border-red-200 bg-red-100 text-red-700':
                          card.avg_score < 1.5,
                      }"
                    >
                      {{ card.avg_score.toFixed(2) }}
                    </Badge>
                    <span v-else class="text-sm text-brand-maroon-900/50">
                      -
                    </span>
                  </TableCell>
                  <TableCell class="text-center">
                    {{ card.attempts_count }}
                  </TableCell>
                  <TableCell class="text-center">
                    <div class="text-sm">
                      <p class="font-medium">
                        {{ formatDate(card.created_at) }}
                      </p>
                      <p class="text-brand-maroon-900/50">
                        {{ card.created_by?.name ?? "-" }}
                      </p>
                    </div>
                  </TableCell>
                  <TableCell class="text-center">
                    <div
                      v-if="card.updated_at !== card.created_at"
                      class="text-sm"
                    >
                      <p class="font-medium">
                        {{ formatDate(card.updated_at) }}
                      </p>
                      <p class="text-brand-maroon-900/50">
                        {{ card.updated_by?.name ?? "-" }}
                      </p>
                    </div>
                    <p v-else class="text-sm text-brand-maroon-900/50">-</p>
                  </TableCell>
                  <TableCell class="text-center">
                    <Button
                      variant="secondary"
                      asChild
                      class="uppercase text-xs px-3 py-1.5"
                    >
                      <RouterLink
                        :to="{
                          name: 'cards.edit',
                          params: { deckId, cardId: card.id },
                        }"
                      >
                        Edit
                      </RouterLink>
                    </Button>
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
                  <TableHead class="text-center">Matching</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow v-for="member in memberships" :key="member.user.id">
                  <TableCell>
                    <p class="font-medium">{{ member.user.name }}</p>
                    <p class="text-brand-maroon-900/50">
                      {{ member.user.email }}
                    </p>
                  </TableCell>
                  <TableCell class="text-center">
                    <Boolean :modelValue="member.has_attempted_all_cards" />
                  </TableCell>
                  <TableCell class="text-center">
                    <Boolean :modelValue="member.has_quiz_activity" />
                  </TableCell>
                  <TableCell class="text-center">
                    <Boolean :modelValue="member.has_matching_activity" />
                  </TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>
        </div>
      </DeckContextProvider>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import PageHeader from "@/components/PageHeader.vue";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout/AuthenticatedLayout.vue";
import { useDeckByIdQuery } from "@/queries/decks";
import { computed } from "vue";
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
import MatchingSide from "@/pages/Activities/MatchingGamePage/MatchingSide.vue";
import { Badge } from "@/components/ui/badge";
import { useDeckSummaryReportQuery } from "@/queries/decks/useDeckSummaryReportQuery";
import DeckContextProvider from "@/components/DeckContextProvider.vue";
import { RouterLink } from "vue-router";
import { IconArrowRight } from "@/components/icons";
import Button from "@/components/ui/button/Button.vue";

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);
const { data: deck } = useDeckByIdQuery(deckIdRef);
const { data: report } = useDeckSummaryReportQuery(deckIdRef);
const memberships = computed(() => report.value?.memberships_with_stats ?? []);
const cards = computed(() => report.value?.cards_with_stats ?? []);

function formatDate(dateString: string): string {
  return new Date(dateString).toLocaleString(undefined, {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "numeric",
    minute: "2-digit",
  });
}
</script>
<style scoped></style>
