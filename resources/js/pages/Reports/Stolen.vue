<script setup lang="ts">
  import { Button } from '@/components/ui/button';
  import { Input } from '@/components/ui/input';
  import { Label } from '@/components/ui/label';
  import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
  import InputDatePicker from '@/components/ui/time-picker/InputDatePicker.vue';
  import { toast, Toaster } from '@/components/ui/toast';
  import { Link, router } from '@inertiajs/vue3';
  import { DateValue } from 'reka-ui';
  import { ref } from 'vue';
  import { useLocation } from './useLocation';

  interface Company {
    id: string;
    name: string;
    active: boolean;
  }
  const { companies = [] } = defineProps<{
    companies: Company[];
  }>();

  const customerName = ref('');
  const serialCode = ref('');
  const dateTime = ref<DateValue | undefined>();
  const country = ref('');
  const city = ref('');
  const streetAddress = ref('');
  const companyId = ref(''); // must map to company ID (string for FormData compatibility)
  const itemType = ref('');
  const idCardImage = ref<File | null>(null);
  const ownershipDocument = ref<File | null>(null);
  const lostOwnershipDocument = ref(false);

  const errors = ref<Record<string, string>>({});

  function validateForm(): boolean {
    let isValid = true;
    errors.value = {};

    if (!customerName.value) {
      errors.value.customerName = 'Required';
      errors.value.serialCode = 'Required';
      isValid = false;
    }

    if (!dateTime.value) {
      errors.value.dateTime = 'Required';
      isValid = false;
    }

    if (!country.value) {
      errors.value.country = 'Required';
      isValid = false;
    }

    if (!city.value) {
      errors.value.city = 'Required';
      isValid = false;
    }

    if (!streetAddress.value) {
      errors.value.streetAddress = 'Required';
      isValid = false;
    }

    if (!companyId.value) {
      errors.value.companyId = 'Required';
      isValid = false;
    }

    if (!itemType.value) {
      errors.value.itemType = 'Required';
      isValid = false;
    }

    if (!idCardImage.value) {
      errors.value.idCardImage = 'Required';
      isValid = false;
    }

    if (!lostOwnershipDocument.value && !ownershipDocument.value) {
      errors.value.ownershipDocument = 'Provide document or mark as lost';
      isValid = false;
    }

    return isValid;
  }

  function handleIdCardImage(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) idCardImage.value = file;
  }

  function handleOwnershipDocument(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0];
    if (file) ownershipDocument.value = file;
  }

  async function handleSubmit() {
    if (!validateForm()) {
      toast({ title: 'Validation Error', description: 'Please fix errors.' });
      return;
    }

    const formData = new FormData();
    formData.append('type', 'stolen');
    formData.append('customer_name', customerName.value);
    formData.append('serial_code', serialCode.value);
    formData.append('item_type', itemType.value);
    formData.append('date_time', dateTime.value?.toString() ?? '');
    formData.append('country', country.value);
    formData.append('city', city.value);
    formData.append('street_address', streetAddress.value);
    formData.append('company_id', companyId.value);
    formData.append('lost_ownership_document', lostOwnershipDocument.value ? '1' : '0');

    if (idCardImage.value) {
      formData.append('id_card_image', idCardImage.value, idCardImage.value.name);
    }

    if (ownershipDocument.value) {
      formData.append('files[]', ownershipDocument.value, ownershipDocument.value.name);
    }

    router.post('items/stolen', formData, {
      forceFormData: true,
      onSuccess: (p) => {
        console.log(p);

        toast({ title: 'Success', description: 'Report submitted.' });
      },
      onError: (formErrors) => {
        errors.value = formErrors;
        toast({ title: 'Error', description: formErrors.message });
      },
    });
  }
  const { coords, fetchAddress } = useLocation({
    country,
    city,
    streetAddress,
  });
</script>

<template>
  <Toaster />
  <div class="mt-2 hidden w-[340px] rounded-md bg-slate-950 p-4"></div>
  <section class="body-font container relative mx-auto py-10 sm:py-10">
    <div class="col-span-full flex items-center justify-between pb-10 text-4xl font-bold text-[#ddd] sm:text-5xl md:text-8xl">
      <h1>Stolen Report</h1>
      <Link class="rounded-full bg-[#333] px-3 py-2 text-sm md:px-6" :href="route('logout')">Logout </Link>
    </div>

    <form @submit.prevent="handleSubmit" enctype="multipart/form-data" class="mx-auto grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3">
      <div>
        <Label for="customer_name">Customer Name</Label>
        <Input id="customer_name" v-model="customerName" placeholder="Enter customer name" :class="{ 'border-red-500': errors.customerName }" />
        <span v-if="errors.customerName" class="text-sm text-red-500">{{ errors.customerName }}</span>
      </div>
      <div>
        <Label for="serial_code">Serial Code</Label>
        <Input id="serial_code" v-model="serialCode" placeholder="Enter serial code" :class="{ 'border-red-500': errors.serial_code }" />
        <span v-if="errors.serialCode" class="text-sm text-red-500">{{ errors.serialCode }}</span>
      </div>
      <div>
        <Label for="date_time">Stolen Date</Label>
        <InputDatePicker class="w-full" v-model="dateTime" placeholder="Enter the date and time" />
        <!-- <DatePicker
          :value="dateTime"
          @modelValue="
            (val: any) => {
              dateTime = val;
            }
          "
        /> -->
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
        <Label for="company">Company</Label>
        <Select id="company" v-model="companyId" :class="{ 'border-red-500': errors.company }">
          <SelectTrigger>
            <SelectValue placeholder="Select company" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem v-for="company in companies" :key="company.name" :value="company.id"> {{ company.name }} </SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
        <span v-if="errors.company" class="text-sm text-red-500">{{ errors.company }}</span>
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
        <Label for="ownership_document">Upload Ownership Document</Label>
        <Input
          id="ownership_document"
          type="file"
          accept="image/*"
          @change="handleOwnershipDocument"
          :class="{ 'border-red-500': errors.ownershipDocument }"
          :disabled="lostOwnershipDocument"
        />

        <div class="mt-2 flex items-center">
          <input type="checkbox" id="lost_ownership_document" v-model="lostOwnershipDocument" class="mr-2" />
          <Label for="lost_ownership_document">I have lost the ownership document</Label>
        </div>
        <span v-if="errors.ownershipDocument" class="text-sm text-red-500">{{ errors.ownershipDocument }}</span>
      </div>
      <div
        class="flex flex-col justify-center"
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
