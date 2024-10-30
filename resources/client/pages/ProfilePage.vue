<template>
  <AuthenticatedLayout>
    <div v-if="currentUser" class="max-w-screen-md mx-auto">
      <PageHeader title="Profile" :subtitle="currentUser.name" />
      <div
        class="bg-brand-oatmeal-50 flex flex-col items-center justify-center gap-4 p-8 rounded-lg my-8"
      >
        <Tuple label="Name">
          {{ currentUser.name }}
        </Tuple>
        <Tuple label="Email">
          {{ currentUser.email }}
        </Tuple>
        <Button asChild>
          <a :href="config.api.logoutUrl">Logout</a>
        </Button>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { useAuthQuery } from "@/queries/auth";
import { AuthenticatedLayout } from "@/layouts/AuthenticatedLayout";
import PageHeader from "@/components/PageHeader.vue";
import Tuple from "@/components/Tuple.vue";
import { Button } from "@/components/ui/button";
import config from "@/config";

const { data: currentUser } = useAuthQuery();
</script>
<style scoped></style>
