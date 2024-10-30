<template>
  <DropdownMenu>
    <DropdownMenuTrigger asChild>
      <Button variant="ghost" size="icon">
        <IconEllipsesVertical />
        <span class="sr-only">More</span>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuItem asChild v-if="deck.capabilities.canUpdate">
        <RouterLink :to="{ name: 'decks.edit', params: { deckId: deck.id } }">
          <IconPencil class="size-5 mr-4" />
          Edit Name
        </RouterLink>
      </DropdownMenuItem>
      <DropdownMenuItem asChild>
        <RouterLink
          :to="{ name: 'decks.practice', params: { deckId: deck.id } }"
        >
          <IconCirclePlay class="size-5 mr-4" />
          Practice
        </RouterLink>
      </DropdownMenuItem>
      <!-- <DropdownMenuItem asChild v-if="deck.capabilities.canUpdate">
        <RouterLink
          :to="{ name: 'decks.import', params: { deckId: deck.id } }"
          class="btn"
          v-if="deck.capabilities.canUpdate"
        >
          <IconUpload class="size-5 mr-4" />
          Import Cards
        </RouterLink>
      </DropdownMenuItem> -->
      <DropdownMenuItem
        v-if="deck.capabilities.canLeave"
        @click="handleLeaveDeck(deck.id)"
      >
        <IconExit class="size-5 mr-4" />
        Leave
      </DropdownMenuItem>

      <DropdownMenuItem
        class="text-red-700 hover:!bg-red-50 hover:!text-red-600"
        v-if="deck.capabilities.canDelete"
        @click="state.isConfirmDeleteModalOpen = true"
      >
        <IconTrash class="size-5 mr-4" />
        Delete
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
  <Modal
    title="Delete Deck"
    submitButtonLabel="Delete"
    @submit="
      () => {
        deleteDeck(deck.id);
        state.isConfirmDeleteModalOpen = false;
        router.push({ name: 'decks.index' });
      }
    "
    v-if="deck.capabilities.canDelete"
    :open="state.isConfirmDeleteModalOpen"
    @update:open="state.isConfirmDeleteModalOpen = false"
    triggerButtonVariant="none"
    submitButtonVariant="destructive"
    variant="danger"
  >
    <p>
      Are you sure you want to delete this deck? All members will lose access.
      This cannot be undone.
    </p>
  </Modal>
</template>
<script setup lang="ts">
import {
  IconEllipsesVertical,
  IconPencil,
  IconExit,
  IconTrash,
  IconUpload,
  IconCirclePlay,
} from "@/components/icons";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Button } from "@/components/ui/button";
import * as T from "@/types";
import { useDeleteDeckMutation } from "@/queries/decks";
import { useLeaveDeckMutation } from "@/queries/deckMemberships";
import Modal from "@/components/Modal.vue";
import { reactive } from "vue";
import { useRouter } from "vue-router";

const props = defineProps<{
  deck: T.Deck;
}>();

const state = reactive({
  isConfirmDeleteModalOpen: false,
});

const { mutate: deleteDeck } = useDeleteDeckMutation();
const { mutate: leaveDeck } = useLeaveDeckMutation();
const router = useRouter();

function handleLeaveDeck(deckId: number) {
  leaveDeck(deckId);
  router.push({ name: "decks.index" });
}
</script>
<style scoped></style>
