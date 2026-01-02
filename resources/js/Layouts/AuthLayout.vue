<script setup lang="ts">
import { usePage } from '@inertiajs/vue3'
import { watch } from 'vue'
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
  <div class="min-h-screen bg-gray-100 relative overflow-hidden flex items-center justify-center">
    <!-- Top Background Image -->
    <div class="absolute top-0 right-0 w-full h-auto pointer-events-none">
      <img src="/images/top-bg.svg" alt="" class="w-full h-auto opacity-80" />
    </div>

    <!-- Bottom Background Image -->
    <div class="absolute bottom-0 left-0 w-full h-auto pointer-events-none">
      <img src="/images/bottom-bg.svg" alt="" class="w-full h-auto opacity-80" />
    </div>

    <!-- Decorative Dots Pattern (recreating the dots from the design) -->
    <div class="absolute inset-0 pointer-events-none">
      <div class="absolute top-1/4 right-1/4 w-64 h-64">
        <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="dots" x="0" y="0" width="30" height="30" patternUnits="userSpaceOnUse">
              <circle cx="5" cy="5" r="2" fill="#CBD5E1" opacity="0.5" />
            </pattern>
          </defs>
          <rect width="100%" height="100%" fill="url(#dots)" />
        </svg>
      </div>
    </div>

    <!-- Content -->
    <div class="relative z-10 w-full max-w-md px-4">
      <!-- Login Card -->
      <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12">
        <slot />
      </div>
    </div>

    <!-- Toast Notifications -->
    <Toast />
  </div>
</template>
