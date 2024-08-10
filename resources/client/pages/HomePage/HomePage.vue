<template>
  <div
    class="flex flex-col items-center justify-center gap-4 text-center min-h-screen home-page relative bg-umn-maroon-900"
  >
    <header class="mb-8 flex flex-col gap-4 justify-center items-center">
      <IconBlockM class="text-umn-gold-700 h-12" />
      <h1 class="text-6xl font-bold text-stone-50">Smarty Cards</h1>
      <p class="text-3xl font-bold text-stone-400">Learn anything</p>
    </header>

    <Button
      class="text-xl py-3 px-8 bg-umn-gold-700 hover:bg-umn-gold-500 text-black"
      asChild
    >
      <RouterLink :to="{ name: 'auth.login' }">Login</RouterLink>
    </Button>

    <!-- <ExampleCardSelector /> -->
  </div>
</template>
<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { RouterLink } from 'vue-router';
// import ExampleCardSelector from './ExampleCardSelector.vue';
import IconBlockM from '@/components/icons/IconBlockM.vue';
</script>
<style scoped></style>
