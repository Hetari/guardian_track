<template>
  <div class="flex items-center justify-center">
    <Form as="" keep-values :validation-schema="formSchema">
      <div v-for="(action, index) in actions" :key="action.name" class="mx-2">
        <Dialog v-model:open="dialogOpen[index]">
          <DialogTrigger as-child>
            <Button variant="secondary">
              <component :is="action.icon" />
            </Button>
          </DialogTrigger>
          <DialogContent class="sm:max-w-[425px]">
            <form id="dialogForm" @submit.prevent="() => onSubmit(action.onSubmit, index)">
              <template v-if="action.name.toLowerCase() === 'edit'">
                <!-- Name Field -->
                <FormField v-slot="{ componentField }" name="name">
                  <FormItem>
                    <FormLabel>Name</FormLabel>
                    <FormControl>
                      <Input type="text" v-model="companyData[index].name" v-bind="componentField.modelValue" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>

                <!-- Email Field -->
                <FormField v-slot="{ componentField }" name="email">
                  <FormItem>
                    <FormLabel>Email</FormLabel>
                    <FormControl>
                      <Input type="email" placeholder="example@mail.com" v-model="companyData[index].email" v-bind="componentField.modelValue" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>

                <!-- Active Field -->
                <FormField v-slot="{ componentField }" name="active">
                  <FormItem>
                    <FormLabel>Active</FormLabel>
                    <FormControl>
                      <Select v-model="companyData[index].active" v-bind="componentField.modelValue">
                        <SelectTrigger>
                          <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem :value="true as unknown as AcceptableValue">Active</SelectItem>
                          <SelectItem :value="false as unknown as AcceptableValue">Inactive</SelectItem>
                        </SelectContent>
                      </Select>
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>
              </template>

              <!-- Delete Confirmation -->
              <template v-else-if="action.name.toLowerCase() === 'delete'">
                <p class="text-red-500">Are you sure you want to delete this user?</p>
                <p class="text-red-500">This action cannot be undone.</p>
              </template>
            </form>

            <DialogFooter>
              <Button type="submit" variant="secondary" form="dialogForm">
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
  import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
  import { toTypedSchema } from '@vee-validate/zod';
  import { AcceptableValue } from 'reka-ui';
  import { ref, watchEffect } from 'vue';
  import * as z from 'zod';
  import { Company } from './Index.vue';

  const { actions = [] } = defineProps<{
    actions: {
      name: string;
      icon: any;
      onSubmit: (d: any) => void;
      row: any;
    }[];
  }>();

  const formSchema = toTypedSchema(
    z.object({
      name: z.string().min(1, 'Name is required'),
      email: z.string().email('Invalid email address').min(1, 'Email is required'),
      active: z.boolean(),
    }),
  );

  // Holds individual company data per dialog instance
  const companyData = ref<Record<number, Company>>({});
  // Dialog state per action
  const dialogOpen = ref<boolean[]>(actions.map(() => false));

  watchEffect(() => {
    actions.forEach((action, index) => {
      companyData.value[index] = { ...action.row };
    });
  });

  const onSubmit = async (actionFn: (data: Company) => void, index: number) => {
    try {
      formSchema.parse(companyData.value[index]);

      // Run the passed in handler
      await actionFn(companyData.value[index]);

      // Optional: reset or close dialog
      dialogOpen.value[index] = false;
      companyData.value[index] = { name: '', email: '', active: false };
    } catch (err) {
      console.error('Validation error:', err);
    }
  };
</script>
