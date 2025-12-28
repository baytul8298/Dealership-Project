<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import SidebarMenuItem from './SidebarMenuItem.vue'
import { navigationConfig, systemNavigationConfig } from '@/config/navigation'
import type { PageProps } from '@/types/inertia'

const page = usePage<PageProps>()

const isActive = (href?: string) => {
  if (!href) return false
  return window.location.pathname === href
}
</script>

<template>
  <aside class="w-64 bg-white border-r border-gray-200 h-screen overflow-y-auto flex flex-col">
    <!-- Logo -->
    <div class="h-16 flex items-center px-6 border-b border-gray-200">
      <Link href="/" class="flex items-center gap-2">
        <div class="w-8 h-8 bg-gradient-to-br from-orange-400 to-orange-600 rounded-lg flex items-center justify-center">
          <span class="text-white font-bold text-lg">D</span>
        </div>
        <span class="text-xl font-bold text-gray-800">Dealership</span>
      </Link>
    </div>

    <!-- Main Navigation -->
    <nav class="flex-1 py-4">
      <SidebarMenuItem
        v-for="item in navigationConfig"
        :key="item.name"
        :item="item"
        :active="isActive(item.href)"
      />

      <!-- System Section -->
      <div class="mt-8">
        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
          System
        </div>
        <SidebarMenuItem
          v-for="item in systemNavigationConfig"
          :key="item.name"
          :item="item"
          :active="isActive(item.href)"
        />
      </div>
    </nav>
  </aside>
</template>
