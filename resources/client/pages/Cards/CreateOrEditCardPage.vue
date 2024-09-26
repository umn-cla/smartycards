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
        <div v-if="deck">
          <header class="mb-8 flex gap-8 flex-wrap justify-between items-start">
            <div>
              <PageTitle>{{ isCreateMode ? "Create" : "Edit" }} Card</PageTitle>
              <PageSubtitle>{{ deck?.name }}</PageSubtitle>
            </div>
            <div class="flex gap-2 flex-wrap">
              <Button asChild variant="ghost">
                <RouterLink
                  :to="{ name: 'decks.show', params: { deckId } }"
                  class="btn"
                >
                  Cancel
                </RouterLink>
              </Button>
              <Button
                v-if="isCreateMode"
                @click="handleSave({ saveAndAddAnother: true })"
                variant="outline"
                class="bg-transparent border-brand-maroon-800"
                >Create + Another</Button
              >
              <Button @click="handleSave">
                {{ isCreateMode ? "Create" : "Save" }}
              </Button>
            </div>
          </header>
          <div class="my-4 grid sm:grid-cols-2 gap-4 mb-12">
            <div v-for="side in ['front', 'back']">
              <CardSideInput
                :id="`${deckId}-${side}`"
                v-model="form[side]"
                :label="capitalize(side)"
              />
              <p
                class="text-sm text-red-500"
                v-show="hasAttemptedSave && errors[side]"
              >
                {{ errors[side] }}
              </p>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { computed, reactive, watch, ref, onMounted, capitalize } from "vue";
import {
  useUpdateCardMutation,
  useCreateCardMutation,
  useCardByIdQuery,
} from "@/queries/cards";
import { useDeckByIdQuery } from "@/queries/decks";
import { useRouter } from "vue-router";
import * as T from "@/types";
import CardSideInput from "@/components/CardSideInput.vue";
import { IconChevronLeft } from "@/components/icons";
import { Button } from "@/components/ui/button";
import PageTitle from "@/components/PageTitle.vue";
import PageSubtitle from "@/components/PageSubtitle.vue";

const props = defineProps<{
  deckId: number;
  cardId?: number | null;
}>();

const form = reactive<{
  front: T.ContentBlock[];
  back: T.ContentBlock[];
}>({
  front: [],
  back: [],
});

const isCreateMode = computed(() => !props.cardId);
const deckIdRef = computed(() => props.deckId);
const cardIdRef = computed(() => props.cardId ?? null);
const { data: deck } = useDeckByIdQuery(deckIdRef);
const { data: card } = useCardByIdQuery(cardIdRef);

function createTextContentBlock(): T.TextContentBlock {
  return {
    id: crypto.randomUUID(),
    type: "text",
    content: "",
    meta: null,
  };
}

onMounted(() => {
  if (!isCreateMode.value) {
    return;
  }

  // add two text content blocks
  form.front = [createTextContentBlock()];
  form.back = [createTextContentBlock()];
});

watch(
  card,
  () => {
    if (card.value) {
      form.front = card.value.front;
      form.back = card.value.back;
    }
  },
  { immediate: true },
);

const { mutate: updateCard } = useUpdateCardMutation();
const { mutate: createCard } = useCreateCardMutation();

const router = useRouter();

const hasAttemptedSave = ref(false);
const errors = computed(() => {
  return {
    front: form.front.length < 1 && "Front side must have some content blocks",
    back: form.back.length < 1 && "Back side must have some content blocks",
  };
});

const hasErrors = computed(() => {
  return Object.values(errors.value).some((error) => !!error);
});

function handleSave({ saveAndAddAnother = false } = {}) {
  hasAttemptedSave.value = true;

  if (hasErrors.value) {
    return;
  }

  const onSuccess = () => {
    if (saveAndAddAnother) {
      form.front = [createTextContentBlock()];
      form.back = [createTextContentBlock()];
      return;
    }

    router.push({ name: "decks.show", params: { deckId: props.deckId } });
  };

  if (isCreateMode.value) {
    createCard(
      {
        deck_id: props.deckId,
        front: form.front,
        back: form.back,
      },
      { onSuccess },
    );
    return;
  }

  if (!card.value) {
    throw new Error(
      `Card with id ${props.cardId} not found in deck with id ${props.deckId}`,
    );
  }

  updateCard(
    {
      ...card.value,
      front: form.front,
      back: form.back,
    },
    { onSuccess },
  );
}
</script>
<style scoped></style>
