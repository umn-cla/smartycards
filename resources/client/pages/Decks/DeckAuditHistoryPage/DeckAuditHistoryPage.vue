<template>
  <AuthenticatedLayout>
    <div v-if="deck" class="max-w-screen-lg mx-auto">
      <DeckContextProvider :deck="deck">
        <PageHeader
          title="Deck History"
          :subtitle="deck?.name"
          :backLabel="deck?.name"
          :backTo="{ name: 'decks.show', params: { deckId } }"
          class="mb-8"
        >
          <div class="flex justify-end gap-4">
            <Tuple label="Total Changes">
              {{ auditHistory?.meta.total ?? 0 }}
            </Tuple>
          </div>
        </PageHeader>

        <div class="bg-brand-oatmeal-50 rounded-md shadow overflow-clip">
          <Table>
            <TableHeader>
              <TableRow class="bg-brand-maroon-900/10">
                <TableHead class="w-8"></TableHead>
                <TableHead>
                  <SortableHeader
                    label="Object"
                    field="auditable_type"
                    :current-sort="sortField"
                    :current-direction="sortDirection"
                    @sort="handleSort"
                  />
                </TableHead>
                <TableHead>
                  <SortableHeader
                    label="Object ID"
                    field="auditable_id"
                    :current-sort="sortField"
                    :current-direction="sortDirection"
                    @sort="handleSort"
                  />
                </TableHead>
                <TableHead>
                  <SortableHeader
                    label="Action"
                    field="event"
                    :current-sort="sortField"
                    :current-direction="sortDirection"
                    @sort="handleSort"
                  />
                </TableHead>
                <TableHead>
                  <SortableHeader
                    label="User"
                    field="user"
                    :current-sort="sortField"
                    :current-direction="sortDirection"
                    @sort="handleSort"
                  />
                </TableHead>
                <TableHead>
                  <SortableHeader
                    label="Date & Time"
                    field="created_at"
                    :current-sort="sortField"
                    :current-direction="sortDirection"
                    @sort="handleSort"
                  />
                </TableHead>
              </TableRow>
              <!-- Filter row -->
              <TableRow>
                <TableHead class="w-8 py-2">
                  <Button
                    v-if="hasActiveFilters"
                    data-cy="audit-clear-filters-button"
                    title="Clear filters"
                    @click="clearFiltersAndFocusObjectFilter"
                    class="uppercase text-[0.66rem] px-2 py-0.5 font-semibold rounded"
                  >
                    Clear
                  </Button>
                </TableHead>
                <TableHead class="py-2">
                  <select
                    ref="objectFilterSelect"
                    v-model="filterObject"
                    data-cy="audit-object-filter-select"
                    class="text-base md:text-xs border-none rounded px-1.5 py-1 bg-brand-maroon-900/5 w-20 font-medium"
                  >
                    <option value="">All</option>
                    <option value="Deck">Deck</option>
                    <option value="Card">Card</option>
                  </select>
                </TableHead>
                <TableHead class="py-2">
                  <input
                    v-model="filterId"
                    type="text"
                    placeholder="ID..."
                    class="text-base md:text-xs border-none rounded px-1.5 py-1 bg-brand-maroon-900/5 w-16 placeholder:text-black/25"
                  />
                </TableHead>
                <TableHead class="py-2">
                  <select
                    v-model="filterAction"
                    class="text-base md:text-xs border-none rounded px-1.5 py-1 bg-brand-maroon-900/5 font-medium w-full"
                  >
                    <option value="">All</option>
                    <option value="created">Created</option>
                    <option value="updated">Updated</option>
                    <option value="deleted">Deleted</option>
                    <option value="restored">Restored</option>
                  </select>
                </TableHead>
                <TableHead class="py-2">
                  <input
                    v-model="filterUser"
                    data-cy="audit-user-filter-input"
                    type="text"
                    placeholder="Filter..."
                    class="text-base md:text-xs border-none rounded px-1.5 py-1 bg-brand-maroon-900/5 placeholder:text-black/25 w-full"
                  />
                </TableHead>
                <TableHead class="py-2">
                  <div class="flex gap-1 items-center">
                    <input
                      v-model="filterDateFrom"
                      type="date"
                      class="text-base md:text-xs border-none rounded px-1.5 py-1 bg-brand-maroon-900/5 placeholder:text-black/25 w-28 font-medium"
                      title="From date"
                    />
                    <span class="text-brand-maroon-900/40">-</span>
                    <input
                      v-model="filterDateTo"
                      type="date"
                      class="text-base md:text-xs border-none rounded px-1.5 py-1 bg-brand-maroon-900/5 placeholder:text-black/25 w-28 font-medium"
                      title="To date"
                    />
                  </div>
                </TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-if="isErrorWithoutData">
                <TableCell
                  colspan="6"
                  class="text-center py-8 text-brand-maroon-900/70"
                >
                  Could not load audit history.
                </TableCell>
              </TableRow>
              <!-- Empty state -->
              <TableRow v-else-if="isLoadedAndEmpty">
                <TableCell
                  colspan="6"
                  class="text-center py-8 text-brand-maroon-900/50"
                >
                  <p v-if="hasActiveFilters">
                    No changes match the current filters.
                  </p>
                  <p v-else>No edit history found for this deck.</p>
                </TableCell>
              </TableRow>
              <!-- Data rows -->
              <template v-for="audit in audits" :key="audit.id">
                <TableRow
                  class="cursor-pointer hover:bg-brand-oatmeal-50"
                  @click="toggleRow(audit.id)"
                >
                  <TableCell class="w-8">
                    <Button
                      variant="ghost"
                      size="icon"
                      class="size-6"
                      data-cy="toggle-audit-row-button"
                      :aria-expanded="expandedRows.has(audit.id)"
                    >
                      <span class="sr-only">
                        Changes to {{ audit.auditable_type }}
                        {{ audit.auditable_id }},
                        {{ formatDateTime(audit.created_at) }}
                      </span>
                      <span aria-hidden="true">
                        <ChevronDownIcon
                          class="size-4 text-brand-maroon-900/50 transition-transform"
                          :class="{ 'rotate-180': expandedRows.has(audit.id) }"
                        />
                      </span>
                    </Button>
                  </TableCell>
                  <TableCell>
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                      :class="
                        audit.auditable_type === 'Deck'
                          ? 'bg-purple-100 text-purple-700'
                          : 'bg-blue-100 text-blue-700'
                      "
                    >
                      {{ audit.auditable_type }}
                    </span>
                  </TableCell>
                  <TableCell class="text-brand-maroon-900/70 font-mono text-sm">
                    {{ audit.auditable_id }}
                  </TableCell>
                  <TableCell>
                    <AuditEventBadge :event="audit.event" />
                  </TableCell>
                  <TableCell>
                    {{ audit.user?.name ?? "System" }}
                  </TableCell>
                  <TableCell class="text-brand-maroon-900/70">
                    {{ formatDateTime(audit.created_at) }}
                  </TableCell>
                </TableRow>
                <TableRow
                  v-if="expandedRows.has(audit.id)"
                  data-cy="audit-row-changes"
                >
                  <TableCell colspan="6" class="bg-white p-0">
                    <div class="px-6 py-4">
                      <AuditValuesDiff
                        :old-values="audit.old_values"
                        :new-values="audit.new_values"
                        :auditable-type="audit.auditable_type"
                        :event="audit.event"
                      />
                    </div>
                  </TableCell>
                </TableRow>
              </template>
            </TableBody>
          </Table>

          <!-- Pagination -->
          <div
            v-if="auditHistory && auditHistory.meta.last_page > 1"
            class="mt-6 flex items-center justify-center gap-4"
          >
            <Button
              variant="outline"
              :disabled="!auditHistory.links.prev"
              @click="page--"
            >
              Previous
            </Button>
            <span class="text-sm text-brand-maroon-900/70">
              Page {{ auditHistory.meta.current_page }} of
              {{ auditHistory.meta.last_page }}
            </span>
            <Button
              variant="outline"
              :disabled="!auditHistory.links.next"
              @click="page = Math.min(page + 1, auditHistory.meta.last_page)"
            >
              Next
            </Button>
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
import { useDeckDocumentTitle } from "@/lib/documentTitle";
import { useDeckAuditHistoryQuery } from "@/queries/decks/useDeckAuditHistoryQuery";
import { computed, ref, watch } from "vue";
import { refDebounced } from "@vueuse/core";
import { useAnnouncer } from "@vue-a11y/announcer";
import { pluralize } from "@/utils/pluralize";
import { Button } from "@/components/ui/button";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import Tuple from "@/components/Tuple.vue";
import DeckContextProvider from "@/components/DeckContextProvider.vue";
import AuditEventBadge from "./AuditEventBadge.vue";
import AuditValuesDiff from "./AuditValuesDiff.vue";
import SortableHeader from "./SortableHeader.vue";
import { ChevronDownIcon } from "@radix-icons/vue";
import type {
  AuditEvent,
  AuditableType,
  DeckAuditHistoryResponse,
} from "@/types";

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);

