<template>
  <div class="">LogOut Page</div>
</template>
<script setup lang="ts">
import { useLogoutMutation } from '@/queries/auth';
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';

const { mutate: logout } = useLogoutMutation();
const router = useRouter();

onMounted(() => {
  logout();
  router.push({ name: 'home' });
});
</script>
<style scoped></style>
