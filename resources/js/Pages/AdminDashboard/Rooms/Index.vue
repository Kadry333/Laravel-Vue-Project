<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Badge } from '@/components/ui/badge'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
} from '@/components/ui/dialog'
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select'
import { Pencil, Trash2, Plus, Building2, Users } from 'lucide-vue-next'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    rooms:  Array,
    floors: Array,
})

const openForm   = ref(false)
const openDelete = ref(false)
const editMode   = ref(false)
const deleteId   = ref(null)

const form = useForm({
    number:   '',
    capacity: '',
    price:    '',
    floor_id: '',
})

function openCreate() {
    editMode.value = false
    form.reset()
    form.clearErrors()
    openForm.value = true
}

function openEdit(room) {
    editMode.value    = true
    form.number       = room.number
    form.capacity     = room.capacity
    form.price        = room.price / 100
    form.floor_id     = String(room.floor_id)
    form.clearErrors()
    openForm.value    = true
    deleteId.value    = room.id
}

function submit() {
    if (editMode.value) {
        form.transform(data => ({
            ...data,
            price: data.price * 100,
        })).put(`/admin/rooms/${deleteId.value}`, {
            onSuccess: () => { openForm.value = false; form.reset() }
        })
    } else {
        form.transform(data => ({
            ...data,
            price: data.price * 100,
        })).post('/admin/rooms', {
            onSuccess: () => { openForm.value = false; form.reset() }
        })
    }
}

function confirmDelete(id) {
    deleteId.value   = id
    openDelete.value = true
}

function destroy() {
    router.delete(`/admin/rooms/${deleteId.value}`, {
        onSuccess: () => { openDelete.value = false }
    })
}
</script>

<template>

</template>
