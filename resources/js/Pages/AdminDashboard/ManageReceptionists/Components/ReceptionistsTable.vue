<script setup>
import { computed, h } from 'vue'
import { Link } from '@inertiajs/vue3'
import { FlexRender, getCoreRowModel, useVueTable } from '@tanstack/vue-table'
import { ArrowDown, ArrowUp, ArrowUpDown, Ban, Pencil, Trash2, Undo2 } from 'lucide-vue-next'

import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'

const DEFAULT_AVATAR_PATH = '/images/default.png'

const props = defineProps({
  receptionists: {
    type: Array,
    default: () => [],
  },
  showManagerColumn: {
    type: Boolean,
    default: false,
  },
  sort: {
    type: String,
    default: 'created_at',
  },
  direction: {
    type: String,
    default: 'desc',
  },
})

const emit = defineEmits(['sort', 'delete', 'ban', 'unban'])

const sortIcon = (column) => {
  if (props.sort !== column) {
    return ArrowUpDown
  }

  return props.direction === 'asc' ? ArrowUp : ArrowDown
}

const sortableHeader = (label, column) =>
  h(
    Button,
    {
      variant: 'ghost',
      class: 'h-auto px-0 text-xs font-semibold uppercase tracking-[0.16em] text-slate-500 hover:bg-transparent hover:text-[#0c1a2e]',
      onClick: () => emit('sort', column),
    },
    {
      default: () => [label, h(sortIcon(column), { class: 'ml-1 h-3.5 w-3.5' })],
    },
  )

const resolveAvatar = (path) => {
  if (!path || path === 'default.png') {
    return DEFAULT_AVATAR_PATH
  }

  if (
    path.startsWith('http://') ||
    path.startsWith('https://') ||
    path.startsWith('/')
  ) {
    return path
  }

  return `/storage/${path}`
}

const columns = computed(() => {
  const baseColumns = [
    {
      accessorKey: 'name',
      header: () => sortableHeader('Name', 'name'),
      cell: ({ row }) =>
        h('div', { class: 'flex items-center gap-3' }, [
          h(
            Avatar,
            { class: 'h-10 w-10 border border-slate-200' },
            {
              default: () => [
                h(AvatarImage, {
                  src: resolveAvatar(row.original.avatar_image),
                  alt: row.original.name,
                  class: 'object-cover',
                }),
                h(
                  AvatarFallback,
                  { class: 'bg-[#dbe7f3] font-semibold text-[#0c1a2e]' },
                  () => row.original.name.slice(0, 2).toUpperCase(),
                ),
              ],
            },
          ),
          h('div', { class: 'min-w-0' }, [
            h('p', { class: 'truncate font-semibold text-[#0c1a2e]' }, row.original.name),
            h('p', { class: 'truncate text-xs text-slate-500' }, row.original.national_id || 'No national ID'),
          ]),
        ]),
    },
    {
      accessorKey: 'email',
      header: () => sortableHeader('Email', 'email'),
      cell: ({ row }) => h('span', { class: 'text-sm text-slate-600' }, row.original.email),
    },
    {
      accessorKey: 'created_at',
      header: () => sortableHeader('Created At', 'created_at'),
      cell: ({ row }) => h('span', { class: 'text-sm text-slate-600' }, row.original.created_at || '-'),
    },
  ]

  if (props.showManagerColumn) {
    baseColumns.splice(3, 0, {
      accessorKey: 'manager_name',
      header: () => h('span', { class: 'text-xs font-semibold uppercase tracking-[0.16em] text-slate-500' }, 'Manager Name'),
      cell: ({ row }) => h('span', { class: 'text-sm text-slate-600' }, row.original.manager_name || '-'),
    })
  }

  baseColumns.push(
    {
      accessorKey: 'status_label',
      header: () => sortableHeader('Status', 'banned_at'),
      cell: ({ row }) =>
        h(
          Badge,
          {
            class: row.original.is_banned
              ? 'border-red-200 bg-red-50 text-red-700'
              : 'border-emerald-200 bg-emerald-50 text-emerald-700',
          },
          { default: () => row.original.status_label },
        ),
    },
    {
      id: 'actions',
      header: () => h('div', { class: 'text-right text-xs font-semibold uppercase tracking-[0.16em] text-slate-500' }, 'Actions'),
      cell: ({ row }) => {
        if (!row.original.can_manage) {
          return h('span', { class: 'text-xs text-slate-400' }, 'View only')
        }

        return h('div', { class: 'flex items-center justify-end gap-2' }, [
          h(
            Link,
            { href: route('admins.receptionists.edit', row.original.id) },
            {
              default: () =>
                h(
                  Button,
                  {
                    variant: 'outline',
                    size: 'icon',
                    class: 'border-slate-200 text-slate-600 hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700',
                  },
                  { default: () => h(Pencil, { class: 'h-4 w-4' }) },
                ),
            },
          ),
          h(
            Button,
            {
              variant: 'outline',
              size: 'icon',
              class: row.original.is_banned
                ? 'border-emerald-200 text-emerald-700 hover:bg-emerald-50'
                : 'border-amber-200 text-amber-700 hover:bg-amber-50',
              onClick: () => emit(row.original.is_banned ? 'unban' : 'ban', row.original),
            },
            {
              default: () =>
                row.original.is_banned ? h(Undo2, { class: 'h-4 w-4' }) : h(Ban, { class: 'h-4 w-4' }),
            },
          ),
          h(
            Button,
            {
              variant: 'outline',
              size: 'icon',
              class: 'border-red-200 text-red-600 hover:bg-red-50',
              onClick: () => emit('delete', row.original),
            },
            { default: () => h(Trash2, { class: 'h-4 w-4' }) },
          ),
        ])
      },
    },
  )

  return baseColumns
})

const table = useVueTable({
  get data() {
    return props.receptionists
  },
  get columns() {
    return columns.value
  },
  getCoreRowModel: getCoreRowModel(),
})
</script>

<template>
  <div class="overflow-hidden rounded-3xl border border-[#d9e2ec] bg-white shadow-sm">
    <Table>
      <TableHeader class="bg-slate-50/80">
        <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id" class="hover:bg-transparent">
          <TableHead
            v-for="header in headerGroup.headers"
            :key="header.id"
            :class="header.id === 'actions' ? 'text-right' : ''"
          >
            <FlexRender
              v-if="!header.isPlaceholder"
              :render="header.column.columnDef.header"
              :props="header.getContext()"
            />
          </TableHead>
        </TableRow>
      </TableHeader>

      <TableBody>
        <template v-if="table.getRowModel().rows.length">
          <TableRow v-for="row in table.getRowModel().rows" :key="row.id">
            <TableCell
              v-for="cell in row.getVisibleCells()"
              :key="cell.id"
              :class="cell.column.id === 'actions' ? 'text-right' : ''"
            >
              <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
            </TableCell>
          </TableRow>
        </template>

        <TableRow v-else>
          <TableCell
            :colspan="showManagerColumn ? 6 : 5"
            class="py-14 text-center text-sm text-slate-500"
          >
            No receptionists matched the current filters.
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </div>
</template>
