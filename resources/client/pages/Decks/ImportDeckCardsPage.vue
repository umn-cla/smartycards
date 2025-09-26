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
      <header class="mb-8 pb-8 border-b border-black/10">
        <PageTitle class="mb-2">Import Cards</PageTitle>
        <PageSubtitle>{{ deck.name }}</PageSubtitle>
      </header>
      <main>
        <section class="my-8">
          <p class="my-4">
            Import cards from a spreadsheet in the following format:
          </p>

          <ul class="list-disc ml-6">
            <li>
              File extension must be <code>.csv</code>, <code>.xls</code>, or
              <code>.xlsx</code>.
            </li>
            <li>Only text is supported (no images).</li>
            <li>
              The first row <strong>must</strong> contain the column headers
              <code>front</code> and <code>back</code>.
            </li>
          </ul>

          <div class="my-4">
            <div class="flex justify-between items-center mb-2 gap-4 flex-wrap">
              <h3 class="uppercase text-xs tracking-wider font-bold">
                Example
              </h3>
              <Button
                variant="secondary"
                class="text-xs"
                as="a"
                href="/examples/french-english.csv"
                download
              >
                <DownloadIcon class="size-4 mr-2" />
                Download
              </Button>
            </div>
            <div
              class="mb-2 text-sm border border-black/10 rounded-md bg-white/50 p-2 font-mono"
            >
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>front</TableHead>
                    <TableHead>back</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  <TableRow>
                    <TableCell>Bonjour</TableCell>
                    <TableCell>Hello</TableCell>
                  </TableRow>
                  <TableRow>
                    <TableCell>Comment ça va?</TableCell>
                    <TableCell>How are you?</TableCell>
                  </TableRow>
                </TableBody>
              </Table>
            </div>
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
            @change="handleFileInputChange"
            accept=".csv, .xls, .xlsx"
          />
          <div
            v-if="importError"
            class="text-red-700 my-4 flex items-center gap-2 bg-red-700/10 p-4 rounded-md text-sm"
          >
            <IconExclamationTriangle class="size-6 flex-shrink-0" />
            <div>
              <h2 class="uppercase font-bold">Import Error</h2>
              <p>
                There was a problem with your import file. Be sure the
                <b>first row</b> contains headers <code>front</code> and
                <code>back</code>. If the problem persists, please contact
                <a
                  href="mailto:latistecharch@umn.edu"
                  class="underline text-red-900"
                  >latistecharch@umn.edu</a
                >
                for help.
              </p>
            </div>
          </div>
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
import { DownloadIcon } from "@radix-icons/vue";

const props = defineProps<{
  deckId: number;
}>();

const fileInput = ref<HTMLInputElement | null>(null);
const selectedFile = ref<File | null>(null);
const deckIdRef = computed(() => props.deckId);
const importError = ref<string | null>(null);
const { data: deck } = useDeckByIdQuery(deckIdRef);

const router = useRouter();

function handleFileInputChange(event: Event) {
  const target = event.target as HTMLInputElement;
  if (!target.files) {
    return;
  }

  selectedFile.value = target.files[0];
}

async function handleImport() {
  if (!selectedFile.value) {
    throw new Error("No file selected");
  }

  try {
    await api.importDeckCards(props.deckId, selectedFile.value, {
      skipErrorNotifications: true,
    });
    router.push(`/decks/${props.deckId}`);
  } catch (error) {
    console.error("Error importing cards:", error);
    if (error instanceof Error) {
      importError.value = error.message;
    } else {
      importError.value = "An unknown error occurred while importing cards.";
    }
    selectedFile.value = null;
    if (fileInput.value) {
      fileInput.value.value = "";
    }
  }
}
</script>
<style scoped>
code {
  background-color: hsla(0, 0%, 100%, 0.5);
  display: inline-block;
  padding: 0.125em 0.25em;
  border-radius: 0.25em;
}
</style>
