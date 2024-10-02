<template>
  <AuthenticatedLayout>
    <div v-if="deck && deckMemberships" class="max-w-screen-md mx-auto">
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
      </header>

      <section class="border-b border-brand-maroon-900/25 pb-8 mb-8">
        <h3 class="text-xl font-bold mb-4">Invite</h3>
        <p>Share the link below to invite others to this deck.</p>
        <div class="flex gap-4 items-baseline flex-wrap">
          <Label
            for="share-view-link"
            class="text-brand-maroon-800 flex gap-1 mt-4 mb-2 w-10"
          >
            View
          </Label>
          <CopyableInput
            :value="shareViewUrl ?? ''"
            id="share-view-link"
            class="flex-1"
          />
        </div>
        <div class="flex gap-4 items-baseline mt-4 flex-wrap">
          <Label
            for="share-edit-link"
            class="text-brand-maroon-800 flex gap-1 mt-4 mb-2 w-10"
          >
            Edit
          </Label>
          <CopyableInput
            :value="shareEditUrl ?? ''"
            id="share-edit-link"
            class="flex-1"
          />
        </div>
      </section>
      <section>
        <h2 class="text-xl font-bold mb-4">Members</h2>
        <ul v-if="deckMemberships" class="flex flex-col gap-2">
          <DeckMembership
            v-for="membership in deckMemberships"
            :key="membership.id"
            :membership="membership"
          />
        </ul>
        <p v-else>No one yet</p>
      </section>
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
const { data: shareViewUrl } = useDeckShareViewLinkQuery(deckIdRef);
const { data: shareEditUrl } = useDeckShareEditLinkQuery(deckIdRef);
</script>
<style scoped></style>
