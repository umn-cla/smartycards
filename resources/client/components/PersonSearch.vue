<template>
  <div class="relative">
    <Label class="block">{{ label }}</Label>
    <input
      :value="searchQuery"
      placeholder="name or internet id"
      @input="handleInputChange"
      type="search"
    />
    <ul
      class="max-h-[50vh] overflow-y-scroll absolute top-full bg-neutral-50 border border-black z-10 w-full mt-1 p-2 shadow"
      v-if="users.length"
    >
      <li
        v-for="user in users"
        :key="user.umndid"
      >
        <button
          type="button"
          @click="handleUserSelect(user)"
        >
          <p>
            {{ user.display_name }}
            ({{ user.internet_id }})
          </p>
        </button>
      </li>
    </ul>
  </div>
</template>
<script setup lang="ts">
import { ref } from 'vue';
import { type LDAPUser } from '@/types';
import pDebounce from 'p-debounce';
import { lookupUsers } from '@/api';
import { Label } from './ui/label';

const props = withDefaults(
  defineProps<{
    id?: string;
    label: string;
    query?: string;
  }>(),
  {
    id: 'person-search',
    query: '',
  }
);

const emit = defineEmits<{
  (event: 'selectUser', user: LDAPUser): void;
}>();

const searchQuery = ref(props.query);
const users = ref<LDAPUser[]>([]);

const debouncedLookup = pDebounce(lookupUsers, 200);

async function handleInputChange(event: Event) {
  const query = (event.target as HTMLInputElement).value;
  searchQuery.value = query;
  users.value = await debouncedLookup(query);
}

function handleUserSelect(user: LDAPUser) {
  emit('selectUser', user);
  searchQuery.value = '';
  users.value = [];
}
</script>
<style scoped></style>
