<template>
  <DropdownMenu>
    <DropdownMenuTrigger asChild>
      <Button variant="ghost" size="icon" data-cy="more-card-actions-button">
        <IconEllipsesVertical />
        <span class="sr-only">More actions</span>
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuItem asChild v-if="canEdit">
        <RouterLink :to="{ name: 'cards.edit', params: { cardId: card.id } }">
          <IconPencil class="size-5 mr-4" />
          Edit Card
        </RouterLink>
      </DropdownMenuItem>

      <DropdownMenuItem
        class="text-red-700 hover:!bg-red-50 hover:!text-red-600"
        v-if="canDelete"
        @click="state.isConfirmDeleteModalOpen = true"
      >
        <IconTrash class="size-5 mr-4" />
        Delete Card
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
  <Modal
    title="Delete Card"
    submitButtonLabel="Delete"
    @submit="
      () => {
        $emit('delete', card);
        state.isConfirmDeleteModalOpen = false;
      }
    "
    v-if="canDelete"
    :open="state.isConfirmDeleteModalOpen"
    @update:open="state.isConfirmDeleteModalOpen = false"
    triggerButtonVariant="none"
    submitButtonVariant="destructive"
    variant="danger"
  >
    <p>Are you sure you want to delete this card?</p>
  </Modal>
</template>
<script setup lang="ts">
import {
  IconEllipsesVertical,
  IconPencil,
  IconTrash,
} from "@/components/icons";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Button } from "@/components/ui/button";
import * as T from "@/types";
import Modal from "@/components/Modal.vue";
import { reactive } from "vue";

defineProps<{
  canEdit: boolean;
  canDelete: boolean;
  card: T.Card;
}>();

const state = reactive({
  isConfirmDeleteModalOpen: false,
});

defineEmits<{
  (eventName: "delete", card: T.Card): void;
}>();
</script>
<style scoped></style>
