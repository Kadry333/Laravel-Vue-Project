<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue'
import { ref, computed, onMounted } from 'vue'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import {
    Dialog, DialogContent, DialogHeader,
    DialogTitle, DialogDescription
} from '@/components/ui/dialog'
import { Users, Building2, CheckCircle2, ArrowRight, XCircle } from 'lucide-vue-next'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useForm, usePage, Link } from '@inertiajs/vue3'

defineOptions({ layout: ClientLayout })

const props = defineProps({ rooms: Array })

const page = usePage()
const open = ref(false)
const selected = ref(null)
const showSuccess = ref(false)
const showCancel = ref(false)
const successData = ref(null)

const today = new Date().toISOString().split('T')[0]

const form = useForm({
    room_id: null,
    check_in_date: '',
    check_out_date: '',
    accompany_number: 0,
})


const bookedRanges = computed(() => selected.value?.blocked_ranges ?? [])


const minCheckout = computed(() => {
    if (!form.check_in_date) return today
    const d = new Date(form.check_in_date)
    d.setDate(d.getDate() + 1)
    return d.toISOString().split('T')[0]
})

const nights = computed(() => {
    if (!form.check_in_date || !form.check_out_date) return 0
    const diff = new Date(form.check_out_date) - new Date(form.check_in_date)
    return Math.round(diff / (1000 * 60 * 60 * 24))
})

const totalPrice = computed(() => {
    if (!selected.value || !nights.value) return '0.00'
    return ((selected.value.price / 100) * nights.value).toFixed(2)
})

function openBooking(room) {
    selected.value = room
    open.value = true
    form.reset()
    form.clearErrors()
}

function confirm() {
    form.transform(data => ({
        ...data,
        room_id: selected.value.id,
    })).post('/client/reservation/create', {
        onSuccess: () => {
            open.value = false
            form.reset()
        },
        onError: () => {
            console.log(form.errors)
        },

    })
}

onMounted(() => {
    if (page.props.flash?.payment_success) {
        successData.value = page.props.flash.payment_success
        showSuccess.value = true
    }
    if (page.props.flash?.payment_cancelled) {
        showCancel.value = true
    }
})
</script>

<template>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Available Rooms</h1>
                <p class="text-slate-500 mt-2">Select your preferred room and book instantly.</p>
            </div>
            <Badge variant="outline" class="hidden md:block px-4 py-1.5 border-slate-200 text-slate-600 bg-white">
                {{ rooms.length }} Rooms Found
            </Badge>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="room in rooms" :key="room.id"
                class="group bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300">

                <div class="flex items-start justify-between mb-4">
                    <div class="space-y-1">
                        <p class="text-3xl font-black text-slate-900 leading-none">#{{ room.number }}</p>
                        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">{{ room.floor.name }}</p>
                    </div>
                    <Badge class="bg-emerald-50 text-emerald-700 border-emerald-200 px-3 py-1">Available</Badge>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-xl">
                        <Users class="w-4 h-4 text-slate-400" />
                        <span class="text-sm font-semibold text-slate-600">{{ room.capacity }} Guests</span>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-xl">
                        <span class="text-sm font-bold text-slate-900">${{ (room.price / 100).toFixed(2) }}</span>
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

        <!-- Booking Dialog -->
        <Dialog v-model:open="open">
            <DialogContent class="sm:max-w-md rounded-[2rem]">
                <DialogHeader>
                    <DialogTitle class="text-xl font-bold">Complete Your Booking</DialogTitle>
                    <DialogDescription>Room #{{ selected?.number }}</DialogDescription>
                </DialogHeader>

                <div v-if="selected" class="space-y-5 py-2">

                    <!-- Room Info -->
                    <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100 flex justify-between">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Capacity</p>
                            <p class="text-sm font-bold text-slate-800">{{ selected.capacity }} Person</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] uppercase font-bold text-slate-400 tracking-widest">Per Night</p>
                            <p class="text-sm font-bold text-emerald-600">${{ (selected.price / 100).toFixed(2) }}</p>
                        </div>
                    </div>
                    <!-- Booked Dates -->
                    <div v-if="bookedRanges.length" class="bg-red-50 border border-red-100 rounded-xl p-3 space-y-1">
                        <p class="text-[10px] uppercase font-bold text-red-400 tracking-widest mb-2">Unavailable Dates
                        </p>
                        <div v-for="range in bookedRanges" :key="range.start"
                            class="flex items-center justify-between text-xs text-red-500 font-medium bg-white rounded-lg px-3 py-1.5 border border-red-100">
                            <span>{{ range.start }}</span>
                            <span class="text-red-300 mx-2">→</span>
                            <span>{{ range.end }}</span>
                        </div>
                    </div>
                    <!-- Dates -->
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <Label class="text-slate-700 font-semibold text-sm">Check-in</Label>
                                <Input type="date" v-model="form.check_in_date" :min="today" class="rounded-xl h-11" />
                                <p v-if="form.errors.check_in_date" class="text-xs text-red-500">{{
                                    form.errors.check_in_date }}</p>
                            </div>
                            <div class="space-y-1.5">
                                <Label class="text-slate-700 font-semibold text-sm">Check-out</Label>
                                <Input type="date" v-model="form.check_out_date" :min="minCheckout"
                                    :disabled="!form.check_in_date" class="rounded-xl h-11" />
                                <p v-if="form.errors.check_out_date" class="text-xs text-red-500">{{
                                    form.errors.check_out_date }}</p>
                            </div>
                        </div>

                        <!-- room_id error = overlap error -->
                        <p v-if="form.errors.room_id"
                            class="text-xs text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded-xl flex items-center gap-1.5">
                            <XCircle class="w-3.5 h-3.5 shrink-0" /> {{ form.errors.room_id }}
                        </p>
                    </div>

                    <!-- Total -->
                    <div v-if="nights > 0"
                        class="bg-emerald-50 border border-emerald-100 rounded-xl px-4 py-3 flex justify-between items-center">
                        <span class="text-sm text-emerald-700 font-semibold">{{ nights }} Night{{ nights > 1 ? 's' : ''
                            }}</span>
                        <span class="text-sm font-black text-emerald-700">Total: ${{ totalPrice }}</span>
                    </div>

                    <!-- Accompany -->
                    <div class="space-y-1.5">
                        <Label class="text-slate-700 font-semibold text-sm">Accompany Number</Label>
                        <Input type="number" min="0" :max="selected.capacity" v-model="form.accompany_number"
                            class="rounded-xl h-11" />
                        <p v-if="form.errors.accompany_number"
                            class="text-xs text-red-600 bg-red-50 border border-red-200 px-3 py-2 rounded-xl flex items-center gap-1.5">
                            <XCircle class="w-3.5 h-3.5 shrink-0" /> {{ form.errors.accompany_number }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-3 pt-1">
                        <Button variant="outline" @click="open = false" class="flex-1 rounded-xl h-12">Cancel</Button>
                        <Button @click="confirm"
                            :disabled="form.processing || !form.check_in_date || !form.check_out_date"
                            class="flex-1 rounded-xl h-12 bg-slate-900 disabled:opacity-40">
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
