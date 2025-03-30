<script setup lang="ts">
  import { Button } from '@/components/ui/button';
  import DatePicker from '@/components/ui/DatePicker.vue';
  import { Input } from '@/components/ui/input';
  import { Label } from '@/components/ui/label';
  import { Select } from '@/components/ui/select';
  import SelectContent from '@/components/ui/select/SelectContent.vue';
  import SelectGroup from '@/components/ui/select/SelectGroup.vue';
  import SelectItem from '@/components/ui/select/SelectItem.vue';
  import SelectTrigger from '@/components/ui/select/SelectTrigger.vue';
  import SelectValue from '@/components/ui/select/SelectValue.vue';
  import { toast } from '@/components/ui/toast';
  import Toaster from '@/components/ui/toast/Toaster.vue';
  import { router } from '@inertiajs/vue3';
  import { DateValue } from 'reka-ui';
  import { ref } from 'vue';
  import { useLocation } from './useLocation';

  const productName = ref('');
  const email = ref('');
  const serialCode = ref('');
  const dateTime = ref<DateValue | undefined>();
  const country = ref('');
  const city = ref('');
  const streetAddress = ref('');
  const purchaseLocation = ref('');
  const itemType = ref('');
  const idCardImage = ref<File | null>(null);
  const files = ref<File[]>([]);

  const { coords, fetchAddress } = useLocation({
    country,
    city,
    streetAddress,
  });

  function handleIdCardImage(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
      idCardImage.value = target.files[0];
    }
  }
  function handleFiles(e: Event) {
    const target = e.target as HTMLInputElement;
    if (target.files) {
      // Convert FileList to an Array
      files.value = Array.from(target.files);
    }
  }

  async function handleSubmit() {
    const formData = new FormData();
    formData.append('type', 'stolen');
    formData.append('product_name', productName.value);
    formData.append('email', email.value);
    formData.append('serial_code', serialCode.value);
    formData.append('item_type', itemType.value);
    formData.append('date_time', dateTime.value ? dateTime.value.toString() : '');
    formData.append('country', country.value);
    formData.append('city', city.value);
    formData.append('street_address', streetAddress.value);
    formData.append('purchase_location', purchaseLocation.value);

    if (idCardImage.value) {
      formData.append('id_card_image', idCardImage.value, idCardImage.value.name);
    }
    files.value.forEach((file) => {
      formData.append('files[]', file, file.name);
    });

    router.post('items/stolen', formData, {
      preserveState: true,
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
  <Toaster />

  <div class="mt-2 hidden w-[340px] rounded-md bg-slate-950 p-4"></div>
  <section class="body-font container relative mx-auto pt-24">
    <h1 class="col-span-full pb-10 text-8xl font-bold text-[#ddd]">Stolen Report</h1>

    <form @submit.prevent="handleSubmit" enctype="multipart/form-data" class="mx-auto grid grid-cols-3 gap-4">
      <div>
        <Label for="product_name">Product Name</Label>
        <Input id="product_name" v-model="productName" placeholder="Enter product name" />
      </div>
      <div>
        <Label for="email">Email Address</Label>
        <Input id="email" type="email" v-model="email" placeholder="Enter your email" />
      </div>
      <div>
        <Label for="serial_code">Serial Code</Label>
        <Input id="serial_code" v-model="serialCode" placeholder="Enter serial code" />
      </div>
      <div>
        <Label for="date_time">Stolen Date &amp; Time </Label>
        <DatePicker
          :value="dateTime"
          @modelValue="
            (val: any) => {
              dateTime = val;
            }
          "
        />
      </div>
      <div>
        <Label for="country">Country</Label>
        <Input id="country" v-model="country" placeholder="Enter country" />
      </div>
      <div>
        <Label for="city">City</Label>
        <Input id="city" v-model="city" placeholder="Enter city" />
      </div>
      <div>
        <Label for="street_address">Street Address</Label>
        <Input id="street_address" v-model="streetAddress" placeholder="Enter street address" />
      </div>
      <div>
        <Label for="purchase_location">Purchase Location</Label>
        <Input id="purchase_location" v-model="purchaseLocation" placeholder="Enter purchase location" />
      </div>
      <div>
        <Label for="item_type">Item Type </Label>
        <Select onchange="(itemType = $event.target.value)" id="item_type" v-model="itemType">
          <SelectTrigger>
            <SelectValue placeholder="Select item" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem v-for="item in ['Bag', 'Shoe', 'Watch', 'Other']" :key="item" :value="item"> {{ item }} </SelectItem>
            </SelectGroup>
          </SelectContent></Select
        >
      </div>
      <div>
        <Label for="id_card_image">Upload ID Card</Label>
        <Input id="id_card_image" type="file" accept="image/*" @change="handleIdCardImage" />
      </div>
      <div>
        <Label for="files">Upload Files</Label>
        <Input id="files" type="file" accept="image/*" multiple @change="handleFiles" />
      </div>
      <div
        class="flex flex-col justify-end"
        @click="
          () => {
            fetchAddress(coords.latitude, coords.longitude, {
              country,
              city,
              streetAddress,
            });
          }
        "
      >
        <Button type="button" class="w-full bg-primary-foreground text-primary hover:text-primary-foreground">Get Address</Button>
      </div>

      <div class="col-span-full flex w-full flex-col justify-end">
        <Button class="ml-auto w-32" type="submit">Submit</Button>
      </div>
    </form>
  </section>
</template>
