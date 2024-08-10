<template>
  <div class="border py-4 px-2 flex flex-col rounded-lg gap-2">
    <Button
      asChild
      variant="ghost"
    >
      <RouterLink
        :to="{ name: 'cards.edit', params: { deckId: card.deck_id, cardId: card.id } }"
        variant="secondary"
      >
        <Icons.IconPencil class="size-6" />
        <span class="sr-only">Edit</span>
      </RouterLink>
    </Button>

    <Modal
      title="Delete Card"
      @submit="$emit('delete')"
      variant="danger"
      submitButtonVariant="destructive"
      submitButtonLabel="Delete"
    >
      <template #trigger>
        <Button variant="ghost">
          <Icons.IconTrash class="size-6" />
          <span class="sr-only">Delete</span>
        </Button>
      </template>
      <p>Are you sure you want to delete this card?</p>
    </Modal>
  </div>
</template>
<script setup lang="ts">
import { Button } from '@/components/ui/button';
import * as T from '@/types';
import * as Icons from './icons';
import Modal from './Modal.vue';

defineProps<{
  card: T.Card;
}>();

defineEmits<{
  (eventName: 'delete'): void;
}>();
</script>
<style scoped></style>
