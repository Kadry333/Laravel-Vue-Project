<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router } from "@inertiajs/vue3";
import { ref, watch, computed } from "vue";
import { useVueTable, getCoreRowModel } from "@tanstack/vue-table";
import {
    Pencil,
    Trash2,
    CheckCircle,
    Plus,
    Search,
    Users,
} from "lucide-vue-next";

defineOptions({ layout: AdminLayout });

const props = defineProps({
    clients:   Object,
    countries: Array,
    filters:   Object,
    canCreate: Boolean,
    canDelete: Boolean,
    role:      String,
    pageTitle: String,
});

const isReceptionist = computed(() => props.role === "receptionist");
const isApprovedPage = computed(() => !!props.pageTitle);
const title          = computed(() => props.pageTitle ?? "Manage Clients");

const search     = ref(props.filters?.search     ?? "");
const country_id = ref(props.filters?.country_id ?? "");
const gender     = ref(props.filters?.gender     ?? "");
const status     = ref(props.filters?.status     ?? "");

function applyFilters() {
    const routeName = isApprovedPage.value
        ? "admins.clients.approved"
        : "admins.clients.index";

    router.get(
        route(routeName),
        {
            search:     search.value     || undefined,
            country_id: country_id.value || undefined,
            gender:     gender.value     || undefined,
            status:     !isReceptionist.value && !isApprovedPage.value
                            ? status.value || undefined
                            : undefined,
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
        { accessorKey: "name",       header: "Client Details"   },
        { accessorKey: "mobile",     header: "Mobile"           },
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

const table = useVueTable({
    get data()    { return props.clients?.data ?? []; },
    get columns() { return columns.value; },
    getCoreRowModel:  getCoreRowModel(),
    manualPagination: true,
    manualFiltering:  true,
    pageCount: props.clients?.last_page ?? 1,
});

const showDeleteModal = ref(false);
const clientToDelete  = ref(null);

function confirmDelete(client) {
    clientToDelete.value = client;
    showDeleteModal.value = true;
}

function deleteClient() {
    router.delete(route("admins.clients.destroy", clientToDelete.value.id), {
        onSuccess: () => {
            showDeleteModal.value = false;
            clientToDelete.value  = null;
        },
    });
}

function approveClient(client) {
    router.patch(route("admins.clients.approve", client.id));
}

function goToPage(url) {
    if (url) router.get(url, {
        search:     search.value     || undefined,
        country_id: country_id.value || undefined,
        gender:     gender.value     || undefined,
        status:     status.value     || undefined,
    }, { preserveState: true });
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
            <div class="flex items-center gap-3">
                <button
                    v-if="isReceptionist && !isApprovedPage"
                    @click="router.get(route('admins.clients.approved'))"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors"
                >
                    <Users class="w-4 h-4" />
                    My Approved Clients
                </button>
                <button
                    v-if="isReceptionist && isApprovedPage"
                    @click="router.get(route('admins.clients.index'))"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors"
                >
                    ← Pending Clients
                </button>
                <button
                    v-if="canCreate"
                    @click="router.get(route('admins.clients.create'))"
                    class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-colors"
                >
                    <Plus class="w-4 h-4" />
                    Add Client
                </button>
            </div>
        </div>

        <!-- Status tabs -->
        <div v-if="!isReceptionist && !isApprovedPage" class="flex gap-2 mb-4">
            <button @click="status = ''"
                :class="['px-4 py-1.5 rounded-full text-xs font-bold transition-colors border',
                    status === '' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50']">
                All
            </button>
            <button @click="status = 'approved'"
                :class="['px-4 py-1.5 rounded-full text-xs font-bold transition-colors border',
                    status === 'approved' ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-white text-emerald-700 border-emerald-200 hover:bg-emerald-50']">
                Approved
            </button>
            <button @click="status = 'pending'"
                :class="['px-4 py-1.5 rounded-full text-xs font-bold transition-colors border',
                    status === 'pending' ? 'bg-amber-500 text-white border-amber-500' : 'bg-white text-amber-700 border-amber-200 hover:bg-amber-50']">
                Pending
            </button>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap gap-3 mb-4">
            <div class="relative flex-1 min-w-[220px]">
                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
                <input v-model="search" type="text" placeholder="Search by name or email..."
                    class="w-full pl-9 pr-4 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300" />
            </div>
            <select v-model="country_id"
                class="px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300 text-slate-600">
                <option value="">All Countries</option>
                <option v-for="c in countries" :key="c.id" :value="c.id">{{ c.name }}</option>
            </select>
            <select v-model="gender"
                class="px-3 py-2 text-sm bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300 text-slate-600">
                <option value="">All Genders</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600">
                        <th v-for="header in table.getFlatHeaders()" :key="header.id"
                            class="px-5 py-3 text-left">
                            <span :class="['text-xs font-semibold text-slate-500 uppercase tracking-wide',
                                header.column.id === 'actions' ? 'flex justify-center' : '']">
                                {{ header.column.columnDef.header }}
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="table.getRowModel().rows.length === 0">
                        <td :colspan="columns.length" class="px-5 py-20 text-center text-slate-400 italic">
                            No clients found.
                        </td>
                    </tr>
                    <tr v-for="row in table.getRowModel().rows" :key="row.id"
                        class="border-b border-slate-100 hover:bg-slate-50 transition-colors">

                        <!-- Client Details -->
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img
                                    :src="row.original.avatar_image && row.original.avatar_image !== 'default.png'
                                        ? '/storage/avatars/' + row.original.avatar_image
                                        : '/images/default.png'"
                                    class="w-9 h-9 rounded-full object-cover border border-slate-200 flex-shrink-0"
                                />
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-900">{{ row.original.name }}</span>
                                    <span class="text-[11px] text-slate-500">{{ row.original.email }}</span>
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
                                <span class="text-slate-700 font-medium">{{ countryName(row.original.country_id) }}</span>
                                <span class="text-[10px] text-slate-400 uppercase font-bold">{{ row.original.gender }}</span>
                            </div>
                        </td>

                        <!-- Status -->
                        <td v-if="!isReceptionist && !isApprovedPage" class="px-5 py-4">
                            <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold capitalize',
                                row.original.is_approved ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700']">
                                {{ row.original.is_approved ? "Approved" : "Pending Verification" }}
                            </span>
                        </td>

                        <!-- Actions -->
                        <td v-if="!isApprovedPage" class="px-5 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <button
                                    v-if="!row.original.is_approved"
                                    @click="approveClient(row.original)"
                                    class="h-8 inline-flex items-center gap-1.5 px-3 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors"
                                >
                                    <CheckCircle class="w-3.5 h-3.5 flex-shrink-0" />
                                    Approve
                                </button>
                                <div v-else class="h-8 w-[82px] flex-shrink-0"></div>

                                <button
                                    v-if="canCreate"
                                    @click="router.get(route('admins.clients.edit', row.original.id))"
                                    class="h-8 inline-flex items-center gap-1.5 px-3 text-xs font-semibold text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors"
                                >
                                    <Pencil class="w-3.5 h-3.5 flex-shrink-0" />
                                </button>

                                <button
                                    v-if="canDelete"
                                    @click="confirmDelete(row.original)"
                                    class="h-8 inline-flex items-center gap-1.5 px-3 text-xs font-semibold text-red-600 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors"
                                >
                                    <Trash2 class="w-3.5 h-3.5 flex-shrink-0" />
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex items-center justify-between mt-4">
            <span class="text-sm text-slate-600 font-medium">
                Showing {{ clients.from ?? 0 }}–{{ clients.to ?? 0 }} of {{ clients.total }}
            </span>
            <div class="flex gap-1">
                <button v-for="link in clients.links" :key="link.label"
                    :disabled="!link.url"
                    @click="goToPage(link.url)"
                    v-html="link.label"
                    :class="['min-w-[34px] h-8 px-2 text-sm rounded-lg border transition-colors',
                        link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50',
                        !link.url ? 'opacity-40 cursor-not-allowed' : 'cursor-pointer']"
                />
            </div>
        </div>

        <!-- Delete modal -->
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40">
            <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                        <Trash2 class="w-5 h-5 text-red-600" />
                    </div>
                    <h3 class="text-base font-semibold text-slate-800">Delete client</h3>
                </div>
                <p class="text-sm text-slate-500 mb-6">
                    Are you sure you want to delete
                    <strong class="text-slate-800">{{ clientToDelete?.name }}</strong>?
                    This cannot be undone.
                </p>
                <div class="flex justify-end gap-3">
                    <button @click="showDeleteModal = false"
                        class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                        Cancel
                    </button>
                    <button @click="deleteClient"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>