<script setup lang="ts">
  import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
  } from '@/components/ui/alert-dialog';
  import { cn } from '@/lib/utils';
  import { router } from '@inertiajs/vue3';
  import { useTimeAgo } from '@vueuse/core';
  import { computed, ref } from 'vue';

  const { reports } = defineProps<{
    statuses: { [key: string]: string };
    reports: {
      id: string;
      type: string;
      customer_name: string;
      serial_code: string;
      tracking_code: string;
      date_time: string;
      country: string;
      city: string;
      street_address: string;
      item_type: string;
      lost_ownership_document: boolean;
      company: {
        name: string;
        active: boolean;
        email: string;
      };
      status: string;
      updated_at: string;
      created_at: string;
    }[];
  }>();

  const trackingCodeInput = ref('');
  const submittedCode = ref('');
  const inputSubmitted = ref(false);

  const filteredReports = computed(() => {
    if (!inputSubmitted.value || !submittedCode.value) return [];
    return reports.filter((r) => r.tracking_code === submittedCode.value);
  });

  const submitTrackingCode = () => {
    submittedCode.value = trackingCodeInput.value.trim();
    inputSubmitted.value = true;
  };

  const cancelReport = (id: string) => {
    router.delete(route('reports.delete', id));
    router.reload();
  };
</script>

<template>
  <section class="body-font container relative mx-auto py-10 sm:py-10">
    <div class="col-span-full flex items-center justify-between pb-10 text-4xl font-bold text-[#ddd] sm:text-5xl md:text-8xl">
      <h1>Report Status</h1>
      <Link class="rounded-full bg-[#333] px-3 py-2 text-sm md:px-6" :href="route('logout')">Logout </Link>
    </div>

    <template v-if="!inputSubmitted && filteredReports.length !== 1">
      <div class="mb-10 flex flex-col items-center justify-center gap-4">
        <input
          v-model="trackingCodeInput"
          placeholder="Enter tracking code..."
          class="rounded-md border border-gray-600 bg-[#111] px-4 py-2 text-white placeholder-gray-500 focus:outline-none"
        />
        <button @click="submitTrackingCode" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
          Submit
        </button>
      </div>
    </template>
    <div class="grid grid-cols-1 gap-4" v-if="reports && reports.length">
      <template v-if="inputSubmitted && filteredReports.length == 1">
        <div v-for="report in filteredReports" :key="report.id" class="rounded-2xl border border-[#333] bg-[#1F1F1F] p-6 shadow-xl backdrop-blur-lg">
          <div class="flex flex-col space-y-4">
            <!-- Report Details -->
            <div class="grid grid-cols-4 gap-4">
              <div>
                <p class="text-sm font-semibold text-[#ddd] opacity-50">Customer Name</p>
                <h3 class="text-sm font-semibold text-gray-300">{{ report.customer_name }}</h3>
              </div>
              <div>
                <p class="text-sm font-semibold text-[#ddd] opacity-50">Serial Code</p>
                <h3 class="text-sm font-semibold text-gray-300">{{ report.serial_code }}</h3>
              </div>
              <div>
                <p class="text-sm font-semibold text-[#ddd] opacity-50">Tracking Code</p>
                <h3 class="text-sm font-semibold text-gray-300">{{ report.tracking_code }}</h3>
              </div>
              <div>
                <p class="text-sm font-semibold text-[#ddd] opacity-50">Date</p>
                <h3 class="text-sm font-semibold text-gray-300">{{ new Date(report.date_time).toLocaleDateString() }}</h3>
              </div>
              <div>
                <p class="text-sm font-semibold text-[#ddd] opacity-50">Type</p>
                <h3 class="text-sm font-semibold text-gray-300">{{ report.type }}</h3>
              </div>
              <div>
                <p class="text-sm font-semibold text-[#ddd] opacity-50">Ownership Document Lost</p>
                <h3 class="text-sm font-semibold text-gray-300">{{ report.lost_ownership_document ? 'Yes' : 'No' }}</h3>
              </div>
              <div>
                <p class="text-sm font-semibold text-[#ddd] opacity-50">Company Name</p>
                <h3 class="text-sm font-semibold text-gray-300">#{{ report.company.name }}</h3>
              </div>
            </div>

            <!-- Progress Tracker -->
            <div class="relative pt-8">
              <h2 class="sr-only">Steps</h2>
              <ol class="relative z-10 grid grid-cols-3 flex-col justify-between gap-2 text-sm font-medium text-gray-400 lg:grid-cols-5">
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

            <!--  -->
            <div class="border-t border-[#333] pt-4 text-sm text-gray-400">
              <p>
                Handled by: <span class="font-medium text-gray-200">{{ report.company.name }}</span>
              </p>
              <p>
                Last edit on:
                <span class="font-medium text-gray-200">{{ useTimeAgo(new Date(report.updated_at).toLocaleString()) }}</span>
              </p>
            </div>

            <!-- Contact Support Box -->
            <div class="mt-6 rounded-lg border border-blue-500 bg-[#1A1F2A] p-4 text-blue-300">
              <h4 class="mb-2 font-semibold text-blue-400">Need Help?</h4>
              <p class="mb-3 text-sm">You can contact customer support directly via email.</p>
              <a
                :href="`mailto:${report.company.email}`"
                class="inline-block rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
              >
                Contact Support
              </a>
            </div>

            <!-- Cancel Report Box -->
            <!-- Cancel Report Dialog -->
            <AlertDialog>
              <AlertDialogTrigger
                class="mt-6 inline-block rounded-md border border-red-500 bg-[#2A1A1A] px-4 py-2 text-sm font-semibold text-red-300 transition hover:bg-red-600 hover:text-white"
              >
                Cancel Report
              </AlertDialogTrigger>
              <AlertDialogContent>
                <AlertDialogHeader>
                  <AlertDialogTitle>Cancel This Report?</AlertDialogTitle>
                  <AlertDialogDescription :class="cn('text-sm text-gray-400')">
                    If you've already found the lost item, you can cancel this report. This action cannot be undone.
                  </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                  <AlertDialogCancel :class="cn('rounded-md border border-gray-600 px-4 py-2 text-sm text-white hover:bg-gray-700')">
                    Keep Report
                  </AlertDialogCancel>
                  <AlertDialogAction
                    @click="cancelReport(report.id)"
                    :class="cn('rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700')"
                  >
                    Confirm Cancel
                  </AlertDialogAction>
                </AlertDialogFooter>
              </AlertDialogContent>
            </AlertDialog>
          </div>
        </div>
      </template>
    </div>
    <div v-else class="flex min-h-[70vh] items-center justify-center text-center text-sm text-gray-500">
      <p>No reports found.</p>
    </div>
  </section>
</template>
