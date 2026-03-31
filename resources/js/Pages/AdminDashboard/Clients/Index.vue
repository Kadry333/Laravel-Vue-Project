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
import { Link, router } from "@inertiajs/vue3";
import { getCoreRowModel, useVueTable } from "@tanstack/vue-table";
import { CheckCircle, Pencil, Plus, Trash2, Users } from "lucide-vue-next";
import { computed, ref, watch } from "vue";

defineOptions({ layout: AdminLayout });

const props = defineProps({
    clients: Object,
    countries: Array,
    filters: Object,
    canCreate: Boolean,
    canDelete: Boolean,
    role: String,
    pageTitle: String,
});

const isReceptionist = computed(() => props.role === "receptionist");
const isApprovedPage = computed(() => !!props.pageTitle);
const title = computed(() => props.pageTitle ?? "Manage Clients");

const search = ref(props.filters?.search ?? "");
const country_id = ref(props.filters?.country_id ?? "");
const gender = ref(props.filters?.gender ?? "");
const status = ref(props.filters?.status ?? "");
const sort = ref(props.filters?.sort ?? "");
const direction = ref(props.filters?.direction ?? "asc");

function applyFilters() {
    const routeName = isApprovedPage.value
        ? "admins.clients.approved"
        : "admins.clients.index";

    router.get(
        route(routeName),
        {
            search: search.value || undefined,
            country_id: country_id.value || undefined,
            gender: gender.value || undefined,
            status:
                !isReceptionist.value && !isApprovedPage.value
                    ? status.value || undefined
                    : undefined,
            sort: sort.value || undefined,
            direction: sort.value ? direction.value : undefined,
        },
        { preserveState: true, replace: true },
    );
}

let searchTimeout;
watch(search, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(applyFilters, 400);
});
watch([country_id, gender, status], applyFilters);

function countryName(id) {
    return props.countries.find((c) => c.id === id)?.name ?? "—";
}

const columns = computed(() => {
    const cols = [
        { accessorKey: "name", header: "Client Details" },
        { accessorKey: "mobile", header: "Mobile" },
        { accessorKey: "country_id", header: "Country / Gender" },
    ];
    if (!isReceptionist.value && !isApprovedPage.value) {
        cols.push({ accessorKey: "is_approved", header: "Status" });
    }
    if (!isApprovedPage.value) {
        cols.push({ accessorKey: "actions", header: "Actions" });
    }
    return cols;
});
function toggleSort() {
    if (sort.value !== "name") {
        sort.value = "name";
        direction.value = "asc";
    } else if (direction.value === "asc") {
        direction.value = "desc";
    } else {
        sort.value = "";
        direction.value = "asc";
    }
    applyFilters();
}

const table = useVueTable({
    get data() {
        return props.clients?.data ?? [];
    },
    get columns() {
        return columns.value;
    },
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
    manualFiltering: true,
    pageCount: props.clients?.last_page ?? 1,
});

// ── Delete modal ─────────────────────────────────────────────
const openDelete = ref(false);
const clientToDelete = ref(null);

function confirmDelete(client) {
    clientToDelete.value = client;
    openDelete.value = true;
}

function deleteClient() {
    router.delete(route("admins.clients.destroy", clientToDelete.value.id), {
        onSuccess: () => {
            openDelete.value = false;
            clientToDelete.value = null;
        },
    });
}

function approveClient(client) {
    router.patch(route("admins.clients.approve", client.id));
}

function goToPage(url) {
    if (url)
        router.get(
            url,
            {
                search: search.value || undefined,
                country_id: country_id.value || undefined,
                gender: gender.value || undefined,
                status: status.value || undefined,
                sort: sort.value || undefined,
                direction: sort.value ? direction.value : undefined,
            },
            { preserveState: true },
        );
}
</script>

