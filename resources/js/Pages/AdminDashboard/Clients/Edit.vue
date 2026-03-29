<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { ArrowLeft } from "lucide-vue-next";

defineOptions({ layout: AdminLayout });

const props = defineProps({
    client: Object,
    countries: Array,
});

const form = useForm({
    name: props.client.name ?? "",
    email: props.client.email ?? "",
    mobile: props.client.mobile ?? "",
    country_id: props.client.country_id ?? "",
    gender: props.client.gender ?? "",
    avatar_image: null,
});

const previewUrl = ref(null);
const currentAvatar =
    props.client.avatar_image
        ? "/storage/avatars/" + props.client.avatar_image
        : '/images/default.png';

function handleFile(e) {
    const file = e.target.files[0];
    form.avatar_image = file;
    previewUrl.value = file ? URL.createObjectURL(file) : null;
}

function submit() {
    form.post(route("admins.clients.update", props.client.id), {
        forceFormData: true,
    });
}
</script>

<template>
    <div>
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <button
                @click="router.get(route('admins.clients.index'))"
                class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors text-gray-500"
            >
                <ArrowLeft class="w-4 h-4" />
            </button>
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">
                    Edit Client
                </h1>
                <p class="text-sm text-gray-400 mt-0.5">
                    Fill in the details to edit the client.
                </p>
            </div>
        </div>

        <!-- Form card -->
        <div class="bg-white rounded-2xl border border-gray-200 p-8">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Avatar -->
                <div>
                    <p class="text-sm font-medium text-gray-700 mb-3">Image</p>
                    <div class="w-20 h-20 rounded-full bg-gray-100 border border-gray-200 overflow-hidden flex items-center justify-center flex-shrink-0">
    <img
        :src="previewUrl || currentAvatar"
        @error="($event.target.src = '/images/default.png')"
        class="w-full h-full object-cover"
    />
</div>
                </div>

                <div class="border-t border-gray-100"></div>

                <!-- Name -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 mb-1.5"
                        >Name</label
                    >
                    <input
                        v-model="form.name"
                        type="text"
                        :class="[
                            'w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 bg-white',
                            form.errors.name
                                ? 'border-red-400'
                                : 'border-gray-200',
                        ]"
                    />
                    <p
                        v-if="form.errors.name"
                        class="text-xs text-red-500 mt-1"
                    >
                        {{ form.errors.name }}
                    </p>
                </div>

                <!-- Email -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 mb-1.5"
                        >Email</label
                    >
                    <input
                        v-model="form.email"
                        type="email"
                        :class="[
                            'w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 bg-white',
                            form.errors.email
                                ? 'border-red-400'
                                : 'border-gray-200',
                        ]"
                    />
                    <p
                        v-if="form.errors.email"
                        class="text-xs text-red-500 mt-1"
                    >
                        {{ form.errors.email }}
                    </p>
                </div>

                <!-- Mobile -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 mb-1.5"
                        >Mobile</label
                    >
                    <input
                        v-model="form.mobile"
                        type="text"
                        :class="[
                            'w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 bg-white',
                            form.errors.mobile
                                ? 'border-red-400'
                                : 'border-gray-200',
                        ]"
                    />
                    <p
                        v-if="form.errors.mobile"
                        class="text-xs text-red-500 mt-1"
                    >
                        {{ form.errors.mobile }}
                    </p>
                </div>

                <!-- Country -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 mb-1.5"
                        >Country</label
                    >
                    <select
                        v-model="form.country_id"
                        :class="[
                            'w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 bg-white',
                            form.errors.country_id
                                ? 'border-red-400'
                                : 'border-gray-200',
                        ]"
                    >
                        <option value="">Select a country</option>
                        <option
                            v-for="c in countries"
                            :key="c.id"
                            :value="c.id"
                        >
                            {{ c.name }}
                        </option>
                    </select>
                    <p
                        v-if="form.errors.country_id"
                        class="text-xs text-red-500 mt-1"
                    >
                        {{ form.errors.country_id }}
                    </p>
                </div>

                <!-- Gender -->
                <div>
                    <label
                        class="block text-sm font-medium text-gray-700 mb-1.5"
                        >Gender</label
                    >
                    <select
                        v-model="form.gender"
                        :class="[
                            'w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-gray-400 bg-white',
                            form.errors.gender
                                ? 'border-red-400'
                                : 'border-gray-200',
                        ]"
                    >
                        <option value="">Select gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                    <p
                        v-if="form.errors.gender"
                        class="text-xs text-red-500 mt-1"
                    >
                        {{ form.errors.gender }}
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3 pt-2">
                    <button
                        type="button"
                        @click="router.get(route('admins.clients.index'))"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-gray-900 hover:bg-gray-700 disabled:opacity-60 rounded-lg transition-colors"
                    >
                        {{ form.processing ? "Saving..." : "Update Client" }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
