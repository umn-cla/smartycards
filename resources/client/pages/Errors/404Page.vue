<template>
  <GuestLayout>
    <div>
      <h1>Not Found</h1>
      <p>Sorry, the page you are looking for does not exist.</p>
      <Button asChild>
        <RouterLink :to="{ name: 'home' }" class="btn">Go Home</RouterLink>
      </Button>
    </div>
  </GuestLayout>
</template>
<script setup lang="ts">
import GuestLayout from "@/layouts/GuestLayout.vue";
import Button from "@/components/ui/button/Button.vue";
</script>
<style scoped></style>
