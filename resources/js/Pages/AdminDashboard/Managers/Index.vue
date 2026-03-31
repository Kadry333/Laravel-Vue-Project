<script setup>
import AlertDialog from "@/components/ui/alert-dialog/AlertDialog.vue";
import AlertDialogAction from "@/components/ui/alert-dialog/AlertDialogAction.vue";
import AlertDialogCancel from "@/components/ui/alert-dialog/AlertDialogCancel.vue";
import AlertDialogContent from "@/components/ui/alert-dialog/AlertDialogContent.vue";
import AlertDialogDescription from "@/components/ui/alert-dialog/AlertDialogDescription.vue";
import AlertDialogFooter from "@/components/ui/alert-dialog/AlertDialogFooter.vue";
import AlertDialogHeader from "@/components/ui/alert-dialog/AlertDialogHeader.vue";
import AlertDialogTitle from "@/components/ui/alert-dialog/AlertDialogTitle.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Link, router, useForm, usePage } from "@inertiajs/vue3";
import { getCoreRowModel, useVueTable } from "@tanstack/vue-table";
import { Eye, Pencil, Plus, Trash2 } from "lucide-vue-next";
import { computed, ref, watch } from "vue";
import { toast } from "vue-sonner";
import { debounce } from "lodash";

const page = usePage();
const user = computed(() => page.props.auth.user);

defineOptions({ layout: AdminLayout });

const openDelete = ref(false);
const targetId = ref(null);

const form = useForm({});

function confirmDelete(id) {
    targetId.value = id;
    openDelete.value = true;
}

function destroy() {
    form.transform(() => ({ _method: "DELETE" })).post(
        `/admins/managers/${targetId.value}`,
        {
            onSuccess: () => {
                openDelete.value = false;
                targetId.value = null;
            },
        },
    );
}

const props = defineProps({
    managers: Object,
    filters: Object,
});

const search = ref(props.filters?.search || "");
const sorting = ref(props.filters?.sort || "id");
const direction = ref(props.filters?.direction || "asc");

function fetchData(extra = {}) {
    router.get(
        "/admins/managers",
        {
            search: search.value,
            sort: sorting.value,
            direction: direction.value,
            ...extra,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}

const debouncedFetch = debounce(() => {
    fetchData({ page: 1 });
}, 500);

watch(search, (val) => {
    debouncedFetch();
});

function changeSort(column) {
    if (sorting.value === column) {
        direction.value = direction.value === "asc" ? "desc" : "asc";
    } else {
        sorting.value = column;
        direction.value = "asc";
    }
    fetchData();
}

const table = useVueTable({
    data: props.managers.data,
    columns: [
        {
            accessorKey: "name",
            header: () => "Name",
        },
        {
            accessorKey: "email",
            header: () => "Email",
        },
        {
            accessorKey: "national_id",
            header: () => "National ID",
        },
        {
            accessorKey: "avatar_image",
            header: () => "Image",
        },
    ],
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
    pageCount: props.managers.last_page,
});
</script>

<template>
    <div>
        <div class="mb-6 flex items-end justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Manage managers
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ props.managers.total }} managers total
                </p>
            </div>

            <Link
                href="/admins/managers/create"
                :class="{ 'pointer-events-none opacity-50': form.processing }"
            >
                <Button :disabled="form.processing">
                    <Plus class="w-4 h-4 mr-2" /> Add Manager
                </Button>
            </Link>
        </div>

        <div class="mb-4 flex flex-col gap-1">
            <label class="text-xs text-slate-500">Search</label>
            <Input
                v-model="search"
                placeholder="Search by name, email or national id"
                class="border px-3 py-2 rounded w-64 bg-white"
            />
        </div>

        <div v-if="props.managers.data.length > 0">
            <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b">
                            <th
                                @click="changeSort('name')"
                                class="cursor-pointer px-5 py-3"
                            >
                                Name
                            </th>

                            <th
                                @click="changeSort('email')"
                                class="cursor-pointer px-5 py-3"
                            >
                                Email
                            </th>

                            <th
                                @click="changeSort('national_id')"
                                class="cursor-pointer px-5 py-3"
                            >
                                National ID
                            </th>

                            <th class="cursor-pointer px-5 py-3">Image</th>

                            <th class="px-5 py-3">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-for="manager in props.managers.data"
                            :key="manager.id"
                            class="border-b hover:bg-slate-50"
                        >
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-1.5">
                                    {{ manager.name }}
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center gap-1.5">
                                    {{ manager.email }}
                                </div>
                            </td>

                            <td class="px-5 py-4 font-semibold">
                                {{ manager.national_id }}
                            </td>

                            <td class="px-5 py-4">
                                <img
                                    :src="
                                        manager.avatar_image
                                            ? `/storage/${manager.avatar_image}`
                                            : '/images/default.png'
                                    "
                                    class="w-10 h-10 rounded-full object-cover border"
                                    alt="Manager Avatar"
                                />
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex gap-2">
                                    <Link
                                        :href="`/admins/managers/${manager.id}`"
                                    >
                                        <Button
                                            variant="outline"
                                            size="icon"
                                            :disabled="form.processing"
                                        >
                                            <Eye class="w-3.5 h-3.5" />
                                        </Button>
                                    </Link>

                                    <Link
                                        :href="`/admins/managers/${manager.id}/edit`"
                                    >
                                        <Button
                                            variant="outline"
                                            size="icon"
                                            :disabled="form.processing"
                                        >
                                            <Pencil class="w-3.5 h-3.5" />
                                        </Button>
                                    </Link>

                                    <Button
                                        variant="outline"
                                        class="text-red-500"
                                        @click="confirmDelete(manager.id)"
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
                    :disabled="!props.managers.prev_page_url || form.processing"
                    @click="
                        fetchData({ page: props.managers.current_page - 1 })
                    "
                >
                    Prev
                </Button>

                <span>
                    Page {{ props.managers.current_page }} of
                    {{ props.managers.last_page }}
                </span>

                <Button
                    :disabled="!props.managers.next_page_url || form.processing"
                    @click="
                        fetchData({ page: props.managers.current_page + 1 })
                    "
                >
                    Next
                </Button>
            </div>
        </div>
        <div
            v-else
            class="flex flex-col items-center justify-center py-16 text-muted-foreground"
        >
            <p class="text-base font-medium">No managers available</p>
            <p class="text-sm">
                Try adjusting your filters or add a new manager
            </p>
        </div>

        <AlertDialog v-model:open="openDelete">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete Manager</AlertDialogTitle>
                    <AlertDialogDescription>
                        Are you sure? This action cannot be undone.
                    </AlertDialogDescription>
                </AlertDialogHeader>

                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction class="bg-red-500" @click="destroy">
                        Delete
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>