<template>
    <div>
        <!-- Header -->
        <div class="mb-6 flex items-end justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">{{ title }}</h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ clients.total }} total registrations
                </p>
            </div>

            <div class="flex items-center gap-2">
                <!-- My Approved Clients — receptionist on main index -->
                <Button
                    v-if="isReceptionist && !isApprovedPage"
                    variant="outline"
                    @click="router.get(route('admins.clients.approved'))"
                >
                    <Users class="w-4 h-4 mr-2" />
                    My Approved Clients
                </Button>

                <!-- Back to Pending — receptionist on approved page -->
                <Button
                    v-if="isReceptionist && isApprovedPage"
                    variant="outline"
                    @click="router.get(route('admins.clients.index'))"
                >
                    ← Pending Clients
                </Button>

                <!-- Add Client — admin + manager only -->
                <Link v-if="canCreate" :href="route('admins.clients.create')">
                    <Button>
                        <Plus class="w-4 h-4 mr-2" />
                        Add Client
                    </Button>
                </Link>
            </div>
        </div>

        <!-- Search + filters row -->
        <div class="flex flex-wrap gap-3 mb-4">
            <Input
                v-model="search"
                placeholder="Search by name or email..."
                class="w-64 bg-white"
            />

            <select
                v-model="country_id"
                class="px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-slate-400 text-slate-600"
            >
                <option value="">All Countries</option>
                <option v-for="c in countries" :key="c.id" :value="c.id">
                    {{ c.name }}
                </option>
            </select>

            <select
                v-model="gender"
                class="px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-slate-400 text-slate-600"
            >
                <option value="">All Genders</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>

            <!-- Status filter — admin + manager only -->
            <select
                v-if="!isReceptionist && !isApprovedPage"
                v-model="status"
                class="px-3 py-2 text-sm bg-white border border-slate-200 rounded-md focus:outline-none focus:ring-1 focus:ring-slate-400 text-slate-600"
            >
                <option value="">All Statuses</option>
                <option value="approved">Approved</option>
                <option value="pending">Pending</option>
            </select>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b">
                        <th
                            v-for="header in table.getFlatHeaders()"
                            :key="header.id"
                            class="px-5 py-3"
                            :class="{
                                'cursor-pointer': header.column.id === 'name',
                            }"
                            @click="header.column.id === 'name' && toggleSort()"
                        >
                            {{ header.column.columnDef.header }}
                            <span
                                v-if="
                                    header.column.id === 'name' &&
                                    sort === 'name'
                                "
                            >
                                {{ direction === "asc" ? "↑" : "↓" }}
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="table.getRowModel().rows.length === 0">
                        <td
                            :colspan="columns.length"
                            class="px-5 py-20 text-center text-slate-400 italic"
                        >
                            No clients found.
                        </td>
                    </tr>

                    <tr
                        v-for="row in table.getRowModel().rows"
                        :key="row.id"
                        class="border-b hover:bg-slate-50"
                    >
                        <!-- Client Details -->
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img
                                    :src="
                                        row.original.avatar_image &&
                                        row.original.avatar_image !==
                                            'default.png'
                                            ? '/storage/avatars/' +
                                              row.original.avatar_image
                                            : '/images/default.png'
                                    "
                                    class="w-9 h-9 rounded-full object-cover border border-slate-200 flex-shrink-0"
                                />
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-900">{{
                                        row.original.name
                                    }}</span>
                                    <span class="text-[11px] text-slate-500">{{
                                        row.original.email
                                    }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Mobile -->
                        <td class="px-5 py-4 text-slate-700">
                            {{ row.original.mobile ?? "—" }}
                        </td>

                        <!-- Country + Gender -->
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                <span class="text-slate-700 font-medium">{{
                                    countryName(row.original.country_id)
                                }}</span>
                                <span
                                    class="text-[10px] text-slate-400 uppercase font-bold"
                                    >{{ row.original.gender }}</span
                                >
                            </div>
                        </td>

                        <!-- Status -->
                        <td
                            v-if="!isReceptionist && !isApprovedPage"
                            class="px-5 py-4"
                        >
                            <span
                                :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize',
                                    row.original.is_approved
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : 'bg-amber-100 text-amber-700',
                                ]"
                            >
                                {{
                                    row.original.is_approved
                                        ? "Approved"
                                        : "Pending Verification"
                                }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td v-if="!isApprovedPage" class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <!-- Approve -->
                                <Button
                                    v-if="!row.original.is_approved"
                                    variant="outline"
                                    size="sm"
                                    class="text-emerald-600 border-emerald-200 bg-emerald-50 hover:bg-emerald-100"
                                    @click="approveClient(row.original)"
                                >
                                    <CheckCircle class="w-3.5 h-3.5 mr-1.5" />
                                    Approve
                                </Button>
                                <div v-else class="w-[90px]"></div>

                                <!-- Edit -->
                                <Link
                                    v-if="canCreate"
                                    :href="
                                        route(
                                            'admins.clients.edit',
                                            row.original.id,
                                        )
                                    "
                                >
                                    <Button variant="outline" size="icon">
                                        <Pencil class="w-3.5 h-3.5" />
                                    </Button>
                                </Link>

                                <!-- Delete -->
                                <Button
                                    v-if="canDelete"
                                    variant="outline"
                                    size="icon"
                                    class="text-red-500"
                                    @click="confirmDelete(row.original)"
                                >
                                    <Trash2 class="w-3.5 h-3.5" />
                                </Button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between mt-4">
            <Button
                variant="outline"
                :disabled="!clients.prev_page_url"
                @click="goToPage(clients.prev_page_url)"
            >
                Prev
            </Button>

            <span class="text-sm text-slate-600 font-medium self-center">
                Page {{ clients.current_page }} of {{ clients.last_page }}
            </span>

            <Button
                variant="outline"
                :disabled="!clients.next_page_url"
                @click="goToPage(clients.next_page_url)"
            >
                Next
            </Button>
        </div>

        <!-- Delete AlertDialog (same as manager page) -->
        <AlertDialog v-model:open="openDelete">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete Client</AlertDialogTitle>
                    <AlertDialogDescription>
                        Are you sure you want to delete
                        <strong>{{ clientToDelete?.name }}</strong
                        >? This action cannot be undone.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction class="bg-red-500" @click="deleteClient">
                        Delete
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>
