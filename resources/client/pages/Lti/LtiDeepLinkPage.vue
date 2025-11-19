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
        <p v-if="!decks || decks.length === 0" class="text-sm text-gray-500 mt-1">
          No decks available. Create a deck first.
        </p>
      </div>

      <!-- Configuration (only shown when deck selected) -->
      <template v-if="selectedDeckId">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Assignment Title
          </label>
          <input
            v-model="config.title"
            type="text"
            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-teal-500 focus:border-transparent"
            placeholder="e.g., Week 1 Vocabulary Practice"
          />
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">
            Description (optional)
          </label>
          <textarea
            v-model="config.description"
            rows="2"
            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:ring-2 focus:ring-brand-teal-500 focus:border-transparent"
            placeholder="Brief description"
          ></textarea>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2 pt-2">
          <button
            @click="submitSelection"
            :disabled="isSubmitting"
            class="flex-1 bg-brand-teal-600 text-white py-2 px-4 rounded-md text-sm font-semibold hover:bg-brand-teal-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
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

    <!-- Hidden form for LTI deep link submission -->
    <!-- This form is used to POST the deck selection back to Canvas via the LTI Deep Linking protocol.
         The backend will generate a signed JWT and auto-submit it back to the LMS. -->
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
      <input
        type="hidden"
        name="title"
        :value="config.title || `Practice: ${selectedDeck?.name}`"
      />
      <input
        v-if="config.description"
        type="hidden"
        name="description"
        :value="config.description"
      />
    </form>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { useAllDecksQuery } from "@/queries/decks";
import { SimpleSelect, SelectOption } from "@/components/SimpleSelect";

// Get LTI launch data from window
const ltiData = window.SmartyCards.ltiDeepLink;

if (!ltiData) {
  throw new Error("LTI deep link data not found");
}

const { data: decks, isLoading: isLoadingDecks, error } = useAllDecksQuery();

const selectedDeckId = ref<string | null>(null);
const isSubmitting = ref(false);

const config = ref({
  title: "",
  description: "",
});

const selectedDeck = computed(() => {
  if (!selectedDeckId.value || !decks.value) return null;
  return (
    decks.value.find((d) => d.id.toString() === selectedDeckId.value) || null
  );
});

// Set default title when deck is selected
watch(selectedDeck, (deck) => {
  if (deck && !config.value.title) {
    config.value.title = `Practice: ${deck.name}`;
  }
});

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

const cancel = () => {
  // In a real LTI implementation, this would navigate back to the LMS
  // For now, just show an alert
  if (
    confirm(
      "Are you sure you want to cancel? This will close the assignment setup.",
    )
  ) {
    window.close();
  }
};
</script>
