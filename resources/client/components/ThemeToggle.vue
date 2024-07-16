<template>
  <Button @click="toggleTheme">
    <span class="capitalize">{{ theme }}</span>
  </Button>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { Button } from '@/components/ui/button';

const themes = [
  'light',
  'dark',
  // 'cafe',
  // 'contrast',
  // 'system',
];
const theme = ref(localStorage.getItem('theme') || 'system');

const toggleTheme = () => {
  const currentIndex = themes.indexOf(theme.value);
  const nextIndex = (currentIndex + 1) % themes.length;
  theme.value = themes[nextIndex];
};

watch(theme, (newTheme) => {
  localStorage.setItem('theme', newTheme);
  updateTheme(newTheme);
});

const updateTheme = (newTheme: string) => {
  if (newTheme === 'system') {
    const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches
      ? 'dark'
      : 'light';
    document.documentElement.setAttribute('data-theme', systemTheme);
  } else {
    document.documentElement.setAttribute('data-theme', newTheme);
  }
};

// Initial theme setup
updateTheme(theme.value);

// Listen for system theme changes
window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
  if (theme.value === 'system') {
    updateTheme('system');
  }
});
</script>
