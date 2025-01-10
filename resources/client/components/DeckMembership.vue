<template>
  <li
    class="grid sm:grid-cols-4 lg:grid-cols-6 gap-2 sm:gap-4 border p-1 rounded-xl sm:border-none"
  >
    <div
      class="sm:col-span-3 lg:col-span-5 bg-brand-maroon-800/5 rounded-lg p-4 sm:flex justify-between items-center"
    >
      <div>
        <p>{{ membership.user.name }}</p>
        <p class="text-sm text-neutral-500">{{ membership.user.email }}</p>
      </div>
      <div>
        <p
          v-if="
            membership.role === T.MembershipRole.OWNER ||
            !membership.capabilities.canUpdate
          "
          class="px-4 py-3 border border-black/10 rounded-lg text-sm capitalize leading-none"
        >
          {{ membership.role }}
        </p>
        <Select v-else v-model="selectedRole">
          <SelectTrigger class="bg-brand-maroon-800/5">
            <SelectValue placeholder="Select a role" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem :value="T.MembershipRole.VIEWER">Viewer</SelectItem>
              <SelectItem :value="T.MembershipRole.EDITOR">Editor</SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
      </div>
    </div>
    <div
      class="flex gap-1 items-center justify-start flex-row-reverse sm:flex-row sm:justify-center sm:border sm:rounded-lg sm:p-2 flex-wrap"
      v-if="
        membership.capabilities.canUpdate || membership.capabilities.canDelete
      "
    >
      <Button
        variant="outline"
        class="disabled:opacity-25 bg-brand-maroon-800/5 hover:bg-brand-maroon-800/10"
        :disabled="selectedRole === membership.role"
        @click="handleSave"
        v-if="membership.capabilities.canUpdate"
      >
        <IconCheck class="size-4" />
        <span class="sr-only">Save</span>
      </Button>
      <Modal
        variant="danger"
        title="Remove User"
        submitButtonVariant="destructive"
        submitButtonLabel="Remove"
        v-if="membership.capabilities.canDelete"
        @submit="deleteMembership(membership)"
      >
        <p>
          Are you sure you want to remove <b>{{ membership.user.name }}</b> from
          the deck?
        </p>
        <template #trigger>
          <Button variant="ghost">
            <IconX class="size-4" />
            <span class="sr-only">Remove</span>
          </Button>
        </template>
      </Modal>
    </div>
  </li>
</template>
<script setup lang="ts">
import { ref } from "vue";
import * as T from "@/types";
import {
  useUpdateDeckMembershipMutation,
  useDeleteDeckMembershipMutation,
} from "@/queries/deckMemberships";
import { Button } from "./ui/button";
import {
  Select,
  SelectTrigger,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectValue,
} from "./ui/select";
import IconX from "./icons/IconX.vue";
import { IconCheck } from "./icons";
import Modal from "./Modal.vue";

const props = defineProps<{
  membership: T.DeckMembership;
}>();

const selectedRole = ref<T.DeckMembership["role"] | "">(props.membership.role);

const { mutate: updateMembership } = useUpdateDeckMembershipMutation();
const { mutate: deleteMembership } = useDeleteDeckMembershipMutation();

function handleSave() {
  if (selectedRole.value === "") {
    const isConfirmed = confirm(
      "Are you sure you want to remove this user from the deck?",
    );
    if (isConfirmed) {
      deleteMembership(props.membership);
    }
    return;
  }

  updateMembership({
    ...props.membership,
    role: selectedRole.value,
  });
}
</script>
<style scoped></style>
