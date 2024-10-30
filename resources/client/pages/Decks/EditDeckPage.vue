<template>
  <AuthenticatedLayout>
    <div class="max-w-screen-md mx-auto">
      <PageHeader
        class="mb-8"
        title="Edit Deck"
        :subtitle="deck?.name"
        backLabel="Decks"
        :backTo="{ name: 'decks.index' }"
      />
      <form @submit.prevent="handleSubmit" class="flex flex-col gap-2">
        <InputGroup label="Name" v-model="form.name" id="name" required />
        <InputGroup
          label="Description"
          v-model="form.description"
          id="description"
        />
        <div class="flex justify-end gap-2 mt-4">
          <Button asChild variant="secondary">
            <RouterLink :to="{ name: 'decks.index' }" class="btn"
              >Cancel</RouterLink
            >
          </Button>
          <Button type="submit">Save</Button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { computed, reactive, watch } from "vue";
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { useRouter } from "vue-router";
import InputGroup from "@/components/InputGroup.vue";
import { useAllDecksQuery, useUpdateDeckMutation } from "@/queries/decks";
import PageHeader from "@/components/PageHeader.vue";
import { Button } from "@/components/ui/button";

const props = defineProps<{
  deckId: number;
}>();

const form = reactive({
  name: "",
  description: "",
});

const router = useRouter();
const { data: allDecks } = useAllDecksQuery();

const deck = computed(() => {
  return allDecks.value?.find((deck) => deck.id === props.deckId);
});

watch(
  deck,
  (deck) => {
    if (deck) {
      form.name = deck.name;
      form.description = deck.description;
    }
  },
  { immediate: true },
);

const { mutate: updateDeck } = useUpdateDeckMutation();

async function handleSubmit() {
  if (!deck.value) {
    throw new Error(`Deck with id ${props.deckId} not found`);
  }

  updateDeck(
    {
      ...deck.value,
      ...form,
    },
    {
      onSuccess: () => {
        router.push({ name: "decks.index" });
      },
    },
  );
}
</script>
<style scoped></style>
