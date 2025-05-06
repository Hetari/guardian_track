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
                <FormField v-slot="{ componentField }" name="name">
                  <FormItem>
                    <FormLabel>Name</FormLabel>
                    <FormControl>
                      <Input type="text" v-model="product[index].name" v-bind="componentField.modelValue" />
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
  import Input from '@/components/ui/input/Input.vue';
  import { toTypedSchema } from '@vee-validate/zod';
  import { ref, watchEffect } from 'vue';
  import * as z from 'zod';
  import { Product } from './Index.vue';

  const { actions = [] } = defineProps<{
    actions: {
      name: string;
      icon: any;
      onSubmit: (data: Product) => void | Promise<void>;
      row: Product;
    }[];
  }>();

  const formSchema = toTypedSchema(
    z.object({
      name: z.string().min(1, 'Name is required'),
    }),
  );
  // Holds form data for each row
  const product = ref<Record<number, Product>>({});
  const dialogOpen = ref<boolean[]>(actions.map(() => false));

  watchEffect(() => {
    actions.forEach((action, index) => {
      product.value[index] = JSON.parse(JSON.stringify(action.row));
    });
  });

  const onSubmit = async (actionFn: (data: Product) => void, index: number) => {
    try {
      // Validate before submit
      formSchema.parse(product.value[index]);

      // Execute the action handler
      await actionFn(product.value[index]);

      // Close dialog and optionally reset form
      dialogOpen.value[index] = false;
    } catch (err) {
      console.error('Validation failed:', err);
    }
  };
</script>
