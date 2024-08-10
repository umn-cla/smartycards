<template>
  <div class="">Logging in...</div>
</template>
<script setup lang="ts">
import config from '@/config';
import { useAuthQuery } from '@/queries/auth';
import { watch } from 'vue';
import { useRouter } from 'vue-router';

const { data: currentUser, isPending, isError } = useAuthQuery();
const router = useRouter();

watch(
  [currentUser, isPending, isError],
  () => {
    console.log('login page', {
      isPending: isPending.value,
      isError: isError.value,
      currentUser: currentUser.value,
    });

    if (isPending.value) {
      return;
    }

    if (!isError.value && currentUser.value) {
      router.push({ name: 'auth.callback' });
      return;
    }

    window.location.href = config.api.loginUrl;
  },
  { immediate: true }
);
</script>
<style scoped></style>
