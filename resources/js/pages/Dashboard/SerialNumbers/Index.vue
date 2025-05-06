<script setup lang="ts">
  import { Button } from '@/components/ui/button';
  import { Dialog, DialogContent, DialogFooter, DialogTrigger } from '@/components/ui/dialog';
  import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
  import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
  import { Input } from '@/components/ui/input';
  import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
  import AppLayout from '@/layouts/AppLayout.vue';
  import { cn, valueUpdater } from '@/lib/utils';
  import { type BreadcrumbItem } from '@/types';
  import { Head, router } from '@inertiajs/vue3';
  import type { ColumnFiltersState, ExpandedState, SortingState, VisibilityState } from '@tanstack/vue-table';
  import {
    createColumnHelper,
    FlexRender,
    getCoreRowModel,
    getExpandedRowModel,
    getFacetedMinMaxValues,
    getFacetedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
  } from '@tanstack/vue-table';
  import { toTypedSchema } from '@vee-validate/zod';
  import { ChevronDown, PenIcon, Trash } from 'lucide-vue-next';
  import { AcceptableValue } from 'reka-ui';
  import { h, ref } from 'vue';
  import { z } from 'zod';
  import Action from './Action.vue';

  import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';

  const {
    serials: data = [],
    users = [],
    products = [],
  } = defineProps<{
    serials: SerialNumber[];
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

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Dashboard',
      href: '/dashboard',
    },
    {
      title: 'Serial Numbers',
      href: '/serial-numbers',
    },
  ];

  export interface SerialNumber {
    id?: string;
    serial_code: string;
    user: {
      id: string;
      name: string;
      email: string;
    };
    product: {
      id: string;
      name: string;
    };
  }
  const serialNumber = ref<SerialNumber>({
    serial_code: '',
    user: {
      id: '',
      name: '',
      email: '',
    },
    product: {
      id: '',
      name: '',
    },
  });

  const columnHelper = createColumnHelper<SerialNumber>();
  const columns = [
    columnHelper.accessor((row) => row.user.id, {
      id: 'user_id',
      header: 'User ID',
      cell: ({ row }) => h('div', {}, row.original.user.id),
      filterFn: 'includesString',
      enableColumnFilter: true,
      enableGlobalFilter: true,
    }),

    columnHelper.accessor((row) => row.user.name, {
      id: 'user_name',
      header: 'User Name',
      cell: ({ row }) => h('div', {}, row.original.user.name),
      filterFn: 'includesString',
      enableColumnFilter: true,
      enableGlobalFilter: true,
    }),

    columnHelper.accessor((row) => row.product.name, {
      id: 'product_name',
      header: 'Product Name',
      cell: ({ row }) => h('div', {}, row.original.product.name),
      filterFn: 'includesString',
      enableColumnFilter: true,
      enableGlobalFilter: true,
    }),
    columnHelper.accessor((row) => row.serial_code, {
      id: 'serial_code',
      header: 'Serial Number',
      cell: ({ row }) => h('div', {}, row.original.serial_code),
      filterFn: 'includesString',
      enableColumnFilter: true,
      enableGlobalFilter: true,
    }),
    {
      id: 'actions',
      enableHiding: false,
      cell: ({ row }: any) => {
        const actions = [
          {
            name: 'Edit',
            icon: PenIcon,
            onSubmit: (data: any) => {
              router.post(route('dashboard.serial-numbers.edit'), {
                id: row.original.id,
                ...data,
              });
            },
            row: row.original,
            users,
            products,
          },
          {
            name: 'Delete',
            icon: Trash,
            onSubmit: () => {
              router.delete(route('dashboard.serial-numbers.delete', row.original.id));
            },
            row: row.original,
          },
        ];
        return h(Action, {
          actions,
          users,
          products,
          onActionClick: (action: any) => action.action(),
        });
      },
    },
  ];

  const sorting = ref<SortingState>([]);
  const columnVisibility = ref<VisibilityState>({});
  const rowSelection = ref({});
  const expanded = ref<ExpandedState>({});
  const columnFilters = ref<ColumnFiltersState>([]);
  const globalFilter = ref('');

  const table = useVueTable({
    data,
    columns,
    onColumnFiltersChange: (updaterOrValue) => {
      columnFilters.value = typeof updaterOrValue === 'function' ? updaterOrValue(columnFilters.value) : updaterOrValue;
    },
    onGlobalFilterChange: (updaterOrValue) => {
      globalFilter.value = typeof updaterOrValue === 'function' ? updaterOrValue(globalFilter.value) : updaterOrValue;
    },
    getCoreRowModel: getCoreRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getFacetedRowModel: getFacetedRowModel(),
    getFacetedMinMaxValues: getFacetedMinMaxValues(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getExpandedRowModel: getExpandedRowModel(),

    onSortingChange: (updaterOrValue) => valueUpdater(updaterOrValue, sorting),

    onColumnVisibilityChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnVisibility),
    onRowSelectionChange: (updaterOrValue) => valueUpdater(updaterOrValue, rowSelection),
    onExpandedChange: (updaterOrValue) => valueUpdater(updaterOrValue, expanded),
    state: {
      get columnFilters() {
        return columnFilters.value;
      },
      get globalFilter() {
        return globalFilter.value;
      },
      get sorting() {
        return sorting.value;
      },
      get columnVisibility() {
        return columnVisibility.value;
      },
      get rowSelection() {
        return rowSelection.value;
      },
      get expanded() {
        return expanded.value;
      },
    },
  });

  const formSchema = toTypedSchema(
    z.object({
      user: z.object({
        name: z.string().min(1, 'User name is required'),
        email: z.string().email('Invalid email address'),
        id: z.string().optional(),
      }),
      product: z.object({
        name: z.string().min(1, 'Product name is required'),
        id: z.string().optional(),
      }),
    }),
  );

  const dialogOpen = ref(false);
  const createSerialNumber = (data: SerialNumber) => {
    router.post(
      route('dashboard.serial-numbers.store'),
      {
        user_id: data.user.id,
        product_id: data.product.id,
      },
      {
        onSuccess: () => {
          serialNumber.value = {
            serial_code: '',
            user: { id: '', name: '', email: '' },
            product: { id: '', name: '' },
          };
          dialogOpen.value = false;
        },
        onError: (errors) => {
          console.error('Create serial number failed', errors);
        },
      },
    );
  };
