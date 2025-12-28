<script setup lang="ts">
import { useNotificationsStore } from '@/stores/notifications'
import { X, CheckCircle, XCircle, AlertTriangle, Info } from 'lucide-vue-next'

const notificationsStore = useNotificationsStore()

const iconMap = {
  success: CheckCircle,
  error: XCircle,
  warning: AlertTriangle,
  info: Info,
}

const colorMap = {
  success: 'bg-green-50 border-green-200 text-green-800',
  error: 'bg-red-50 border-red-200 text-red-800',
  warning: 'bg-yellow-50 border-yellow-200 text-yellow-800',
  info: 'bg-blue-50 border-blue-200 text-blue-800',
}

const iconColorMap = {
  success: 'text-green-600',
  error: 'text-red-600',
  warning: 'text-yellow-600',
  info: 'text-blue-600',
}
</script>

<template>
  <div class="fixed top-4 right-4 z-50 space-y-2 max-w-md">
    <TransitionGroup
      enter-active-class="transition ease-out duration-300"
      enter-from-class="translate-x-full opacity-0"
      enter-to-class="translate-x-0 opacity-100"
      leave-active-class="transition ease-in duration-200"
      leave-from-class="translate-x-0 opacity-100"
      leave-to-class="translate-x-full opacity-0"
    >
      <div
        v-for="notification in notificationsStore.notifications"
        :key="notification.id"
        :class="[
          'flex items-start gap-3 p-4 rounded-lg border shadow-lg',
          colorMap[notification.type],
        ]"
      >
        <component
          :is="iconMap[notification.type]"
          :class="['w-5 h-5 flex-shrink-0 mt-0.5', iconColorMap[notification.type]]"
        />
        <div class="flex-1 min-w-0">
          <p class="font-semibold text-sm">{{ notification.title }}</p>
          <p class="text-sm mt-0.5 opacity-90">{{ notification.message }}</p>
        </div>
        <button
          @click="notificationsStore.remove(notification.id)"
          class="flex-shrink-0 hover:opacity-70 transition-opacity"
        >
          <X class="w-4 h-4" />
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>
