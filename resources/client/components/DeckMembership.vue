<template>
  <li
    class="flex flex-col sm:flex-row border p-1 rounded-xl sm:border-none gap-1 flex-wrap"
  >
    <div
      :class="[
        'bg-brand-maroon-800/5 rounded-lg p-4 flex-1 sm:flex justify-between items-center flex-wrap',
        {
          'bg-brand-gold-500/30 text-brand-maroon-900': isCurrentUserMembership,
        },
      ]"
    >
      <div>
        <p class="flex items-center gap-2">
          {{ membership.user.name }}
          <Badge
            v-if="isCurrentUserMembership"
            class="bg-brand-gold-500 text-brand-maroon-900"
            >You</Badge
          >
        </p>
        <p class="text-sm text-brand-maroon-900/50">
          {{ membership.user.email }}
        </p>
      </div>
      <div>
        <p
          v-if="!membership.capabilities.canUpdate"
          class="px-4 py-3 border border-black/10 rounded-lg text-sm capitalize leading-none"
        >
          {{ membership.role }}
        </p>
        <Select v-else v-model="selectedRole">
          <SelectTrigger
            class="bg-brand-maroon-800/5 border-brand-maroon-900/10"
          >
            <SelectValue placeholder="Select a role" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem :value="T.MembershipRole.VIEWER">Viewer</SelectItem>
              <SelectItem :value="T.MembershipRole.EDITOR">Editor</SelectItem>
              <SelectItem :value="T.MembershipRole.OWNER">Owner</SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
      </div>
    </div>
    <div
      class="flex sm:flex-col gap-2 sm:gap-1 justify-end"
      v-if="
        membership.capabilities.canUpdate || membership.capabilities.canDelete
      "
    >
      <Button
        variant="ghost"
        class="test disabled:bg-brand-maroon-900/10 disabled:text-brand-maroon-900/50 disabled:cursor-not-allowed !pointer-events-auto bg-brand-teal-500/10 text-brand-teal-500 hover:bg-brand-teal-300 hover:text-white flex gap-2 items-center justify-start"
        :disabled="selectedRole === membership.role"
        @click="handleSave"
        v-if="membership.capabilities.canUpdate"
      >
        <IconCheck class="size-4" />
        <span>Save</span>
      </Button>
      <Modal
        variant="danger"
        :title="isCurrentUserMembership ? 'Leave Deck?' : 'Remove User?'"
        submitButtonVariant="destructive"
        :submitButtonLabel="isCurrentUserMembership ? 'Leave' : 'Remove'"
        v-if="membership.capabilities.canDelete"
        @submit="deleteMembership(membership)"
      >
        <p v-if="isCurrentUserMembership">
          Are you sure you want to <b>remove yourself</b> from this deck? You
          will lose access until you are re-invited.
        </p>
        <p v-else>
          Are you sure you want to remove <b>{{ membership.user.name }}</b> from
          the deck?
        </p>
        <template #trigger>
          <Button
            variant="ghost"
            class="disabled:opacity-25 text-brand-orange-500 bg-brand-orange-500/5 hover:bg-brand-orange-500 hover:text-white flex gap-2 items-center justify-start"
          >
            <IconTrash class="size-4 flex-shrink-0" />
            <span>Remove</span>
          </Button>
        </template>
      </Modal>
    </div>
  </li>
</template>
<script setup lang="ts">
import { ref, computed } from "vue";
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
import { IconCheck, IconTrash } from "./icons";
import Modal from "./Modal.vue";
import { useAuthQuery } from "@/queries/auth";
import { Badge } from "./ui/badge";

const props = defineProps<{
  membership: T.DeckMembership;
}>();

const selectedRole = ref<T.DeckMembership["role"] | "">(props.membership.role);

const { mutate: updateMembership } = useUpdateDeckMembershipMutation();
const { mutate: deleteMembership } = useDeleteDeckMembershipMutation();
const { data: currentUser } = useAuthQuery();

const isCurrentUserMembership = computed(
  () => props.membership.user.id === currentUser.value?.id,
);

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
