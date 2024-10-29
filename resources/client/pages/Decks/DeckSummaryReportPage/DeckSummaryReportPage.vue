<template>
  <AuthenticatedLayout>
    <div v-if="deck && report" class="max-w-screen-lg mx-auto">
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
                    variant="outline"
                    class="text-sm"
                    :class="{
                      'border-teal-300 bg-teal-100 text-teal-700':
                        card.avg_score >= 2.5,
                      'border-amber-400 bg-amber-100 text-amber-700':
                        card.avg_score >= 2.0 && card.avg_score < 2.5,
                      'border-orange-400 bg-orange-100 text-orange-700':
                        card.avg_score >= 1.5 && card.avg_score < 2.0,
                      'border-red-400 bg-red-100 text-red-700':
                        card.avg_score < 1.5,
                    }"
                  >
                    {{ card.avg_score.toFixed(2) }}
                  </Badge>
                </TableCell>
                <TableCell class="text-center">
                  {{ card.attempts_count }}
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

const deckIdRef = computed(() => props.deckId);
const { data: deck } = useDeckByIdQuery(deckIdRef);
const { data: report } = useDeckSummaryReportQuery(deckIdRef);
const memberships = computed(() => report.value?.memberships_with_stats ?? []);
const cards = computed(() => report.value?.cards_with_stats ?? []);
</script>
<style scoped></style>
