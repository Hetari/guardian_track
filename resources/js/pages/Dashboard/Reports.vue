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
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
  } from '@tanstack/vue-table';
  import { ChevronDown } from 'lucide-vue-next';
  import { h, ref } from 'vue';
  import Action from './Action.vue';

  const breadcrumbs: BreadcrumbItem[] = [
    {
      title: 'Dashboard',
      href: '/dashboard',
    },
    {
      title: 'Reports',
      href: '/reports',
    },
  ];

  export interface Report {
    id: string;
    user_id: string;
    user: {
      id: string;
      name: string;
      email: string;
      phone: string;
    };
    type: string;
    product_name: string;
    serial_code: string;
    date_time: string;
    country: string;
    city: string;
    street_address: string;
    purchase_location: string;
    item_type: string;
    status: string;
  }

  const { reports: data = [] } = defineProps<{
    reports: Report[];
  }>();

  const columnHelper = createColumnHelper<Report>();
  const columns = [
    columnHelper.display({
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
      enableSorting: false,
      enableHiding: false,
    }),
    columnHelper.accessor('product_name', {
      header: 'Product Name',
      cell: ({ row }) => h('div', {}, row.original.product_name),
    }),
    columnHelper.accessor('type', {
      header: 'Report Type',
      cell: ({ row }) =>
        h(
          'div',
          {
            class: cn(
              'w-fit rounded-full px-3 py-1 text-center text-sm font-medium text-white',
              row.original.type === 'stolen' ? 'bg-red-600' : 'bg-yellow-600',
            ),
          },
          row.original.type,
        ),
    }),
    columnHelper.accessor('serial_code', {
      header: 'Serial Code',
      cell: ({ row }) => h('div', {}, row.original.serial_code),
    }),
    columnHelper.accessor('date_time', {
      header: 'Date & Time',
      cell: ({ row }) => h('div', {}, new Date(row.original.date_time).toLocaleString()),
    }),
    columnHelper.accessor('country', {
      header: 'Country',
      cell: ({ row }) => h('div', {}, row.original.country),
    }),
    columnHelper.accessor('city', {
      header: 'City',
      cell: ({ row }) => h('div', {}, row.original.city),
    }),
    columnHelper.accessor('status', {
      header: 'Status',
      cell: ({ row }) => {
        const statusColors: Record<string, string> = {
          received: 'bg-amber-600',
          checking_brand: 'bg-slate-600',
          checking_company: 'bg-violet-600',
          transferred_to_police: 'bg-cyan-600',
          done: 'bg-zinc-600',
        };

        return h(
          'div',
          {
            class: cn('w-fit rounded-full px-3 py-1 text-center text-sm font-medium text-white', statusColors[row.original.status] || 'bg-gray-500'),
          },
          row.original.status.replace(/_/g, ' '),
        );
      },
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
              router.post('/dashboard/reports/edit', {
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
              router.delete(`/dashboard/reports/delete/${row.original.id}`);
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
  const columnFilters = ref<ColumnFiltersState>([]);
  const columnVisibility = ref<VisibilityState>({});
  const rowSelection = ref({});
  const expanded = ref<ExpandedState>({});

  const table = useVueTable({
    data,
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getExpandedRowModel: getExpandedRowModel(),
    onSortingChange: (updaterOrValue) => valueUpdater(updaterOrValue, sorting),
    onColumnFiltersChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnFilters),
    onColumnVisibilityChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnVisibility),
    onRowSelectionChange: (updaterOrValue) => valueUpdater(updaterOrValue, rowSelection),
    onExpandedChange: (updaterOrValue) => valueUpdater(updaterOrValue, expanded),
    state: {
      get sorting() {
        return sorting.value;
      },
      get columnFilters() {
        return columnFilters.value;
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
            placeholder="Filter prduct names..."
            :model-value="table.getColumn('product_name')?.getFilterValue() as string"
            @update:model-value="table.getColumn('product_name')?.setFilterValue($event)"
          />
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
