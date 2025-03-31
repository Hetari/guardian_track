<template>
  <Head title="Welcome">
    <link rel="preconnect" href="https://rsms.me/" />
  </Head>

  <div class="min-h-svh overflow-clip">
    <header class="container relative z-[999999] mx-auto flex items-center justify-between px-12 pt-12 text-lg">
      <div>
        <img src="/logo.png" alt="" />
      </div>
      <div class="flex items-center gap-x-4">
        <Link v-if="!is_authenticated" class="rounded-full bg-gradient-to-r from-[#003332] from-[-10%] to-[#4FBBB9] px-6 py-2" :href="route('login')"
          >login</Link
        >
        <Link
          v-else-if="is_authenticated && auth?.user?.role == 'admin'"
          class="rounded-full bg-gradient-to-r from-[#003332] from-[-10%] to-[#4FBBB9] px-6 py-2"
          :href="route('register')"
          >create account
        </Link>
        <Link v-if="is_authenticated" class="rounded-full bg-[#222] px-6 py-2" :href="route('reports.index')">reports</Link>
      </div>
    </header>

    <main class="relative flex flex-col items-start pt-12">
      <Main />

      <Lines />
    </main>
  </div>

  <!-- Container for lines -->
  <div class="absolute inset-0 grid grid-cols-12">
    <!-- 12 Vertical Lines -->
    <template v-for="i in 12" :key="i">
      <div class="h-full w-[5px] bg-gradient-to-b from-black to-transparent opacity-10" />
    </template>
  </div>
</template>

<script setup lang="ts">
  import Lines from '@/components/website/Lines.vue';
  import Main from '@/components/website/Main.vue';
  import { Head, Link, usePage } from '@inertiajs/vue3';
  import { computed, onBeforeMount } from 'vue';

  const page = usePage();
  const auth = computed(() => {
    const user = page.props.auth as {
      user: {
        email: string;
        id: number;
        name: string;
        role: string;
      };
    };
    return user;
  });

  const { is_authenticated = false } = defineProps<{ is_authenticated: boolean }>();
  // remove dark class
  onBeforeMount(() => {
    const htmlElement = document.querySelector('html');
    if (htmlElement) {
      const classes = htmlElement.classList;
      if (!classes.contains('dark')) {
        classes.add('dark');
      }
    }
  });
</script>
