<template>
  <div class="min-h-screen flex flex-col font-sans text-brand-maroon-800">
    <ErrorModal />
    <RouterView />
    <!-- <VueQueryDevtools /> -->
  </div>
</template>
<script setup lang="ts">
import { RouterView } from "vue-router";
import ErrorModal from "./components/ErrorModal.vue";
// import { VueQueryDevtools } from '@tanstack/vue-query-devtools';
</script>
<style scoped></style>
