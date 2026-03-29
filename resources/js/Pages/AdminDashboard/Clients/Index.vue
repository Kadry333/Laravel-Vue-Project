<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { Input } from '@/components/ui/input';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
    CheckCircle2, 
    XCircle, 
    Users, 
    Search,
    ChevronLeft,
    ChevronRight,
    UserCircle
} from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
    clients: Object,
    filters: Object,
});

defineOptions({ layout: AdminLayout });

const search = ref(props.filters?.search || '');

function fetchData(extra = {}) {
    router.get(route('admins.clients.index'), {
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

function toggleApproval(id) {
    router.patch(route('admins.clients.toggle-approval', { client: id }), {}, {
        preserveScroll: true
    });
}
</script>

<template>
    <Head title="Manage Clients" />

    <div>
        <!-- Heading Section -->
        <div class="mb-6 flex items-end justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Registered Clients</h1>
                <p class="text-sm text-slate-500 mt-1">
                    {{ props.clients.total }} total registrations
                </p>
            </div>
        </div>

        <!-- Search Section -->
        <div class="mb-4">
            <Input
                v-model="search"
                placeholder="Search by name, email or National ID..."
                class="border px-3 py-2 rounded w-64 bg-white"
            />
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl border shadow-sm overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b text-slate-600">
                        <th class="px-5 py-3 text-left">Ref</th>
                        <th class="px-5 py-3 text-left">Client Details</th>
                        <th class="px-5 py-3 text-left">Nationality / Gender</th>
                        <th class="px-5 py-3 text-left">National ID</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="props.clients.data.length === 0">
                        <td colspan="6" class="px-5 py-20 text-center text-slate-400 italic">
                            No clients found.
                        </td>
                    </tr>
                    <tr
                        v-for="client in props.clients.data"
                        :key="client.id"
                        class="border-b hover:bg-slate-50 transition-colors"
                    >
                        <td class="px-5 py-4 font-mono text-xs text-indigo-600 font-bold">
                            #{{ client.id }}
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img
                                    :src="client.avatar_image ? `/avatars/${client.avatar_image}` : '/images/default.png'"
                                    class="w-9 h-9 rounded-full object-cover border border-slate-200"
                                    alt="Client Avatar"
                                />
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-900">{{ client.name }}</span>
                                    <span class="text-[11px] text-slate-500">{{ client.email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-col">
                                <span class="text-slate-700 font-medium capitalize">{{ client.country }}</span>
                                <span class="text-[10px] text-slate-400 uppercase font-bold">{{ client.gender }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 font-semibold text-slate-700">
                            {{ client.national_id }}
                        </td>
                        <td class="px-5 py-4">
                            <Badge 
                                :variant="client.is_approved ? 'default' : 'secondary'" 
                                class="capitalize px-2 rounded-full text-[10px] font-bold"
                            >
                                {{ client.is_approved ? 'Approved' : 'Pending Verification' }}
                            </Badge>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex justify-center gap-2">
                                <Button
                                    v-if="!client.is_approved"
                                    variant="outline"
                                    class="text-green-600 border-green-200 bg-green-50 hover:bg-green-100 h-8 px-4"
                                    @click="toggleApproval(client.id)"
                                >
                                    <CheckCircle2 class="h-3.5 w-3.5 mr-1.5" /> Approve
                                </Button>
                                <Button
                                    v-else
                                    variant="outline"
                                    class="text-amber-600 border-amber-200 bg-amber-50 hover:bg-amber-100 h-8 px-4"
                                    @click="toggleApproval(client.id)"
                                >
                                    <XCircle class="h-3.5 w-3.5 mr-1.5" /> Suspend
                                </Button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Section -->
        <div class="flex justify-between mt-4">
            <Button
                :disabled="!props.clients.prev_page_url"
                @click="fetchData({ page: props.clients.current_page - 1 })"
                variant="outline"
                size="sm"
            >
                Prev
            </Button>

            <span class="text-sm text-slate-600 font-medium">
                Page {{ props.clients.current_page }} of {{ props.clients.last_page }}
            </span>

            <Button
                :disabled="!props.clients.next_page_url"
                @click="fetchData({ page: props.clients.current_page + 1 })"
                variant="outline"
                size="sm"
            >
                Next
            </Button>
        </div>
    </div>
</template>
