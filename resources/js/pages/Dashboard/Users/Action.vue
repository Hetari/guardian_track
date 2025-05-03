<template>
  <div class="flex items-center justify-center">
    <Form as="" keep-values :validation-schema="formSchema">
      <div v-for="action in actions" :key="action.name" class="mx-2">
        <Dialog>
          <DialogTrigger as-child>
            <Button variant="secondary">
              <component :is="action.icon" />
            </Button>
          </DialogTrigger>
          <DialogContent class="sm:max-w-[425px]">
            <form id="dialogForm" @submit.prevent="() => onSubmit(action.onSubmit)">
              <template v-if="action.name.toLowerCase() === 'edit'">
                <FormField v-slot="{ componentField }" name="name">
                  <FormItem>
                    <FormLabel>Name</FormLabel>
                    <FormControl>
                      <Input type="text" v-model="user.name" v-bind="componentField.modelValue" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="email">
                  <FormItem>
                    <FormLabel>Email</FormLabel>
                    <FormControl>
                      <Input type="email" placeholder="example@mail.com" v-model="user.email" v-bind="componentField.modelValue" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>

                <FormField name="country">
                  <FormItem>
                    <FormLabel>Country</FormLabel>
                    <FormControl>
                      <Select :model-value="user.country" @update:model-value="(val) => (user.country = (val as string) ?? '')">
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
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="phone">
                  <FormItem>
                    <FormLabel>Phone</FormLabel>
                    <FormControl>
                      <Input type="text" placeholder="123-456-7890" v-model="user.phone" v-bind="componentField.modelValue" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>
              </template>
              <template v-else-if="action.name.toLowerCase() === 'delete'">
                <p class="text-red-500">Are you sure you want to delete this user?</p>
                <p class="text-red-500">This action cannot be undone.</p>
              </template>
            </form>

            <DialogFooter>
              <Button type="submit" variant="secondary" :disabled="!formSchema.parse(user)" form="dialogForm">
                {{ action.name.toLowerCase() === 'delete' ? 'Delete' : 'Save' }}
              </Button>
            </DialogFooter>
          </DialogContent>
        </Dialog>
      </div>
    </Form>
  </div>
</template>

<script setup lang="ts">
  import { Button } from '@/components/ui/button';
  import { Dialog, DialogContent, DialogFooter, DialogTrigger } from '@/components/ui/dialog';
  import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
  import { Input } from '@/components/ui/input';
  import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
  import { toTypedSchema } from '@vee-validate/zod';
  import { tryOnBeforeMount } from '@vueuse/core';
  import { ref, watchEffect } from 'vue';
  import * as z from 'zod';

  const formSchema = toTypedSchema(
    z.object({
      username: z.string().min(2).max(50),
      email: z.string().email(),
      phone: z.string().optional(),
      country: z.string().optional(),
    }),
  );

  const user = ref<{ name: string; email: string; phone?: string; country: string }>({ name: '', email: '', phone: '', country: '' });

  const { actions = [] } = defineProps<{
    actions: {
      name: string;
      icon: any;
      onSubmit: (d: any) => void;
      row: any;
    }[];
  }>();

  const onSubmit = (func: (data: any) => void) => {
    // Perform validation and then call the onSubmit function
    formSchema.parse(user.value);
    func(user.value);
    // Reset user data after submission
    user.value = { name: '', email: '', phone: '', country: '' };
  };

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

  // Update user info when the dialog opens
  watchEffect(() => {
    if (actions.length > 0) {
      const action = actions[0]; // Or any logic to select the action
      user.value = { ...action.row }; // Initialize user with the selected action's row data
    }
  });
</script>
