<template>
  <UniversityHeader class="university-header !bg-transparent" />
</template>
<script setup lang="ts">
import { UniversityHeader } from "@umn-latis/cla-vue-template";
import "@umn-latis/cla-vue-template/dist/style.css";
</script>

<style>
/* override default university header styles */
.university-header .fill__maroon {
  fill: #ffde7a;
}

.university-header .fill__dark-gray {
  fill: #f8f0df;
}

.university-header .university-header-button :is(a, svg) {
  color: #f8f0df;
  fill: #f8f0df;
}

.university-header .university-header-button {
  border-radius: 1rem;
}

.university-header .university-header-button:hover {
  background: hsla(0, 0%, 100%, 0.1);
}
</style>
