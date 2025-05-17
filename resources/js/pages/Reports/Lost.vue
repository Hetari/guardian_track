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

  interface Company {
    id: string;
    name: string;
    active: boolean;
  }

  const { companies = [] } = defineProps<{
    companies: Company[];
    products: {
      id: string;
      name: string;
      active: boolean;
    }[];
  }>();

  const customerName = ref('');
  const serialCode = ref('');
  const dateTime = ref<DateValue | undefined>();
  const country = ref('');
  const city = ref('');
  const streetAddress = ref('');
  const companyId = ref('');
  const itemType = ref('');
  const idCardImage = ref<File | null>(null);
  const ownershipDocument = ref<File | null>(null);
  const lostOwnershipDocument = ref(false);
  const latitude = ref<number | null>(null);
  const longitude = ref<number | null>(null);
  const showMap = ref(false);
  const tracking = ref(false);

  const errors = ref<Record<string, string>>({});

  function validateForm(): boolean {
    let isValid = true;
    errors.value = {};
    if (!customerName.value) {
      errors.value.customer_name = 'Required';
      isValid = false;
    }
    if (!serialCode.value) {
      errors.value.serial_code = 'Required';
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
      errors.value.ownershipDocument = 'Required';
      isValid = false;
    }
    if (!latitude.value || !longitude.value) {
      errors.value.map = 'Please select a location on the map';
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
    formData.append('type', 'lost');
    formData.append('customer_name', customerName.value);
    formData.append('serial_code', serialCode.value);
    formData.append('item_type', itemType.value);
    formData.append('date_time', dateTime.value?.toString() ?? '');
    formData.append('country', country.value);
    formData.append('city', city.value);
    formData.append('street_address', streetAddress.value);
    formData.append('company_id', companyId.value);
    formData.append('lost_ownership_document', lostOwnershipDocument.value ? '1' : '0');
    formData.append('latitude', latitude.value?.toString() ?? '');
    formData.append('longitude', longitude.value?.toString() ?? '');

    if (idCardImage.value) formData.append('id_card_image', idCardImage.value, idCardImage.value.name);
    if (ownershipDocument.value) formData.append('files[]', ownershipDocument.value, ownershipDocument.value.name);

    if (tracking.value) {
      router.post('items/lost', formData, {
        forceFormData: true,
        onSuccess: (response) => {
          toast({ title: 'Success', description: 'Report submitted.' });
          const trackingCode = response.props.tracking_code; // Assuming tracking_code is returned in the response
          router.get(`/reports/tracking/${trackingCode}`); // Navigate to tracking page
        },
        onError: (formErrors) => {
          errors.value = formErrors;
          toast({ title: 'Error', description: formErrors.message });
        },
      });
    } else {
      router.post('items/lost', formData, {
        forceFormData: true,
        onSuccess: () => {
          toast({ title: 'Success', description: 'Report submitted.' });
        },
        onError: (formErrors) => {
          errors.value = formErrors;
          toast({ title: 'Error', description: formErrors.message });
        },
      });
    }
  }

  function initMap() {
    const defaultLocation = { lat: 23.588, lng: 58.3829 }; // Default map location
    setTimeout(() => {
      const map = new google.maps.Map(document.getElementById('map') as HTMLElement, {
        center: defaultLocation,
        zoom: 7,
      });

      let marker: google.maps.Marker | null = null;

      // Add a click listener to the map
      map.addListener('click', (e) => {
        if (marker) marker.setMap(null); // Remove the previous marker
        marker = new google.maps.Marker({ position: e.latLng, map }); // Add a new marker

        // Update latitude and longitude fields
        latitude.value = e.latLng.lat();
        longitude.value = e.latLng.lng();
      });
    }, 300);
  }
  async function geocodeAddress() {
    const address = `${streetAddress.value}, ${city.value}, ${country.value}`;
    const geocoder = new google.maps.Geocoder();

    geocoder.geocode({ address }, (results, status) => {
      if (status === 'OK' && results && results[0]) {
        const location = results[0].geometry.location;
        latitude.value = location.lat();
        longitude.value = location.lng();
        toast({ title: 'Location Found', description: `Lat: ${latitude.value}, Lng: ${longitude.value}` });
      } else {
        toast({ title: 'Geocoding Error', description: 'Failed to get location from address' });
        console.error('Geocoding failed:', status);
      }
    });
  }
</script>

<template>
  <Toaster />
  <section class="body-font container relative mx-auto py-10 sm:py-10">
    <div class="col-span-full flex items-center justify-between pb-10 text-4xl font-bold text-[#ddd] sm:text-5xl md:text-8xl">
      <h1>Lost Report</h1>
      <Link class="rounded-full bg-[#333] px-3 py-2 text-sm md:px-6" :href="route('logout')">Logout</Link>
    </div>

    <form @submit.prevent="handleSubmit" method="POST" enctype="multipart/form-data" class="mx-auto grid grid-cols-1 gap-4 sm:grid-cols-3">
      <!-- الحقول الرئيسية -->
      <div>
        <Label for="customer_name">Customer Name</Label>
        <Input id="customer_name" v-model="customerName" placeholder="Enter customer name" :class="{ 'border-red-500': errors.customer_name }" />
        <span v-if="errors.customer_name" class="text-sm text-red-500">{{ errors.customer_name }}</span>
      </div>

      <div>
        <Label for="serial_code">Serial Code</Label>
        <Input id="serial_code" v-model="serialCode" placeholder="Enter serial code" :class="{ 'border-red-500': errors.serial_code }" />
        <span v-if="errors.serial_code" class="text-sm text-red-500">{{ errors.serial_code }}</span>
      </div>

      <div>
        <Label for="date_time">Lost Date</Label>
        <InputDatePicker class="w-full" v-model="dateTime" placeholder="Enter the date and time" />
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
        <Select id="company" v-model="companyId" :class="{ 'border-red-500': errors.companyId }">
          <SelectTrigger>
            <SelectValue placeholder="Select company" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem v-for="company in companies" :key="company.name" :value="company.id">{{ company.name }} </SelectItem>
            </SelectGroup>
          </SelectContent>
        </Select>
        <span v-if="errors.companyId" class="text-sm text-red-500">{{ errors.companyId }}</span>
      </div>

      <div>
        <Label for="item_type">Item Type</Label>
        <Select id="item_type" v-model="itemType" :class="{ 'border-red-500': errors.itemType }">
          <SelectTrigger>
            <SelectValue placeholder="Select item" />
          </SelectTrigger>
          <SelectContent>
            <SelectGroup>
              <SelectItem v-for="item in products" :key="item.id" :value="item.name">{{ item.name }}</SelectItem>
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

      <div>
        <input type="checkbox" id="tracking" v-model="tracking" class="mr-2" />
        <Label for="tracking">Do you want tracking?</Label>
      </div>

      <!-- زر العنوان -->
      <div class="col-span-3 mt-4 flex justify-center">
        <Button
          type="button"
          class="rounded bg-blue-600 px-6 py-2 font-bold text-white shadow hover:bg-blue-700"
          @click="
            () => {
              showMap = true;
              initMap();
              geocodeAddress();
            }
          "
        >
          Get Address
        </Button>
      </div>

      <!-- عرض الإحداثيات -->
      <div class="col-span-1">
        <Label for="latitude">Latitude</Label>
        <Input id="latitude" v-model="latitude" />
      </div>

      <div class="col-span-1">
        <Label for="longitude">Longitude</Label>
        <Input id="longitude" v-model="longitude" />
      </div>

      <!-- الخريطة -->
      <div v-if="showMap" class="col-span-3 mt-4">
        <div id="map" style="height: 400px; width: 100%"></div>
      </div>

      <!-- زر الإرسال -->
      <div class="col-span-3 mt-6 flex justify-end">
        <Button class="w-32" type="submit">Submit</Button>
      </div>
    </form>
  </section>
</template>
