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
                data-cy="create-and-another-card-button"
                >Create + Another</Button
              >
              <Button @click="handleSave" data-cy="save-card-button">
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
                :data-cy="`${side}-side-input`"
              />
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import {
  computed,
  reactive,
  watch,
  ref,
  onMounted,
  capitalize,
  provide,
} from "vue";
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
import { useIsDeckTTSEnabled } from "@/composables/useIsDeckTTSEnabled";
import { makeContentBlock } from "@/lib/makeContentBlock";
import { IS_DECK_TTS_ENABLED_INJECTION_KEY } from "@/constants";

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

onMounted(() => {
  if (!isCreateMode.value) {
    return;
  }

  // add two text content blocks
  form.front = [makeContentBlock("text")];
  form.back = [makeContentBlock("text")];
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

function removeEmptyBlocks(blocks: T.ContentBlock[]) {
  return blocks.filter((block) => {
    if (typeof block.content === "string") {
      return block.content.trim().length > 0;
    }

    return true;
  });
}

function handleSave({ saveAndAddAnother = false } = {}) {
  hasAttemptedSave.value = true;

  form.back = removeEmptyBlocks(form.back);
  form.front = removeEmptyBlocks(form.front);

  // snapshot the types of the front and back blocks before saving
  // so that we can use them as the default for the next card
  // if user clicks "Create + Another"
  const frontTypes = form.front.map((block) => block.type);
  const backTypes = form.back.map((block) => block.type);

  const onSuccess = () => {
    if (saveAndAddAnother) {
      // when saving and adding another, use the previous card's front
      // and back types as the default for the new card. If there are no types
      // (e.g. the user deleted all content), default to text
      form.front = frontTypes.length
        ? frontTypes.map(makeContentBlock)
        : [makeContentBlock("text")];
      form.back = backTypes.length
        ? backTypes.map(makeContentBlock)
        : [makeContentBlock("text")];
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

// provide info about TTS to any card blocks that need it
const { isDeckTTSEnabled } = useIsDeckTTSEnabled(deck);
provide(IS_DECK_TTS_ENABLED_INJECTION_KEY, isDeckTTSEnabled);
</script>
<style scoped></style>
