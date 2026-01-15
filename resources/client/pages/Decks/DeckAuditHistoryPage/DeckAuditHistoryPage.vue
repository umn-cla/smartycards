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

        <div v-if="auditHistory && auditHistory.data.length > 0" class="space-y-4">
          <AuditTimelineItem
            v-for="audit in auditHistory.data"
            :key="audit.id"
            :audit="audit"
          />

          <div
            v-if="auditHistory.meta.last_page > 1"
            class="mt-8 flex items-center justify-center gap-4"
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

        <div
          v-else-if="auditHistory"
          class="text-center py-12 text-brand-maroon-900/50"
        >
          <p>No edit history found for this deck.</p>
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
import Tuple from "@/components/Tuple.vue";
import DeckContextProvider from "@/components/DeckContextProvider.vue";
import AuditTimelineItem from "./AuditTimelineItem.vue";

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);
const page = ref(1);

const { data: deck } = useDeckByIdQuery(deckIdRef);
const { data: auditHistory } = useDeckAuditHistoryQuery(deckIdRef, page);
</script>
