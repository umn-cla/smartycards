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
        <div class="flex gap-2 items-center">
          <Switch
            id="tts-enabled"
            :checked="form.isTTSEnabled"
            @update:checked="form.isTTSEnabled = $event"
            label="Enable TTS"
            data-cy="tts-switch"
          />
          <Label for="tts-enabled">
            Enable Text-to-Speech
            <HintTooltip>
              When enabled, a button to read text aloud will be available. This
              is useful for learning pronunciation.
            </HintTooltip>
          </Label>
        </div>
        <div
          v-show="form.isTTSEnabled"
          class="flex gap-4 p-4 border border-brand-maroon-800/10 rounded-md"
        >
          <div>
            <Label for="default-front-locale">Front Side Language</Label>
            <SelectLanguage
              id="default-front-locale"
              v-model="form.ttsLocaleFront"
            />
          </div>
          <div>
            <Label for="default-back-locale">Back Side Language</Label>
            <SelectLanguage
              id="default-back-locale"
              v-model="form.ttsLocaleBack"
            />
          </div>
        </div>

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
import { Switch } from "@/components/ui/switch";
import HintTooltip from "@/components/HintTooltip.vue";
import { Label } from "@/components/ui/label";
import SelectLanguage from "@/components/SelectLanguage.vue";
import * as T from "@/types";

const props = defineProps<{
  deckId: number | null;
}>();

const form = reactive({
  name: "",
  description: "",
  isTTSEnabled: false,
  ttsLocaleFront: null as T.LanguageOption["locale"] | null,
  ttsLocaleBack: null as T.LanguageOption["locale"] | null,
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
      form.description = deck.value.description ?? "";
      form.isTTSEnabled = deck.value.is_tts_enabled;
      form.ttsLocaleFront = deck.value.tts_locale_front;
      form.ttsLocaleBack = deck.value.tts_locale_back;
    }
  },
  { immediate: true },
);

async function handleSubmit() {
  if (isCreateMode.value) {
    createDeck(form, {
      onSuccess: (newDeck) => {
        router.push({ name: "decks.show", params: { deckId: newDeck.id } });
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
      onSuccess: (updatedDeck: T.Deck) => {
        router.push({ name: "decks.show", params: { deckId: updatedDeck.id } });
      },
    },
  );
}
</script>
<style scoped></style>
