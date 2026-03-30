<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Hotel, Lock, CheckCircle2 } from 'lucide-vue-next';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Set New Password | Grand Luxury" />

    <div class="min-h-screen flex flex-col items-center justify-center bg-slate-50 p-4 sm:p-6 lg:p-8 py-12">
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
                <CardTitle class="text-2xl font-bold tracking-tight text-slate-900">Set New Password</CardTitle>
                <CardDescription class="text-slate-500 text-sm">
                    Enter your new secure password below to regain access.
                </CardDescription>
            </CardHeader>

            <CardContent class="px-6 sm:px-10 pb-8 bg-white">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Email (Read-only as per context) -->
                    <div class="space-y-2 opacity-60">
                        <Label for="email" class="text-sm font-medium text-slate-700">Email Address</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            readonly
                            class="h-11 border-slate-200 bg-slate-50 cursor-not-allowed"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <!-- New Password -->
                    <div class="space-y-2">
                        <Label for="password" class="text-sm font-medium text-slate-700">New Password</Label>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            required
                            placeholder="••••••••"
                            autocomplete="new-password"
                            class="h-11 border-slate-200 focus:ring-indigo-600 focus:border-indigo-600"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <!-- Confirm New Password -->
                    <div class="space-y-2">
                        <Label for="password_confirmation" class="text-sm font-medium text-slate-700">Confirm New Password</Label>
                        <Input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            type="password"
                            required
                            placeholder="••••••••"
                            autocomplete="new-password"
                            class="h-11 border-slate-200 focus:ring-indigo-600 focus:border-indigo-600"
                        />
                    </div>

                    <Button 
                        class="w-full h-11 text-base font-semibold bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-[0.98]" 
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Updating Password...</span>
                        <span v-else class="flex items-center">
                             <Lock class="mr-2 h-4 w-4" /> Reset Password
                        </span>
                    </Button>
                </form>
            </CardContent>

            <CardFooter class="flex flex-col space-y-4 bg-slate-50/50 border-t border-slate-100 py-6 text-center">
                <p class="text-xs text-slate-500 italic flex items-center justify-center gap-1">
                    <CheckCircle2 class="h-3 w-3 text-indigo-500" /> All sessions will be updated.
                </p>
            </CardFooter>
        </Card>

        <p class="mt-8 text-center text-xs text-slate-400">
            &copy; {{ new Date().getFullYear() }} Grand Luxury Hotel System. All rights reserved.
        </p>
    </div>
</template>
