<template>
  <GuestLayout>
    <div>
      <h1>Not Found</h1>
      <p>Sorry, the page you are looking for does not exist.</p>
      <RouterLink :to="{ name: 'home' }" class="btn">Go Home</RouterLink>
    </div>
  </GuestLayout>
</template>
<script setup lang="ts">
import GuestLayout from '@/layouts/GuestLayout.vue';
</script>
<style scoped></style>
