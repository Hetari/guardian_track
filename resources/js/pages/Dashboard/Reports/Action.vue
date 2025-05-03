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
            <DialogHeader>
              <DialogTitle>{{ action.name.toLowerCase() === 'delete' ? 'Delete Report' : 'Update Status' }}</DialogTitle>
              <DialogDescription>
                {{
                  action.name === 'delete'
                    ? 'Are you sure you want to delete this report? This action cannot be undone.'
                    : 'Change the current status for this item'
                }}
              </DialogDescription>
            </DialogHeader>

            <form id="dialogForm" @submit.prevent="() => onSubmit(action.onSubmit, action.name)">
              <template v-if="action.name.toLowerCase() === 'edit'">
                <FormField name="status">
                  <FormItem>
                    <FormLabel>Status</FormLabel>
                    <FormControl>
                      <Select v-model="user.status">
                        <SelectTrigger>
                          <SelectValue placeholder="Select status" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem v-for="status in statusOptions" :key="status" :value="status">
                            {{ formatStatus(status) }}
                          </SelectItem>
                        </SelectContent>
                      </Select>
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>
              </template>

              <template v-else-if="action.name.toLowerCase() === 'delete'">
                <p class="mt-4 font-medium text-red-500">Are you sure you want to delete this user?</p>
                <p class="text-red-500">This action cannot be undone.</p>
              </template>
            </form>

            <DialogFooter>
              <Button type="submit" form="dialogForm">
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
  import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from '@/components/ui/dialog';
  import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
  import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
  import { toTypedSchema } from '@vee-validate/zod';
  import { ref, watchEffect } from 'vue';
  import * as z from 'zod';

  const statusOptions = ['received', 'checking_brand', 'checking_company', 'transferred_to_police', 'done'];

  const formSchema = toTypedSchema(
    z.object({
      status: z.enum(['received', 'checking_brand', 'checking_company', 'transferred_to_police', 'done']),
    }),
  );

  const user = ref<{
    status: 'received' | 'checking_brand' | 'checking_company' | 'transferred_to_police' | 'done';
  }>({
    status: 'received',
  });

  const { actions = [] } = defineProps<{
    actions: {
      name: string;
      icon: any;
      onSubmit: (data: any) => void;
      row: any;
    }[];
  }>();

  const onSubmit = (func: (data: any) => void, actionName: string) => {
    try {
      if (actionName.toLowerCase() === 'edit') {
        formSchema.parse(user.value); // Validate only if editing
      }
      func(user.value);
      user.value = { status: 'received' };
    } catch (err) {
      console.error('Validation Error:', err);
    }
  };

  // Format status to a readable format
  const formatStatus = (status: string) =>
    status
      .split('_')
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
      .join(' ');

  // Update status when a dialog is opened
  watchEffect(() => {
    if (actions.length > 0) {
      const action = actions[0]; // You can update this logic as needed
      if (action?.row?.status) {
        user.value.status = action.row.status;
      }
    }
  });
</script>
