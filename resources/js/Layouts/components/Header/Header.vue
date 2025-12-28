<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { Home, Bell, Maximize, Sun, Moon, Globe } from 'lucide-vue-next'
import UserMenu from './UserMenu.vue'

const isDark = ref(false)
const currentLang = ref('EN')

const toggleTheme = () => {
  isDark.value = !isDark.value
  // Implement theme toggle logic
}

const toggleFullscreen = () => {
  if (!document.fullscreenElement) {
    document.documentElement.requestFullscreen()
  } else {
    document.exitFullscreen()
  }
}
</script>

<template>
  <header class="h-16 bg-white border-b border-gray-200 px-6 flex items-center justify-between sticky top-0 z-40">
    <!-- Left side - can add breadcrumbs or search here -->
    <div class="flex-1">
      <!-- Reserved for breadcrumbs or search -->
    </div>

    <!-- Right side - Actions and User Menu -->
    <div class="flex items-center gap-2">
      <!-- Home Icon -->
      <Link
        href="/dashboard"
        class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
        title="Home"
      >
        <Home class="w-5 h-5 text-gray-600" />
      </Link>

      <!-- Notifications -->
      <button
        class="relative w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
        title="Notifications"
      >
        <Bell class="w-5 h-5 text-gray-600" />
        <span class="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full"></span>
      </button>

      <!-- Fullscreen -->
      <button
        @click="toggleFullscreen"
        class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
        title="Fullscreen"
      >
        <Maximize class="w-5 h-5 text-gray-600" />
      </button>

      <!-- Theme Toggle -->
      <button
        @click="toggleTheme"
        class="w-10 h-10 flex items-center justify-center rounded-lg hover:bg-gray-100 transition-colors"
        title="Toggle Theme"
      >
        <Sun v-if="!isDark" class="w-5 h-5 text-gray-600" />
        <Moon v-else class="w-5 h-5 text-gray-600" />
      </button>

      <!-- Language Selector -->
      <button
        class="flex items-center gap-1 px-3 h-10 rounded-lg hover:bg-gray-100 transition-colors"
        title="Language"
      >
        <Globe class="w-4 h-4 text-gray-600" />
        <span class="text-sm font-medium text-gray-700">{{ currentLang }}</span>
      </button>

      <!-- Divider -->
      <div class="w-px h-8 bg-gray-200 mx-2"></div>

      <!-- User Menu -->
      <UserMenu />
    </div>
  </header>
</template>
