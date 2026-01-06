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
          <div v-if="hasAssignments" class="flex justify-end gap-4">
            <Tuple label="Canvas Assignments">
              {{ assignmentCount }}
            </Tuple>
          </div>
        </PageHeader>

        <!-- No Assignments Message -->
        <div
          v-if="!hasAssignments"
          class="bg-amber-50 border border-amber-200 rounded-lg p-6 text-center"
        >
          <p class="text-amber-900">
            No Canvas assignments found for this deck.
          </p>
        </div>

        <!-- Assignments List - Grouped by Course -->
        <section v-if="hasAssignments" class="mb-12">
          <div
            v-for="course in groupedByCourse"
            :key="course.courseName"
            class="mb-12"
          >
            <h2 class="text-brand-maroon-900/70 text-2xl font-bold mb-6">
              {{ course.courseName }}
            </h2>

            <div
              v-for="assignment in course.assignments"
              :key="assignment.id"
              class="mb-6 ml-4"
            >
              <div class="bg-brand-oatmeal-50 px-6 py-4 rounded-md">
                <div class="flex items-start justify-between gap-4">
                  <div class="flex-1">
                    <h3 class="text-lg font-medium mb-1">
                      {{ assignment.title }}
                    </h3>
                    <p class="text-sm text-brand-maroon-900/50 mb-2">
                      {{ assignment.context_label }}
                    </p>

                    <template v-if="!assignment.is_staff">
                      <div v-if="assignment.score" class="mt-3">
                        <div class="flex items-baseline gap-2">
                          <span class="text-sm text-brand-maroon-900/70"
                            >Your Score:</span
                          >
                          <span class="text-2xl font-bold">
                            {{ assignment.score.score_percentage.toFixed(0) }}%
                          </span>
                          <span class="text-sm text-brand-maroon-900/50">
                            ({{ assignment.score.score_given }} /
                            {{ assignment.score.score_maximum }})
                          </span>
                        </div>
                        <p class="text-xs text-brand-maroon-900/50 mt-1">
                          Submitted
                          {{ formatDate(assignment.score.submitted_at) }}
                        </p>
                      </div>

                      <div v-else class="mt-3">
                        <p class="text-sm text-brand-maroon-900/50">
                          No score yet
                        </p>
                      </div>
                    </template>
                  </div>

                  <div v-if="assignment.canvas_url">
                    <a
                      :href="assignment.canvas_url"
                      target="_blank"
                      rel="noopener noreferrer"
                      class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-brand-maroon-200 rounded-md text-brand-maroon-700 hover:bg-brand-maroon-50 transition-colors"
                    >
                      View in Canvas
                      <ExternalLinkIcon class="w-4 h-4" />
                    </a>
                  </div>
                </div>
              </div>
            </div>
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
import { useUserAssignmentsQuery } from "@/queries/decks/useUserAssignmentsQuery";
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
const { data: assignmentsData } = useUserAssignmentsQuery(deckIdRef);

const assignments = computed(() => assignmentsData.value?.assignments ?? []);

const assignmentCount = computed(() => assignments.value.length);

const hasAssignments = computed(() => assignmentCount.value > 0);

const groupedByCourse = computed(() => {
  if (!assignments.value) return [];

  const courseMap = new Map<
    string,
    { courseName: string; assignments: T.UserAssignment[] }
  >();

  assignments.value.forEach((assignment) => {
    const courseName = assignment.context_title;

    if (!courseMap.has(courseName)) {
      courseMap.set(courseName, { courseName, assignments: [] });
    }

    courseMap.get(courseName)!.assignments.push(assignment);
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
