<template>
  <GuestLayout class="not-found-page">
    <FlippableCard class="mx-auto" :showLabels="false">
      <template #front> Front!!! </template>
      <template #back> Back!!! </template>
    </FlippableCard>
  </GuestLayout>
</template>
<script setup lang="ts">
import GuestLayout from "@/layouts/GuestLayout.vue";
import FlippableCard from "@/components/FlippableCard.vue";
</script>
<style scoped>
.not-found-page__text-container {
  padding: 1rem;
}

.not-found-page__heading {
  font-size: 6rem;
  font-weight: light;
  margin: 0;
  color: #ccc;
  line-height: 1;
}
.not-found-page__subheading {
  font-size: 3rem;
  margin-top: 0;
  margin-bottom: 1.5rem;
}

@media (min-width: 48rem) {
  .not-found-page__content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    grid-template-areas: "text img";
  }

  .not-found-page__img {
    grid-area: img;
    object-fit: cover;
    width: 100%;
    height: 100%;
  }
  .not-found-page__text-container {
    grid-area: text;
    padding-left: 2rem;
    padding-right: 2rem;
  }
}
</style>
<style>
.not-found-page .inner-container {
  padding: 0;
}
</style>
