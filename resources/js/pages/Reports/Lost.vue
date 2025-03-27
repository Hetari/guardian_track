<script setup lang="ts">
  import { AutoForm, AutoFormLabel } from '@/components/ui/auto-form';
  import { DependencyType } from '@/components/ui/auto-form/interface';
  import { Button } from '@/components/ui/button';
  import { FormControl, FormField, FormItem, FormMessage } from '@/components/ui/form';
  import { toast } from '@/components/ui/toast';
  import { router } from '@inertiajs/vue3';
  import { toTypedSchema } from '@vee-validate/zod';
  import { useForm } from 'vee-validate';
  import { z } from 'zod';
  import { useLocation } from './useLocation';

  const schema = z.object({
    name: z.string().min(1, 'Name is required'),
    email: z.string().email('Invalid email'),
    serial_code: z.string().min(1, 'Serial code is required'),
    lost_date_time: z.coerce.date(),
    phone: z.string().min(1, 'Phone number is required'),
    country: z.string().min(1, 'Country is required'),
    city: z.string().min(1, 'City is required'),
    street_address: z.string().min(1, 'Street address is required'),
    purchase_location: z.string().min(1, 'Purchase location is required'),
    id_card_image: z.any().optional().default(null),
    files: z.any().optional().default([]),
    lost_item_type: z
      .enum(['Bag', 'Shoe', 'Watch', 'Other'], {
        errorMap: () => ({ message: 'Select a valid item type' }),
      })
      .default('Other'),
    location: z.string().optional(),
  });

  const { coords, fetchAddress, form } = useLocation(
    useForm({
      validationSchema: toTypedSchema(schema),
    }),
  );

  // values: Record<string, any>
  function onSubmit(values: Record<string, any>) {
    const formData = new FormData();
    Object.entries(values).forEach(([key, value]) => {
      if (value === null || value === undefined) {
        return;
      }

      if (key === 'files' && Array.isArray(value) && value.length) {
        value.forEach((file) => {
          formData.append('files[]', file);
        });
      } else if (key === 'id_card_image' && value && value instanceof File) {
        formData.append('id_card_image', value);
      } else if (key === 'lost_date_time' && value) {
        const formattedDate = new Date(value).toISOString().split('T')[0];
        formData.append('lost_date_time', formattedDate);
      } else {
        formData.append(key, value);
      }
    });

    router.post('lost/lost-items', formData, {
      onSuccess: () => {
        toast({
          title: 'Success',
          description: 'Report submitted successfully!',
        });
      },
      onError: (errors) => {
        console.error(errors);
        toast({ title: 'Error', description: 'Failed to submit report.' });
      },
    });
  }
</script>

<template>
  <div class="mt-2 hidden w-[340px] rounded-md bg-slate-950 p-4"></div>
  <section class="body-font container relative mx-auto py-24">
    <h1 class="col-span-full pb-24 text-9xl font-bold text-[#ddd]">Lost Report</h1>

    <AutoForm
      class="grid grid-cols-3 gap-4"
      :schema="schema"
      :form="form"
      :field-config="{
        name: {
          label: 'Full Name',
          inputProps: { type: 'text', placeholder: 'Enter your name' },
        },
        email: {
          label: 'Email Address',
          inputProps: { type: 'email', placeholder: 'Enter your email' },
        },
        serial_code: {
          label: 'Product Serial Code',
          inputProps: { type: 'text', placeholder: 'Enter the serial code' },
        },
        lost_date_time: {
          // @ts-ignore
          label: 'Lost Date & Time',
          description: 'Specify when the item was lost.',
          inputProps: { type: 'datetime-local' },
        },
        phone: {
          label: 'Phone Number',
          inputProps: { type: 'tel', placeholder: 'Enter your phone number' },
        },
        country: {
          label: 'Country',
          inputProps: { type: 'text', placeholder: 'Enter your country' },
        },
        city: {
          label: 'City',
          inputProps: { type: 'text', placeholder: 'Enter your city' },
        },
        street_address: {
          label: 'Street Address',
          inputProps: { type: 'text', placeholder: 'Enter your street address' },
        },
        purchase_location: {
          label: 'Purchase Location',
          inputProps: { type: 'text', placeholder: 'Enter the purchase location' },
        },
        id_card_image: {
          label: 'Upload ID Card',
          description: 'Accepted formats: JPG, PNG, PDF.',
          component: 'file',
          inputProps: { accept: '.jpg,.png,.pdf' },
        },
        files: {
          label: 'Upload Files',
          description: 'Upload any relevant documents or images.',
          component: 'file',
          inputProps: { multiple: true },
        },
        lost_item_type: {
          label: 'Lost Item Type',
          description: 'Select the type of item you lost.',
        },
        location: {},
      }"
      :dependencies="[
        {
          sourceField: 'lost_item_type',
          type: DependencyType.SETS_OPTIONS,
          targetField: 'lost_item_type',
          when: (sourceFieldValue) => sourceFieldValue === 'lost_item_type',
          options: ['Bag', 'Shoe', 'Watch', 'Other'],
        },
      ]"
      @submit="onSubmit"
    >
      <template #location="slotProps">
        <div
          @click="
            () => {
              fetchAddress(coords.latitude, coords.longitude, form);
            }
          "
        >
          <FormField :name="slotProps.fieldName">
            <FormItem v-bind="$attrs">
              <AutoFormLabel>Location </AutoFormLabel>

              <FormControl>
                <Button class="block h-10 w-full rounded-lg border border-input" type="button" variant="secondary" v-bind="slotProps"
                  >Location</Button
                >
              </FormControl>

              <!-- <FormDescription> Select the location where you purchased the item. </FormDescription> -->
              <FormMessage />
            </FormItem>
          </FormField>
        </div>
      </template>
      <Button class="col-span-full ml-auto mt-4 w-32" type="submit"> Submit </Button>
    </AutoForm>
  </section>
</template>
