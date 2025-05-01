<script setup lang="ts">
  import { Button } from '@/components/ui/button';
  import { Toaster, useToast } from '@/components/ui/toast';
  import { usePage } from '@inertiajs/vue3';
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

  <div class="absolute inset-0 flex items-center justify-center">
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
</template>
