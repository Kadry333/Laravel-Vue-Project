<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Calendar, CreditCard, Hotel, Eye, MapPin, UserCircle } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';

const props = defineProps({
    reservations: Object,
});

defineOptions({ layout: AdminLayout });

function fetchData(page) {
    router.get(route('reservation.index'), { page }, {
        preserveState: true,
        replace: true
    });
}

const getStatusVariant = (status) => {
    switch (status.toLowerCase()) {
        case 'approved': return 'default';
        case 'pending': return 'outline';
        case 'cancelled': return 'destructive';
        default: return 'secondary';
    }
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};
</script>

<template>
    <Head title="My Reservations | Grand Luxury" />

    <div class="p-6  space-y-8 min-h-screen">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Your Reservations</h1>
                <p class="text-slate-500 mt-1 flex items-center gap-1.5">
                    <Hotel class="h-4 w-4" />
                    Managing {{ props.reservations.total }} bookings in your history.
                </p>
            </div>
            <Link href="/client/rooms">
                <Button class="bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all hover:scale-[1.02]">
                    Book a New Room
                </Button>
            </Link>
        </div>

        <!-- Premium Table Section -->
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50/50 border-b text-slate-500 font-bold uppercase text-[11px] tracking-widest">
                        <th class="px-6 py-4 text-left">Reference</th>
                        <th class="px-6 py-4 text-left">Room Details</th>
                        <th class="px-6 py-4 text-left">Stay Period</th>
                        <th class="px-6 py-4 text-left text-right">Value</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y">
                    <tr v-if="props.reservations.data.length === 0">
                        <td colspan="5" class="px-6 py-20 text-center text-slate-400 italic">
                            <Hotel class="h-12 w-12 mx-auto mb-2 opacity-10" />
                            No reservations found. Start your journey by booking a room!
                        </td>
                    </tr>
                    <tr
                        v-for="res in props.reservations.data"
                        :key="res.id"
                        class="hover:bg-indigo-50/30 transition-colors group"
                    >
                        <td class="px-6 py-5 font-mono text-xs text-indigo-600 font-black">
                            #{{ res.id }}
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="h-8 w-8 rounded-lg bg-slate-50 border flex items-center justify-center">
                                    <Hotel class="h-4 w-4 text-slate-600" />
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-700">Room {{ res.room?.number || res.room?.id }}</span>
                                    <span class="text-[10px] text-slate-400 uppercase font-bold">{{ res.room?.type || 'Standard' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex flex-col text-xs space-y-1">
                                <div class="flex items-center gap-1.5 text-emerald-600 font-bold">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                    {{ formatDate(res.check_in_date) }}
                                </div>
                                <div class="flex items-center gap-1.5 text-rose-600 font-bold">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                    {{ formatDate(res.check_out_date) }}
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-right">
                             <div class="flex flex-col">
                                <span class="text-base font-black text-slate-900">${{ res.paid_price.toLocaleString() }}</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase italic">Full Payment</span>
                             </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <Badge :variant="getStatusVariant(res.status)" class="capitalize px-3 rounded-full text-[11px] font-black border shadow-sm">
                                {{ res.status }}
                            </Badge>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Table Footer / Pagination -->
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <p class="text-xs text-slate-500">
                    Showing <span class="font-bold text-slate-700">{{ props.reservations.from || 0 }}</span> to 
                    <span class="font-bold text-slate-700">{{ props.reservations.to || 0 }}</span> of 
                    <span class="font-bold text-slate-700">{{ props.reservations.total }}</span> entries.
                </p>
                <div class="flex gap-2">
                    <Button 
                        variant="outline" 
                        size="sm" 
                        :disabled="!props.reservations.prev_page_url"
                        @click="fetchData(props.reservations.current_page - 1)"
                        class="h-8 text-xs font-semibold"
                    >
                        Previous
                    </Button>
                    <Button 
                        variant="outline" 
                        size="sm"
                        :disabled="!props.reservations.next_page_url"
                        @click="fetchData(props.reservations.current_page + 1)"
                        class="h-8 text-xs font-semibold"
                    >
                        Next
                    </Button>
                </div>
            </div>
        </div>
    </div>
</template>
