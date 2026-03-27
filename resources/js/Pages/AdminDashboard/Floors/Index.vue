<script setup>
import AlertDialog from '@/components/ui/alert-dialog/AlertDialog.vue'
import AlertDialogAction from '@/components/ui/alert-dialog/AlertDialogAction.vue'
import AlertDialogCancel from '@/components/ui/alert-dialog/AlertDialogCancel.vue'
import AlertDialogContent from '@/components/ui/alert-dialog/AlertDialogContent.vue'
import AlertDialogDescription from '@/components/ui/alert-dialog/AlertDialogDescription.vue'
import AlertDialogFooter from '@/components/ui/alert-dialog/AlertDialogFooter.vue'
import AlertDialogHeader from '@/components/ui/alert-dialog/AlertDialogHeader.vue'
import AlertDialogTitle from '@/components/ui/alert-dialog/AlertDialogTitle.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router, useForm, usePage } from '@inertiajs/vue3'
import { getCoreRowModel, useVueTable } from '@tanstack/vue-table'
import { Eye, Pencil, Plus, Trash2 } from 'lucide-vue-next'
import { computed, ref, watch } from 'vue'
import { toast } from 'vue-sonner'

const page = usePage()
const user = computed(() => page.props.auth.user);
const isAdmin = computed(() =>
  user.value?.roles?.some(role => role.name === 'admin')
)


defineOptions({ layout: AdminLayout })

const openDelete = ref(false)
const targetId = ref(null)

const form = useForm({})

function confirmDelete(id) {
    targetId.value = id
    openDelete.value = true
}

function destroy() {
    form.transform(() => ({ _method: 'DELETE' }))
        .post(`/admins/floors/${targetId.value}`, {
            onSuccess: () => {
              openDelete.value = false
              targetId.value = null;
             },
        })
}


const props = defineProps({
    floors: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const sorting = ref(props.filters?.sort || 'id')
const direction = ref(props.filters?.direction || 'asc')
const maxFloor = ref(props.filters?.max_floor);
const minFloor = ref(props.filters?.min_floor);

function fetchData(extra = {}) {
    router.get('/admins/floors', {
        search: search.value,
        sort: sorting.value,
        direction: direction.value,
        min_floor: minFloor.value,
        max_floor: maxFloor.value,
        ...extra
    }, {
        preserveState: true,
        replace: true
    })
}

watch([search, minFloor, maxFloor], (val) => {
    fetchData({ page: 1 })
})

function changeSort(column) {
    if (sorting.value === column) {
        direction.value = direction.value === 'asc' ? 'desc' : 'asc'
    } else {
        sorting.value = column
        direction.value = 'asc'
    }
    fetchData()
}

const table = useVueTable({
    data: props.floors.data,
    columns: isAdmin ? [
        {
            accessorKey: 'name',
            header: () => 'Name',
        },
        {
            accessorKey: 'id',
            header: () => 'Number',
        },
        {
            accessorKey: 'manager.name',
            header: () => 'Manager',
        },
    ] : [
        {
            accessorKey: 'name',
            header: () => 'Name',
        },
        {
            accessorKey: 'id',
            header: () => 'Number',
        },
    ],
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
    pageCount: props.floors.last_page,
})


</script>

<template>
<div>
    <div class="mb-6 flex items-end justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manage floors</h1>
            <p class="text-sm text-slate-500 mt-1">
                {{ props.floors.total }} floors total
            </p>
        </div>

        <Link href="/admins/floors/create" :class="{ 'pointer-events-none opacity-50': form.processing }">
            <Button :disabled="form.processing">
                <Plus class="w-4 h-4 mr-2" /> Add Floor
            </Button>
        </Link>
    </div>

    <div class="mb-4">
        <Input
            v-model="search"
            :placeholder="isAdmin ? 'Search by name, manager' : 'Search by name'"
            class="border px-3 py-2 rounded w-64 bg-white"
        />

        <Input
    v-model="minFloor"
    type="number"
    placeholder="Min Floors"
    class="border px-3 py-2 rounded w-40 bg-white"
/>

<Input
    v-model="maxFloor"
    type="number"
    placeholder="Max Floors"
    class="border px-3 py-2 rounded w-40 bg-white"
/>
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b">

                    <th @click="changeSort('name')" class="cursor-pointer px-5 py-3">
                        Name
                    </th>

                    <th @click="changeSort('id')" class="cursor-pointer px-5 py-3">
                        Number
                    </th>

                    <th v-if="isAdmin" @click="changeSort('manager.name')" class="cursor-pointer px-5 py-3">
                        Manager
                    </th>

                    <th class="px-5 py-3">
                        Actions
                    </th>
                </tr>
            </thead>

            <tbody>
                <tr
                    v-for="floor in props.floors.data"
                    :key="floor.id"
                    class="border-b hover:bg-slate-50"
                >

                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1.5">
                            {{ floor.name }}
                        </div>
                    </td>

                    <td class="px-5 py-4">
                        <div class="flex items-center gap-1.5">
                            {{ floor.id }}
                        </div>
                    </td>

                    <td v-if="isAdmin" class="px-5 py-4 font-semibold">
                        {{ floor.manager.name }}
                    </td>

                    <td class="px-5 py-4">
                        <div v-if="isAdmin || user.id === floor.manager.id" class="flex gap-2">

                            <Link :href="`/admins/floors/${floor.id}`">
                                <Button variant="outline" size="icon" :disabled="form.processing">
                                    <Eye class="w-3.5 h-3.5" />
                                </Button>
                            </Link>


                            <Link :href="`/admins/floors/${floor.id}/edit`">
                                <Button variant="outline" size="icon" :disabled="form.processing">
                                    <Pencil class="w-3.5 h-3.5" />
                                </Button>
                            </Link>

                            <Button
                                variant="outline"
                                class="text-red-500"
                                @click="confirmDelete(floor.id)"
                                :disabled="form.processing"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                            </Button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="flex justify-between mt-4">
        <Button
            :disabled="!props.floors.prev_page_url || form.processing"
            @click="fetchData({ page: props.floors.current_page - 1 })"
        >
            Prev
        </Button>

        <span>
            Page {{ props.floors.current_page }} of {{ props.floors.last_page }}
        </span>

        <Button
            :disabled="!props.floors.next_page_url || form.processing"
            @click="fetchData({ page: props.floors.current_page + 1 })"
        >
            Next
        </Button>
    </div>


    <AlertDialog v-model:open="openDelete">
        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>Delete Floor</AlertDialogTitle>
                <AlertDialogDescription>
                    Are you sure? This action cannot be undone.
                </AlertDialogDescription>
            </AlertDialogHeader>

            <AlertDialogFooter>
                <AlertDialogCancel>Cancel</AlertDialogCancel>
                <AlertDialogAction
                    class="bg-red-500"
                    @click="destroy"
                >
                    Delete
                </AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</div>

</template>
