<script setup>
import { XCircle, ArrowLeft, RefreshCcw } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Link, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

const countdown = ref(5)

onMounted(() => {
    const interval = setInterval(() => {
        countdown.value--
        if (countdown.value === 0) {
            clearInterval(interval)
            router.visit('/client/rooms')
        }
    }, 1000)
})
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 px-4">
        <div class="relative bg-white rounded-[2.5rem] shadow-[0_30px_70px_rgba(0,0,0,0.1)] p-10 text-center max-w-md w-full overflow-hidden">

            <div class="absolute -top-12 -right-12 h-32 w-32 bg-red-100 rounded-full blur-3xl opacity-40"></div>
            <div class="absolute -bottom-12 -left-12 h-32 w-32 bg-orange-100 rounded-full blur-3xl opacity-40"></div>

            <div class="relative mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-50 mb-8 border border-red-100">
                <XCircle class="h-12 w-12 text-red-400 relative z-10" />
            </div>

            <h1 class="text-3xl font-black text-slate-900 mb-2">Payment Cancelled</h1>
            <p class="text-slate-500 text-sm leading-relaxed mb-8">
                No charges were made. Redirecting in
                <span class="font-bold text-slate-700">{{ countdown }}s</span>...
            </p>

            <div class="flex flex-col gap-3">
                <Link href="/client/rooms">
                    <Button class="w-full h-14 bg-slate-900 hover:bg-black text-white font-bold rounded-2xl group transition-all">
                        <RefreshCcw class="w-4 h-4 mr-2" />
                       Go to Dashboard
                    </Button>
                </Link>

            </div>
        </div>
    </div>
</template>
