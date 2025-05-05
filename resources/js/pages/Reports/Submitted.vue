<script setup lang="ts">
  import { Button } from '@/components/ui/button';
  import { Toaster, useToast } from '@/components/ui/toast';
  import { Link, usePage } from '@inertiajs/vue3';
  import { ref } from 'vue';

  const { toast } = useToast();
  const trackingCode = usePage().props.tracking_code as string;
  const copied = ref(false);

  function copyTrackingCode() {
    navigator.clipboard.writeText(trackingCode).then(() => {
      copied.value = true;
      toast({
        title: 'Copied!',
        description: 'Tracking code has been copied to clipboard.',
      });
    });
  }
</script>

<template>
  <Toaster />
  <section class="body-font container relative mx-auto h-screen overflow-x-clip py-10 sm:py-10">
    <div class="col-span-full flex items-center justify-between pb-10 text-4xl font-bold text-[#ddd] sm:text-5xl md:text-8xl">
      <h1>Reported Submitted</h1>
      <Link class="rounded-full bg-[#333] px-3 py-2 text-sm md:px-6" :href="route('logout')">Logout </Link>
    </div>

    <div class="flex h-2/3 items-center justify-center">
      <div class="mx-auto mt-10 max-w-lg space-y-4 rounded-lg bg-white p-6 text-center shadow-md dark:bg-zinc-900">
        <h1 class="text-2xl font-semibold text-green-600 dark:text-green-500">Report Submitted Successfully</h1>
        <p class="text-muted-foreground">Your tracking code is:</p>
        <div class="flex items-center justify-center space-x-2">
          <span class="rounded bg-zinc-100 px-3 py-1 font-mono text-lg text-primary dark:bg-zinc-800">
            {{ trackingCode }}
          </span>
          <Button variant="outline" size="sm" @click="copyTrackingCode">
            {{ copied ? 'Copied!' : 'Copy' }}
          </Button>
        </div>
        <p class="text-sm text-red-600 dark:text-red-500">Please <b>save</b> it to follow up on your report status later.</p>
      </div>
    </div>
  </section>
</template>
