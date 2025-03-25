<template>
  <Head title="Reports">
    <link rel="preconnect" href="https://rsms.me/" />
  </Head>

  <section class="body-font container mx-auto grid grid-cols-3 gap-10 py-24">
    <h1 class="col-span-full text-9xl font-bold text-[#ddd]">Reports</h1>

    <template v-for="circle in circles" :key="circle.name">
      <Link
        :href="`/reports/${circle.title.toLowerCase()}`"
        class="group relative z-0 mb-6 w-full select-none rounded-lg bg-[#191919] p-4 transition-all duration-200 ease-in-out hover:bg-[#222] lg:mb-0"
      >
        <div class="h-full text-center">
          <img
            class="pointer-events-none z-50 mb-8 inline-block size-20 rounded-full p-3.5"
            :class="{
              'bg-red-500': circle.accent === 'red',
              'bg-yellow-500': circle.accent === 'yellow',
              'bg-green-500': circle.accent === 'green',
            }"
            :src="circle.svg"
          />
          <h2 class="title-font z-50 text-lg font-medium tracking-wider text-white transition-colors duration-200 ease-in-out group-hover:text-black">
            {{ circle.title }}
          </h2>

          <p class="z-[999999] pb-14 leading-relaxed text-gray-400 transition-colors duration-200 ease-in-out group-hover:text-gray-600">
            {{ circle.body }}
          </p>
          <span
            class="absolute bottom-8 left-1/2 -z-10 inline-block h-1 w-14 !origin-top -translate-x-1/2 rounded transition-all duration-200 ease-in-out group-hover:inset-x-0 group-hover:bottom-0 group-hover:size-full group-hover:-translate-x-0"
            :class="{
              'bg-red-500': circle.accent === 'red',
              'bg-yellow-500': circle.accent === 'yellow',
              'bg-green-500': circle.accent === 'green',
            }"
          />
        </div>
      </Link>
    </template>
  </section>
</template>

<script setup lang="ts">
  import { Head, Link } from '@inertiajs/vue3';

  const circles = [
    {
      svg: 'stolen.svg',
      title: 'Stolen',
      body: 'Report a stolen product',
      accent: 'red',
    },
    {
      svg: 'lost.svg',
      title: 'Lost',
      body: 'Report a lost product',
      accent: 'yellow',
    },
    {
      svg: 'checked.svg',
      title: 'Report',
      body: 'Track your report status',
      accent: 'green',
    },
  ];
</script>
