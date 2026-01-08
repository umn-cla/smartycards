<template>
  <AuthenticatedLayout>
    <div v-if="deck" class="max-w-screen-lg mx-auto">
      <DeckContextProvider :deck="deck">
        <PageHeader
          title="Assignments"
          :subtitle="deck?.name"
          :backLabel="deck?.name"
          :backTo="{ name: 'decks.show', params: { deckId } }"
          class="mb-8"
        >
          <div v-if="hasEntries" class="flex justify-end gap-4">
            <Tuple label="Canvas Assignments">
              {{ entryCount }}
            </Tuple>
          </div>
        </PageHeader>

        <!-- No Entries Message -->
        <div
          v-if="!hasEntries"
          class="bg-amber-50 shadow-sm rounded-lg p-6 text-center"
        >
          <p class="text-amber-900">
            No Canvas assignments found for this deck.
          </p>
        </div>

        <!-- Entries List - Grouped by Course -->
        <section v-if="hasEntries" class="mb-12">
          <div
            v-for="course in groupedByCourse"
            :key="course.courseName"
            class="mb-12"
          >
            <h2 class="text-brand-maroon-900/70 text-2xl font-bold mb-6">
              {{ course.courseName }}
            </h2>

            <ul
              v-for="entry in course.entries"
              :key="entry.id"
              class="space-y-4"
            >
              <li class="bg-brand-oatmeal-50 p-4 rounded-md shadow-sm">
                <div class="flex items-center justify-between gap-6 flex-wrap">
                  <!-- col 1 -->
                  <div class="flex-1">
                    <h3 class="text-lg">
                      {{ entry.resource_link?.title ?? "Unknown Assignment" }}
                    </h3>
                    <p class="text-sm text-brand-maroon-900/50">
                      {{ entry.resource_link?.context_label }}
                    </p>
                  </div>
                  <!-- col 2 -->
                  <div v-if="!entry.is_staff" class="text-right">
                    <template v-if="entry.score">
                      <p class="text-lg">
                        {{ entry.score.score_percentage.toFixed(0) }}%
                      </p>
                      <p
                        v-if="entry.score.completed_at"
                        class="text-xs text-brand-maroon-900/50"
                      >
                        {{ formatDate(entry.score.completed_at) }}
                      </p>
                    </template>
                    <p v-else class="text-sm text-brand-maroon-900/50">-</p>
                  </div>
                  <!-- col 3 -->
                  <div v-if="entry.resource_link?.canvas_url">
                    <a
                      :href="entry.resource_link.canvas_url"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="inline-flex items-center gap-2 px-4 py-2 text-brand-maroon-700 bg-brand-maroon-900/5 hover:bg-brand-maroon-900/10 rounded transition-colors text-xs uppercase"
                    >
                      View in Canvas
                      <ExternalLinkIcon class="w-3 h-3" />
                    </a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </section>
      </DeckContextProvider>
    </div>
  </AuthenticatedLayout>
</template>

<script setup lang="ts">
import PageHeader from "@/components/PageHeader.vue";
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout/AuthenticatedLayout.vue";
import { useDeckByIdQuery } from "@/queries/decks";
import { useDeckAssignmentsQuery } from "@/queries/decks/useDeckAssignmentsQuery";
import { computed } from "vue";
import Tuple from "@/components/Tuple.vue";
import DeckContextProvider from "@/components/DeckContextProvider.vue";
import type * as T from "@/types";
import { ExternalLinkIcon } from "@radix-icons/vue";

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);
const { data: deck } = useDeckByIdQuery(deckIdRef);
const { data: entries } = useDeckAssignmentsQuery(deckIdRef);

const entryCount = computed(() => entries.value?.length ?? 0);

const hasEntries = computed(() => entryCount.value > 0);

const groupedByCourse = computed(() => {
  if (!entries.value) return [];

  const courseMap = new Map<
    string,
    { courseName: string; entries: T.LtiResourceLinkEntry[] }
  >();

  entries.value.forEach((entry) => {
    const courseName = entry.resource_link?.context_title ?? "Unknown Course";

    if (!courseMap.has(courseName)) {
      courseMap.set(courseName, { courseName, entries: [] });
    }

    courseMap.get(courseName)!.entries.push(entry);
  });

  return Array.from(courseMap.values());
});

function formatDate(dateString: string): string {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat("en-US", {
    dateStyle: "medium",
    timeStyle: "short",
  }).format(date);
}
</script>

<style scoped></style>
