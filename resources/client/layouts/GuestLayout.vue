<template>
  <div
    class="min-h-screen guest-layout relative flex flex-col gap-4 bg-brand-maroon-900"
  >
    <UniversityHeader />
    <main
      id="main-content"
      tabindex="-1"
      class="flex flex-1 flex-col gap-4 focus:outline-none"
    >
      <slot />
    </main>
  </div>
</template>
<script setup lang="ts">
import UniversityHeader from "@/components/UniversityHeader.vue";
</script>
<style scoped>
.guest-layout {
  background: linear-gradient(180deg, #600014 0%, #4e1324 6%, #2e091e 35%);
}
</style>
