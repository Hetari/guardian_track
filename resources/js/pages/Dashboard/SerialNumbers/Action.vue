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
              <!-- Edit Form -->
              <template v-if="action.name.toLowerCase() === 'edit'">
                <FormField v-slot="{ componentField }" name="user.id">
                  <FormItem>
                    <FormLabel>User</FormLabel>

                    <FormControl>
                      <Select v-model="serialNumber[index].user.id" v-bind="componentField.modelValue">
                        <SelectTrigger>
                          <SelectValue placeholder="Select user" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem v-for="user in users" :key="user.id" :value="user.id as unknown as AcceptableValue">
                            {{ user.name }}
                          </SelectItem>
                        </SelectContent>
                      </Select>
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>

                <FormField v-slot="{ componentField }" name="product.id">
                  <FormItem>
                    <FormLabel>Product</FormLabel>
                    <FormControl>
                      <Select v-model="serialNumber[index].product.id" v-bind="componentField.modelValue">
                        <SelectTrigger>
                          <SelectValue placeholder="Select product" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem v-for="product in products" :key="product.id" :value="product.id as unknown as AcceptableValue">
                            {{ product.name }}
                          </SelectItem>
                        </SelectContent>
                      </Select>
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>
              </template>

              <!-- Delete Confirmation -->
              <template v-else-if="action.name.toLowerCase() === 'delete'">
                <p class="text-red-500">Are you sure you want to delete this item?</p>
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
  import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
  import { toTypedSchema } from '@vee-validate/zod';
  import { AcceptableValue } from 'reka-ui';
  import { ref, watchEffect } from 'vue';
  import * as z from 'zod';

  interface SerialNumber {
    id?: string;
    user: {
      id: string;
      name: string;
      email: string;
    };
    product: {
      id: string;
      name: string;
    };
    active: boolean; // Add active status to SerialNumber
  }

  const {
    actions = [],
    users = [],
    products = [],
  } = defineProps<{
    actions: {
      name: string;
      icon: any;
      onSubmit: (data: SerialNumber) => void | Promise<void>;
      row: SerialNumber;
    }[];
    users: {
      id: string;
      name: string;
      email: string;
    }[];
    products: {
      id: string;
      name: string;
    }[];
  }>();

  const formSchema = toTypedSchema(
    z.object({
      user: z.object({
        id: z.string().min(1, 'User is required'),
      }),
      product: z.object({
        id: z.string().min(1, 'Product is required'),
      }),
      active: z.boolean(),
    }),
  );
  // Holds form data for each row
  const serialNumber = ref<Record<number, SerialNumber>>({});
  const dialogOpen = ref<boolean[]>(actions.map(() => false));

  watchEffect(() => {
    actions.forEach((action, index) => {
      serialNumber.value[index] = JSON.parse(JSON.stringify(action.row));
    });
  });

  const onSubmit = async (actionFn: (data: SerialNumber) => void, index: number) => {
    try {
      // Validate before submit
      formSchema.parse(serialNumber.value[index]);

      // Execute the action handler
      await actionFn(serialNumber.value[index]);

      // Close dialog and optionally reset form
      dialogOpen.value[index] = false;
    } catch (err) {
      console.error('Validation failed:', err);
    }
  };
</script>
