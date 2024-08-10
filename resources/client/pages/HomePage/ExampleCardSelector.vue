<template>
  <div class="flex gap-8 items-center">
    <div class="mt-8 relative">
      <Card class="w-[200px] h-[300px] flex flex-col items-center justify-center p-4 relative z-10">
        <p class="flex-1 flex items-center justify-center">Example card goes here</p>
        <p v-if="showHint" class="text-sm text-gray-500 my-4">Hint goes here</p>
        <Button variant="secondary" size="sm" @click="showHint = !showHint">Hint</Button>
      </Card>
      <Card
        class="w-[200px] h-[300px] flex flex-col items-center justify-center p-4 absolute bottom-0 rotate-3"
      />
      <Card
        class="w-[200px] h-[300px] flex flex-col items-center justify-center p-4 absolute bottom-0 -rotate-3"
      />
    </div>
    <div class="flex flex-col items-start border">
      <Button
        variant="ghost"
        v-for="(topic, index) in options"
        :key="index"
        @click="activeOptionIndex = index"
        :class="cn({ 'bg-white': activeOptionIndex === index })"
      >
        {{ topic }}
      </Button>
    </div>
  </div>
</template>

<script lang="ts" setup>
import { cn } from '@/lib/utils';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';

const options = ['Spanish', 'Geography', 'Orinthology', 'Art History', 'Anatomy & Physiology'];

const activeOptionIndex = ref(0);
const showHint = ref(false);
</script>
