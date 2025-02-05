<template>
  <div
    class="min-h-screen home-page relative bg-umn-maroon-900 text-brand-oatmeal-100 h-full"
  >
    <UniversityHeader />
    <main>
      <section
        class="text-brand-oatmeal-100 flex items-center justify-center flex-col px-4 text-center gap-4"
      >
        <img
          :src="FoxOwlStudyImgUrl"
          alt="A friendly owl and a fox with glasses, studying together at a table with a book. The owl is holding flashcards, chatting with the fox. The fox is slightly smiling while listening and looking at the flashcards."
          class="block w-[66dvw] max-w-64 md:max-w-full md:w-96"
        />
        <div>
          <SmartycardsWordmark class="text-brand-gold-500" />
          <p class="text-xl md:text-4xl leading-none mt-2">
            Digital flashcards for every learner
          </p>
        </div>

        <div class="flex gap-4 items-baseline mt-8">
          <RouterLink
            :to="{ name: 'decks.index' }"
            class="bg-brand-oatmeal-100 text-brand-maroon-950 md:text-2xl rounded-xl px-6 py-2 block border-2 border-brand-oatmeal-100"
          >
            Sign In
          </RouterLink>
          <a
            href="https://umn-cla.github.io/smartycards/"
            class="text-brand-oatmeal-100 px-6 py-2 md:text-2xl rounded-xl border-2 border-brand-oatmeal-100"
          >
            Learn more
          </a>
        </div>
      </section>
      <section class="p-8 my-8">
        <h2 class="sr-only">Multimedia Cards</h2>
        <div class="card-grid mx-auto max-w-screen-lg !gap-8">
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
        </div>
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
import SmartycardsWordmark from "@/components/SmartycardsWordmark.vue";
</script>
<style scoped>
.home-page {
  background: linear-gradient(180deg, #600014 0%, #4e1324 6%, #2e091e 35%);
}
</style>
