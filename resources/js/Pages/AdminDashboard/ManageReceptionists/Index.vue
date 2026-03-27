<script setup>
import { computed, ref, watch } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Ban, Search, ShieldCheck, UserCheck, UsersRound } from 'lucide-vue-next'

import AdminLayout from '@/Layouts/AdminLayout.vue'
import ReceptionistsTable from './Components/ReceptionistsTable.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  receptionists: {
    type: Object,
    required: true,
  },
  filters: {
    type: Object,
    default: () => ({}),
  },
  permissions: {
    type: Object,
    default: () => ({}),
  },
  stats: {
    type: Object,
    default: () => ({}),
  },
})

const search = ref(props.filters.search || '')
const sort = ref(props.filters.sort || 'created_at')
const direction = ref(props.filters.direction || 'desc')
const dialogOpen = ref(false)
const actionType = ref('delete')
const selectedReceptionist = ref(null)
const actionForm = useForm({})

let searchDebounce

const actionCopy = computed(() => {
  if (actionType.value === 'ban') {
    return {
      title: 'Ban receptionist',
      description: 'This receptionist will be signed out and blocked from protected routes until you unban the account.',
      button: 'Ban receptionist',
      class: 'bg-amber-600 text-white hover:bg-amber-700',
    }
  }

  if (actionType.value === 'unban') {
    return {
      title: 'Unban receptionist',
      description: 'This receptionist will regain access to the system immediately after signing in again.',
      button: 'Unban receptionist',
      class: 'bg-emerald-600 text-white hover:bg-emerald-700',
    }
  }

  return {
    title: 'Delete receptionist',
    description: 'This will permanently remove the receptionist account and its uploaded avatar.',
    button: 'Delete receptionist',
    class: 'bg-red-600 text-white hover:bg-red-700',
  }
})

const visit = (extra = {}) => {
  router.get(
    route('admins.receptionists.index'),
    {
      search: search.value || undefined,
      sort: sort.value,
      direction: direction.value,
      ...extra,
    },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
    },
  )
}

watch(search, () => {
  clearTimeout(searchDebounce)
  searchDebounce = setTimeout(() => {
    visit({ page: 1 })
  }, 300)
})

const toggleSort = (column) => {
  if (sort.value === column) {
    direction.value = direction.value === 'asc' ? 'desc' : 'asc'
  } else {
    sort.value = column
    direction.value = column === 'created_at' ? 'desc' : 'asc'
  }

  visit({ page: 1 })
}

const openActionDialog = (type, receptionist) => {
  actionType.value = type
  selectedReceptionist.value = receptionist
  dialogOpen.value = true
}

const submitAction = () => {
  if (!selectedReceptionist.value) {
    return
  }

  if (actionType.value === 'delete') {
    actionForm.delete(route('admins.receptionists.destroy', selectedReceptionist.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        dialogOpen.value = false
      },
    })

    return
  }

  actionForm
    .transform(() => ({ _method: 'patch' }))
    .post(
      route(
        actionType.value === 'ban' ? 'admins.receptionists.ban' : 'admins.receptionists.unban',
        selectedReceptionist.value.id,
      ),
      {
        preserveScroll: true,
        onSuccess: () => {
          dialogOpen.value = false
        },
      },
    )
}
</script>

