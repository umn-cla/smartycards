<template>
  <AuthenticatedLayout>
    <div v-if="deck" class="max-w-screen-lg mx-auto">
      <DeckContextProvider :deck="deck">
        <PageHeader
          title="Edit History"
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

        <div v-if="auditHistory">
          <Table>
            <TableHeader>
              <TableRow>
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
                  <span class="text-brand-maroon-900/70">ID</span>
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
              <TableRow class="bg-gray-50">
                <TableHead class="w-8 py-2">
                  <button
                    v-if="hasActiveFilters"
                    class="text-xs text-brand-teal-600 hover:text-brand-teal-700"
                    title="Clear filters"
                    @click="clearFilters"
                  >
                    Clear
                  </button>
                </TableHead>
                <TableHead class="py-2">
                  <select
                    v-model="filterObject"
                    class="text-xs border border-brand-maroon-900/20 rounded px-1.5 py-1 bg-white w-full"
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
                    class="text-xs border border-brand-maroon-900/20 rounded px-1.5 py-1 bg-white w-16"
                  />
                </TableHead>
                <TableHead class="py-2">
                  <select
                    v-model="filterAction"
                    class="text-xs border border-brand-maroon-900/20 rounded px-1.5 py-1 bg-white w-full"
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
                    type="text"
                    placeholder="Filter..."
                    class="text-xs border border-brand-maroon-900/20 rounded px-1.5 py-1 bg-white w-full"
                  />
                </TableHead>
                <TableHead class="py-2">
                  <div class="flex gap-1 items-center">
                    <input
                      v-model="filterDateFrom"
                      type="date"
                      class="text-xs border border-brand-maroon-900/20 rounded px-1 py-1 bg-white"
                      title="From date"
                    />
                    <span class="text-brand-maroon-900/40">-</span>
                    <input
                      v-model="filterDateTo"
                      type="date"
                      class="text-xs border border-brand-maroon-900/20 rounded px-1 py-1 bg-white"
                      title="To date"
                    />
                  </div>
                </TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <!-- Empty state -->
              <TableRow v-if="audits.length === 0">
                <TableCell colspan="6" class="text-center py-8 text-brand-maroon-900/50">
                  <p v-if="hasActiveFilters">No changes match the current filters.</p>
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
                    <ChevronDownIcon
                      class="size-4 text-brand-maroon-900/50 transition-transform"
                      :class="{ 'rotate-180': expandedRows.has(audit.id) }"
                    />
                  </TableCell>
                  <TableCell>
                    <span
                      class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                      :class="audit.auditable_type === 'Deck'
                        ? 'bg-purple-100 text-purple-700'
                        : 'bg-blue-100 text-blue-700'"
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
                <TableRow v-if="expandedRows.has(audit.id)">
                  <TableCell colspan="6" class="bg-gray-50 p-0">
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
            v-if="auditHistory.meta.last_page > 1"
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
              @click="page++"
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
import { useDeckAuditHistoryQuery } from "@/queries/decks/useDeckAuditHistoryQuery";
import { computed, ref, watch } from "vue";
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
import type { AuditEvent, AuditableType } from "@/types";

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
  id: filterId.value || undefined,
  action: filterAction.value || undefined,
  user: filterUser.value || undefined,
  from: filterDateFrom.value || undefined,
  to: filterDateTo.value || undefined,
  sort: sortField.value,
  direction: sortDirection.value,
}));

// Reset to page 1 when filters or sort changes
watch(
  [filterObject, filterId, filterAction, filterUser, filterDateFrom, filterDateTo, sortField, sortDirection],
  () => {
    page.value = 1;
  }
);

const { data: deck } = useDeckByIdQuery(deckIdRef);
const { data: auditHistory } = useDeckAuditHistoryQuery(deckIdRef, queryParams);

// Computed helpers
const hasActiveFilters = computed(() =>
  Boolean(
    filterObject.value ||
    filterId.value ||
    filterAction.value ||
    filterUser.value ||
    filterDateFrom.value ||
    filterDateTo.value
  )
);

const audits = computed(() => auditHistory.value?.data ?? []);

// Actions
function clearFilters() {
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
