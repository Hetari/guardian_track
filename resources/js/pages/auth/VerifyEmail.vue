<script setup lang="ts">
  import TextLink from '@/components/TextLink.vue';
  import { Button } from '@/components/ui/button';
  import AuthLayout from '@/layouts/AuthLayout.vue';
  import { Head, router, useForm } from '@inertiajs/vue3';
  import { LoaderCircle } from 'lucide-vue-next';
  import { ref } from 'vue';

  defineProps<{
    status?: string;
  }>();

  const message = ref('');
  const form = useForm({});

  // const submit1 = () => {
  //   form.post(route('verification.send'));
  // };
  const submit = () => {
    router.post(
      '/email/verification-notification',
      {},
      {
        onSuccess: () => {
          message.value = 'Verification email resent!';
        },
      },
    );
  };
</script>

<template>
  <AuthLayout title="Verify email" description="Please verify your email address by clicking on the link we just emailed to you.">
    <Head title="Email verification" />

    <div v-if="status === 'verification-link-sent'" class="mb-4 text-center text-sm font-medium text-green-600">
      A new verification link has been sent to the email address you provided during registration.
    </div>

    <form @submit.prevent="submit" class="space-y-6 text-center">
      <Button :disabled="form.processing" variant="secondary">
        <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
        Resend verification email
      </Button>

      <TextLink :href="route('logout')" method="post" as="button" class="mx-auto block text-sm"> Log out </TextLink>
    </form>
    <div v-if="message" class="mt-4 text-green-600">
      {{ message }}
    </div>
  </AuthLayout>
</template>
