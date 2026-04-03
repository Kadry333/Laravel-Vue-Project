<script setup>
import { Link } from '@inertiajs/vue3'
import { SidebarTrigger } from '@/components/ui/sidebar'
import { Separator } from '@/components/ui/separator'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import { Avatar, AvatarFallback } from '@/components/ui/avatar'
import { Settings, LogOut } from 'lucide-vue-next'
</script>

<template>
  <header class="sticky top-0 z-40 h-[62px] bg-white border-b border-slate-200
                 flex items-center px-6 gap-3.5 shadow-[0_1px_4px_rgba(0,0,0,0.06)]">

    <SidebarTrigger class="text-slate-400 hover:text-slate-700 -ml-1" />
    <Separator orientation="vertical" class="h-5 mr-1" />

    <div class="flex-1" />

    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <button class="flex items-center gap-2.5 outline-none">
          <Avatar class="w-9 h-9 border-2 border-slate-200 hover:border-sky-400 transition-colors">
            <AvatarFallback
              class="text-[13px] font-bold text-white"
              style="background: linear-gradient(135deg,#8b5cf6,#ec4899)"
            >
              AM
            </AvatarFallback>
          </Avatar>
          <div class="hidden md:block text-left">
            <p class="text-[13px] font-semibold text-slate-800 leading-tight">Ahmed Mostafa</p>
            <p class="text-[11px] text-slate-400">Client</p>
          </div>
        </button>
      </DropdownMenuTrigger>

      <DropdownMenuContent align="end" class="w-52">
        <DropdownMenuLabel class="font-normal">
          <p class="text-[13px] font-semibold">Ahmed Mostafa</p>
          <p class="text-[11.5px] text-muted-foreground">ahmed@email.com</p>
        </DropdownMenuLabel>
        <DropdownMenuSeparator />
        <DropdownMenuItem as-child>
          <Link :href="route('client.profile.edit')" class="flex items-center gap-2 cursor-pointer">
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
