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
          <div v-if="report?.has_lti_context" class="flex justify-end gap-4">
            <Tuple label="Canvas Assignments">
              {{ report.resource_links.length }}
            </Tuple>
          </div>
        </PageHeader>

        <!-- No LTI Context Message -->
        <div
          v-if="report && !report.has_lti_context"
          class="bg-amber-50 border border-amber-200 rounded-lg p-6 text-center"
        >
          <p class="text-amber-900">
            This deck is not connected to a Canvas assignment. Grade submissions
            are only available for decks launched via LTI.
          </p>
        </div>

        <!-- Grades Tables - Grouped by Assignment -->
        <div v-if="report?.has_lti_context">
          <div
            v-for="resourceLinkGroup in report.resource_links"
            :key="resourceLinkGroup.resource_link.id"
            class="mb-12"
          >
            <div class="mb-4">
              <h2 class="text-brand-maroon-900/70 text-2xl font-bold">
                {{ resourceLinkGroup.resource_link.context_title }}
              </h2>
              <div class="my-4">
                <b class="text-xs uppercase text-brand-maroon-900/50"
                  >Assignment</b
                >
                <p>{{ resourceLinkGroup.resource_link.title }}</p>
              </div>
            </div>

            <p
              v-if="resourceLinkGroup.submissions.length === 0"
              class="text-center"
            >
              No grade submissions yet for this assignment.
            </p>

            <div v-else class="bg-brand-oatmeal-50 px-4 py-2 rounded-md">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Student</TableHead>
                    <TableHead class="text-center">Score</TableHead>
                    <TableHead class="text-center">Status</TableHead>
                    <TableHead class="text-center">Submitted</TableHead>
                    <TableHead class="text-center">Actions</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow
                    v-for="submission in resourceLinkGroup.submissions"
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
                    <TableCell class="text-center">
                      <Badge
                        :class="{
                          'bg-green-100 text-green-700 border-green-200':
                            submission.success,
                          'bg-red-100 text-red-700 border-red-200':
                            !submission.success,
                        }"
                      >
                        {{ submission.success ? "Success" : "Failed" }}
                      </Badge>
                      <p
                        v-if="submission.error_message"
                        class="text-xs text-red-600 mt-1"
                        :title="submission.error_message"
                      >
                        {{ truncateError(submission.error_message) }}
                      </p>
                    </TableCell>
                    <TableCell class="text-center text-sm">
                      {{ formatDate(submission.submitted_at) }}
                    </TableCell>
                    <TableCell class="text-center">
                      <Button
                        v-if="submission.can_retry"
                        @click="handleRetry(submission.id)"
                        variant="outline"
                        size="sm"
                        :disabled="isRetrying"
                      >
                        Retry
                      </Button>
                    </TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>
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
