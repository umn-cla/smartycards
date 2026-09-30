<template>
  <GuestLayout>
    <h1>Forbidden</h1>

    <p>Sorry, you are not allowed to access this page.</p>
  </GuestLayout>
</template>
<script setup lang="ts">
import GuestLayout from "@/layouts/GuestLayout.vue";
import { usePageTitle } from "@/lib/usePageTitle";

usePageTitle(["Forbidden"]);
</script>
<style scoped></style>
