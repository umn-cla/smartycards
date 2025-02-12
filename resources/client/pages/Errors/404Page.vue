<template>
  <GuestLayout class="not-found-page">
    <div class="flex-1 flex items-center justify-center p-8">
      <FlippableCardSimple class="m-auto my-auto w-full" :showLabels="false">
        <template #front>
          <div class="my-auto text-center">
            <h1 class="text-8xl font-black text-brand-maroon-900/20">404</h1>
            <h2 class="text-2xl font-bold">Page not found</h2>
            <Button asChild class="mt-6">
              <RouterLink to="/" class="text-white"> Go back home </RouterLink>
            </Button>
          </div>
        </template>
        <template #back>
          <div
            class="my-auto text-center flex items-center flex-col justify-center gap-4"
          >
            <img
              src="/img/goldy-404.jpg"
              alt="Goldy gopher with his head on backwards"
              class="w-full max-w-48 block p-1 border border-brand-maroon-900/20 bg-brand-oatmeal-50 rounded-md"
            />

            <p class="text-sm">tbh, we're not sure the best way forward</p>
          </div>
        </template>
      </FlippableCardSimple>
    </div>
  </GuestLayout>
</template>
<script setup lang="ts">
import GuestLayout from "@/layouts/GuestLayout.vue";
import FlippableCardSimple from "@/components/FlippableCardSimple.vue";
import { Button } from "@/components/ui/button";
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
