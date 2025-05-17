<script setup lang="ts">
  import { onMounted, onUnmounted, ref } from 'vue';
  // Removed import of useRoute from 'vue-router' due to missing package
  import axios from 'axios';

  // Instead of useRoute, get trackingCode from props
  const props = defineProps({
    trackingCode: String,
  });

  const trackingCode = props.trackingCode;

  const location = ref<{ lat: number; lng: number } | null>(null);
  const error = ref<string | null>(null);
  let intervalId: number | null = null;

  async function fetchLocation() {
    try {
      const response = await axios.get(`/reports/items/live-tracking/${trackingCode}/location`);
      location.value = {
        lat: response.data.latitude,
        lng: response.data.longitude,
      };
      console.log({ response });

      error.value = null;
    } catch (err) {
      error.value = 'Failed to fetch location';
    }
  }

  onMounted(() => {
    fetchLocation();
    intervalId = window.setInterval(fetchLocation, 10000);
  });

  onUnmounted(() => {
    if (intervalId) {
      clearInterval(intervalId);
    }
  });
</script>

<template>
  <section class="container mx-auto p-4">
    <h1 class="mb-4 text-2xl font-bold">Live Tracking: {{ trackingCode }}</h1>
    <div v-if="error" class="text-red-600">{{ error }}</div>
    <div v-if="location" class="h-96 w-full">
      <iframe
        :src="`https://www.google.com/maps?q=${location.lat},${location.lng}&z=15&output=embed`"
        class="h-full w-full border-0"
        allowfullscreen
        loading="lazy"
        referrerpolicy="no-referrer-when-downgrade"
      ></iframe>
    </div>
    <div v-else class="text-gray-500">Loading location...</div>
  </section>
</template>

<style scoped>
  /* Add any specific styles here */
</style>
