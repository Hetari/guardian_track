<template>
  <Head title="Welcome">
    <link rel="preconnect" href="https://rsms.me/" />
  </Head>

  <div class="min-h-svh overflow-clip">
    <header class="container relative z-[999999] mx-auto flex items-center justify-between px-6 pt-12 text-sm md:px-12 md:text-lg">
      <div>
        <img src="/logo.png" alt="" />
      </div>
      <div class="flex items-center gap-x-4">
        <Link
          v-if="!is_authenticated"
          class="rounded-full bg-gradient-to-r from-[#003332] from-[-10%] to-[#4FBBB9] px-3 py-2 md:px-6"
          :href="route('login')"
          >login</Link
        >
        <Link
          v-else-if="is_authenticated && auth?.user?.role == 'admin'"
          class="rounded-full bg-gradient-to-r from-[#003332] from-[-10%] to-[#4FBBB9] px-3 py-2 md:px-6"
          :href="route('register')"
          >create account
        </Link>
        <Link v-if="is_authenticated" class="rounded-full bg-[#222] px-3 py-2 md:px-6" :href="route('reports.index')">reports</Link>
      </div>
    </header>

    <main class="relative flex h-[80vh] flex-col items-start pt-12">
      <Main />
      <Lines />

      <!-- circles -->
      <div class="absolute -bottom-1/4 left-1/2 h-[90vh] w-[90vw] -translate-x-1/2 translate-y-1/3 bg-[#4FBBB9] opacity-30 blur-[200px]" />
      <div class="absolute bottom-0 left-1/2 h-[50vh] w-[50vw] -translate-x-1/2 translate-y-1/2 bg-[#4FBBB9] opacity-50 blur-[150px]" />
      <div class="absolute bottom-0 left-1/2 size-[30vw] -translate-x-1/2 translate-y-1/2 rounded-full bg-[#4FBBB9] opacity-100 blur-[100px]" />
    </main>
  </div>

  <!-- Container for lines -->
  <!-- <div class="absolute inset-0 grid grid-cols-12">
    <template v-for="i in 12" :key="i">
      <div class="h-full w-[5px] bg-gradient-to-b from-black to-transparent opacity-10" />
    </template>
  </div> -->
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
        console.log('dark class removed');
        classes.add('dark');
      }
    }
  });
</script>
