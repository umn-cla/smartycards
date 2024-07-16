<template>
  <AuthenticatedLayout>
    <div
      v-if="deck && deckMemberships"
      class="max-w-screen-lg mx-auto"
    >
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
          <p class="text-5xl text-black/25 font-bold">
            {{ deck.name }}
          </p>
        </div>
        <div class="flex gap-1">
          <Modal
            title="Share Link"
            noFooter
            triggerButtonVariant="outline"
          >
            <Label
              for="share-link"
              class="sr-only"
              >Share Link</Label
            >
            <p class="text-neutral-500 my-2">Anyone with this link can view the deck.</p>
            <CopyableInput
              :value="shareLink"
              id="share-link"
            />
          </Modal>
          <Modal
            title="Add User"
            @submit="handleShareDeck"
            submitButtonLabel="Share"
            :submitButtonDisabled="!form.user || !form.role"
          >
            <PersonSearch
              v-if="!form.user"
              @selectUser="handleSelectUser"
              label="Add User"
            />
            <div v-if="form.user">
              <p>{{ form.user.display_name }}</p>
              <p>{{ form.user.email }}</p>
              <Button
                type="button"
                @click="form.user = null"
              >
                Change
              </Button>
            </div>

            <div>
              <Label for="role">Role</Label>
              <select
                v-model="form.role"
                class="w-full"
              >
                <option value="">-</option>
                <option value="viewer">Viewer</option>
                <option value="editor">Editor</option>
              </select>
            </div>
          </Modal>
        </div>
      </header>
      <ul
        v-if="deckMemberships"
        class="flex flex-col gap-2"
      >
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
import { AuthenticatedLayout } from '@/layouts/AuthenticatedLayout';
import { reactive, computed } from 'vue';
import {
  useDeckMembershipsQuery,
  useCreateDeckMembershipMutation,
  useDeckShareLinkQuery,
} from '@/queries/deckMemberships';
import { useDeckByIdQuery } from '@/queries/decks';
import DeckMembership from '@/components/DeckMembership.vue';
import PersonSearch from '@/components/PersonSearch.vue';
import * as T from '@/types';
import config from '@/config';
import { IconChevronLeft } from '@/components/icons';
import { Label } from '@/components/ui/label';
import CopyableInput from '@/components/CopyableInput.vue';
import Modal from '@/components/Modal.vue';
import { Button } from '@/components/ui/button';

const props = defineProps<{
  deckId: number;
}>();

const form = reactive({
  user: null as T.LDAPUser | null,
  role: '' as 'viewer' | 'editor' | '',
});

const deckIdRef = computed(() => props.deckId);
const { data: deck } = useDeckByIdQuery(deckIdRef);
const { data: deckMemberships } = useDeckMembershipsQuery(deckIdRef);
const { data: apiShareUrl } = useDeckShareLinkQuery(deckIdRef);
const { mutate: shareDeck } = useCreateDeckMembershipMutation();

const shareLink = computed(() => {
  const url = new URL(`${config.client.baseUrl}/decks/${props.deckId}/invite`);
  url.searchParams.append('url', apiShareUrl.value ?? '');

  return url.toString();
});

function handleSelectUser(user: T.LDAPUser) {
  form.user = user;
}

function handleShareDeck() {
  if (!deckIdRef.value || !form.user || !form.role) {
    throw new Error(`deckId, email, and role are required`);
  }

  shareDeck({
    deckId: deckIdRef.value,
    umndid: form.user.umndid,
    role: form.role,
  });

  form.user = null;
  form.role = '';
}
</script>
<style scoped></style>
