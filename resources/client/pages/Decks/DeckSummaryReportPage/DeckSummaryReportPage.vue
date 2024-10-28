<template>
  <AuthenticatedLayout>
    <div v-if="deck" class="max-w-screen-lg mx-auto">
      <PageHeader
        title="Summary Report"
        :subtitle="deck?.name"
        :backLabel="deck?.name"
        :backTo="{ name: 'decks.show', params: { deckId } }"
        class="mb-12"
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

      <div>
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

const deckIdRef = computed(() => props.deckId);
const { data: deck } = useDeckByIdQuery(deckIdRef);
</script>
<style scoped></style>
