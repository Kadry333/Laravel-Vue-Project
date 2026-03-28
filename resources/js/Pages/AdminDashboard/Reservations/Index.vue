<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    CheckCircle, 
    XCircle, 
    Hotel, 
    Search,
    ChevronLeft,
    ChevronRight,
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
    reservations: Object,
    filters: Object,
});

defineOptions({ layout: AdminLayout });

const search = ref(props.filters?.search || '');

function fetchData(extra = {}) {
    router.get(route('admins.reservations.index'), {
        search: search.value,
        ...extra
    }, {
        preserveState: true,
        replace: true
    });
}

watch(search, () => {
    fetchData({ page: 1 });
});

function updateStatus(id, status) {
    router.patch(route('admins.reservations.update-status', { reservation: id }), {
        status: status
    }, {
        preserveScroll: true
    });
}

const getStatusVariant = (status) => {
    switch (status.toLowerCase()) {
        case 'approved': return 'default';
        case 'pending': return 'secondary';
        case 'cancelled': return 'destructive';
        default: return 'outline';
    }
};
</script>

<template>
    <Head title="Manage Reservations" />

    <div>
        <!-- Heading Section (Exactly like Managers) -->
        <div class="mb-6 flex items-end justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Manage Reservations</h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ props.reservations.total }} reservations total
                </p>
            </div>
            <!-- Potential Export or other action button here -->
        </div>

        <!-- Search Section (Exactly like Managers) -->
        <div class="mb-4">
            <Input
                v-model="search"
                placeholder="Search by client name, email or room..."
                class="border px-3 py-2 rounded w-64 bg-white"
            />
        </div>

        <!-- Table Section (Exactly like Managers) -->
        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b">
                        <th class="px-5 py-3 text-left">ID</th>
                        <th class="px-5 py-3 text-left">Guest Info</th>
                        <th class="px-5 py-3 text-left">Room</th>
                        <th class="px-5 py-3 text-left">Period</th>
                        <th class="px-5 py-3 text-left">Amount</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="props.reservations.data.length === 0">
                        <td colspan="7" class="px-5 py-20 text-center text-slate-400 italic">
                            No reservations found.
                        </td>
                    </tr>
                    <tr
                        v-for="res in props.reservations.data"
                        :key="res.id"
                        class="border-b hover:bg-slate-50 transition-colors"
                    >
                        <td class="px-5 py-4 font-mono text-xs text-indigo-600 font-bold">
                            #{{ res.id }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-900">{{ res.client?.name }}</span>
                                <span class="text-xs text-slate-500">{{ res.client?.email }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-semibold italic text-slate-700">
                            Room {{ res.room?.number || res.room?.id }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col text-xs space-y-0.5">
                                <span class="text-green-600 font-medium">In: {{ res.check_in_date }}</span>
                                <span class="text-rose-600 font-medium">Out: {{ res.check_out_date }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-bold text-slate-900">
                            ${{ res.paid_price.toLocaleString() }}
                        </td>
                        <td class="px-5 py-4">
                            <Badge :variant="getStatusVariant(res.status)" class="capitalize px-2 rounded-full text-[10px] font-bold">
                                {{ res.status }}
                            </Badge>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-center gap-2">
                                <template v-if="res.status.toLowerCase() === 'pending'">
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        class="h-8 w-8 border-green-200 text-green-600 hover:bg-green-50"
                                        @click="updateStatus(res.id, 'approved')"
                                        title="Approve"
                                    >
                                        <CheckCircle class="h-4 w-4" />
                                    </Button>
                                    <Button
                                        variant="outline"
                                        size="icon"
                                        class="h-8 w-8 border-rose-200 text-rose-600 hover:bg-rose-50"
                                        @click="updateStatus(res.id, 'cancelled')"
                                        title="Reject"
                                    >
                                        <XCircle class="h-4 w-4" />
                                    </Button>
                                </template>
                                <span v-else class="text-[10px] font-bold uppercase tracking-widest text-slate-300">Finalized</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Section (Exactly like Managers) -->
        <div class="flex justify-between mt-4">
            <Button
                :disabled="!props.reservations.prev_page_url"
                @click="fetchData({ page: props.reservations.current_page - 1 })"
                variant="outline"
                size="sm"
            >
                Prev
            </Button>

            <span class="text-sm text-slate-600 font-medium">
                Page {{ props.reservations.current_page }} of {{ props.reservations.last_page }}
            </span>

            <Button
                :disabled="!props.reservations.next_page_url"
                @click="fetchData({ page: props.reservations.current_page + 1 })"
                variant="outline"
                size="sm"
            >
                Next
            </Button>
        </div>
    </div>
</template>
