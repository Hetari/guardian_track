<script setup lang="ts">
  import { AutoForm } from '@/components/ui/auto-form';
  import { DependencyType } from '@/components/ui/auto-form/interface';
  import { Button } from '@/components/ui/button';
  import { toast } from '@/components/ui/toast';
  import { useForm } from '@inertiajs/vue3';
  import * as z from 'zod';

  const schema = z.object({
    name: z.string().min(1, 'Name is required'),
    email: z.string().email('Invalid email'),
    serialCode: z.string().min(1, 'Serial code is required'),
    lostDateTime: z.string().min(1, 'Loss date and time are required'),
    phone: z.string().min(1, 'Phone number is required'),
    country: z.string().min(1, 'Country is required'),
    city: z.string().min(1, 'City is required'),
    streetAddress: z.string().min(1, 'Street address is required'),
    idCardImage: z.any(),
    purchaseLocation: z.string().min(1, 'Purchase location is required'),
    files: z.any(),
    lostItemType: z.enum(['Bag', 'Shoe', 'Watch', 'Other'], {
      errorMap: () => ({ message: 'Select a valid item type' }),
    }),
  });

  const form = useForm({
    name: '',
    email: '',
    serialCode: '',
    lostDateTime: '',
    phone: '',
    country: '',
    city: '',
    streetAddress: '',
    idCardImage: null,
    purchaseLocation: '',
    files: [],
    lostItemType: '',
  });

  function onSubmit(values: Record<string, any>) {
    form.post('/lost-items', {
      preserveScroll: true,
      onSuccess: () => {
        toast({ title: 'Success', description: 'Report submitted successfully!' });
      },
      onError: (errors) => {
        console.error(errors);
        toast({ title: 'Error', description: 'Failed to submit report.' });
      },
    });
  }
</script>

<template>
  <section class="body-font container relative mx-auto py-24">
    <h1 class="col-span-full pb-24 text-9xl font-bold text-[#ddd]">Lost Report</h1>

    <AutoForm
      class="grid grid-cols-3 gap-4"
      :schema="schema"
      :field-config="{
        name: { inputProps: { type: 'text', placeholder: 'Name' } },
        email: { inputProps: { type: 'email', placeholder: 'Email' } },
        serialCode: { inputProps: { type: 'text', placeholder: 'Product Serial Code' } },
        lostDateTime: { inputProps: { type: 'datetime-local' } },
        phone: { inputProps: { type: 'tel', placeholder: 'Phone Number' } },
        country: { inputProps: { type: 'text', placeholder: 'Country' } },
        city: { inputProps: { type: 'text', placeholder: 'City' } },
        streetAddress: { inputProps: { type: 'text', placeholder: 'Street Address' } },
        idCardImage: { inputProps: { type: 'file' } },
        purchaseLocation: { inputProps: { type: 'text', placeholder: 'Purchase Location' } },
        files: { inputProps: { type: 'file', multiple: true } },
        lostItemType: {
          component: 'radio',
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
      <Button class="col-span-full ml-auto w-32" type="submit"> Submit </Button>
    </AutoForm>
  </section>
</template>
