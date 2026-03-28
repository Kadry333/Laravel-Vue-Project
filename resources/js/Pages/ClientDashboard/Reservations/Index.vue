<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { Calendar, CreditCard, Hotel, Eye, MapPin } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';

const props = defineProps({
    reservations: Object,
});

defineOptions({ layout: ClientLayout });

function fetchData(page) {
    router.get(route('reservation.index'), { page }, {
        preserveState: true,
        replace: true
    });
}

const getStatusVariant = (status) => {
    switch (status.toLowerCase()) {
        case 'approved': return 'default'; // Success color usually
        case 'pending': return 'outline';
        case 'cancelled': return 'destructive';
        default: return 'secondary';
    }
};
</script>

<template>
    <Head title="My Reservations | Grand Luxury" />

    <div class="p-6 lg:p-10 space-y-8 bg-slate-50/50 min-h-screen">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-slate-900">Your Reservations</h1>
                <p class="text-slate-500 mt-1 flex items-center gap-1.5">
                    <Hotel class="h-4 w-4" />
                    Managing {{ props.reservations.total }} bookings in your history.
                </p>
            </div>
            <Link :href="route('rooms.index')">
                <Button class="bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all hover:scale-[1.02]">
                    Book a New Room
                </Button>
            </Link>
        </div>

        <!-- Reservations Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50/80 border-b border-slate-200 text-slate-600 font-medium">
                            <th class="px-6 py-4 text-left">Ref ID</th>
                            <th class="px-6 py-4 text-left">Room Details</th>
                            <th class="px-6 py-4 text-left">Stay Period</th>
                            <th class="px-6 py-4 text-left">Total Paid</th>
                            <th class="px-6 py-4 text-left">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-if="props.reservations.data.length === 0">
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400 italic">
                                No reservations found. Start your journey by booking a room!
                            </td>
                        </tr>
                        <tr 
                            v-for="res in props.reservations.data" 
                            :key="res.id"
                            class="hover:bg-slate-50/50 transition-colors"
                        >
                            <td class="px-6 py-5 font-mono text-xs text-indigo-600 font-bold">
                                #{{ res.id }}
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-900">Room {{ res.room?.number || res.room?.id }}</span>
                                    <span class="text-xs text-slate-500 flex items-center gap-1">
                                        <MapPin class="h-3 w-3" /> {{ res.room?.type || 'Standard' }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2 text-slate-600">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-semibold uppercase text-slate-400">In</span>
                                        <span>{{ new Date(res.check_in_date).toLocaleDateString() }}</span>
                                    </div>
                                    <div class="h-8 w-px bg-slate-200 mx-2"></div>
                                    <div class="flex flex-col">
                                        <span class="text-xs font-semibold uppercase text-slate-400">Out</span>
                                        <span>{{ new Date(res.check_out_date).toLocaleDateString() }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 font-bold text-slate-900 italic">
                                <div class="flex items-center gap-1.5">
                                    <CreditCard class="h-4 w-4 text-green-600" />
                                    ${{ res.paid_price.toLocaleString() }}
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <Badge :variant="getStatusVariant(res.status)" class="capitalize px-2.5 py-0.5 rounded-full text-xs font-semibold">
                                    {{ res.status }}
                                </Badge>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <Button variant="ghost" size="icon" title="View Details" class="text-slate-400 hover:text-indigo-600 hover:bg-indigo-50">
                                    <Eye class="h-4 w-4" />
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

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
