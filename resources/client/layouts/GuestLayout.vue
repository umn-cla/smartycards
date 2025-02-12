<template>
  <div class="min-h-screen guest-layout relative h-full bg-brand-maroon-900">
    <UniversityHeader />
    <main class="py-8">
      <slot />
    </main>
  </div>
</template>
<script setup lang="ts">
import { UniversityHeader as DefaultUniversityHeader } from "@umn-latis/cla-vue-template";
import UniversityHeader from "@/components/UniversityHeader.vue";
</script>
<style scoped>
.guest-layout {
  background: linear-gradient(180deg, #600014 0%, #4e1324 6%, #2e091e 35%);
}
</style>
