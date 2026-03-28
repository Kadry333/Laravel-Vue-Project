<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Button } from '@/components/ui/button'
import { Link, router } from '@inertiajs/vue3'
import { ArrowLeft, Pencil, Trash2 } from 'lucide-vue-next'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

import AlertDialog from '@/components/ui/alert-dialog/AlertDialog.vue'
import AlertDialogAction from '@/components/ui/alert-dialog/AlertDialogAction.vue'
import AlertDialogCancel from '@/components/ui/alert-dialog/AlertDialogCancel.vue'
import AlertDialogContent from '@/components/ui/alert-dialog/AlertDialogContent.vue'
import AlertDialogDescription from '@/components/ui/alert-dialog/AlertDialogDescription.vue'
import AlertDialogFooter from '@/components/ui/alert-dialog/AlertDialogFooter.vue'
import AlertDialogHeader from '@/components/ui/alert-dialog/AlertDialogHeader.vue'
import AlertDialogTitle from '@/components/ui/alert-dialog/AlertDialogTitle.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    floor: Object,
})

const openDelete = ref(false)

function destroy() {
    router.delete(`/admins/floors/${props.floor.id}`);
}
</script>

<template>
    <div class="w-full">

        <!-- HEADER -->
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Floor Details</h1>
                <p class="text-sm text-slate-500 mt-1">
                    View full information about this floor
                </p>
            </div>

            <div class="flex gap-2">
                <Link :href="`/admins/floors/${floor.id}/edit`">
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

        <!-- CONTENT -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-6">

            <!-- FLOOR INFO -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Floor Name -->
                <div>
                    <p class="text-sm text-slate-500">Floor Name</p>
                    <p class="font-medium text-slate-900">
                        {{ floor.name }}
                    </p>
                </div>

                <!-- Floor Number -->
                <div>
                    <p class="text-sm text-slate-500">Floor Number</p>
                    <p class="font-medium text-slate-900">
                        {{ floor.id }}
                    </p>
                </div>

                <!-- Manager -->
                <div class="md:col-span-2">
                    <p class="text-sm text-slate-500">Manager</p>
                    <p class="font-medium text-slate-900">
                        {{ floor.manager?.name ?? 'No Manager Assigned' }}
                    </p>
                </div>

            </div>
        </div>

        <!-- BACK BUTTON -->
        <div class="pt-6 flex">
            <Link href="/admins/floors">
                <Button variant="outline">
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    Back
                </Button>
            </Link>
        </div>

        <!-- DELETE MODAL -->
        <AlertDialog v-model:open="openDelete">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>Delete Floor</AlertDialogTitle>
                    <AlertDialogDescription>
                        Are you sure you want to delete this floor? This action cannot be undone.
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