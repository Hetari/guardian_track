<template>
  <section class="body-font container relative mx-auto py-10 sm:py-10">
    <div class="col-span-full flex items-center justify-between pb-10 text-4xl font-bold text-[#ddd] sm:text-5xl md:text-8xl">
      <h1>Tracking Page</h1>
    </div>
    <p class="text-lg text-gray-700">This is the tracking page. Content will be added here later.</p>
    <div class="mt-6">
      <Button variant="primary" @click="() => router.push('/tracking')"> Go to Tracking Page </Button>
    </div>
  </section>
</template>

<script setup lang="ts">
  import { router } from '@inertiajs/vue3';
  // Add any required logic here in the future
</script>

<style scoped>
  /* Add any specific styles for the tracking page here */
</style>
