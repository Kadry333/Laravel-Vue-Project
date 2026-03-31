<script setup>
import { Head, Link, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Input } from "@/components/ui/input";
import AdminLayout from "@/Layouts/AdminLayout.vue";
import {
    CheckCircle,
    XCircle,
    Hotel,
    Search,
    ChevronLeft,
    ChevronRight,
    Calendar,
    DollarSign,
    Users,
    ClipboardList,
    Clock,
    UserCircle,
} from "lucide-vue-next";
import { ref, watch } from "vue";

const props = defineProps({
    reservations: Object,
    filters: Object,
    stats: Object,
});

defineOptions({ layout: AdminLayout });

const search = ref(props.filters?.search || "");
const statusFilter = ref(props.filters?.status || "");

function fetchData(extra = {}) {
    router.get(
        route("admins.reservations.index"),
        {
            search: search.value,
            status: statusFilter.value,
            ...extra,
        },
        {
            preserveState: true,
            replace: true,
        },
    );
}

watch([search, statusFilter], () => {
    fetchData({ page: 1 });
});

function updateStatus(id, status) {
    if (!confirm(`Are you sure you want to set this reservation to ${status}?`))
        return;

    router.patch(
        route("admins.reservations.update-status", { reservation: id }),
        {
            status: status,
        },
        {
            preserveScroll: true,
        },
    );
}

