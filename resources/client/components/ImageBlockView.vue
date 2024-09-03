<template>
  <img
    :src="src"
    :alt="alt"
    class="rounded-sm w-full h-full object-contain"
    @click="isLightboxVisible = true"
  />
  <Teleport to="body">
    <div
      v-if="isLightboxVisible"
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-80 z-50"
    >
      <div class="relative" @click.stop>
        <img :src="src" :alt="alt" class="max-w-full max-h-full" />
        <button
          @click="isLightboxVisible = false"
          class="absolute top-2 right-2 text-white text-3xl rounded-full w-10 h-10 bg-black/50 flex items-center justify-center hover:bg-black/70"
        >
          <IconX class="size-5" />
          <span class="sr-only">Close</span>
        </button>
      </div>
    </div>
  </Teleport>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref, watch } from "vue";
import { defineProps } from "vue";
import { IconX } from "./icons";

defineProps<{
  src: string;
  alt: string;
}>();

const isLightboxVisible = ref(false);

function closeOnEscape(e: KeyboardEvent) {
  if (e.key === "Escape") {
    isLightboxVisible.value = false;
  }
}

function closeOnClickOutside(e: MouseEvent) {
  if (e.target === document.querySelector(".fixed")) {
    isLightboxVisible.value = false;
  }
}

onMounted(() => {
  // Close lightbox on escape key press
  window.addEventListener("keydown", closeOnEscape);

  // Close lightbox on click outside
  window.addEventListener("click", closeOnClickOutside);
});

onUnmounted(() => {
  window.removeEventListener("keydown", closeOnEscape);
  window.removeEventListener("click", closeOnClickOutside);
});

// watch(isLightboxVisible, () => {
//  // Disable scrolling when lightbox is visible?
//   document.body.style.overflow = isLightboxVisible.value ? "hidden" : "";
// });
</script>

<style scoped></style>
