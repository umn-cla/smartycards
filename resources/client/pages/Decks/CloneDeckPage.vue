<template>
  <AuthenticatedLayout>
    <div class="max-w-screen-md mx-auto">
      <PageHeader
        title="Clone Deck"
        :subtitle="deck?.name"
        :backTo="backTo"
        class="mb-8"
      />

      <main>
        <form
          v-if="deck"
          @submit.prevent="handleClone"
          class="bg-brand-maroon-800/5 p-4 rounded-lg flex flex-col gap-4"
        >
          <div class="text-lg">
            <p>
              This will <b>clone</b> the <b>{{ deck?.name }}</b> deck to create
              a new deck with the same cards. No members or study history will
              be copied.
            </p>
          </div>

          <InputGroup id="cloned-deck-name" label="Name" v-model="form.name" />
          <InputGroup
            id="cloned-deck-description"
            label="Description"
            v-model="form.description"
          />

          <div class="flex gap-2 items-center">
            <Switch
              id="tts-enabled"
              :checked="form.isTTSEnabled"
              @update:checked="form.isTTSEnabled = $event"
              label="Enable TTS"
            />
            <Label for="tts-enabled">
              Enable Text-to-Speech
              <HintTooltip>
                When enabled, a button to read text aloud will be available.
                This is useful for learning pronunciation.
              </HintTooltip>
            </Label>
          </div>

          <div class="flex items-center justify-end gap-2">
            <Button asChild variant="secondary">
              <RouterLink :to="backTo"> Cancel </RouterLink>
            </Button>
            <Button type="submit">Clone</Button>
          </div>
        </form>
      </main>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import PageHeader from "@/components/PageHeader.vue";
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { computed, reactive, watch } from "vue";
import { useDeckByIdQuery } from "@/queries/decks";
import { useRouter } from "vue-router";
import InputGroup from "@/components/InputGroup.vue";
import { Button } from "@/components/ui/button";
import Switch from "@/components/ui/switch/Switch.vue";
import Label from "@/components/ui/label/Label.vue";
import HintTooltip from "@/components/HintTooltip.vue";
import { useCloneDeckMutation } from "@/queries/decks/useCloneDeckMutation";
import invariant from "tiny-invariant";

const props = defineProps<{
  deckId: number | null;
}>();

const form = reactive({
  name: "",
  description: "",
  isTTSEnabled: false,
});

const deckIdRef = computed(() => props.deckId);
const { data: deck } = useDeckByIdQuery(deckIdRef);
const { mutate: cloneDeck } = useCloneDeckMutation();

const router = useRouter();

const backTo = computed(() => {
  return String(router.options.history.state.back) || { name: "decks.index" };
});

// once we have deck data, use it it populate the form
// then stop watching
const unwatchDeck = watch(
  deck,
  () => {
    if (!deck.value) {
      return;
    }
    form.name = `Copy of ${deck.value.name}`;
    form.description = deck.value.description;
    form.isTTSEnabled = deck.value.is_tts_enabled;

    unwatchDeck();
  },
  {
    immediate: true,
  },
);

async function handleClone() {
  invariant(deckIdRef.value, "Deck ID is required to clone a deck");

  cloneDeck(
    {
      sourceDeckId: deckIdRef.value,
      name: form.name,
      description: form.description,
      isTTSEnabled: form.isTTSEnabled,
    },
    {
      onSuccess: (newDeck) => {
        console.log("cloneDeck onSuccess", { newDeck });
        router.push({ name: "decks.show", params: { deckId: newDeck.id } });
      },
    },
  );
}
</script>
<style scoped></style>
