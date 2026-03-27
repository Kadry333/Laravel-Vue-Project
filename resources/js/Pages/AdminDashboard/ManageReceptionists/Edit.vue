<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { Badge } from '@/components/ui/badge'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ReceptionistForm from './Components/ReceptionistForm.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
  receptionist: {
    type: Object,
    required: true,
  },
})

const form = useForm({
  name: props.receptionist.name ?? '',
  email: props.receptionist.email ?? '',
  national_id: props.receptionist.national_id ?? '',
  avatar_image: null,
})

const submit = () => {
  form
    .transform((data) => ({
      ...data,
      _method: 'patch',
    }))
    .post(route('admins.receptionists.update', props.receptionist.id), {
      forceFormData: true,
      preserveScroll: true,
    })
}
</script>

<template>
  <Head :title="`Edit ${receptionist.name}`" />

  <div class="space-y-6">
    <section class="rounded-[28px] border border-[#d9e2ec] bg-white p-6 shadow-sm sm:p-8">
      <div class="space-y-3">
        <Badge class="border-sky-200 bg-sky-50 text-sky-700">Receptionists</Badge>
        <div>
          <h1 class="text-3xl font-semibold tracking-tight text-[#0c1a2e] sm:text-4xl">
            Edit Receptionist
          </h1>
          <p class="mt-2 max-w-2xl text-sm leading-6 text-[#5b708b]">
            Update the receptionist profile, replace the avatar when needed, and keep the account data consistent with the current staff records.
          </p>
        </div>
      </div>
    </section>

    <ReceptionistForm
      :form="form"
      :receptionist="receptionist"
      mode="edit"
      title="Receptionist Details"
      description="Adjust the saved profile information while preserving the existing admin dashboard experience."
      @submit="submit"
    />
  </div>
</template>
