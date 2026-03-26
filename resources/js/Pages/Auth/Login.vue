<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Checkbox from '@/Components/Checkbox.vue';
import { Hotel, LogIn } from 'lucide-vue-next';
import InputError from '@/Components/InputError.vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Sign In | Grand Luxury" />

    <div class="min-h-screen flex flex-col items-center justify-center bg-slate-50 p-4 sm:p-6 lg:p-8">
        <!-- Brand Logo/Name -->
        <Link :href="'/'" class="mb-8 flex items-center gap-2 group">
            <div class="rounded-xl bg-indigo-600 p-2 text-white transition-transform group-hover:scale-110">
                <Hotel :size="28" />
            </div>
            <span class="text-2xl font-bold tracking-tight text-indigo-900">Grand Luxury</span>
        </Link>

        <Card class="w-full max-w-md border-none shadow-xl shadow-indigo-100/50 overflow-hidden">
            <div class="h-2 bg-indigo-600 w-full"></div>
            <CardHeader class="space-y-1 pb-6 pt-8 text-center">
                <CardTitle class="text-2xl font-bold tracking-tight text-slate-900">Welcome Back</CardTitle>
                <CardDescription class="text-slate-500">
                    Sign in to your account to continue
                </CardDescription>
            </CardHeader>

            <CardContent>
                <div v-if="status" class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-md border border-green-100">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <!-- Email -->
                    <div class="space-y-2">
                        <Label for="email" class="text-sm font-medium text-slate-700">Email Address</Label>
                        <Input
                            id="email"
                            v-model="form.email"
                            type="email"
                            placeholder="name@example.com"
                            autofocus
                            class="border-slate-200 focus:ring-indigo-600 focus:border-indigo-600"
                        />
                        <InputError :message="form.errors.email" />
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <Label for="password" class="text-sm font-medium text-slate-700">Password</Label>
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-xs font-medium text-indigo-600 hover:underline"
                            >
                                Forgot password?
                            </Link>
                        </div>
                        <Input
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="border-slate-200 focus:ring-indigo-600 focus:border-indigo-600"
                        />
                        <InputError :message="form.errors.password" />
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center space-x-2">
                        <Checkbox 
                            id="remember" 
                            v-model:checked="form.remember"
                            class="border-indigo-200 text-indigo-600 focus:ring-indigo-600"
                        />
                        <label
                            for="remember"
                            class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70 text-slate-600 cursor-pointer"
                        >
                            Remember me
                        </label>
                    </div>

                    <Button 
                        class="w-full h-11 text-base font-semibold bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all active:scale-[0.98]" 
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Signing in...</span>
                        <span v-else class="flex items-center">
                            <LogIn class="mr-2 h-4 w-4" /> Sign In
                        </span>
                    </Button>
                </form>
            </CardContent>

            <CardFooter class="flex flex-col space-y-4 bg-slate-50/50 border-t border-slate-100 py-6 text-center">
                <p class="text-sm text-slate-600">
                    Don't have an account? 
                    <Link :href="route('register')" class="font-semibold text-indigo-600 hover:text-indigo-700 ml-1">
                        Register as Client
                    </Link>
                </p>
            </CardFooter>
        </Card>

        <p class="mt-8 text-center text-xs text-slate-400">
            &copy; {{ new Date().getFullYear() }} Grand Luxury Hotel System. All rights reserved.
        </p>
    </div>
</template>
