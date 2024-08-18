<template>
  <div class="min-h-screen home-page relative bg-umn-maroon-900">
    <UniversityHeader />
    <div
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
    </div>
  </div>
</template>
<script setup lang="ts">
import { Button } from "@/components/ui/button";
import { RouterLink } from "vue-router";
// import ExampleCardSelector from './ExampleCardSelector.vue';
import IconBlockM from "@/components/icons/IconBlockM.vue";
import UniversityHeader from "@/components/UniversityHeader.vue";
import FoxOwlStudyImgUrl from "@/assets/fox-owl-study.svg";
</script>
<style scoped>
.home-page {
  background: linear-gradient(180deg, #600014 0%, #4e1324 6%, #2e091e 35%);
}
</style>