// Filters
const filterObject = ref<AuditableType | "">("");
const filterId = ref("");
const filterAction = ref<AuditEvent | "">("");
const filterUser = ref("");
const filterDateFrom = ref("");
const filterDateTo = ref("");

const debouncedFilterId = refDebounced(filterId, 300);
const debouncedFilterUser = refDebounced(filterUser, 300);

// Sorting
type SortField = "auditable_type" | "event" | "user" | "created_at";
type SortDirection = "asc" | "desc";

const sortField = ref<SortField>("created_at");
const sortDirection = ref<SortDirection>("desc");

// Pagination
const page = ref(1);

// Build query params from filter/sort state
const queryParams = computed(() => ({
  page: page.value,
  object: filterObject.value || undefined,
  id: debouncedFilterId.value || undefined,
  action: filterAction.value || undefined,
  user: debouncedFilterUser.value || undefined,
  from: filterDateFrom.value || undefined,
  to: filterDateTo.value || undefined,
  sort: sortField.value,
  direction: sortDirection.value,
}));

// Reset to page 1 when filters or sort changes
watch(
  [
    filterObject,
    debouncedFilterId,
    filterAction,
    debouncedFilterUser,
    filterDateFrom,
    filterDateTo,
    sortField,
    sortDirection,
  ],
  () => {
    page.value = 1;
  },
);

