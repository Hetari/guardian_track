<template>
  <Head title="Reports">
    <link rel="preconnect" href="https://rsms.me/" />
  </Head>

  <section class="body-font container mx-auto grid grid-cols-3 gap-10 py-24">
    <h1 class="col-span-full text-9xl font-bold text-[#ddd]">Reports</h1>

    <template v-for="circle in circles" :key="circle.name">
      <Link
        :href="`/reports/${circle.title.toLowerCase()}`"
        class="mb-6 w-full select-none rounded-lg bg-[#191919] p-4 text-gray-600 transition-all duration-150 hover:scale-105 hover:bg-[#222] lg:mb-0"
      >
        <div class="h-full text-center">
          <img
            class="pointer-events-none mb-8 inline-block size-20 rounded-full border-2 p-3.5"
            :class="{
              'bg-red-500 brightness-90': circle.accent === 'red',
              'bg-yellow-500 brightness-90': circle.accent === 'yellow',
              'bg-green-500 brightness-90': circle.accent === 'green',
            }"
            :src="circle.svg"
          />
          <h2 class="title-font text-sm font-medium tracking-wider text-gray-100">{{ circle.title }}</h2>

          <div class="flex h-1/2 flex-col items-center justify-between">
            <p class="leading-relaxed">
              {{ circle.body }}
            </p>
            <span
              class="m-6 h-1 w-14 rounded"
              :class="{
                'bg-red-500': circle.accent === 'red',
                'bg-yellow-500': circle.accent === 'yellow',
                'bg-green-500': circle.accent === 'green',
              }"
            />
          </div>
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
      body: 'If your product is stolen',
      accent: 'red',
    },
    {
      svg: 'lost.svg',
      title: 'Lost',
      body: 'If your product is lost',
      accent: 'yellow',
    },
    {
      svg: 'checked.svg',
      title: 'Report',
      body: "Follow up on a report you've made",
      accent: 'green',
    },
  ];
</script>
