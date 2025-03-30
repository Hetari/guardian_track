<script setup lang="ts">
  defineProps<{
    reports: {
      id: string;
      type: string;
      product_name: string;
      serial_code: string;
      date_time: string;
      country: string;
      city: string;
      street_address: string;
      purchase_location: string;
      item_type: string;
      status: string;
    }[];
  }>();
</script>

<template>
  <div class="mt-2 hidden w-[340px] rounded-md bg-slate-950 p-4"></div>
  <section class="body-font container relative mx-auto pt-24">
    <h1 class="col-span-full pb-10 text-8xl font-bold text-[#ddd]">Report Status</h1>

    <div>
      {{ reports[0] }}
    </div>
  </section>
</template>
