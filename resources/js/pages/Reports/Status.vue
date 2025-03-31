<script setup lang="ts">
  defineProps<{
    statuses: {
      [key: string]: string;
    };
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
  <section class="body-font container relative mx-auto pt-24">
    <h1 class="col-span-full pb-10 text-8xl font-bold text-[#ddd]">Report Status</h1>

    <div class="grid grid-cols-3 gap-4">
      <div v-for="report in reports" :key="report.id" class="rounded-2xl border border-[#333] bg-[#1F1F1F] p-6 shadow-xl backdrop-blur-lg">
        <div class="flex flex-col space-y-4">
          <!-- Report Details -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <h3 class="text-2xl font-semibold leading-none text-gray-300">{{ report.product_name }}</h3>
              <p class="text-xs font-semibold text-[#ddd] opacity-50">Product Name</p>
            </div>
            <div>
              <h3 class="text-2xl font-semibold leading-none text-gray-300">{{ report.serial_code }}</h3>
              <p class="text-xs font-semibold text-[#ddd] opacity-50">Serial Code</p>
            </div>
            <div>
              <h3 class="text-2xl font-semibold leading-none text-gray-300">{{ new Date(report.date_time).toLocaleDateString() }}</h3>
              <p class="text-xs font-semibold text-[#ddd] opacity-50">Date</p>
            </div>
            <div>
              <h3 class="text-2xl font-semibold leading-none text-gray-300">{{ report.type }}</h3>
              <p class="text-xs font-semibold text-[#ddd] opacity-50">Type</p>
            </div>
          </div>

          <!-- Progress Tracker -->
          <div class="relative pt-8">
            <h2 class="sr-only">Steps</h2>
            <ol class="relative z-10 flex flex-col justify-between gap-2 text-sm font-medium text-gray-400">
              <li
                v-for="(key, _, index) in statuses"
                :key="index"
                class="flex items-center gap-2 rounded-full px-4 py-2"
                :class="Object.keys(statuses).indexOf(report.status) >= index ? 'bg-[#2B2B2B]' : 'bg-[#333]'"
              >
                <span
                  class="flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-bold"
                  :class="Object.keys(statuses).indexOf(report.status) >= index ? 'bg-[#3B82F6] text-white' : 'bg-[#444] text-gray-300'"
                >
                  {{ index + 1 }}
                </span>
                <span :class="Object.keys(statuses).indexOf(report.status) >= index ? 'text-white' : 'text-gray-300'">
                  {{ key }}
                </span>
              </li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>
