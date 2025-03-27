<script setup lang="ts">
  import { AutoForm } from '@/components/ui/auto-form';
  import { DependencyType } from '@/components/ui/auto-form/interface';
  import { Button } from '@/components/ui/button';
  import { toast } from '@/components/ui/toast';
  import { router } from '@inertiajs/vue3';
  import { toTypedSchema } from '@vee-validate/zod';
  import { useForm } from 'vee-validate';
  import { z } from 'zod';

  const schema = z.object({
    name: z.string().min(1, 'Name is required'),
    email: z.string().email('Invalid email'),
    serialCode: z.string().min(1, 'Serial code is required'),
    lostDateTime: z.coerce.date().optional(),
    phone: z.string().min(1, 'Phone number is required'),
    country: z.string().min(1, 'Country is required'),
    city: z.string().min(1, 'City is required'),
    streetAddress: z.string().min(1, 'Street address is required'),
    idCardImage: z.any().optional(),
    purchaseLocation: z.string().min(1, 'Purchase location is required'),
    files: z.any().optional(),
    lostItemType: z
      .enum(['Bag', 'Shoe', 'Watch', 'Other'], {
        errorMap: () => ({ message: 'Select a valid item type' }),
      })
      .default('Other'),
  });

  const form = useForm({
    validationSchema: toTypedSchema(schema),
  });

  // values: Record<string, any>
  function onSubmit(values: Record<string, any>) {
    const formData = new FormData();
    Object.entries(values).forEach(([key, value]) => {
      if (key === 'files' && Array.isArray(value)) {
        value.forEach((file) => {
          formData.append('files[]', file);
        });
      } else if (key === 'idCardImage' && value instanceof File) {
        console.log(key, value);

        formData.append('idCardImage', value);
      } else if (key === 'lostDateTime' && value) {
        const formattedDate = new Date(value).toISOString().split('T')[0];
        formData.append('lostDateTime', formattedDate);
      } else {
        formData.append(key, value);
      }
    });

    console.log(values);

    router.visit('lost/lost-items', {
      method: 'post',
      data: formData,
      headers: {
        'Content-Type': 'multipart/form-data',
      },
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
        serialCode: {
          label: 'Product Serial Code',
          inputProps: { type: 'text', placeholder: 'Enter the serial code' },
        },
        lostDateTime: {
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
        streetAddress: {
          label: 'Street Address',
          inputProps: { type: 'text', placeholder: 'Enter your street address' },
        },
        purchaseLocation: {
          label: 'Purchase Location',
          inputProps: { type: 'text', placeholder: 'Enter purchase location' },
        },
        idCardImage: {
          label: 'Upload ID Card',
          description: 'Accepted formats: JPG, PNG, PDF.',
          component: 'file',
        },
        files: {
          label: 'Upload Files',
          description: 'Upload any relevant documents or images.',
          component: 'file',
          inputProps: { multiple: true },
        },
        lostItemType: {
          label: 'Lost Item Type',
          description: 'Select the type of item you lost.',
        },
      }"
      :dependencies="[
        {
          sourceField: 'lostItemType',
          type: DependencyType.SETS_OPTIONS,
          targetField: 'lostItemType',
          when: (sourceFieldValue) => sourceFieldValue === 'lostItemType',
          options: ['Bag', 'Shoe', 'Watch', 'Other'],
        },
      ]"
      @submit="onSubmit"
    >
      <Button class="col-span-full ml-auto mt-4 w-32" type="submit"> Submit </Button>
    </AutoForm>
  </section>
</template>
