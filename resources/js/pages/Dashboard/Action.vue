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
  import { toTypedSchema } from '@vee-validate/zod';
  import { ref, watchEffect } from 'vue';
  import * as z from 'zod';

  const formSchema = toTypedSchema(
    z.object({
      username: z.string().min(2).max(50),
      email: z.string().email(),
      phone: z.string().optional(),
    }),
  );

  const user = ref({ name: '', email: '', phone: '' });

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
    user.value = { name: '', email: '', phone: '' };
  };

  // Update user info when the dialog opens
  watchEffect(() => {
    if (actions.length > 0) {
      const action = actions[0]; // Or any logic to select the action
      user.value = { ...action.row }; // Initialize user with the selected action's row data
    }
  });
</script>
