<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Hotel, KeyRound, ArrowLeft, Lock, CheckCircle2 } from 'lucide-vue-next';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    status: {
        type: String,
    },
    otpSent: {
        type: Boolean,
        default: false,
    },
    email: {
        type: String,
        default: '',
    },
});

const emailForm = useForm({
    email: props.email || '',
});

const otpForm = useForm({
    email: props.email || '',
    otp: '',
});

const sendOtp = () => {
    emailForm.post(route('password.email'));
};

const goToReset = () => {
    // Use emailForm.email which is what the user typed
    const email = emailForm.email;
    const otp = otpForm.otp;
    console.log('Navigating with email:', email, 'OTP:', otp);
    const url = `/reset-password-otp/${otp}?email=${encodeURIComponent(email)}`;
    window.location.href = url;
};
</script>

<template>
    <Head title="Reset Password | Grand Luxury" />

    <div class="min-h-screen flex flex-col items-center justify-center bg-slate-50 p-4 sm:p-6 lg:p-8">
        <!-- Brand Logo -->
        <Link :href="'/'" class="mb-8 flex items-center gap-2 group">
            <div class="rounded-xl bg-indigo-600 p-2 text-white transition-transform group-hover:scale-110">
                <Hotel :size="28" />
            </div>
            <span class="text-2xl font-bold tracking-tight text-indigo-900">Grand Luxury</span>
        </Link>

        <Card class="w-full max-w-md border-none shadow-xl shadow-indigo-100/50 overflow-hidden">
            <div class="h-2 bg-indigo-600 w-full"></div>
            
            <!-- STEP 1: Enter Email -->
            <template v-if="!otpSent">
                <CardHeader class="space-y-1 pb-6 pt-8 text-center px-6 sm:px-10">
                    <CardTitle class="text-2xl font-bold tracking-tight text-slate-900">Forgot Password?</CardTitle>
                    <CardDescription class="text-slate-500 text-sm">
                        Enter your email and we'll send you a verification code to reset your password.
                    </CardDescription>
                </CardHeader>

                <CardContent class="px-6 sm:px-10 pb-8">
                    <form @submit.prevent="sendOtp" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="email" class="text-sm font-medium text-slate-700">Email Address</Label>
                            <Input
                                id="email"
                                v-model="emailForm.email"
                                type="email"
                                placeholder="name@example.com"
                                required
                                autofocus
                                class="h-11 border-slate-200 focus:ring-indigo-600 focus:border-indigo-600"
                            />
                            <InputError :message="emailForm.errors.email" />
                        </div>

                        <Button 
                            class="w-full h-11 text-base font-semibold bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-100 transform transition-all active:scale-[0.98]" 
                            :disabled="emailForm.processing"
                        >
                            <span v-if="emailForm.processing">Sending code...</span>
                            <span v-else class="flex items-center">
                                <KeyRound class="mr-2 h-4 w-4" /> Send OTP Code
                            </span>
                        </Button>
                    </form>
                </CardContent>
            </template>

            <!-- STEP 2: Enter OTP -->
            <template v-else>
                <CardHeader class="space-y-1 pb-6 pt-8 text-center px-6 sm:px-10">
                    <CardTitle class="text-2xl font-bold tracking-tight text-slate-900">Enter OTP Code</CardTitle>
                    <CardDescription class="text-slate-500 text-sm">
                        <span v-if="status" class="text-green-600 font-medium">{{ status }}</span>
                        <span v-else>Enter the 6-digit code sent to your email.</span>
                    </CardDescription>
                </CardHeader>

                <CardContent class="px-6 sm:px-10 pb-8">
                    <form @submit.prevent="goToReset" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="otp" class="text-sm font-medium text-slate-700">OTP Code</Label>
                            <Input
                                id="otp"
                                v-model="otpForm.otp"
                                type="text"
                                maxlength="6"
                                placeholder="123456"
                                required
                                autofocus
                                class="h-11 border-slate-200 focus:ring-indigo-600 focus:border-indigo-600 text-center text-lg tracking-[0.5em] font-mono"
                            />
                            <InputError :message="otpForm.errors.otp" />
                            <p class="text-xs text-slate-400 mt-1">
                                <strong>Test Mode:</strong> Use OTP <code class="bg-slate-100 px-1 rounded">123456</code>
                            </p>
                        </div>

                        <Button 
                            class="w-full h-11 text-base font-semibold bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-100 transform transition-all active:scale-[0.98]" 
                            :disabled="otpForm.processing || otpForm.otp.length !== 6"
                        >
                            <span v-if="otpForm.processing">Navigating...</span>
                            <span v-else class="flex items-center">
                                <CheckCircle2 class="mr-2 h-4 w-4" /> Continue to Reset
                            </span>
                        </Button>
                    </form>
                </CardContent>
            </template>

            <CardFooter class="flex justify-center bg-slate-50/50 border-t border-slate-100 py-6">
                <Link
                    :href="route('login')"
                    class="text-sm font-medium text-indigo-600 hover:text-indigo-700 flex items-center gap-2 group"
                >
                    <ArrowLeft class="h-4 w-4 transition-transform group-hover:-translate-x-1" /> Back to Sign In
                </Link>
            </CardFooter>
        </Card>

        <p class="mt-8 text-center text-xs text-slate-400">
            &copy; {{ new Date().getFullYear() }} Grand Luxury Hotel System. All rights reserved.
        </p>
    </div>
</template>
