<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { watch } from 'vue'
import Sidebar from './components/Sidebar/Sidebar.vue'
import Header from './components/Header/Header.vue'
import Toast from '@/Components/shared/Toast.vue'
import { useToast } from '@/composables/useToast'
import type { PageProps } from '@/types/inertia'

const page = usePage<PageProps>()
const toast = useToast()

// Watch for flash messages and trigger toasts
watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.success) {
      toast.success('Success', flash.success)
    }
    if (flash?.error) {
      toast.error('Error', flash.error)
    }
  },
  { immediate: true, deep: true }
)
</script>

<template>
  <div class="min-h-screen bg-gray-50 flex">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0">
      <!-- Header -->
      <Header />

      <!-- Page Content -->
      <main class="flex-1 p-6">
        <slot />
      </main>
    </div>

    <!-- Toast Notifications -->
    <Toast />
  </div>
</template>
