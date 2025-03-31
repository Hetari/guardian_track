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
  import { ref, watch } from 'vue';
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

  const errors = ref({
    productName: '',
    email: '',
    serialCode: '',
    dateTime: '',
    country: '',
    city: '',
    streetAddress: '',
    purchaseLocation: '',
    itemType: '',
    idCardImage: '',
    files: '',
  });

  const { coords, fetchAddress } = useLocation({
    country,
    city,
    streetAddress,
  });

  // Validation function
  const skip = ref(true);
  const validateForm = () => {
    if (skip.value) {
      skip.value = false;
      return true;
    }

    let valid = true;
    // Reset errors
    for (const key in errors.value) {
      errors.value[key as keyof typeof errors.value] = '';
    }

    if (!productName.value) {
      errors.value.productName = 'Product name is required.';
      valid = false;
    }
    if (!email.value || !/\S+@\S+\.\S+/.test(email.value)) {
      errors.value.email = 'Please enter a valid email address.';
      valid = false;
    }
    if (!serialCode.value) {
      errors.value.serialCode = 'Serial code is required.';
      valid = false;
    }
    if (!dateTime.value) {
      errors.value.dateTime = 'Date and time are required.';
      valid = false;
    }
    if (!country.value) {
      errors.value.country = 'Country is required.';
      valid = false;
    }
    if (!city.value) {
      errors.value.city = 'City is required.';
      valid = false;
    }
    if (!streetAddress.value) {
      errors.value.streetAddress = 'Street address is required.';
      valid = false;
    }
    if (!purchaseLocation.value) {
      errors.value.purchaseLocation = 'Purchase location is required.';
      valid = false;
    }
    if (!itemType.value) {
      errors.value.itemType = 'Item type is required.';
      valid = false;
    }

    if (!idCardImage.value) {
      errors.value.idCardImage = 'ID card image is required.';
      valid = false;
    }
    if (files.value.length === 0) {
      errors.value.files = 'At least one file is required.';
      valid = false;
    }

    return valid;
  };

  watch([productName, email, serialCode, dateTime, country, city, streetAddress, purchaseLocation, itemType, idCardImage, files], () => {
    validateForm();
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
      files.value = Array.from(target.files);
    }
  }

  async function handleSubmit() {
    if (!validateForm()) {
      toast({
        title: 'Error',
        description: 'Please fix the errors in the form before submitting.',
      });
      return;
    }

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
        <Input id="product_name" v-model="productName" placeholder="Enter product name" :class="{ 'border-red-500': errors.productName }" />
        <span v-if="errors.productName" class="text-sm text-red-500">{{ errors.productName }}</span>
      </div>
      <div>
        <Label for="email">Email Address</Label>
        <Input id="email" type="email" v-model="email" placeholder="Enter your email" :class="{ 'border-red-500': errors.email }" />
        <span v-if="errors.email" class="text-sm text-red-500">{{ errors.email }}</span>
      </div>
      <div>
        <Label for="serial_code">Serial Code</Label>
        <Input id="serial_code" v-model="serialCode" placeholder="Enter serial code" :class="{ 'border-red-500': errors.serialCode }" />
        <span v-if="errors.serialCode" class="text-sm text-red-500">{{ errors.serialCode }}</span>
      </div>
      <div>
        <Label for="date_time">Stolen Date &amp; Time</Label>
        <DatePicker
          :value="dateTime"
          @modelValue="
            (val: any) => {
              dateTime = val;
            }
          "
        />
        <span v-if="errors.dateTime" class="text-sm text-red-500">{{ errors.dateTime }}</span>
      </div>
      <div>
        <Label for="country">Country</Label>
        <Input id="country" v-model="country" placeholder="Enter country" :class="{ 'border-red-500': errors.country }" />
        <span v-if="errors.country" class="text-sm text-red-500">{{ errors.country }}</span>
      </div>
      <div>
        <Label for="city">City</Label>
        <Input id="city" v-model="city" placeholder="Enter city" :class="{ 'border-red-500': errors.city }" />
        <span v-if="errors.city" class="text-sm text-red-500">{{ errors.city }}</span>
      </div>
      <div>
        <Label for="street_address">Street Address</Label>
        <Input id="street_address" v-model="streetAddress" placeholder="Enter street address" :class="{ 'border-red-500': errors.streetAddress }" />
        <span v-if="errors.streetAddress" class="text-sm text-red-500">{{ errors.streetAddress }}</span>
      </div>
      <div>
        <Label for="purchase_location">Purchase Location</Label>
        <Input
          id="purchase_location"
          v-model="purchaseLocation"
          placeholder="Enter purchase location"
          :class="{ 'border-red-500': errors.purchaseLocation }"
        />
        <span v-if="errors.purchaseLocation" class="text-sm text-red-500">{{ errors.purchaseLocation }}</span>
      </div>
      <div>
        <Label for="item_type">Item Type</Label>
        <Select id="item_type" v-model="itemType" :class="{ 'border-red-500': errors.itemType }">
          <SelectTrigger>
            <SelectValue placeholder="Select item" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem v-for="item in ['Bag', 'Shoe', 'Watch', 'Other']" :key="item" :value="item"> {{ item }} </SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
        <span v-if="errors.itemType" class="text-sm text-red-500">{{ errors.itemType }}</span>
      </div>
      <div>
        <Label for="id_card_image">Upload ID Card</Label>
        <Input id="id_card_image" type="file" accept="image/*" @change="handleIdCardImage" :class="{ 'border-red-500': errors.idCardImage }" />
        <span v-if="errors.idCardImage" class="text-sm text-red-500">{{ errors.idCardImage }}</span>
      </div>
      <div>
        <Label for="files">Upload Files</Label>
        <Input id="files" type="file" accept="image/*" multiple @change="handleFiles" :class="{ 'border-red-500': errors.files }" />
        <span v-if="errors.files" class="text-sm text-red-500">{{ errors.files }}</span>
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
        <Button type="button" class="w-full" variant="secondary">Get Address</Button>
      </div>

      <div class="col-span-full flex w-full flex-col justify-end">
        <Button class="ml-auto w-32" type="submit">Submit</Button>
      </div>
    </form>
  </section>
</template>
