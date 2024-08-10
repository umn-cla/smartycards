<template>
  <AuthenticatedLayout>
    <div
      v-if="currentUser"
      class="max-w-screen-md mx-auto"
    >
      <PageHeader
        title="Profile"
        :subtitle="currentUser.name"
      >
        <Button asChild>
          <RouterLink :to="{ name: 'auth.logout' }"> Logout </RouterLink>
        </Button>
      </PageHeader>
      <Tuple label="Name">
        {{ currentUser.name }}
      </Tuple>
      <Tuple label="Email">
        {{ currentUser.email }}
      </Tuple>
    </div>
  </AuthenticatedLayout>
</template>
<script setup lang="ts">
import { useAuthQuery } from '@/queries/auth';
import { AuthenticatedLayout } from '@/layouts/AuthenticatedLayout';
import PageHeader from '@/components/PageHeader.vue';
import Tuple from '@/components/Tuple.vue';
import { Button } from '@/components/ui/button';

const { data: currentUser } = useAuthQuery();
</script>
<style scoped></style>
