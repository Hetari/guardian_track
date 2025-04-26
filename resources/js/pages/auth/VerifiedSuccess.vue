<template>
  <div class="flex min-h-screen flex-col items-center justify-center">
    <h1 class="mb-4 text-2xl font-bold">Email Verified!</h1>
    <p class="mb-6">Thanks for verifying your email. You can now access your account.</p>

    <a :href="auth.user.role === 'admin' ? '/dashboard' : '/'" class="rounded bg-green-600 px-4 py-2 text-white hover:bg-green-700">
      Go to {{ auth.user.role === 'admin' ? 'Dashboard' : 'Home' }}
    </a>
  </div>
</template>

<script setup lang="ts">
  import { usePage } from '@inertiajs/vue3';
  import { computed } from 'vue';

  type User = {
    user: {
      id: number;
      name: string;
      email: string;
      email_verified_at: string | null;
      phone: string;
      role: string;
    };
  };

  const page = usePage();
  const auth = computed<User>(() => page.props.auth as User);
</script>
