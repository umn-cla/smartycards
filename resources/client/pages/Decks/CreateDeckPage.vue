<template>
  <AuthenticatedLayout>
    <div class="max-w-screen-md mx-auto">
      <PageHeader
        class="mb-8"
        title="Create Deck"
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
        <div class="flex items-center justify-end py-4 gap-2">
          <Button asChild variant="secondary">
            <RouterLink :to="{ name: 'decks.index' }"> Cancel </RouterLink>
          </Button>
          <Button type="submit"> Create Deck </Button>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { reactive } from "vue";
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import { useCreateDeckMutation } from "@/queries/decks";
import { useRouter } from "vue-router";
import InputGroup from "@/components/InputGroup.vue";
import PageHeader from "@/components/PageHeader.vue";
import { Button } from "@/components/ui/button";

const form = reactive({
  name: "",
  description: "",
});

const router = useRouter();
const createDeckMutation = useCreateDeckMutation();

async function handleSubmit() {
  createDeckMutation.mutate(form, {
    onSuccess: () => {
      router.push({ name: "decks.index" });
    },
  });
}
</script>
<style scoped></style>
