<template>
  <AuthenticatedLayout>
    <template v-if="error">
      <h1>Could not accept deck invite</h1>
      <p>{{ error }}</p>
    </template>
    <h1 v-else>Processing deck invite...</h1>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import axios from "@/api/axios";
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { onMounted, ref } from "vue";
import { useRouter } from "vue-router";
import { usePageTitle } from "@/lib/usePageTitle";

usePageTitle(["Deck Invite"]);

const props = defineProps<{
  deckId: number;
  url: string;
}>();

const router = useRouter();
const error = ref<string | null>(null);

onMounted(async () => {
  try {
    // request to the backend to invite a user to a deck
    const res = await axios.get(props.url);
    if (res.status < 200 || res.status > 300) {
      error.value = `Could not add to deck: ${JSON.stringify(res.data)}`;
      return;
    }

    // redirect to the deck page
    router.push(`/decks/${props.deckId}`);
  } catch (err) {
    console.error(err);
    error.value = `Could not add to deck: ${err}`;
  }
});
</script>
<style scoped></style>
