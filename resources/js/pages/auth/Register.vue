<script setup lang="ts">
  import InputError from '@/components/InputError.vue';
  import { Button } from '@/components/ui/button';
  import { Input } from '@/components/ui/input';
  import { Label } from '@/components/ui/label';
  import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
  import AuthBase from '@/layouts/AuthLayout.vue';
  import { Head, useForm } from '@inertiajs/vue3';
  import { tryOnBeforeMount } from '@vueuse/core';
  import { LoaderCircle } from 'lucide-vue-next';
  import { ref } from 'vue';

  interface Country {
    name: {
      common: string;
    };
    cca2: string;
  }

  const allCountries = ref<Country[]>([]);

  tryOnBeforeMount(async () => {
    const res = await fetch('https://restcountries.com/v3.1/all');
    const data = await res.json();
    allCountries.value = data.sort((a: any, b: any) => a?.name?.common?.localeCompare(b?.name?.common));
  });

  const form = useForm({
    name: '',
    email: '',
    phone: '',
    country: '',
    password: '',
    password_confirmation: '',
    ownership_number: '',
    company_name: '',
    national_id_number: '',
    product_type: '',
  });

  const submit = () => {
    form.phone = form.phone.toString();
    form.post(route('register'), {
      onFinish: () => form.reset('password', 'password_confirmation'),
    });
  };
</script>

<template>
  <AuthBase title="Create an account" description="Enter your details below to create your account">
    <Head title="Register" />

    <form @submit.prevent="submit" class="flex flex-col gap-6">
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <div class="grid gap-2">
          <Label for="name">Name</Label>
          <Input id="name" type="text" required autofocus :tabindex="1" autocomplete="name" v-model="form.name" placeholder="Full name" />
          <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
          <Label for="email">Email address</Label>
          <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
          <InputError :message="form.errors.email" />
        </div>

        <div class="grid gap-2">
          <Label for="phone">Phone Number</Label>
          <Input id="phone" type="number" required :tabindex="2" autocomplete="phone" v-model="form.phone" placeholder="123xxxxxx" />
          <InputError :message="form.errors.phone" />
        </div>

        <div class="grid gap-2">
          <Label for="country">Country</Label>
          <Select aria-keyshortcuts="false" v-model="form.country">
            <SelectTrigger id="country">
              <SelectValue placeholder="Select your country" />
            </SelectTrigger>
            <SelectContent>
              <SelectGroup>
                <SelectItem v-for="country in allCountries" :key="country.cca2" :value="country.name.common">
                  {{ country.name.common }}
                </SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>
          <InputError :message="form.errors.country" />
        </div>

        <div class="grid gap-2">
          <Label for="password">Password</Label>
          <Input id="password" type="password" required :tabindex="3" autocomplete="new-password" v-model="form.password" placeholder="Password" />
          <InputError :message="form.errors.password" />
        </div>

        <div class="grid gap-2">
          <Label for="password_confirmation">Confirm password</Label>
          <Input
            id="password_confirmation"
            type="password"
            required
            :tabindex="4"
            autocomplete="new-password"
            v-model="form.password_confirmation"
            placeholder="Confirm password"
          />
          <InputError :message="form.errors.password_confirmation" />
        </div>

        <div class="grid gap-2">
          <Label for="ownership_number">Ownership Number</Label>
          <Input id="ownership_number" type="text" v-model="form.ownership_number" placeholder="Ownership Number" />
          <InputError :message="form.errors.ownership_number" />
        </div>

        <div class="grid gap-2">
          <Label for="company_name">Company Name</Label>
          <Input id="company_name" type="text" v-model="form.company_name" placeholder="Company Name" />
          <InputError :message="form.errors.company_name" />
        </div>

        <div class="grid gap-2">
          <Label for="national_id_number">National ID Number</Label>
          <Input id="national_id_number" type="text" v-model="form.national_id_number" placeholder="National ID Number" />
          <InputError :message="form.errors.national_id_number" />
        </div>

        <div class="grid gap-2">
          <Label for="product_type">Product Type</Label>
          <Input id="product_type" type="text" v-model="form.product_type" placeholder="e.g. Laptop, Phone" />
          <InputError :message="form.errors.product_type" />
        </div>

        <Button type="submit" class="col-span-2 mt-2 w-full" tabindex="5" :disabled="form.processing">
          <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
          Create account
        </Button>
      </div>

      <!-- TODO: -->
      <!-- <div class="text-center text-sm text-muted-foreground">
        Already have an account?
        <TextLink :href="route('login')" class="underline underline-offset-4" :tabindex="6">Log in</TextLink>
      </div> -->
    </form>
  </AuthBase>
</template>