<template>
  <Head title="Manage Receptionists" />

  <div class="space-y-6">
    <section class="overflow-hidden rounded-[30px] border border-[#d9e2ec] bg-white shadow-sm">
      <div class="flex flex-col gap-6 bg-gradient-to-r from-[#0c1a2e] via-[#10233c] to-[#163152] px-6 py-8 text-white sm:px-8 lg:flex-row lg:items-end lg:justify-between">
        <div class="max-w-3xl space-y-3">
          <Badge class="border-sky-400/30 bg-sky-400/10 text-sky-100">Admin Dashboard</Badge>
          <div class="space-y-2">
            <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Manage Receptionists</h1>
            <p class="max-w-2xl text-sm leading-6 text-slate-200/85">
              Create receptionist accounts, review access status, and keep front-desk staff management aligned with the existing admin workflow.
            </p>
          </div>
        </div>

        <div v-if="permissions.can_create" class="shrink-0">
          <Link :href="route('admins.receptionists.create')">
            <Button class="h-11 rounded-xl bg-white px-5 text-sm font-semibold text-[#0c1a2e] hover:bg-slate-100">
              Add Receptionist
            </Button>
          </Link>
        </div>
      </div>

      <div class="grid gap-4 border-t border-white/5 bg-[#f7f9fc] px-6 py-6 sm:grid-cols-3 sm:px-8">
        <Card class="rounded-2xl border-[#d9e2ec] shadow-none">
          <CardContent class="flex items-center justify-between p-5">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#5b708b]">Total</p>
              <p class="mt-2 text-3xl font-semibold text-[#0c1a2e]">{{ stats.total ?? 0 }}</p>
            </div>
            <div class="rounded-2xl bg-sky-50 p-3 text-sky-700">
              <UsersRound class="h-5 w-5" />
            </div>
          </CardContent>
        </Card>

        <Card class="rounded-2xl border-[#d9e2ec] shadow-none">
          <CardContent class="flex items-center justify-between p-5">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#5b708b]">Active</p>
              <p class="mt-2 text-3xl font-semibold text-[#0c1a2e]">{{ stats.active ?? 0 }}</p>
            </div>
            <div class="rounded-2xl bg-emerald-50 p-3 text-emerald-700">
              <UserCheck class="h-5 w-5" />
            </div>
          </CardContent>
        </Card>

        <Card class="rounded-2xl border-[#d9e2ec] shadow-none">
          <CardContent class="flex items-center justify-between p-5">
            <div>
              <p class="text-xs font-semibold uppercase tracking-[0.18em] text-[#5b708b]">Banned</p>
              <p class="mt-2 text-3xl font-semibold text-[#0c1a2e]">{{ stats.banned ?? 0 }}</p>
            </div>
            <div class="rounded-2xl bg-red-50 p-3 text-red-700">
              <ShieldCheck class="h-5 w-5" />
            </div>
          </CardContent>
        </Card>
      </div>
    </section>

    <section class="rounded-[28px] border border-[#d9e2ec] bg-white p-6 shadow-sm">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h2 class="text-xl font-semibold text-[#0c1a2e]">Receptionists Directory</h2>
          <p class="mt-1 text-sm text-[#5b708b]">
            Search by name or email and use the actions column to edit, ban, unban, or delete.
          </p>
        </div>

        <div class="relative w-full max-w-md">
          <Search class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-[#5b708b]" />
          <Input
            v-model="search"
            type="text"
            placeholder="Search receptionists by name or email"
            class="h-11 rounded-xl border-[#d9e2ec] bg-white pl-10 text-[#0c1a2e] placeholder:text-[#8aa0b8] focus-visible:ring-[#0c1a2e]"
          />
        </div>
      </div>

      <div class="mt-6">
        <ReceptionistsTable
          :receptionists="receptionists.data"
          :show-manager-column="permissions.show_manager_column"
          :sort="sort"
          :direction="direction"
          @sort="toggleSort"
          @delete="openActionDialog('delete', $event)"
          @ban="openActionDialog('ban', $event)"
          @unban="openActionDialog('unban', $event)"
        />
      </div>

      <div class="mt-6 flex flex-col gap-4 border-t border-[#e7edf5] pt-5 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm text-[#5b708b]">
          Showing {{ receptionists.from ?? 0 }} to {{ receptionists.to ?? 0 }} of {{ receptionists.total ?? 0 }} receptionists.
        </p>

        <div class="flex items-center gap-2">
          <Button
            variant="outline"
            class="border-[#d9e2ec] text-[#0c1a2e] hover:bg-[#f8fbff]"
            :disabled="!receptionists.prev_page_url"
            @click="visit({ page: receptionists.current_page - 1 })"
          >
            Previous
          </Button>

          <span class="px-2 text-sm font-medium text-[#5b708b]">
            Page {{ receptionists.current_page }} of {{ receptionists.last_page }}
          </span>

          <Button
            variant="outline"
            class="border-[#d9e2ec] text-[#0c1a2e] hover:bg-[#f8fbff]"
            :disabled="!receptionists.next_page_url"
            @click="visit({ page: receptionists.current_page + 1 })"
          >
            Next
          </Button>
        </div>
      </div>
    </section>
  </div>

  <AlertDialog v-model:open="dialogOpen">
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>{{ actionCopy.title }}</AlertDialogTitle>
        <AlertDialogDescription>
          {{ actionCopy.description }}
          <span v-if="selectedReceptionist" class="mt-2 block font-medium text-slate-700">
            {{ selectedReceptionist.name }} ({{ selectedReceptionist.email }})
          </span>
        </AlertDialogDescription>
      </AlertDialogHeader>

      <AlertDialogFooter>
        <AlertDialogCancel>Cancel</AlertDialogCancel>
        <AlertDialogAction :class="actionCopy.class" @click="submitAction">
          <Ban v-if="actionType === 'ban'" class="mr-2 h-4 w-4" />
          {{ actionForm.processing ? 'Processing...' : actionCopy.button }}
        </AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>