const getStatusVariant = (status) => {
    switch (status.toLowerCase()) {
        case "approved":
            return "default";
        case "pending":
            return "secondary";
        case "cancelled":
            return "destructive";
        default:
            return "outline";
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString("en-US", {
        month: "short",
        day: "numeric",
        year: "numeric",
    });
};
</script>

<template>
    <Head title="Admin | Reservations Dashboard" />

    <div class="space-y-6">
        <!-- Executive Header -->
        <div class="flex items-end justify-between">
            <div>
                <h1
                    class="text-3xl font-extrabold tracking-tight text-slate-900"
                >
                    Reservations Insight
                </h1>
                <p class="text-slate-500 font-medium">
                    Monitoring and managing guest bookings across all floors.
                </p>
            </div>
        </div>

        <!-- Professional Stats Cards -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div
                class="bg-white p-6 rounded-2xl border shadow-sm flex items-center gap-4"
            >
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <ClipboardList class="h-6 w-6" />
                </div>
                <div>
                    <p
                        class="text-xs font-bold text-slate-500 uppercase tracking-wider"
                    >
                        Total Bookings
                    </p>
                    <h3 class="text-2xl font-black text-slate-900">
                        {{ stats.total }}
                    </h3>
                </div>
            </div>

            <div
                class="bg-white p-6 rounded-2xl border shadow-sm flex items-center gap-4 border-l-4 border-l-amber-400"
            >
                <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                    <Clock class="h-6 w-6" />
                </div>
                <div>
                    <p
                        class="text-xs font-bold text-slate-500 uppercase tracking-wider"
                    >
                        Pending Action
                    </p>
                    <h3
                        class="text-2xl font-black text-slate-900 text-amber-600"
                    >
                        {{ stats.pending }}
                    </h3>
                </div>
            </div>

            <div
                class="bg-white p-6 rounded-2xl border shadow-sm flex items-center gap-4 border-l-4 border-l-emerald-400"
            >
                <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                    <CheckCircle class="h-6 w-6" />
                </div>
                <div>
                    <p
                        class="text-xs font-bold text-slate-500 uppercase tracking-wider"
                    >
                        Confirmed
                    </p>
                    <h3
                        class="text-2xl font-black text-slate-900 text-emerald-600"
                    >
                        {{ stats.approved }}
                    </h3>
                </div>
            </div>

            <div
                class="bg-white p-6 rounded-2xl border shadow-sm flex items-center gap-4 bg-slate-900"
            >
                <div class="p-3 bg-slate-800 text-indigo-400 rounded-xl">
                    <DollarSign class="h-6 w-6" />
                </div>
                <div>
                    <p
                        class="text-xs font-bold text-slate-400 uppercase tracking-wider"
                    >
                        Total Revenue
                    </p>
                    <h3 class="text-2xl font-black">
                        ${{ stats.revenue.toLocaleString() }}
                    </h3>
                </div>
            </div>
        </div>

        <!-- Advanced Filter Row -->
        <div
            class="flex flex-wrap items-center gap-4 bg-white p-4 rounded-xl border shadow-sm"
        >
            <div class="relative flex-1 min-w-[200px]">
                <Search
                    class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"
                />
                <Input
                    v-model="search"
                    placeholder="Search client, email or room..."
                    class="pl-10 border-slate-200 focus:ring-indigo-500"
                />
            </div>
            <select
                v-model="statusFilter"
                class="border border-slate-200 rounded-md text-sm px-4 py-2 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-500"
            >
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <!-- Premium Table Section -->
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr
                        class="bg-slate-50/50 border-b text-slate-500 font-bold uppercase text-[11px] tracking-widest"
                    >
                        <th class="px-6 py-4 text-left">Reference</th>
                        <th class="px-6 py-4 text-left">Guest Profile</th>
                        <th class="px-6 py-4 text-left">Room Details</th>
                        <th class="px-6 py-4 text-left">Stay Period</th>
                        <th class="px-6 py-4 text-left font-right text-right">
                            Value
                        </th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    <tr v-if="props.reservations.data.length === 0">
                        <td
                            colspan="7"
                            class="px-6 py-20 text-center text-slate-400 italic"
                        >
                            <Hotel class="h-12 w-12 mx-auto mb-2 opacity-10" />
                            No matching reservations found.
                        </td>
                    </tr>
                    <tr
                        v-for="res in props.reservations.data"
                        :key="res.id"
                        class="hover:bg-indigo-50/30 transition-colors group"
                    >
                        <td
                            class="px-6 py-5 font-mono text-xs text-indigo-600 font-black"
                        >
                            #{{ res.id }}
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-3">
                              <div
    class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center border border-slate-200 overflow-hidden"
>
   <img
                                        :src="
                                            res.client.avatar_image &&
                                            res.client.avatar_image !==
                                                'default.png'
                                                ? '/storage/' +
                                                  res.client.avatar_image
                                                : '/images/default.png'
                                        "
                                        class="w-9 h-9 rounded-full object-cover border border-slate-200 flex-shrink-0"
                                    />

    <UserCircle class="h-6 w-6 text-slate-400" />
</div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-900">{{
                                        res.client?.name
                                    }}</span>
                                    <span
                                        class="text-[11px] text-slate-500 font-medium"
                                        >{{ res.client?.email }}</span
                                    >
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div
                                    class="h-8 w-8 rounded-lg bg-slate-50 border flex items-center justify-center"
                                >
                                    <Hotel class="h-4 w-4 text-slate-600" />
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-700"
                                        >Room
                                        {{
                                            res.room?.number || res.room?.id
                                        }}</span
                                    >
                                    <span
                                        class="text-[10px] text-slate-400 uppercase font-bold"
                                        >{{
                                            res.room?.type || "Standard"
                                        }}</span
                                    >
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col text-xs space-y-1">
                                <div
                                    class="flex items-center gap-1.5 text-emerald-600 font-bold"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                                    ></span>
                                    {{ formatDate(res.check_in_date) }}
                                </div>
                                <div
                                    class="flex items-center gap-1.5 text-rose-600 font-bold"
                                >
                                    <span
                                        class="h-1.5 w-1.5 rounded-full bg-rose-500"
                                    ></span>
                                    {{ formatDate(res.check_out_date) }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-right">
                            <div class="flex flex-col">
                                <span
                                    class="text-base font-black text-slate-900"
                                    >${{
                                        res.paid_price.toLocaleString()
                                    }}</span
                                >
                                <span
                                    class="text-[10px] text-slate-400 font-bold uppercase italic"
                                    >Full Payment</span
                                >
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <Badge
                                :variant="getStatusVariant(res.status)"
                                class="capitalize px-3 rounded-full text-[11px] font-black border shadow-sm"
                            >
                                {{ res.status }}
                            </Badge>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex justify-center gap-1">
                                <template
                                    v-if="
                                        res.status.toLowerCase() === 'pending'
                                    "
                                >
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        class="h-9 px-3 border-emerald-100 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white font-bold transition-all"
                                        @click="
                                            updateStatus(res.id, 'approved')
                                        "
                                    >
                                        <CheckCircle class="h-4 w-4 mr-1.5" />
                                        Confirm
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="sm"
                                        class="h-9 px-3 border-rose-100 bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white font-bold transition-all"
                                        @click="
                                            updateStatus(res.id, 'cancelled')
                                        "
                                    >
                                        <XCircle class="h-4 w-4 mr-1.5" />
                                        Cancel
                                    </Button>
                                </template>
                                <span
                                    v-else
                                    class="text-[10px] font-black uppercase tracking-tighter text-slate-300 py-1 px-3 bg-slate-50 rounded-lg border border-dashed"
                                    >History Log Only</span
                                >
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="flex justify-between mt-4">
            <Button
                :disabled="!props.reservations.prev_page_url"
                @click="
                    fetchData({ page: props.reservations.current_page - 1 })
                "
                variant="outline"
            >
                Prev
            </Button>

            <span class="text-sm text-slate-600 font-medium self-center">
                Page {{ props.reservations.current_page }} of
                {{ props.reservations.last_page }}
            </span>

            <Button
                variant="outline"
                :disabled="!props.reservations.next_page_url"
                @click="
                    fetchData({ page: props.reservations.current_page + 1 })
                "
            >
                Next
            </Button>
        </div>
    </div>
</template>
