<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue'
import { ref, computed, watch, onMounted } from 'vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription
} from '@/components/ui/dialog'
import {
    Users,
    Building2,
    CheckCircle2,
    ArrowRight,
    XCircle
} from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useForm, usePage, Link } from '@inertiajs/vue3'

defineOptions({ layout: ClientLayout })

const props = defineProps({
    rooms: Array
})

const page = usePage()
const open = ref(false)
const selected = ref(null)
const showSuccess = ref(false)
const showCancel = ref(false)


const form = useForm({
    room_id: null,
    accompany_number: 0,
})

const successData = ref(null);

onMounted(() => {
    console.log('All Page Props:', page.props);
    if (page.props.flash && page.props.flash.payment_success) {
        successData.value = page.props.flash.payment_success;
        showSuccess.value = true;
    }
    if (page.props.flash && page.props.flash.payment_cancelled) {

        showCancel.value = true

    }
});

function openBooking(room) {
    selected.value = room
    open.value = true
    form.clearErrors()
}

function confirm() {
    form.transform((data) => ({
        ...data,
        room_id: selected.value.id,
    })).post('/client/reservation/create', {
        onSuccess: () => {
            open.value = false
            form.reset()
        }
    })
}
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Available Rooms</h1>
                <p class="text-slate-500 mt-2">Select your preferred room and book instantly.</p>
            </div>
            <div class="hidden md:block">
                <Badge variant="outline" class="px-4 py-1.5 border-slate-200 text-slate-600 bg-white">
                    {{ rooms.length }} Rooms Found
                </Badge>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="room in rooms" :key="room.id"
                class="group bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 relative overflow-hidden">

                <div class="flex items-start justify-between mb-4">
                    <div class="space-y-1">
                        <p class="text-3xl font-black text-slate-900 leading-none">#{{ room.number }}</p>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">{{ room.floor.name }}</p>
                    </div>
                    <Badge class="bg-emerald-50 text-emerald-700 border-emerald-200 px-3 py-1">
                        Available
                    </Badge>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-xl text-slate-600">
                        <Users class="w-4 h-4 text-slate-400" />
                        <span class="text-sm font-semibold">{{ room.capacity }} Guests</span>
                    </div>

                    <!-- ✔ FIX CENT -->
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-xl text-slate-600">
                        <span class="text-sm font-bold text-slate-900">
                            ${{ (room.price / 100).toFixed(2) }}
                        </span>
                        <span class="text-[10px] text-slate-400 uppercase">/ Night</span>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <div class="flex items-center gap-2 text-slate-400">
                        <Building2 class="w-4 h-4" />
                        <span class="text-xs font-medium">{{ room.floor.name }}</span>
                    </div>
                    <Button size="sm" @click="openBooking(room)"
                        class="rounded-xl px-5 bg-slate-900 hover:bg-black transition-colors">
                        Book Room
                    </Button>
                </div>
            </div>
        </div>

        <Dialog v-model:open="open">
            <DialogContent class="sm:max-w-md rounded-[2rem]">
                <DialogHeader>
                    <DialogTitle class="text-xl font-bold">Complete Your Booking</DialogTitle>
                    <DialogDescription>Details for room #{{ selected?.number }}</DialogDescription>
                </DialogHeader>

                <div v-if="selected" class="space-y-6 py-4">


                    <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100 flex justify-between items-center">
                        <div class="space-y-1">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">
                                Room Type
                            </span>
                            <p class="text-sm font-bold text-slate-800">
                                {{ selected.capacity }} Person
                            </p>
                        </div>

                      
                        <div class="space-y-1 text-right">
                            <span class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">
                                Price
                            </span>
                            <p class="text-sm font-bold text-emerald-600">
                                ${{ (selected.price / 100).toFixed(2) }}
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3 text-right">
                        <Label class="text-slate-700 font-semibold">Accompany Number</Label>

                        <Input type="number" min="0" :max="selected.capacity" v-model="form.accompany_number"
                            class="rounded-xl h-12" />


                        <p v-if="form.errors.accompany_number"
                            class="text-sm text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded-xl flex items-center gap-2 justify-end">
                            <XCircle class="w-4 h-4 shrink-0" />
                            <span>{{ form.errors.accompany_number }}</span>
                        </p>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <Button variant="outline" @click="open = false" class="flex-1 rounded-xl h-12">Cancel</Button>
                        <Button @click="confirm" :disabled="form.processing"
                            class="flex-1 rounded-xl h-12 bg-slate-900">
                            {{ form.processing ? 'Redirecting...' : 'Confirm & Pay' }}
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>


        <Dialog v-model:open="showSuccess">
            <DialogContent
                class="sm:max-w-[400px] p-0 overflow-hidden border-none shadow-[0_30px_70px_rgba(0,0,0,0.3)] rounded-[2.5rem]">
                <div class="relative p-10 text-center bg-white overflow-hidden">
                    <div class="absolute -top-12 -right-12 h-32 w-32 bg-emerald-100 rounded-full blur-3xl opacity-40">
                    </div>
                    <div class="absolute -bottom-12 -left-12 h-32 w-32 bg-blue-100 rounded-full blur-3xl opacity-40">
                    </div>

                    <div
                        class="relative mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-emerald-50 mb-8 border border-emerald-100">
                        <div class="absolute inset-0 rounded-full bg-emerald-200 animate-ping opacity-20"></div>
                        <CheckCircle2 class="h-12 w-12 text-emerald-500 relative z-10" />
                    </div>

                    <DialogHeader class="mb-8">
                        <DialogTitle class="text-3xl font-black text-center text-slate-900">Payment Success!
                        </DialogTitle>
                        <DialogDescription class="text-center text-slate-500 text-sm leading-relaxed pt-2">
                            Your reservation is confirmed. We've sent details to your email.
                        </DialogDescription>
                    </DialogHeader>

                    <div v-if="successData"
                        class="bg-slate-50 rounded-[1.5rem] p-5 mb-8 flex justify-between items-center border border-slate-100 relative z-10">
                        <div class="text-left">
                            <p class="text-[9px] text-slate-400 uppercase font-black tracking-[0.1em] mb-1">Booking ID
                            </p>
                            <p class="text-sm font-bold text-slate-800">#RES-{{ successData.order_id }}</p>
                        </div>
                        <div class="h-10 w-px bg-slate-200 mx-2"></div>
                        <div class="text-right">
                            <p class="text-[9px] text-slate-400 uppercase font-black tracking-[0.1em] mb-1">Amount Paid
                            </p>
                            <p class="text-sm font-bold text-emerald-600">${{ successData.amount }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 relative z-10">
                        <Link href="">
                            <Button
                                class="w-full h-14 bg-slate-900 hover:bg-black text-white font-bold rounded-2xl shadow-xl shadow-slate-200 group transition-all">
                                My Reservations
                                <ArrowRight class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" />
                            </Button>
                        </Link>
                        <Button variant="ghost" @click="showSuccess = false"
                            class="text-slate-400 hover:text-slate-600 font-semibold h-10">
                            Close
                        </Button>
                    </div>
                </div>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="showCancel">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Payment Cancelled ❌</DialogTitle>
                    <DialogDescription>
                        Your payment was not completed.
                    </DialogDescription>
                </DialogHeader>
            </DialogContent>
        </Dialog>
    </div>
</template>
