<template>
  <div
    class="min-h-screen home-page relative bg-umn-maroon-900 text-brand-oatmeal-100 h-full"
  >
    <UniversityHeader />
    <main>
      <section
        class="text-brand-oatmeal-100 flex items-center justify-center flex-col p-8"
      >
        <img
          :src="FoxOwlStudyImgUrl"
          alt="A friendly owl and a fox with glasses, studying together at a table with a book. The owl is holding flashcards, chatting with the fox. The fox is slightly smiling while listening and looking at the flashcards."
          class="w-64 md:w-96"
        />
        <h1
          class="text-[3rem] md:text-[6rem] font-cheesecake text-brand-gold-500 leading-none mt-8"
        >
          Smarty Cards
        </h1>
        <p class="md:text-4xl leading-none mt-4">
          Digital flashcards for every learner
        </p>

        <RouterLink
          :to="{ name: 'auth.login' }"
          class="bg-brand-oatmeal-100 text-brand-maroon-950 text-xl md:text-2xl px-6 py-3 rounded-xl mt-8"
        >
          Sign In
        </RouterLink>
      </section>
      <section class="p-8 flex items-center justify-center gap-4 flex-wrap">
        <article class="flex flex-col gap-2 items-center">
          <h3 class="text-xl">Audio</h3>
          <AudioDemoCard />
        </article>
        <article class="flex flex-col gap-2 items-center">
          <h3 class="text-xl">Text</h3>
          <TextDemoCard />
        </article>
        <article class="flex flex-col gap-2 items-center">
          <h3 class="text-xl">Image</h3>
          <ImageDemoCard />
        </article>

        <article class="flex flex-col gap-2 items-center">
          <h3 class="text-xl">Video</h3>
          <VideoDemoCard />
        </article>
      </section>
    </main>
  </div>
</template>
<script setup lang="ts">
import { RouterLink } from "vue-router";
import UniversityHeader from "@/components/UniversityHeader.vue";
import FoxOwlStudyImgUrl from "@/assets/fox-owl-study.svg";
import ImageDemoCard from "./DemoCards/ImageDemoCard.vue";
import AudioDemoCard from "./DemoCards/AudioDemoCard.vue";
import TextDemoCard from "./DemoCards/TextDemoCard.vue";
import VideoDemoCard from "./DemoCards/VideoDemoCard.vue";
</script>
<style scoped>
.home-page {
  background: linear-gradient(180deg, #600014 0%, #4e1324 6%, #2e091e 35%);
}
</style>
