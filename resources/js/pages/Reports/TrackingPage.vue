<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Toaster } from '@/components/ui/toast';
import { Link, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import axios from 'axios';

const trackingCode = usePage().props.tracking_code as string;
const userLocation = ref<{ lat: number; lng: number } | null>(null);

onMounted(() => {
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(
            (position) => {
                userLocation.value = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude,
                };

                // Save location to the database every 10 seconds
                setInterval(() => {
                    if (userLocation.value) {
                        axios.post('/reports/items/save-tracking', {
                            user_id: usePage().props.auth.user.id, // Assuming user ID is available in props
                            tracking_code: trackingCode,
                            latitude: userLocation.value.lat,
                            longitude: userLocation.value.lng,
                        });
                    }
                }, 10000);
            },
            (error) => {
                console.error('Error fetching location:', error);
            },
            { enableHighAccuracy: true }
        );
    } else {
        console.error('Geolocation is not supported by this browser.');
    }
});
</script>

<template>
    <Toaster />
    <section class="body-font container relative mx-auto h-screen overflow-x-clip py-10 sm:py-10">
        <div
            class="col-span-full flex items-center justify-between pb-10 text-4xl font-bold text-[#ddd] sm:text-5xl md:text-8xl">
            <h1>Tracking Page</h1>
            <Link class="rounded-full bg-[#333] px-3 py-2 text-sm md:px-6" :href="route('logout')">Logout</Link>
        </div>

        <div class="flex h-2/3 items-center justify-center">
            <div
                class="mx-auto mt-10 max-w-lg space-y-4 rounded-lg bg-white p-6 text-center shadow-md dark:bg-zinc-900">
                <h1 class="text-2xl font-semibold text-green-600 dark:text-green-500">Tracking Code</h1>
                <p class="text-muted-foreground">Your tracking code is:</p>
                <div class="flex items-center justify-center space-x-2">
                    <span class="rounded bg-zinc-100 px-3 py-1 font-mono text-lg text-primary dark:bg-zinc-800">
                        {{ trackingCode }}
                    </span>
                </div>
                <div class="text-center text-red-500 font-semibold mt-4">
                    For live tracking, you cannot close this window. If you close this window, the admin will not see
                    your location.
                </div>
            </div>
        </div>

        <div v-if="userLocation" class="mt-10 h-96 w-full">
            <iframe :src="`https://www.google.com/maps?q=${userLocation.lat},${userLocation.lng}&z=15&output=embed`"
                class="h-full w-full border-0" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>
</template>

<style scoped>
/* Add any specific styles for the tracking page here */
</style>
