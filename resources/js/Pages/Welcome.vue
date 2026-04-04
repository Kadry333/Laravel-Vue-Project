<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Hotel, User, LogIn, UserPlus, Star, ShieldCheck, CreditCard } from 'lucide-vue-next';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Welcome | Luxury Grand Hotel" />

    <div class="min-h-screen bg-slate-50 font-sans text-slate-900">
        <!-- Navigation Header -->
        <nav class="sticky top-0 z-50 w-full border-b bg-white/80 backdrop-blur-md">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-2">
                    <div class="rounded-lg bg-indigo-600 p-2 text-white">
                        <Hotel :size="24" />
                    </div>
                    <span class="text-xl font-bold tracking-tight text-indigo-900">Grand Luxury</span>
                </div>

                <div v-if="canLogin" class="flex items-center gap-4">
                    <!-- Check auth user via inertia global props if available -->
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="text-sm font-medium hover:text-indigo-600 transition-colors"
                    >
                        Dashboard
                    </Link>

                    <template v-else>
                        <Link :href="route('login')">
                            <Button variant="ghost" class="text-indigo-600 hover:bg-slate-50">
                                <LogIn class="mr-2 h-4 w-4" /> Sign In
                            </Button>
                        </Link>

                        <Link v-if="canRegister" :href="route('register')">
                            <Button class="bg-indigo-600 hover:bg-indigo-700 shadow-lg shadow-indigo-200">
                                <UserPlus class="mr-2 h-4 w-4" /> Get Started
                            </Button>
                        </Link>
                    </template>
                </div>
            </div>
        </nav>

        <main>
            <!-- Hero Section -->
            <section class="relative h-[80vh] w-full overflow-hidden bg-indigo-950">

                <div class="relative mx-auto flex h-full max-w-7xl flex-col items-center justify-center px-4 pt-10 text-center text-white sm:px-6 lg:px-8">
                    <Badge variant="outline" class="mb-4 border-indigo-400 text-indigo-200 font-medium tracking-wide">
                        PREMIUM HOTEL SYSTEM
                    </Badge>
                    <h1 class="mb-6 text-5xl font-extrabold tracking-tight sm:text-6xl lg:text-7xl">
                        A World of <span class="text-indigo-400">Luxury</span> <br />
                        Awaits You
                    </h1>
                    <p class="mb-10 max-w-2xl text-lg text-indigo-100 sm:text-xl">
                        Experience elegance and comfort combined. Manage your stay at the Grand Luxury Hotel effortlessly with our modern reservation system.
                    </p>
                    <div class="flex flex-col gap-4 sm:flex-row">
                        <Link v-if="canRegister && !$page.props.auth?.user" :href="route('register')">
                            <Button size="lg" class="h-12 px-8 text-lg bg-indigo-600 hover:bg-indigo-700">
                                Book Now
                            </Button>
                        </Link>
                        <Link v-if="!$page.props.auth?.user" :href="route('login')">
                            <Button size="lg" variant="outline" class="h-12 px-8 text-lg border-white/30 bg-white/10 backdrop-blur-md hover:bg-white/20">
                                Learn More
                            </Button>
                        </Link>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="py-24">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-16 text-center">
                        <h2 class="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Everything you need for a perfect stay</h2>
                        <div class="mt-4 flex justify-center">
                            <div class="h-1 w-20 rounded bg-indigo-600"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                        <Card class="border-none bg-indigo-50/50 transition-all hover:translate-y-[-4px] hover:shadow-xl hover:shadow-indigo-100">
                            <CardHeader>
                                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
                                    <Star :size="24" />
                                </div>
                                <CardTitle class="text-xl">Luxurious Rooms</CardTitle>
                                <CardDescription class="text-slate-600">
                                    Our suites are designed to provide maximum comfort with premium silk linens and breathtaking views.
                                </CardDescription>
                            </CardHeader>
                        </Card>

                        <Card class="border-none bg-slate-50 transition-all hover:translate-y-[-4px] hover:shadow-xl hover:shadow-slate-200">
                            <CardHeader>
                                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-900 text-white shadow-lg shadow-indigo-200">
                                    <ShieldCheck :size="24" />
                                </div>
                                <CardTitle class="text-xl">Professional Staff</CardTitle>
                                <CardDescription class="text-slate-600">
                                    Our dedicated managers and receptionists are available 24/7 to ensure your experience is seamless.
                                </CardDescription>
                            </CardHeader>
                        </Card>

                        <Card class="border-none bg-indigo-50/50 transition-all hover:translate-y-[-4px] hover:shadow-xl hover:shadow-indigo-100">
                            <CardHeader>
                                <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-200">
                                    <CreditCard :size="24" />
                                </div>
                                <CardTitle class="text-xl">Secure Payments</CardTitle>
                                <CardDescription class="text-slate-600">
                                    Pay for your stays safely and securely via Stripe. Your privacy and financial safety are our top priorities.
                                </CardDescription>
                            </CardHeader>
                        </Card>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t bg-white py-12">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <div class="flex items-center justify-center gap-2 mb-4">
                    <Hotel class="text-indigo-600" :size="20" />
                    <span class="text-lg font-bold text-indigo-900">Grand Luxury</span>
                </div>
                <p class="text-sm text-slate-500">
                    &copy; {{ new Date().getFullYear() }} Grand Luxury Hotel System. All rights reserved. <br/>
                    Powered by Laravel {{ laravelVersion }} (PHP v{{ phpVersion }})
                </p>
            </div>
        </footer>
    </div>
</template>
