<template>
  <AuthenticatedLayout>
    <PageHeader
      v-if="deck"
      :backLabel="deck.name"
      :backTo="{ name: 'decks.show', params: { deckId } }"
      :title="`Create Card`"
      :subtitle="deck.name"
    >
      <div class="flex gap-2">
        <Button
          asChild
          variant="secondary"
        >
          <RouterLink
            :to="{ name: 'decks.show', params: { deckId } }"
            class="btn"
          >
            Cancel
          </RouterLink>
        </Button>
        <Button @click="handleSubmit"> Save </Button>
      </div>
    </PageHeader>

    <div class="my-4 grid grid-cols-2 gap-4">
      <div>
        <ColHeader>Front</ColHeader>
        <CardSideInput
          v-model="form.front"
          label="Front"
          id="front"
          :deckId="deckId"
        />
      </div>
      <div>
        <ColHeader>Back</ColHeader>
        <CardSideInput
          v-model="form.back"
          label="Back"
          id="back"
          :deckId="deckId"
        />
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { AuthenticatedLayout } from '@/layouts/AuthenticatedLayout';
import { reactive, computed } from 'vue';
import { useCreateCardMutation } from '@/queries/cards';
import CardSideInput from '@/components/CardSideInput/CardSideInput.vue';
import { useRouter } from 'vue-router';
import { useDeckByIdQuery } from '@/queries/decks';
import { Button } from '@/components/ui/button';
import ColHeader from '@/components/ColHeader.vue';
import * as T from '@/types';
import PageHeader from '@/components/PageHeader.vue';

const props = defineProps<{
  deckId: number;
}>();

function generateCardSide(): T.CardSide {
  return {
    type: 'text',
    content: '',
    meta: {
      hint: '',
      alt: '',
    },
  };
}

const deckIdRef = computed(() => props.deckId);
const { data: deck } = useDeckByIdQuery(deckIdRef);

const form = reactive<{
  front: T.CardSide;
  back: T.CardSide;
}>({
  front: generateCardSide(),
  back: generateCardSide(),
});

const { mutate: createCard } = useCreateCardMutation();
const router = useRouter();

function handleSubmit() {
  createCard(
    {
      deck_id: props.deckId,
      front: form.front,
      back: form.back,
    },
    {
      onSuccess() {
        router.push({ name: 'decks.show', params: { deckId: props.deckId } });
      },
    }
  );
}
</script>
<style scoped></style>
