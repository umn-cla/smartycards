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
              <TableRow v-if="filteredAudits.length === 0">
                <TableCell colspan="5" class="text-center py-8 text-brand-maroon-900/50">
                  <p v-if="hasActiveFilters">No changes match the current filters.</p>
                  <p v-else>No edit history found for this deck.</p>
                </TableCell>
              </TableRow>
              <!-- Data rows -->
              <template v-for="audit in sortedAudits" :key="audit.id">
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
                  <TableCell colspan="5" class="bg-gray-50 p-0">
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
import { computed, ref } from "vue";
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
import type { AuditRecord, AuditEvent, AuditableType } from "@/types";

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);
const page = ref(1);

const { data: deck } = useDeckByIdQuery(deckIdRef);
const { data: auditHistory } = useDeckAuditHistoryQuery(deckIdRef, page);

// Filters
const filterObject = ref<AuditableType | "">("");
const filterAction = ref<AuditEvent | "">("");
const filterUser = ref("");
const filterDateFrom = ref("");
const filterDateTo = ref("");

const hasActiveFilters = computed(() =>
  Boolean(
    filterObject.value ||
    filterAction.value ||
    filterUser.value ||
    filterDateFrom.value ||
    filterDateTo.value
  )
);

function clearFilters() {
  filterObject.value = "";
  filterAction.value = "";
  filterUser.value = "";
  filterDateFrom.value = "";
  filterDateTo.value = "";
}

// Sorting
type SortField = "auditable_type" | "event" | "user" | "created_at";
type SortDirection = "asc" | "desc";

const sortField = ref<SortField>("created_at");
const sortDirection = ref<SortDirection>("desc");

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

// Filtered audits
const filteredAudits = computed((): AuditRecord[] => {
  if (!auditHistory.value?.data) return [];

  return auditHistory.value.data.filter((audit) => {
    if (filterObject.value && audit.auditable_type !== filterObject.value) {
      return false;
    }
    if (filterAction.value && audit.event !== filterAction.value) {
      return false;
    }
    if (filterUser.value) {
      const userName = audit.user?.name?.toLowerCase() ?? "";
      if (!userName.includes(filterUser.value.toLowerCase())) {
        return false;
      }
    }
    if (filterDateFrom.value || filterDateTo.value) {
      const auditDate = new Date(audit.created_at);
      if (filterDateFrom.value) {
        const fromDate = new Date(filterDateFrom.value);
        fromDate.setHours(0, 0, 0, 0);
        if (auditDate < fromDate) {
          return false;
        }
      }
      if (filterDateTo.value) {
        const toDate = new Date(filterDateTo.value);
        toDate.setHours(23, 59, 59, 999);
        if (auditDate > toDate) {
          return false;
        }
      }
    }
    return true;
  });
});

// Sorted audits
const sortedAudits = computed((): AuditRecord[] => {
  const audits = [...filteredAudits.value];

  audits.sort((a, b) => {
    let comparison = 0;

    switch (sortField.value) {
      case "auditable_type":
        comparison = a.auditable_type.localeCompare(b.auditable_type);
        break;
      case "event":
        comparison = a.event.localeCompare(b.event);
        break;
      case "user":
        comparison = (a.user?.name ?? "").localeCompare(b.user?.name ?? "");
        break;
      case "created_at":
        comparison = new Date(a.created_at).getTime() - new Date(b.created_at).getTime();
        break;
    }

    return sortDirection.value === "asc" ? comparison : -comparison;
  });

  return audits;
});

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
