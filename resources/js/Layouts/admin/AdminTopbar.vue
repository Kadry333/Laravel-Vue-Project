<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { SidebarTrigger } from '@/components/ui/sidebar'
import { Separator }      from '@/components/ui/separator'
import { Input }          from '@/components/ui/input'
import { Button }         from '@/components/ui/button'
import {
  DropdownMenu, DropdownMenuContent, DropdownMenuItem,
  DropdownMenuLabel, DropdownMenuSeparator, DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Search, Bell, Settings, LogOut } from 'lucide-vue-next'

const page = usePage()
const DEFAULT_AVATAR_PATH = '/images/default.png'

const authUser = computed(() => page.props.auth?.user ?? null)

const profileHref = computed(() => page.props.auth?.profile_route ?? '/profile')

const avatarSrc = computed(() => {
  const path = authUser.value?.avatar_image

  if (!path || path === 'default.png') {
    return DEFAULT_AVATAR_PATH
  }

  if (
    path.startsWith('http://') ||
    path.startsWith('https://') ||
    path.startsWith('/')
  ) {
    return path
  }

  return `/storage/${path}`
})

const initials = computed(() => {
  const name = authUser.value?.name ?? 'John Doe'

  return name
    .split(' ')
    .map((part) => part[0] ?? '')
    .join('')
    .slice(0, 2)
    .toUpperCase()
})
</script>

<template>
  <header class="sticky top-0 z-40 h-[62px] bg-white border-b border-slate-200
                 flex items-center px-6 gap-3.5 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">

    <SidebarTrigger class="text-slate-400 hover:text-slate-700 -ml-1" />
    <Separator orientation="vertical" class="h-5 mr-1" />

    <div class="flex-1" />

    <div class="relative hidden sm:block">
      <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" />
      <Input
        placeholder="Search…"
        class="pl-9 w-52 h-9 bg-slate-50 border-slate-200 text-[13px]
               focus-visible:ring-sky-500/20 focus-visible:border-sky-400"
      />
    </div>

    <Button variant="outline" size="icon" class="h-9 w-9 relative border-slate-200">
      <Bell class="w-4 h-4 text-slate-500" />
      <span class="absolute top-1.5 right-1.5 w-[7px] h-[7px]
                   bg-red-500 rounded-full border-2 border-white" />
    </Button>

    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <button class="flex items-center gap-2.5 outline-none">
          <Avatar class="w-9 h-9 border-2 border-slate-200 hover:border-sky-400 transition-colors">
            <AvatarImage :src="avatarSrc" :alt="authUser?.name ?? 'User'" class="object-cover" />
            <AvatarFallback
              class="text-[13px] font-bold text-white"
              style="background: linear-gradient(135deg,#8b5cf6,#ec4899)"
            >
              {{ initials }}
            </AvatarFallback>
          </Avatar>
          <div class="hidden md:block text-left">
            <p class="text-[13px] font-semibold text-slate-800 leading-tight">{{ authUser?.name ?? 'John Doe' }}</p>
            <p class="text-[11px] text-slate-400">{{ page.props.auth?.role_label ?? 'Administrator' }}</p>
          </div>
        </button>
      </DropdownMenuTrigger>

      <DropdownMenuContent align="end" class="w-52">
        <DropdownMenuLabel class="font-normal">
          <p class="text-[13px] font-semibold">{{ authUser?.name ?? 'John Doe' }}</p>
          <p class="text-[11.5px] text-muted-foreground">{{ authUser?.email ?? 'john@luxestay.com' }}</p>
        </DropdownMenuLabel>
        <DropdownMenuSeparator />
        <DropdownMenuItem as-child>
          <Link :href="profileHref" class="flex items-center gap-2 cursor-pointer">
            <Settings class="w-4 h-4" /> Profile Settings
          </Link>
        </DropdownMenuItem>
        <DropdownMenuSeparator />
        <DropdownMenuItem as-child>
          <Link href="/logout" method="post" as="button"
                class="flex items-center gap-2 w-full text-red-500 focus:text-red-500 cursor-pointer">
            <LogOut class="w-4 h-4" /> Sign Out
          </Link>
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  </header>
</template>
