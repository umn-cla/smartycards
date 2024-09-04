<template>
  <AuthenticatedLayout>
    <div v-if="deck && deckMemberships" class="max-w-screen-lg mx-auto">
      <nav class="mb-4">
        <RouterLink
          :to="{ name: 'decks.show', params: { deckId: deck.id } }"
          class="flex gap-2 items-center"
        >
          <IconChevronLeft class="size-5" />
          {{ deck.name }}
        </RouterLink>
      </nav>
      <header class="mb-8 flex gap-8 flex-wrap justify-between items-start">
        <div>
          <h1 class="text-5xl font-bold">Share Deck</h1>
          <p class="text-5xl text-brand-maroon-800/25 font-bold">
            {{ deck.name }}
          </p>
        </div>
        <div class="flex gap-1">
          <Modal title="Share Link" noFooter triggerButtonVariant="outline">
            <div>
              <Label
                for="share-view-link"
                class="text-brand-maroon-800 flex gap-1 mt-4 mb-2"
              >
                View
                <small class="text-neutral-500 font-normal text-xs">
                  Any user with this link will be added with view permissions.
                </small>
              </Label>
              <CopyableInput :value="shareViewLink" id="share-view-link" />
            </div>

            <div>
              <Label
                for="share-edit-link"
                class="text-brand-maroon-800 flex gap-1 mt-4 mb-2"
              >
                Edit
                <small class="text-neutral-500 font-normal text-xs">
                  Any user with this link will be added with edit permissions.
                </small>
              </Label>
              <CopyableInput :value="shareEditLink" id="share-edit-link" />
            </div>
          </Modal>
        </div>
      </header>
      <ul v-if="deckMemberships" class="flex flex-col gap-2">
        <DeckMembership
          v-for="membership in deckMemberships"
          :key="membership.id"
          :membership="membership"
        />
      </ul>
      <p v-else>No one yet</p>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { computed } from "vue";
import {
  useDeckMembershipsQuery,
  useDeckShareViewLinkQuery,
  useDeckShareEditLinkQuery,
} from "@/queries/deckMemberships";
import { useDeckByIdQuery } from "@/queries/decks";
import DeckMembership from "@/components/DeckMembership.vue";
import * as T from "@/types";
import config from "@/config";
import { IconChevronLeft } from "@/components/icons";
import { Label } from "@/components/ui/label";
import CopyableInput from "@/components/CopyableInput.vue";
import Modal from "@/components/Modal.vue";
import { Button } from "@/components/ui/button";

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);
const { data: deck } = useDeckByIdQuery(deckIdRef);
const { data: deckMemberships } = useDeckMembershipsQuery(deckIdRef);
const { data: apiShareViewUrl } = useDeckShareViewLinkQuery(deckIdRef);
const { data: apiShareEditUrl } = useDeckShareEditLinkQuery(deckIdRef);

const shareViewLink = computed(() => {
  const url = new URL(`${config.client.baseUrl}/decks/${props.deckId}/invite`);
  url.searchParams.append("url", apiShareViewUrl.value ?? "");

  return url.toString();
});

const shareEditLink = computed(() => {
  const url = new URL(`${config.client.baseUrl}/decks/${props.deckId}/invite`);
  url.searchParams.append("url", apiShareEditUrl.value ?? "");

  return url.toString();
});
</script>
<style scoped></style>
