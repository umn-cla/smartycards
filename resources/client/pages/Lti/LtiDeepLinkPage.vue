<template>
  <div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto py-8 px-4">
      <header class="mb-8">
        <h1 class="text-3xl font-bold text-brand-maroon-800 mb-2">
          Select a Deck for Your Assignment
        </h1>
        <p class="text-gray-600">
          Choose a deck that students will practice. You can configure
          additional settings after selecting a deck.
        </p>
      </header>

      <div v-if="isLoadingDecks" class="text-center py-12">
        <div
          class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-brand-maroon-800"
        ></div>
        <p class="mt-4 text-gray-600">Loading your decks...</p>
      </div>

      <div
        v-else-if="error"
        class="bg-red-50 border border-red-200 rounded-lg p-4"
      >
        <p class="text-red-800">Error loading decks: {{ error }}</p>
      </div>

      <div v-else>
        <!-- Search/Filter -->
        <div class="mb-6">
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search decks..."
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-teal-500 focus:border-transparent"
          />
        </div>

        <!-- Deck List -->
        <div
          v-if="filteredDecks.length === 0"
          class="text-center py-12 bg-white rounded-lg border border-gray-200"
        >
          <p class="text-gray-600">
            No decks found. Create a deck first before setting up an LTI
            assignment.
          </p>
        </div>

        <div v-else class="space-y-3">
          <button
            v-for="deck in filteredDecks"
            :key="deck.id"
            @click="selectDeck(deck)"
            :class="[
              'w-full text-left p-4 rounded-lg border-2 transition-all',
              selectedDeck?.id === deck.id
                ? 'border-brand-teal-500 bg-brand-teal-50'
                : 'border-gray-200 bg-white hover:border-brand-teal-300 hover:bg-gray-50',
            ]"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <h3 class="font-semibold text-lg text-gray-900">
                  {{ deck.name }}
                </h3>
                <p v-if="deck.description" class="text-gray-600 text-sm mt-1">
                  {{ deck.description }}
                </p>
                <div class="flex gap-4 mt-2 text-sm text-gray-500">
                  <span>{{ deck.cards_count }} cards</span>
                  <span
                    v-if="deck.current_user_role === T.MembershipRole.EDITOR"
                    class="text-brand-teal-600"
                    >Shared with you</span
                  >
                </div>
              </div>
              <div
                v-if="selectedDeck?.id === deck.id"
                class="ml-4 flex-shrink-0"
              >
                <svg
                  class="w-6 h-6 text-brand-teal-600"
                  fill="currentColor"
                  viewBox="0 0 20 20"
                >
                  <path
                    fill-rule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clip-rule="evenodd"
                  />
                </svg>
              </div>
            </div>
          </button>
        </div>

        <!-- Configuration Options -->
        <div
          v-if="selectedDeck"
          class="mt-8 bg-white rounded-lg border border-gray-200 p-6"
        >
          <h2 class="text-xl font-semibold text-gray-900 mb-4">
            Assignment Configuration
          </h2>

          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Assignment Title
              </label>
              <input
                v-model="config.title"
                type="text"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-teal-500 focus:border-transparent"
                placeholder="e.g., Week 1 Vocabulary Practice"
              />
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Description (optional)
              </label>
              <textarea
                v-model="config.description"
                rows="3"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-teal-500 focus:border-transparent"
                placeholder="Brief description of what students should focus on"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div v-if="selectedDeck" class="mt-6 flex gap-3">
          <button
            @click="submitSelection"
            :disabled="isSubmitting"
            class="flex-1 bg-brand-teal-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-brand-teal-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {{ isSubmitting ? "Creating Assignment..." : "Create Assignment" }}
          </button>
          <button
            @click="cancel"
            :disabled="isSubmitting"
            class="px-6 py-3 border border-gray-300 rounded-lg font-semibold hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            Cancel
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from "vue";
import { useAllDecksQuery } from "@/queries/decks";
import * as T from "@/types";

// Get LTI launch data from window
const ltiData = (window as any).ltiDeepLink;

const { data: decks, isLoading: isLoadingDecks, error } = useAllDecksQuery();

const searchQuery = ref("");
const selectedDeck = ref<T.Deck | null>(null);
const isSubmitting = ref(false);

const config = ref({
  title: "",
  description: "",
});

const filteredDecks = computed(() => {
  if (!decks.value) return [];

  const query = searchQuery.value.toLowerCase();
  if (!query) return decks.value;

  return decks.value.filter(
    (deck) =>
      deck.name.toLowerCase().includes(query) ||
      deck.description?.toLowerCase().includes(query),
  );
});

const selectDeck = (deck: T.Deck) => {
  selectedDeck.value = deck;

  // Set default title if not already set
  if (!config.value.title) {
    config.value.title = `Practice: ${deck.name}`;
  }
};

const submitSelection = async () => {
  if (!selectedDeck.value) return;

  isSubmitting.value = true;

  try {
    // Create a form and submit to the deep link response endpoint
    const form = document.createElement("form");
    form.method = "POST";
    form.action = "/lti/deep-link/response";

    // Add CSRF token
    const csrfToken = document
      .querySelector('meta[name="csrf-token"]')
      ?.getAttribute("content");
    if (csrfToken) {
      const csrfInput = document.createElement("input");
      csrfInput.type = "hidden";
      csrfInput.name = "_token";
      csrfInput.value = csrfToken;
      form.appendChild(csrfInput);
    }

    // Add launch ID
    const launchIdInput = document.createElement("input");
    launchIdInput.type = "hidden";
    launchIdInput.name = "launch_id";
    launchIdInput.value = ltiData.launchId;
    form.appendChild(launchIdInput);

    // Add deck selection data
    const deckIdInput = document.createElement("input");
    deckIdInput.type = "hidden";
    deckIdInput.name = "deck_id";
    deckIdInput.value = selectedDeck.value.id.toString();
    form.appendChild(deckIdInput);

    // Add configuration
    const titleInput = document.createElement("input");
    titleInput.type = "hidden";
    titleInput.name = "title";
    titleInput.value =
      config.value.title || `Practice: ${selectedDeck.value.name}`;
    form.appendChild(titleInput);

    if (config.value.description) {
      const descInput = document.createElement("input");
      descInput.type = "hidden";
      descInput.name = "description";
      descInput.value = config.value.description;
      form.appendChild(descInput);
    }

    document.body.appendChild(form);
    form.submit();
  } catch (err) {
    console.error("Error submitting selection:", err);
    alert("An error occurred. Please try again.");
    isSubmitting.value = false;
  }
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
