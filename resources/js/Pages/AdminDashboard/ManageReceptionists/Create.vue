<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { Badge } from '@/components/ui/badge'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ReceptionistForm from './Components/ReceptionistForm.vue'

defineOptions({ layout: AdminLayout })

const form = useForm({
  name: '',
  email: '',
  password: '',
  national_id: '',
  avatar_image: null,
})

const submit = () => {
  form.post(route('admins.receptionists.store'), {
    forceFormData: true,
    preserveScroll: true,
  })
}
</script>

<template>
  <Head title="Create Receptionist" />

  <div class="space-y-6">
    <section class="rounded-[28px] border border-[#d9e2ec] bg-white p-6 shadow-sm sm:p-8">
      <div class="space-y-3">
        <Badge class="border-sky-200 bg-sky-50 text-sky-700">Receptionists</Badge>
        <div>
          <h1 class="text-3xl font-semibold tracking-tight text-[#0c1a2e] sm:text-4xl">
            Create Receptionist
          </h1>
          <p class="mt-2 max-w-2xl text-sm leading-6 text-[#5b708b]">
            Add a new front-desk team member using the same dashboard shell, spacing, and card system already used across the admin area.
          </p>
        </div>
      </div>
    </section>

    <ReceptionistForm
      :form="form"
      mode="create"
      title="New Receptionist Account"
      description="Set up a receptionist account with the core identity details and an optional avatar image."
      @submit="submit"
    />
  </div>
</template>
