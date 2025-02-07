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

      <div class="flex flex-col gap-8">
        <section
          class="bg-brand-oatmeal-50 p-4 rounded-md border border-brand-maroon-900/10"
        >
          <h3 class="text-xl font-bold mb-4">Invite</h3>
          <p class="mb-4">
            Share the link below to invite others to this deck with view or edit
            permissions.
          </p>
          <ShareLink
            :url="shareViewUrl ?? ''"
            type="view"
            @regenerate="regenerateViewLink"
            class="mb-4"
          />
          <ShareLink
            :url="shareEditUrl ?? ''"
            type="edit"
            @regenerate="regenerateEditLink"
          />
        </section>

        <!-- Embed links -->
        <section
          class="bg-brand-oatmeal-50 p-4 rounded-md border border-brand-maroon-900/10"
        >
          <h3 class="text-xl font-bold mb-4">Embed Deck</h3>
          <p class="mb-4">
            Copy the following code to embed this deck on your website. Viewers
            will automatically be added with `view` permissions.
          </p>

          <EmbedDeckSection :deck="deck" />
        </section>

        <section
          class="bg-brand-oatmeal-50 p-4 rounded-md border border-brand-maroon-900/10"
        >
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
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { computed } from "vue";
import {
  useDeckMembershipsQuery,
  useDeckShareLinkQuery,
  useRegenerateDeckShareLinkMutation,
} from "@/queries/deckMemberships";
import { useDeckByIdQuery } from "@/queries/decks";
import DeckMembership from "@/components/DeckMembership.vue";
import { IconChevronLeft } from "@/components/icons";
import ShareLink from "@/components/ShareLink.vue";
import EmbedDeckSection from "@/components/EmbedDeckSection.vue";

const props = defineProps<{
  deckId: number;
}>();

const deckIdRef = computed(() => props.deckId);
const { data: deck } = useDeckByIdQuery(deckIdRef);
const { data: deckMemberships } = useDeckMembershipsQuery(deckIdRef);
const { data: shareViewUrl } = useDeckShareLinkQuery(deckIdRef, "view");
const { data: shareEditUrl } = useDeckShareLinkQuery(deckIdRef, "edit");
const { mutate: regenerateViewLink } = useRegenerateDeckShareLinkMutation(
  deckIdRef,
  "view",
);
const { mutate: regenerateEditLink } = useRegenerateDeckShareLinkMutation(
  deckIdRef,
  "edit",
);
</script>
<style scoped></style>