const { data: deck } = useDeckByIdQuery(deckIdRef);
useDeckDocumentTitle(deckIdRef, deck);
const {
  data: auditHistory,
  isError: isAuditHistoryError,
  isPlaceholderData: isAuditHistoryPlaceholder,
} = useDeckAuditHistoryQuery(deckIdRef, queryParams);

// Computed helpers
const hasActiveFilters = computed(() =>
  Boolean(
    filterObject.value ||
    filterId.value ||
    filterAction.value ||
    filterUser.value ||
    filterDateFrom.value ||
    filterDateTo.value,
  ),
);

const audits = computed(() => auditHistory.value?.data ?? []);

const isErrorWithoutData = computed(
  () => isAuditHistoryError.value && !auditHistory.value,
);

const isLoadedAndEmpty = computed(() => auditHistory.value?.data.length === 0);

const isQueryFiltered = computed(() => {
  const { object, id, action, user, from, to } = queryParams.value;
  return [object, id, action, user, from, to].some(Boolean);
});

const announcer = useAnnouncer();
let isResultAnnouncementPending = false;

function describeAuditHistoryResult(
  meta: DeckAuditHistoryResponse["meta"],
  hasActiveFilters: boolean,
): string {
  if (meta.total === 0) {
    return hasActiveFilters
      ? "No changes match the current filters."
      : "No edit history found for this deck.";
  }
  const changeCountSentence = `${meta.total} ${pluralize(meta.total, "change")}.`;
  return meta.last_page > 1
    ? `${changeCountSentence} Page ${meta.current_page} of ${meta.last_page}.`
    : changeCountSentence;
}

interface AuditHistoryAnnouncement {
  message: string;
  politeness: "polite" | "assertive";
}

function toAuditHistoryAnnouncement(query: {
  auditHistory: DeckAuditHistoryResponse | undefined;
  isPlaceholder: boolean;
  isErrorWithoutData: boolean;
  hasActiveFilters: boolean;
}): AuditHistoryAnnouncement | null {
  if (query.isErrorWithoutData) {
    return {
      message: "Could not load audit history.",
      politeness: "assertive",
    };
  }
  if (!query.auditHistory || query.isPlaceholder) return null;
  return {
    message: describeAuditHistoryResult(
      query.auditHistory.meta,
      query.hasActiveFilters,
    ),
    politeness: "polite",
  };
}

watch(queryParams, () => {
  isResultAnnouncementPending = true;
});

watch([auditHistory, isAuditHistoryPlaceholder, isErrorWithoutData], () => {
  if (!isResultAnnouncementPending) return;
  const announcement = toAuditHistoryAnnouncement({
    auditHistory: auditHistory.value,
    isPlaceholder: isAuditHistoryPlaceholder.value,
    isErrorWithoutData: isErrorWithoutData.value,
    hasActiveFilters: isQueryFiltered.value,
  });
  if (!announcement) return;
  isResultAnnouncementPending = false;
  announcer.announce(announcement.message, announcement.politeness);
});

// Actions
const objectFilterSelect = ref<HTMLSelectElement | null>(null);

function clearFiltersAndFocusObjectFilter() {
  objectFilterSelect.value?.focus();
  filterObject.value = "";
  filterId.value = "";
  filterAction.value = "";
  filterUser.value = "";
  filterDateFrom.value = "";
  filterDateTo.value = "";
}

function handleSort(field: string) {
  const sortableField = field as SortField;
  if (sortField.value === sortableField) {
    sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
  } else {
    sortField.value = sortableField;
    sortDirection.value = "asc";
  }
}

// Expandable rows
const expandedRows = ref<Set<number>>(new Set());

function toggleRow(id: number) {
  if (expandedRows.value.has(id)) {
    expandedRows.value.delete(id);
  } else {
    expandedRows.value.add(id);
  }
  // Trigger reactivity
  expandedRows.value = new Set(expandedRows.value);
}

function formatDateTime(dateString: string): string {
  const date = new Date(dateString);
  return date.toLocaleString(undefined, {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
}
</script>
