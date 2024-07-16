<template>
  <AuthenticatedLayout>
    <div class="px-4 pb-12">
      <nav class="mb-4">
        <RouterLink
          :to="{ name: 'decks.show', params: { deckId: props.deckId } }"
          class="flex gap-2 items-center"
        >
          <IconChevronLeft class="size-5" />
          {{ deck?.name }}
        </RouterLink>
      </nav>

      <Transition name="fade">
        <div v-if="deck && card">
          <header
            class="mb-8 flex gap-8 flex-wrap justify-between items-start sticky top-0 bg-white z-10"
          >
            <div>
              <PageTitle>Edit Card</PageTitle>
              <PageSubtitle>{{ deck?.name }}</PageSubtitle>
            </div>
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
              <Button @click="handleSave"> Save </Button>
            </div>
          </header>

          <div class="my-4 grid sm:grid-cols-2 gap-4">
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
        </div>
      </Transition>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { AuthenticatedLayout } from '@/layouts/AuthenticatedLayout';
import { computed, reactive, watch } from 'vue';
import { useUpdateCardMutation } from '@/queries/cards';
import { useDeckByIdQuery } from '@/queries/decks';
import { useRouter } from 'vue-router';
import type { CardSide } from '@/types';
import CardSideInput from '@/components/CardSideInput/CardSideInput.vue';
import { IconChevronLeft } from '@/components/icons';
import { Button } from '@/components/ui/button';
import ColHeader from '@/components/ColHeader.vue';
import PageTitle from '@/components/PageTitle.vue';
import PageSubtitle from '@/components/PageSubtitle.vue';

const props = defineProps<{
  deckId: number;
  cardId: number;
}>();

const form = reactive<{
  front: CardSide | null;
  back: CardSide | null;
}>({
  front: null,
  back: null,
});

const deckIdRef = computed(() => props.deckId);
const { data: deck, status, error } = useDeckByIdQuery(deckIdRef);
const card = computed(() => deck.value?.cards.find((card) => card.id === props.cardId));

watch(
  card,
  (card) => {
    if (card) {
      form.front = card.front;
      form.back = card.back;
    }
  },
  { immediate: true }
);

const { mutate: updateCard } = useUpdateCardMutation();
const router = useRouter();

function handleSave() {
  if (!card.value) {
    throw new Error(`Card with id ${props.cardId} not found in deck with id ${props.deckId}`);
  }

  if (!form.front || !form.back) {
    throw new Error('Front and back are required');
  }

  updateCard(
    {
      ...card.value,
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
