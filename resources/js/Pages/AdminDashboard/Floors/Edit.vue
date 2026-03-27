<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useForm, Link } from '@inertiajs/vue3'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { toast } from 'vue-sonner'
import { CheckIcon, ChevronsUpDownIcon } from 'lucide-vue-next'
import { computed, ref } from 'vue'
import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command'
import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { usePage } from '@inertiajs/vue3'


defineOptions({ layout: AdminLayout })

const props = defineProps({
    managers: Array[Object],
    floor: Object
})

const page = usePage()
const user = computed(() => page.props.auth.user)
const isAdmin = computed(() =>
  user.value?.roles?.some(role => role.name === 'admin')
)


const form = useForm({
    name: props.floor?.name,
    manager_id: props.floor?.manager_id,
  })

function submit() {
  form.post(`/admins/floors/${props.floor?.id}`, {
    _method: 'PUT',
  })
}


const managers = props.managers.map(manager => ({
  label: manager.name,
  value: manager.id
}));

const open = ref(false);

function selectManager(value) {
  form.manager_id = value
  open.value = false
}

const selectedManager = computed(() =>
  managers.find(m => m.value === form.manager_id)
)



</script>

<template>
  {{ JSON.stringify(user) }}
    <div class="w-full">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Edit Floor</h1>
                <p class="text-sm text-slate-500 mt-1">Fill in the details to edit the floor.</p>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-5 w-full">

            <div class="space-y-2">
                <Label>Name</Label>
                <Input v-model="form.name" placeholder="e.g. El-Mahalla Floor" required type="text" :disabled="form.processing" />
                <p v-if="form.errors.name" class="text-xs text-red-500">{{ form.errors.name }}</p>
            </div>


            <div class="space-y-2" v-if="isAdmin">
                <Label>Manager</Label>
                <Popover v-model:open="open">
                  <PopoverTrigger as-child>
                    <Button
                      variant="outline"
                      role="combobox"
                      :aria-expanded="open"
                      class="w-[200px] justify-between"
                    >
                      {{ selectedManager?.label || "Select Manager..." }}
                      <ChevronsUpDownIcon class="opacity-50" />
                    </Button>
                  </PopoverTrigger>
                  <PopoverContent class="w-[200px] p-0">
                    <Command>
                      <CommandInput class="h-9" placeholder="Search Manager..." />
                      <CommandList>
                        <CommandEmpty>No Manager found.</CommandEmpty>
                        <CommandGroup>
                          <CommandItem
                            v-for="manager in managers"
                            :key="manager.value"
                            :value="manager.value"
                            @select="(ev) => {
                              selectManager(ev.detail.value)
                            }"
                          >
                            {{ manager.label }}
                            <CheckIcon
                              :class="cn(
                                'ml-auto',
                                form.manager_id === manager.value ? 'opacity-100' : 'opacity-0',
                              )"
                            />
                          </CommandItem>
                        </CommandGroup>
                      </CommandList>
                    </Command>
                  </PopoverContent>
                </Popover>

                <p v-if="form.errors.manager_id" class="text-xs text-red-500">{{ form.errors.manager_id }}</p>
            </div>

            <div class="flex gap-2 justify-end pt-2">
                <Link href="/admins/floors">
                    <Button variant="outline">Cancel</Button>
                </Link>
                <Button @click="submit" :disabled="form.processing">
                    {{ form.processing ? 'Saving...' : 'Update Floor' }}
                </Button>
            </div>

        </div>
    </div>
</template>
