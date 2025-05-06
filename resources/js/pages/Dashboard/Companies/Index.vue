<script setup lang="ts">
  import { Button } from '@/components/ui/button';
  import { Checkbox } from '@/components/ui/checkbox';
  import { Dialog, DialogContent, DialogFooter, DialogTrigger } from '@/components/ui/dialog';
  import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
  import { Form, FormControl, FormField, FormItem, FormLabel, FormMessage } from '@/components/ui/form';
  import { Input } from '@/components/ui/input';
  import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
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

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Dashboard',
      href: '/dashboard',
    },
    {
      title: 'Companies',
      href: '/companies',
    },
  ];

  export interface Company {
    id?: string;
    name: string;
    active: boolean;
    email: string;
  }
  const company = ref<Company>({
    name: '',
    active: true,
    email: '',
  });

  const { companies: data = [] } = defineProps<{
    companies: Company[];
  }>();

  const columnHelper = createColumnHelper<Company>();
  const columns = [
    columnHelper.group({
      id: 'select',
      header: ({ table }) =>
        h(Checkbox, {
          modelValue: table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
          'onUpdate:modelValue': (value) => table.toggleAllPageRowsSelected(!!value),
          ariaLabel: 'Select all',
        }),
      cell: ({ row }) =>
        h(Checkbox, {
          modelValue: row.getIsSelected(),
          'onUpdate:modelValue': (value) => row.toggleSelected(!!value),
          ariaLabel: 'Select row',
        }),
      enableColumnFilter: true,
      enableGlobalFilter: true,
    }),
    columnHelper.accessor('name', {
      header: 'Name',
      cell: ({ row }) => h('div', {}, row.original.name),
      enableColumnFilter: true,
      enableGlobalFilter: true,
    }),
    columnHelper.accessor('email', {
      header: 'Email',
      cell: ({ row }) => h('div', {}, row.original.email),
      enableColumnFilter: true,
      enableGlobalFilter: true,
    }),
    columnHelper.accessor('active', {
      header: 'Active',
      cell: ({ row }) =>
        h(
          'span',
          {
            class: row.original.active
              ? 'bg-green-200 text-green-600 px-3 py-1 rounded-full font-medium'
              : 'bg-red-200 text-red-600 px-3 py-1 rounded-full font-medium',
          },
          row.original.active ? 'Active' : 'Inactive',
        ),
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
              router.post('/dashboard/companies/edit', {
                id: row.original.id,
                ...data,
              });
            },
            row: row.original,
          },
          {
            name: 'Delete',
            icon: Trash,
            onSubmit: () => {
              router.delete(`/dashboard/companies/delete/${row.original.id}`);
            },
            row: row.original,
          },
        ];
        return h(Action, {
          actions,
          onActionClick: (action: any) => {
            action.action();
          },
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
      name: z.string().min(1, 'Name is required'),
      email: z.string().email('Invalid email address').min(1, 'Email is required'),
      active: z.boolean(),
    }),
  );

  const dialogOpen = ref(false);
  const createCompany = (data: Company) => {
    router.post(
      '/dashboard/companies/create',
      { ...data },
      {
        onSuccess: () => {
          company.value = { name: '', email: '', active: true };
          dialogOpen.value = false;
        },
        onError: (errors) => {
          console.error('Create company failed', errors);
        },
      },
    );
  };
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="w-full">
        <div class="flex items-center gap-2 py-4">
          <Input class="max-w-sm" placeholder="Search companies..." v-model="globalFilter" />
          <Dialog v-model:open="dialogOpen">
            <DialogTrigger as-child>
              <Button>Add New Company</Button>
            </DialogTrigger>
            <DialogContent class="sm:max-w-[425px]">
              <Form id="dialogForm" as="" keep-values :validation-schema="formSchema">
                <!-- Name Field -->
                <FormField name="name">
                  <FormItem>
                    <FormLabel>Name</FormLabel>
                    <FormControl>
                      <Input type="text" v-model="company.name" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>

                <!-- Email Field -->
                <FormField name="email">
                  <FormItem>
                    <FormLabel>Email</FormLabel>
                    <FormControl>
                      <Input type="email" placeholder="example@mail.com" v-model="company.email" />
                    </FormControl>
                    <FormMessage />
                  </FormItem>
                </FormField>

                <!-- Active Field -->
                <FormField name="active">
                  <FormItem>
                    <FormLabel>Active</FormLabel>
                    <FormControl>
                      <Select v-model="company.active as unknown as AcceptableValue">
                        <SelectTrigger class="w-full">
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
              </Form>

              <DialogFooter>
                <Button @click="() => createCompany(company)" variant="secondary" form="dialogForm"> Create New Company </Button>
              </DialogFooter>
            </DialogContent>
          </Dialog>

          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <Button variant="outline" class="ml-auto"> Columns <ChevronDown class="ml-2 h-4 w-4" /> </Button>
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
                <TableCell :colspan="columns.length" class="h-24 text-center"> No results. </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <div class="flex items-center justify-end space-x-2 py-4">
          <div class="flex-1 text-sm text-muted-foreground">
            {{ table.getFilteredSelectedRowModel().rows.length }} of {{ table.getFilteredRowModel().rows.length }} row(s) selected.
          </div>
          <div class="space-x-2">
            <Button variant="outline" size="sm" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()"> Previous </Button>
            <Button variant="outline" size="sm" :disabled="!table.getCanNextPage()" @click="table.nextPage()"> Next </Button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
