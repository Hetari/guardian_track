<script setup lang="ts">
  import { Button } from '@/components/ui/button';
  import AppLayout from '@/layouts/AppLayout.vue';
  import { cn, valueUpdater } from '@/lib/utils';
  import { type BreadcrumbItem } from '@/types';
  import { Head, router } from '@inertiajs/vue3';
  import type { ColumnFiltersState, ExpandedState, SortingState, VisibilityState } from '@tanstack/vue-table';
  import { PenIcon, Trash } from 'lucide-vue-next';

  import { Checkbox } from '@/components/ui/checkbox';
  import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
  import { Input } from '@/components/ui/input';
  import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
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
  import { ChevronDown, ChevronsUpDown } from 'lucide-vue-next';
  import { h, ref } from 'vue';
  import Action from './Action.vue';

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Dashboard',
      href: '/dashboard',
    },
    {
      title: 'Users',
      href: '/users',
    },
  ];

  export interface User {
    id: string;
    name: string;
    email: string;
    phone: string;
    reports_count: number;
    country: string;
  }

  const { users: data = [] } = defineProps<{
    users: User[];
  }>();

  const columnHelper = createColumnHelper<User>();
  const columns = [
    columnHelper.display({
      id: 'select',
      header: ({ table }) =>
        h(Checkbox, {
          modelValue: table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
          'onUpdate:modelValue': (value) => table.toggleAllPageRowsSelected(!!value),
          ariaLabel: 'Select all',
        }),
      cell: ({ row }) => {
        return h(Checkbox, {
          modelValue: row.getIsSelected(),
          'onUpdate:modelValue': (value) => row.toggleSelected(!!value),
          ariaLabel: 'Select row',
        });
      },
      enableSorting: false,
      enableHiding: false,
      enableColumnFilter: true,
      enableGlobalFilter: true,
    }),
    columnHelper.accessor('email', {
      header: ({ column }) => {
        return h(
          Button,
          {
            variant: 'ghost',
            onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
          },
          () => ['Email', h(ChevronsUpDown, { class: 'ml-2 h-4 w-4' })],
        );
      },
      cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('email')),
      enableColumnFilter: true,
      enableGlobalFilter: true,
    }),
    columnHelper.accessor('name', {
      header: ({ column }) => {
        return h(
          Button,
          {
            variant: 'ghost',
            onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
          },
          () => ['Name', h(ChevronsUpDown, { class: 'ml-2 h-4 w-4' })],
        );
      },
      cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('name')),
      enableColumnFilter: true,
      enableGlobalFilter: true,
    }),
    columnHelper.accessor('phone', {
      header: 'Phone',
      cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('phone')),
      enableColumnFilter: true,
      enableGlobalFilter: true,
    }),
    columnHelper.accessor('country', {
      header: 'Country',
      cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('country')),
      enableColumnFilter: true,
      enableGlobalFilter: true,
    }),
    columnHelper.accessor('reports_count', {
      header: 'Reports',
      cell: ({ row }) => h('div', { class: 'lowercase' }, row.getValue('reports_count')),
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
              router.post('/dashboard/users/edit', {
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
              router.delete(`/dashboard/users/delete/${row.original.id}`);
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
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="w-full">
        <div class="flex items-center gap-2 py-4">
          <Input
            class="max-w-sm"
            placeholder="Filter emails..."
            :model-value="table.getColumn('email')?.getFilterValue() as string"
            @update:model-value="table.getColumn('email')?.setFilterValue($event)"
          />
          <Button @click="() => router.get('/register')">Add User</Button>
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
