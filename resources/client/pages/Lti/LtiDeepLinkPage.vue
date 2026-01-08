<template>
  <div class="p-4 max-w-2xl mx-auto">
    <h1 class="text-xl font-bold text-brand-maroon-800 mb-4">
      Create Assignment
    </h1>

    <div v-if="isLoadingDecks" class="text-center py-6">
      <div
        class="inline-block animate-spin rounded-full h-6 w-6 border-b-2 border-brand-maroon-800"
      ></div>
      <p class="mt-2 text-sm text-gray-600">Loading decks...</p>
    </div>

    <div
      v-else-if="error"
      class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4"
    >
      <p class="text-sm text-red-800">Error loading decks: {{ error }}</p>
    </div>

    <div v-else class="space-y-4">
      <!-- Deck Selection -->
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
          Select Deck
        </label>
        <SimpleSelect
          v-model="selectedDeckId"
          placeholder="Choose a deck"
          class="!w-full"
        >
          <SelectOption
            v-for="deck in decks"
            :key="deck.id"
            :value="deck.id.toString()"
          >
            {{ deck.name }} ({{ deck.cards_count }} cards)
          </SelectOption>
        </SimpleSelect>
        <p
          v-if="!decks || decks.length === 0"
          class="text-sm text-gray-500 mt-1"
        >
          No decks available. Create a deck first.
        </p>
      </div>

      <!-- Create New Deck Button -->
      <div class="text-center">
        <button
          @click="createNewDeck"
          type="button"
          class="text-sm text-brand-teal-600 hover:text-brand-teal-700 font-medium hover:underline"
        >
          + Create New Deck
        </button>
      </div>

      <!-- Action Buttons -->
      <template v-if="selectedDeckId">
        <div class="flex gap-2 pt-2">
          <button
            @click="submitSelection"
            :disabled="isSubmitting"
            class="flex-1 bg-brand-teal-600 text-white py-2 px-4 rounded-md text-sm font-semibold bg-brand-teal-700 hover:bg-brand-teal-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {{ isSubmitting ? "Creating..." : "Create Assignment" }}
          </button>
          <button
            @click="cancel"
            :disabled="isSubmitting"
            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-semibold hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Cancel
          </button>
        </div>
      </template>
    </div>

    <!-- Hidden form for LTI deep link submission.
         Posts to backend which returns an auto-submit form with signed JWT. -->
    <form
      ref="ltiFormRef"
      method="POST"
      action="/lti/deep-link/response"
      style="display: none"
    >
      <input type="hidden" name="_token" :value="csrfToken" />
      <input type="hidden" name="launch_id" :value="ltiData.launchId" />
      <input
        type="hidden"
        name="deck_id"
        :value="selectedDeck?.id.toString()"
      />
    </form>
  </div>
</template>

<script setup lang="ts">
import { SelectOption, SimpleSelect } from "@/components/SimpleSelect";
import { useAllDecksQuery } from "@/queries/decks";
import { computed, onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";

// Get LTI launch data from window
const ltiData = window.SmartyCards.ltiDeepLink;

if (!ltiData) {
  throw new Error("LTI deep link data not found");
}

const router = useRouter();
const route = useRoute();

const { data: decks, isLoading: isLoadingDecks, error } = useAllDecksQuery();

const selectedDeckId = ref<string | null>(null);
const isSubmitting = ref(false);

const selectedDeck = computed(
  () =>
    decks.value?.find((d) => d.id.toString() === selectedDeckId.value) ?? null,
);

const ltiFormRef = ref<HTMLFormElement | null>(null);
const csrfToken =
  document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") ||
  "";

const submitSelection = () => {
  if (!selectedDeck.value || !ltiFormRef.value) return;

  isSubmitting.value = true;
  // Submit the hidden form - this performs a full page POST to the backend
  // which generates a signed JWT for the LTI Deep Linking response
  ltiFormRef.value.submit();
};

const createNewDeck = () => {
  router.push({
    name: "decks.create",
    query: { launch_id: ltiData.launchId, launch_type: "deep_link" },
  });
};

// Check if returning from deck creation with a new deck
onMounted(() => {
  if (route.query.newDeckId) {
    selectedDeckId.value = String(route.query.newDeckId);
  }
});

const cancel = () => {
  // TODO: Navigate back to LMS if possible
  if (
    confirm(
      "Are you sure you want to cancel? This will close the assignment setup.",
    )
  ) {
    window.close();
  }
};
</script>
