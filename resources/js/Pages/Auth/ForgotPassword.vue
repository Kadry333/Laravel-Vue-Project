<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Hotel, KeyRound, ArrowLeft } from 'lucide-vue-next';
import InputError from '@/Components/InputError.vue';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
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
            <CardHeader class="space-y-1 pb-6 pt-8 text-center px-6 sm:px-10">
                <CardTitle class="text-2xl font-bold tracking-tight text-slate-900">Forgot Password?</CardTitle>
                <CardDescription class="text-slate-500 text-sm">
                    No problem. Enter your email and we'll send you a password reset link to get you back on track.
                </CardDescription>
            </CardHeader>

            <CardContent class="px-6 sm:px-10 pb-8">
                <div v-if="status" class="mb-6 text-sm font-medium text-green-600 bg-green-50 p-4 rounded-lg border border-green-100 flex items-center shadow-sm">
                   <KeyRound class="mr-2 h-4 w-4" /> {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <Label for="email" class="text-sm font-medium text-slate-700">Email Address</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="name@example.com"
                            required
                            autofocus
                            class="h-11 border-slate-200 focus:ring-indigo-600 focus:border-indigo-600"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <Button 
                        class="w-full h-11 text-base font-semibold bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-100 transform transition-all active:scale-[0.98]" 
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Sending link...</span>
                        <span v-else>Send Reset Link</span>
                    </Button>
                </form>
            </CardContent>

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
