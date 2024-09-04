<template>
  <AuthenticatedLayout>
    <div v-if="deck" class="max-w-screen-lg mx-auto">
      <nav class="mb-4">
        <RouterLink
          :to="{ name: 'decks.show', params: { deckId: deck?.id } }"
          class="flex gap-2 items-center"
        >
          <IconChevronLeft class="size-5" />
          {{ deck.name }}
        </RouterLink>
      </nav>
      <header class="mb-8 pb-8 border-b">
        <PageTitle class="mb-2">Import Cards</PageTitle>
        <PageSubtitle>{{ deck.name }}</PageSubtitle>
      </header>
      <main>
        <section class="my-8">
          <p>
            Import cards into the <b>{{ deck.name }}</b> deck from a CSV file in
            the following format:
          </p>

          <div class="font-mono border my-4 rounded-lg p-4">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead class="w-[100px]">front_type</TableHead>
                  <TableHead>front_content</TableHead>
                  <TableHead>front_alt</TableHead>
                  <TableHead>back_type</TableHead>
                  <TableHead>back_content</TableHead>
                  <TableHead>back_alt</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                <TableRow>
                  <TableCell>image</TableCell>
                  <TableCell>https://...</TableCell>
                  <TableCell>Map of MN</TableCell>
                  <TableCell>text</TableCell>
                  <TableCell>Minnesota</TableCell>
                  <TableCell></TableCell>
                </TableRow>
              </TableBody>
            </Table>
          </div>

          <div
            class="bg-blue-50 border border-blue-100 p-4 rounded-lg text-xs text-blue-800 flex gap-4 items-center"
          >
            <div class="bg-blue-100 p-2 rounded-full">
              <IconExclamationTriangle />
            </div>
            <p>
              Supported <code>front_type</code> and <code>back_type</code>:
              <code>text</code>, <code>image</code>, <code>audio</code>,
              <code>embed</code>
            </p>
          </div>
        </section>

        <form
          @submit.prevent="handleImport"
          class="bg-brand-maroon-800/5 p-4 rounded-lg flex flex-col"
        >
          <Label for="import-file">Import file</Label>
          <Input
            id="import-file"
            type="file"
            ref="fileInput"
            class="p-4 h-auto my-2 border border-dashed border-black/50 rounded-lg"
          />
          <Button
            type="submit"
            v-show="!!selectedFile"
            :disabled="!selectedFile"
          >
            Import
          </Button>
        </form>
      </main>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { computed, ref } from "vue";
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import * as api from "@/api";
import { useRouter } from "vue-router";
import { useDeckByIdQuery } from "@/queries/decks";
import { IconChevronLeft } from "@/components/icons";
import PageTitle from "@/components/PageTitle.vue";
import PageSubtitle from "@/components/PageSubtitle.vue";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { IconExclamationTriangle } from "@/components/icons";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";

const props = defineProps<{
  deckId: number;
}>();

const fileInput = ref<HTMLInputElement | null>(null);
// const selectedFile = ref<File | null>(null);
const selectedFile = computed(() => fileInput.value?.files?.[0] || null);
const deckIdRef = computed(() => props.deckId);
const { data: deck } = useDeckByIdQuery(deckIdRef);

const router = useRouter();

async function handleImport() {
  if (!selectedFile.value) {
    throw new Error("No file selected");
  }

  await api.importDeckCards(props.deckId, selectedFile.value);
  router.push(`/decks/${props.deckId}`);
}
</script>
<style scoped>
code {
  background-color: hsla(0, 0%, 100%, 0.75);
  display: inline-block;
  padding: 0.25em 0.5em;
  border-radius: 0.25em;
}
</style>
