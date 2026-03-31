<script setup>
import { CheckCircle2, ArrowRight } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Link, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

const props = defineProps({
    order_id: String,
    amount: String,
})

const countdown = ref(5)

onMounted(() => {
    const interval = setInterval(() => {
        countdown.value--
        if (countdown.value === 0) {
            clearInterval(interval)
            router.visit('/client/reservation')
        }
    }, 1000)
})
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
        <div
            class="relative bg-white rounded-[2.5rem] shadow-[0_30px_70px_rgba(0,0,0,0.1)] p-10 text-center max-w-md w-full overflow-hidden">

            <div class="absolute -top-12 -right-12 h-32 w-32 bg-emerald-100 rounded-full blur-3xl opacity-40"></div>
            <div class="absolute -bottom-12 -left-12 h-32 w-32 bg-blue-100 rounded-full blur-3xl opacity-40"></div>

            <div
                class="relative mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-emerald-50 mb-8 border border-emerald-100">
                <div class="absolute inset-0 rounded-full bg-emerald-200 animate-ping opacity-20"></div>
                <CheckCircle2 class="h-12 w-12 text-emerald-500 relative z-10" />
            </div>

            <h1 class="text-3xl font-black text-slate-900 mb-2">Payment Success!</h1>
            <p class="text-slate-500 text-sm leading-relaxed mb-8">
                Your reservation is confirmed. Redirecting in
                <span class="font-bold text-slate-700">{{ countdown }}s</span>...
            </p>

            <div
                class="bg-slate-50 rounded-[1.5rem] p-5 mb-8 flex justify-between items-center border border-slate-100">
                <div class="text-left">
                    <p class="text-[9px] text-slate-400 uppercase font-black tracking-[0.1em] mb-1">Booking ID</p>
                    <p class="text-sm font-bold text-slate-800">#RES-{{ order_id }}</p>
                </div>
                <div class="h-10 w-px bg-slate-200 mx-2"></div>
                <div class="text-right">
                    <p class="text-[9px] text-slate-400 uppercase font-black tracking-[0.1em] mb-1">Amount Paid</p>
                    <p class="text-sm font-bold text-emerald-600">${{ amount }}</p>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <Link href="/client/reservation">
                    <Button
                        class="w-full h-14 bg-slate-900 hover:bg-black text-white font-bold rounded-2xl group transition-all">
                        Go Now
                        <ArrowRight class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" />
                    </Button>
                </Link>
                <Link href="/client/rooms">
                    <Button variant="ghost" class="w-full text-slate-400 hover:text-slate-600 font-semibold h-10">
                        Back to Rooms
                    </Button>
                </Link>
            </div>
        </div>
    </div>
</template>
