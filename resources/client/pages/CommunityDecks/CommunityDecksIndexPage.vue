<template>
  <AuthenticatedLayout>
    <header class="text-center">
      <h1 class="text-3xl font-black text-brand-maroon-800 mb-4">
        Community Decks
      </h1>

      <p>SmartyCards shared with everyone.</p>
    </header>

    <Transition name="fade">
      <section v-if="!isLoading" class="my-8">
        <ul v-if="communityDecks.length" class="card-grid">
          <li v-for="deck in communityDecks" :key="deck.id">
            <article
              class="bg-brand-oatmeal-50 p-4 rounded-lg border border-brand-oatmeal-300/75"
            >
              <h3 class="text-lg font-bold">{{ deck.name }}</h3>
              <p class="text-sm text-stone-400 mb-4">{{ deck.description }}</p>

              <div v-if="deck.current_user_role" class="flex gap-2 justify-end">
                <Button asChild variant="secondary">
                  <RouterLink
                    :to="{
                      name: 'decks.show',
                      params: { deckId: deck.id },
                    }"
                    class="btn btn-primary"
                    >View</RouterLink
                  >
                </Button>
                <Button
                  v-if="deck.capabilities.canLeave"
                  @click="leaveDeck(deck.id)"
                  variant="destructive"
                  >Leave</Button
                >
              </div>

              <div v-else class="flex gap-2 justify-end">
                <Button asChild variant="secondary">
                  <RouterLink
                    :to="{
                      name: 'community.decks.show',
                      params: { deckId: deck.id },
                    }"
                    class="btn btn-primary"
                    >Preview</RouterLink
                  >
                </Button>
                <Button
                  v-if="deck.capabilities.canJoinAsViewer"
                  @click="joinDeck(deck.id)"
                  >Join</Button
                >
              </div>
            </article>
          </li>
        </ul>
        <p v-else class="text-center italic text-stone-400">None yet.</p>
      </section>
    </Transition>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import AuthenticatedLayout from "@/layouts/AuthenticatedLayout/AuthenticatedLayout.vue";
import { useCommunityDecksQuery } from "@/queries/community/useCommunityDecksQuery";
import { Button } from "@/components/ui/button";
import { useJoinCommunityDeckMutation } from "@/queries/community";
import { useLeaveDeckMutation } from "@/queries/deckMemberships";

const { data: communityDecks, isLoading } = useCommunityDecksQuery();
const { mutate: joinDeck } = useJoinCommunityDeckMutation();
const { mutate: leaveDeck } = useLeaveDeckMutation();
</script>
<style scoped></style>
