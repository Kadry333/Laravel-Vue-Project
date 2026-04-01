<script setup>
import AdminLayout from "@/Layouts/AdminLayout.vue";
import { Button } from "@/components/ui/button";
import { Link, router, useForm } from "@inertiajs/vue3";
import { Pencil, Trash2, ArrowLeft } from "lucide-vue-next";
import { ref } from "vue";
import { toast } from "vue-sonner";

import AlertDialog from "@/components/ui/alert-dialog/AlertDialog.vue";
import AlertDialogAction from "@/components/ui/alert-dialog/AlertDialogAction.vue";
import AlertDialogCancel from "@/components/ui/alert-dialog/AlertDialogCancel.vue";
import AlertDialogContent from "@/components/ui/alert-dialog/AlertDialogContent.vue";
import AlertDialogDescription from "@/components/ui/alert-dialog/AlertDialogDescription.vue";
import AlertDialogFooter from "@/components/ui/alert-dialog/AlertDialogFooter.vue";
import AlertDialogHeader from "@/components/ui/alert-dialog/AlertDialogHeader.vue";
import AlertDialogTitle from "@/components/ui/alert-dialog/AlertDialogTitle.vue";

defineOptions({ layout: AdminLayout });

const props = defineProps({
    manager: Object,
});

const form = useForm({});

const openDelete = ref(false);

function destroy() {
    form.transform(() => ({ _method: "DELETE" })).post(
        `/admins/managers/${props.manager.id}`,
        {
            onSuccess: () => {
                toast.success("Manager deleted successfully");
                router.visit("/admins/managers");
            },
        },
    );
}
</script>

<template>
    <div class="w-full">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">
                    Manager Details
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    View full information about this manager
                </p>
            </div>

            <div class="flex gap-2">
                <Link :href="`/admins/managers/${manager.id}/edit`">
                    <Button variant="outline">
                        <Pencil class="w-4 h-4 mr-2" />
                        Edit
                    </Button>
                </Link>

                <Button
                    variant="outline"
                    class="text-red-500"
                    @click="openDelete = true"
                >
                    <Trash2 class="w-4 h-4 mr-2" />
                    Delete
                </Button>
            </div>
        </div>

        <div
            class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6"
        >
            <div class="flex items-center gap-6">
                <img
                    :src="
                        manager.avatar_image && manager.avatar_image !== 'default.png'
                            ? `/storage/${manager.avatar_image}`
                            : '/images/default.png'
                    "
                    class="w-28 h-28 rounded-full object-cover border"
                />

                <div>
                    <h2 class="text-xl font-semibold text-slate-900">
                        {{ manager.name }}
                    </h2>
                    <p class="text-sm text-slate-500">Manager Account</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                <div>
                    <p class="text-sm text-slate-500">Name</p>
                    <p class="font-medium text-slate-900">{{ manager.name }}</p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">Email</p>
                    <p class="font-medium text-slate-900">
                        {{ manager.email }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">National ID</p>
                    <p class="font-medium text-slate-900">
                        {{ manager.national_id }}
                    </p>
                </div>

                <div>
                    <p class="text-sm text-slate-500">ID</p>
                    <p class="font-medium text-slate-900">{{ manager.id }}</p>
                </div>
            </div>
        </div>
        <div class="pt-6 flex">
            <Link href="/admins/managers">
                <Button variant="outline">
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Back
                </Button>
            </Link>
        </div>

        <AlertDialog v-model:open="openDelete">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete Manager</AlertDialogTitle>
                    <AlertDialogDescription>
                        Are you sure you want to delete this manager? This
                        action cannot be undone.
                    </AlertDialogDescription>
                </AlertDialogHeader>

                <AlertDialogFooter>
                    <AlertDialogCancel>Cancel</AlertDialogCancel>
                    <AlertDialogAction class="bg-red-500" @click="destroy">
                        Delete
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </div>
</template>
