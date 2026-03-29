<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Hotel, UserPlus, Image as ImageIcon } from 'lucide-vue-next';
import InputError from '@/Components/InputError.vue';
import { computed } from 'vue';

const page = usePage();
const countries = computed(() => page.props.countries || []);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    national_id: '',
    avatar_image: null,
    country: '',
    gender: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
        forceFormData: true,
    });
};
</script>

<template>
    <Head title="Join Grand Luxury | Register" />

    <div class="min-h-screen flex flex-col items-center justify-center bg-slate-50 p-4 sm:p-6 lg:p-8 py-12">
        <!-- Brand Logo -->
        <Link :href="'/'" class="mb-8 flex items-center gap-2 group text-decoration-none">
            <div class="rounded-xl bg-indigo-600 p-2 text-white transition-transform group-hover:scale-110">
                <Hotel :size="28" />
            </div>
            <span class="text-2xl font-bold tracking-tight text-indigo-900">Grand Luxury</span>
        </Link>

        <Card class="w-full max-w-2xl border-none shadow-xl shadow-indigo-100/50 overflow-hidden">
            <div class="h-2 bg-indigo-600 w-full"></div>
            <CardHeader class="space-y-1 pb-6 pt-8 text-center bg-white">
                <CardTitle class="text-2xl font-bold tracking-tight text-slate-900">Create an account</CardTitle>
                <CardDescription class="text-slate-500">
                    Step into premium hospitality. Register your account below.
                </CardDescription>
            </CardHeader>

            <CardContent class="bg-white px-6 sm:px-10">
                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="space-y-2">
                            <Label for="name" class="text-sm font-semibold text-slate-700">Full Name</Label>
                            <Input id="name" v-model="form.name" type="text" placeholder="John Doe"  class="h-10" />
                            <InputError :message="form.errors.name" />
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <Label for="email" class="text-sm font-semibold text-slate-700">Email Address</Label>
                            <Input id="email" v-model="form.email" type="email" placeholder="name@example.com"  class="h-10" />
                            <InputError :message="form.errors.email" />
                        </div>

                        <!-- National ID -->
                        <div class="space-y-2">
                            <Label for="national_id" class="text-sm font-semibold text-slate-700">National ID</Label>
                            <Input id="national_id" v-model="form.national_id" type="text" placeholder="ID Number"  class="h-10" />
                            <InputError :message="form.errors.national_id" />
                        </div>

                        <!-- Avatar -->
                        <div class="space-y-2">
                            <Label for="avatar" class="text-sm font-semibold text-slate-700">Profile Image (JPG/JPEG)</Label>
                            <div class="relative flex items-center">
                                <Input 
                                    id="avatar" 
                                    type="file" 
                                    @input="form.avatar_image = $event.target.files[0]" 
                                    accept=".jpg,.jpeg"
                                    class="cursor-pointer file:mr-2 file:bg-indigo-50 file:text-indigo-700 file:border-0 file:rounded-md h-10 pr-10"
                                />
                                <ImageIcon class="absolute right-3 top-2.5 h-5 w-5 text-slate-400 pointer-events-none" />
                            </div>
                            <InputError :message="form.errors.avatar_image" />
                        </div>

                        <!-- Country -->
                        <div class="space-y-2">
                            <Label class="text-sm font-semibold text-slate-700">Country</Label>
                            <Select v-model="form.country">
                                <SelectTrigger class="w-full h-10">
                                    <SelectValue placeholder="Select a country" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="c in countries" :key="c" :value="c">
                                        {{ c }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.country" />
                        </div>

                        <!-- Gender -->
                        <div class="space-y-2">
                            <Label class="text-sm font-semibold text-slate-700">Gender</Label>
                            <Select v-model="form.gender">
                                <SelectTrigger class="w-full h-10">
                                    <SelectValue placeholder="Select gender" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="male">Male</SelectItem>
                                    <SelectItem value="female">Female</SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.gender" />
                        </div>

                        <!-- Password -->
                        <div class="space-y-2">
                            <Label for="password" class="text-sm font-semibold text-slate-700">Password</Label>
                            <Input id="password" v-model="form.password" type="password"  class="h-10" />
                        </div>

                        <!-- Confirm Password -->
                        <div class="space-y-2">
                            <Label for="password_confirmation" class="text-sm font-semibold text-slate-700">Confirm Password</Label>
                            <Input id="password_confirmation" v-model="form.password_confirmation" type="password"  class="h-10" />
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                         <InputError :message="form.errors.password" />
                         <InputError :message="form.errors.password_confirmation" />
                    </div>

                    <Button 
                        class="w-full h-12 text-lg font-bold bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-100 transition-all hover:scale-[1.01] active:scale-[0.99]" 
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Processing...</span>
                        <span v-else class="flex items-center">
                            <UserPlus class="mr-3 h-5 w-5" /> Join the Luxury Experience
                        </span>
                    </Button>
                </form>
            </CardContent>

            <CardFooter class="flex flex-col space-y-4 bg-slate-50/50 border-t border-slate-100 py-6 text-center">
                <p class="text-sm text-slate-600">
                    Already have an account? 
                    <Link :href="route('login')" class="font-bold text-indigo-600 hover:text-indigo-700 ml-1 underline transition-colors">
                        Sign in here
                    </Link>
                </p>
            </CardFooter>
        </Card>
        
        <p class="mt-8 text-center text-xs text-slate-400">
            &copy; {{ new Date().getFullYear() }} Grand Luxury Hotel System. All rights reserved.
        </p>
    </div>
</template>
