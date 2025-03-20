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
            <div v-for="side in ['front', 'back'] as const" :key="side">
              <CardSideContextProvider :deck="deck" :cardSideName="side">
                <CardSideInput
                  :id="`${deckId}-${side}`"
                  v-model="form[side]"
                  :label="capitalize(side)"
                  :data-cy="`${side}-side-input`"
                  @dragHandle:left="(block) => handleSwapBlockSide(block, side)"
                  @dragHandle:right="
                    (block) => handleSwapBlockSide(block, side)
                  "
                  @dragHandle:up="(block) => moveBlock(block, side, 'up')"
                  @dragHandle:down="(block) => moveBlock(block, side, 'down')"
                />
              </CardSideContextProvider>
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
  nextTick,
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
import { useDeckTTSConfig } from "@/composables/useDeckTTSConfig";
import { makeContentBlock } from "@/lib/makeContentBlock";
import { IS_DECK_TTS_ENABLED_INJECTION_KEY } from "@/constants";
import { focusBlockDragHandle } from "@/lib/blockEditorHelpers";
import { useAnnouncer } from "@vue-a11y/announcer";
import invariant from "tiny-invariant";
import { clamp, move } from "ramda";
import CardSideContextProvide from "@/components/CardSideContextProvider.vue";
import CardSideContextProvider from "@/components/CardSideContextProvider.vue";

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

const announcer = useAnnouncer();

function moveBlock(block, side: "front" | "back", direction: "up" | "down") {
  const delta = direction === "up" ? -1 : 1;

  const fromIndex = form[side].findIndex((b) => b.id === block.id);
  invariant(
    "fromIndex >= 0",
    `Index of block with id ${block.id} not found in modelValue`,
  );

  const toIndex = clamp(0, form[side].length - 1, fromIndex + delta);

  if (fromIndex === toIndex) {
    announcer.assertive(`Block already at position ${toIndex + 1}.`);
    return;
  }

  form[side] = move(fromIndex, toIndex, form[side]);

  nextTick(() => {
    focusBlockDragHandle(block);
    announcer.assertive(`Moved block ${direction} to position ${toIndex + 1}`);
  });
}

function handleSwapBlockSide(
  block: T.ContentBlock,
  currentSide: "front" | "back",
) {
  const otherSide = currentSide === "front" ? "back" : "front";

  // remove block from current side
  form[currentSide] = form[currentSide].filter((b) => b.id !== block.id);

  // add block to other side
  form[otherSide] = [...form[otherSide], block];

  nextTick(() => {
    // focus the block that was just moved
    focusBlockDragHandle(block);
    announcer.assertive(
      `Moved block to ${capitalize(otherSide)} side, position ${form[otherSide].length}`,
    );
  });
}

// provide info about TTS to any card blocks that need it
const { isTTSEnabled: isDeckTTSEnabled } = useDeckTTSConfig(deck);
provide(IS_DECK_TTS_ENABLED_INJECTION_KEY, isDeckTTSEnabled);
</script>
<style scoped></style>
