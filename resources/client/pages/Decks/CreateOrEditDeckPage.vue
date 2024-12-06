<template>
  <AuthenticatedLayout>
    <div class="max-w-screen-md mx-auto">
      <PageHeader
        class="mb-8"
        :title="isCreateMode ? 'Create Deck' : 'Edit Deck'"
        backLabel="Decks"
        :backTo="{ name: 'decks.index' }"
      />
      <form @submit.prevent="handleSubmit" class="flex flex-col gap-4 mt-4">
        <InputGroup label="Name" v-model="form.name" id="name" required />
        <InputGroup
          label="Description"
          v-model="form.description"
          id="description"
        />
        <div class="flex items-center justify-end py-4 gap-2">
          <Button asChild variant="secondary">
            <RouterLink :to="{ name: 'decks.index' }"> Cancel </RouterLink>
          </Button>
          <Button type="submit" :disabled="!form.name">{{
            isCreateMode ? "Create Deck" : "Save"
          }}</Button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { reactive, computed, watch } from "vue";
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import {
  useCreateDeckMutation,
  useUpdateDeckMutation,
  useDeckByIdQuery,
} from "@/queries/decks";
import { useRouter } from "vue-router";
import InputGroup from "@/components/InputGroup.vue";
import PageHeader from "@/components/PageHeader.vue";
import { Button } from "@/components/ui/button";

const props = defineProps<{
  deckId: number | null;
}>();

const form = reactive({
  name: "",
  description: "",
});

const isCreateMode = computed(() => props.deckId === null);
const router = useRouter();
const deckIdRef = computed(() => props.deckId);
const { data: deck } = useDeckByIdQuery(deckIdRef);
const { mutate: createDeck } = useCreateDeckMutation();
const { mutate: updateDeck } = useUpdateDeckMutation();

watch(
  deck,
  () => {
    if (deck.value) {
      form.name = deck.value.name;
      form.description = deck.value.description;
    }
  },
  { immediate: true },
);

async function handleSubmit() {
  if (isCreateMode.value) {
    createDeck(form, {
      onSuccess: () => {
        router.push({ name: "decks.index" });
      },
    });
    return;
  }

  if (!props.deckId) {
    throw new Error("Deck ID is required for editing");
  }

  updateDeck(
    { id: props.deckId, ...form },
    {
      onSuccess: () => {
        router.push({ name: "decks.index" });
      },
    },
  );
}
</script>
<style scoped></style>
