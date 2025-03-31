<script setup lang="ts">
  import AppLayout from '@/layouts/AppLayout.vue';
  import { type BreadcrumbItem } from '@/types';
  import { Head } from '@inertiajs/vue3';

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Dashboard',
      href: '/dashboard',
    },
  ];

  const { users_count = 0, reports_count = 0 } = defineProps<{
    users_count: number;
    reports_count: number;
  }>();

  const dashboardInfo = [
    {
      title: 'Users',
      count: users_count,
    },
    {
      title: 'Reports',
      count: reports_count,
    },
  ];
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="grid auto-rows-min gap-4 md:grid-cols-3 lg:grid-cols-6">
        <div
          v-for="info in dashboardInfo"
          :key="info.title"
          class="relative overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min"
        >
          <div class="flex items-center justify-between px-8 py-12 text-3xl font-bold">
            <h1>
              {{ info.title }}
            </h1>
            <span>{{ info.count }}</span>
          </div>
        </div>
      </div>
      <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
        <!-- <PlaceholderPattern /> -->
      </div>
    </div>
  </AppLayout>
</template>