</script>

<template>
  <Head title="Serial Numbers" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="w-full">
        <div class="flex items-center gap-2 py-4">
          <Input class="max-w-sm" placeholder="Search serial numbers..." v-model="globalFilter" />

          <Dialog v-model:open="dialogOpen">
            <DialogTrigger as-child>
              <Button>Add New Serial Number</Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
              <Form id="dialogForm" as="" keep-values :validation-schema="formSchema">
                <FormField id="dialogForm" name="user.id" as="" keep-values :validation-schema="formSchema">
                  <FormItem>
                    <FormLabel>User</FormLabel>

                    <FormControl>
                      <Select v-model="serialNumber.user.id">
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

                <FormField name="product.id">
                  <FormItem>
                    <FormLabel>Product</FormLabel>
                    <FormControl>
                      <Select v-model="serialNumber.product.id">
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
              </Form>

              <DialogFooter>
                <Button @click="() => createSerialNumber(serialNumber)" variant="secondary" form="dialogForm"> Create New Serial Number </Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>

          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <Button variant="outline" class="ml-auto">
                Columns
                <ChevronDown class="ml-2 h-4 w-4" />
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuCheckboxItem
                v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
                :key="column.id"
                class="capitalize"
                :model-value="column.getIsVisible()"
                @update:model-value="
                  (value: any) => {
                    column.toggleVisibility(!!value);
                  }
                "
              >
                {{ column.id }}
              </DropdownMenuCheckboxItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>

        <div class="rounded-md border">
          <Table>
            <TableHeader>
              <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                <TableHead
                  v-for="header in headerGroup.headers"
                  :key="header.id"
                  :data-pinned="header.column.getIsPinned()"
                  :class="
                    cn({ 'sticky bg-background/95': header.column.getIsPinned() }, header.column.getIsPinned() === 'left' ? 'left-0' : 'right-0')
                  "
                >
                  <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
                </TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <template v-if="table.getRowModel().rows?.length">
                <template v-for="row in table.getRowModel().rows" :key="row.id">
                  <TableRow :data-state="row.getIsSelected() && 'selected'">
                    <TableCell
                      v-for="cell in row.getVisibleCells()"
                      :key="cell.id"
                      :data-pinned="cell.column.getIsPinned()"
                      :class="
                        cn({ 'sticky bg-background/95': cell.column.getIsPinned() }, cell.column.getIsPinned() === 'left' ? 'left-0' : 'right-0')
                      "
                    >
                      <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                    </TableCell>
                  </TableRow>
                  <TableRow v-if="row.getIsExpanded()">
                    <TableCell :colspan="row.getAllCells().length">
                      {{ JSON.stringify(row.original) }}
                    </TableCell>
                  </TableRow>
                </template>
              </template>

              <TableRow v-else>
                <TableCell :colspan="columns.length" class="h-24 text-center">No serial numbers found.</TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <div class="flex items-center justify-end space-x-2 py-4">
          <div class="flex-1 text-sm text-muted-foreground">
            {{ table.getFilteredSelectedRowModel().rows.length }} of {{ table.getFilteredRowModel().rows.length }} row(s) selected.
          </div>
          <div class="space-x-2">
            <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">Previous</Button>
            <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()">Next</Button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
