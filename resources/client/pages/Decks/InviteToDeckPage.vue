<template>
  <AuthenticatedLayout>
    <h1 v-if="!error">Processing deck invite...</h1>
    <p v-if="error">{{ error }}</p>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import axios from '@/api/axios';
import { AuthenticatedLayout } from '@/layouts/AuthenticatedLayout';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';

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
    console.log({ res });
    if (res.status < 200 || res.status > 300) {
      console.log({});
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
