<template>
  <div
    class="grid grid-cols-3 justify-center h-full bg-brand-maroon-950 min-h-dvh"
  >
    <div
      class="mx-auto max-w-screen-sm w-full"
      v-for="(embedcode, key) in embedCodes"
      :key="key"
    >
      <div
        v-if="!embedcode"
        class="text-center bg-brand-oatmeal-50 p-4 text-brand-maroon-900/50"
      >
        <p>Enter your embed code below to preview it.</p>
      </div>

      <div
        v-else
        v-html="embedcode"
        class="border border-neutral-900 bg-white"
      />

      <Textarea
        :modelValue="embedcode"
        :placeholder="`<iframe src=...`"
        @update:modelValue="
          ($event) => {
            embedCodes[key] = $event as string;
          }
        "
        class="bg-white rounded-none placeholder:text-neutral-400"
      />
    </div>
  </div>
</template>
<script setup lang="ts">
import { Textarea } from "@/components/ui/textarea";
import { reactive } from "vue";

// these will have to change if app key changes, but saving me time now
const PRACTICE_EMBED = `<iframe src="https://localhost/decks/1/invite?fromUserId=1&redirectTo=%2Fdecks%2F1%2Fpractice%2Fembed&role=viewer&token=mvZugOYD2ByaIi6V3Kc7meFRIePd9uCY&signature=c8319b633a8c0a812d5c0cb883beef93792d85a7684a61a1d7d065a5a8a39364" width="100%" height="640px" frameborder="0" allowfullscreen></iframe>`;

const QUIZ_EMBED = `<iframe src="https://localhost/decks/1/invite?fromUserId=1&redirectTo=%2Fdecks%2F1%2Fquiz%2Fembed&role=viewer&token=mvZugOYD2ByaIi6V3Kc7meFRIePd9uCY&signature=e5cd1f01a3ac26ea5e4369bae5ca01dc4861d047917550e5a9871b228b245edb" width="100%" height="640px" frameborder="0" allowfullscreen></iframe>`;

const MATCHING_EMBED = `<iframe src="https://localhost/decks/1/invite?fromUserId=1&redirectTo=%2Fdecks%2F1%2Fgames%2Fmatching%2Fembed&role=viewer&token=mvZugOYD2ByaIi6V3Kc7meFRIePd9uCY&signature=67ba65cbdc0fc5c25b5bc26b335a841d95843ff5063761760f0bc2df1ca36c35" width="100%" height="640px" frameborder="0" allowfullscreen></iframe>`;

const embedCodes = reactive({
  practice: PRACTICE_EMBED,
  quiz: QUIZ_EMBED,
  matching: MATCHING_EMBED,
});
</script>
<style scoped></style>
