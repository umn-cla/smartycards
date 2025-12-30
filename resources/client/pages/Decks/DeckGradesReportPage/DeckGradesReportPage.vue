<template>
  <AuthenticatedLayout>
    <div v-if="deck" class="max-w-screen-lg mx-auto">
      <DeckContextProvider :deck="deck">
        <PageHeader
          title="LTI Grades Report"
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

        <!-- No LTI Context Message -->
        <div
          v-if="report?.error_message"
          class="bg-amber-50 border border-amber-200 rounded-lg p-6 text-center"
        >
          <p class="text-amber-900">
            {{ report.error_message }}
          </p>
        </div>

        <!-- Grades Tables - Grouped by Course -->
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
              :key="assignment.resource_link.id"
              class="mb-8 ml-4"
            >
              <div class="mb-4">
                <b class="text-xs uppercase text-brand-maroon-900/50"
                  >Assignment</b
                >
                <p class="text-lg">{{ assignment.resource_link.title }}</p>
              </div>

              <p
                v-if="assignment.submissions.length === 0"
                class="text-center text-brand-maroon-900/50"
              >
                No grade submissions yet for this assignment.
              </p>

              <div v-else class="bg-brand-oatmeal-50 px-4 py-2 rounded-md">
                <Table>
                  <TableHeader>
                    <TableRow>
                      <TableHead>Student</TableHead>
                      <TableHead class="text-center">Score</TableHead>
                      <TableHead class="text-center">Submitted</TableHead>
                      <TableHead class="text-center">Actions</TableHead>
                    </TableRow>
                  </TableHeader>
                  <TableBody>
                    <TableRow
                      v-for="submission in assignment.submissions"
                      :key="submission.id"
                    >
                      <TableCell>
                        <p class="font-medium">{{ submission.user.name }}</p>
                        <p class="text-brand-maroon-900/50 text-sm">
                          {{ submission.user.email }}
                        </p>
                      </TableCell>
                      <TableCell class="text-center">
                        {{ submission.score_percentage.toFixed(0) }}%
                      </TableCell>
                      <TableCell class="text-center text-sm">
                        {{ formatDate(submission.submitted_at) }}
                      </TableCell>
                      <TableCell class="text-center">
                        <Badge
                          v-if="!submission.success"
                          class="bg-red-100 text-red-700 border-red-200"
                        >
                          Error
                        </Badge>
                        <p
                          v-if="submission.error_message"
                          class="text-xs text-red-600 mt-1"
                          :title="submission.error_message"
                        >
                          {{ truncateError(submission.error_message) }}
                        </p>
                        <p>
                          <Button
                            v-if="submission.can_retry"
                            @click="handleRetry(submission.id)"
                            variant="outline"
                            size="sm"
                            :disabled="isRetrying"
                          >
                            Retry
                          </Button>
                        </p>
                      </TableCell>
                    </TableRow>
                  </TableBody>
                </Table>
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
import { useDeckGradesReportQuery } from "@/queries/decks/useDeckGradesReportQuery";
import { useRetryGradeSubmissionMutation } from "@/queries/ltiGradeSubmissions/useRetryGradeSubmissionMutation";
import { computed } from "vue";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import Tuple from "@/components/Tuple.vue";
import DeckContextProvider from "@/components/DeckContextProvider.vue";
import type * as T from "@/types";

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);
const { data: deck } = useDeckByIdQuery(deckIdRef);
const { data: report } = useDeckGradesReportQuery(deckIdRef);
const { mutate: retrySubmission, isPending: isRetrying } =
  useRetryGradeSubmissionMutation(props.deckId);

const assignmentCount = computed(() => {
  return report.value?.resource_links.length ?? 0;
});

const hasAssignments = computed(() => {
  return assignmentCount.value > 0;
});

// Group assignments by course
const groupedByCourse = computed(() => {
  if (!report.value?.resource_links) return [];

  const courseMap = new Map<
    string,
    { courseName: string; assignments: T.ResourceLinkWithSubmissions[] }
  >();

  report.value.resource_links.forEach((resourceLink) => {
    const courseName = resourceLink.resource_link.context_title;

    if (!courseMap.has(courseName)) {
      courseMap.set(courseName, { courseName, assignments: [] });
    }

    courseMap.get(courseName)!.assignments.push(resourceLink);
  });

  return Array.from(courseMap.values());
});

function handleRetry(submissionId: number) {
  retrySubmission(submissionId);
}

function formatDate(dateString: string): string {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat("en-US", {
    dateStyle: "medium",
    timeStyle: "short",
  }).format(date);
}

function truncateError(error: string, maxLength: number = 50): string {
  if (error.length <= maxLength) return error;
  return error.substring(0, maxLength) + "...";
}
</script>

<style scoped></style>
