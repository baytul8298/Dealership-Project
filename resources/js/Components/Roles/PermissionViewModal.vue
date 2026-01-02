<script setup lang="ts">
import type { Module } from '@/types/models/Module'
import type { Permission, Role } from '@/types/models/User'
import { X } from 'lucide-vue-next'
import { computed } from 'vue'

interface Props {
  open: boolean
  role: Role | null
}

const props = defineProps<Props>()
const emit = defineEmits<{
  close: []
}>()

// Group permissions by module
const permissionsByModule = computed(() => {
  if (!props.role?.permissions) return []

  const grouped = new Map<string, { module: Module | null; permissions: Permission[] }>()

  props.role.permissions.forEach(permission => {
    const moduleName = permission.module?.module_name || 'General'

    if (!grouped.has(moduleName)) {
      grouped.set(moduleName, {
        module: permission.module || null,
        permissions: []
      })
    }

    grouped.get(moduleName)!.permissions.push(permission)
  })

  return Array.from(grouped.entries()).map(([name, data]) => ({
    name,
    module: data.module,
    permissions: data.permissions
  }))
})

function handleClose() {
  emit('close')
}
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="open"
        class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
        @click.self="handleClose"
      >
        <Transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div
            v-if="open"
            class="bg-white rounded-lg shadow-xl w-full max-w-3xl max-h-[80vh] overflow-y-auto"
            @click.stop
          >
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white z-10 card-header border-b">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 rounded flex items-center justify-center" style="background-color: rgba(255, 136, 0, 0.1);">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                  </div>
                  <div>
                    <h2 class="text-lg font-semibold text-gray-900">Role Permissions</h2>
                    <p class="text-sm text-gray-500">{{ role?.display_name || role?.name }}</p>
                  </div>
                </div>
                <button
                  type="button"
                  @click="handleClose"
                  class="text-gray-400 hover:text-gray-600 transition-base"
                >
                  <X class="w-5 h-5" />
                </button>
              </div>
            </div>

            <!-- Modal Body -->
            <div class="card-body">
              <!-- No Permissions -->
              <div v-if="!role?.permissions || role.permissions.length === 0" class="text-center py-8">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                  <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <p class="text-gray-500 text-sm">No permissions assigned to this role</p>
              </div>

              <!-- Permissions by Module -->
              <div v-else class="space-y-6">
                <div
                  v-for="group in permissionsByModule"
                  :key="group.name"
                  class="border border-gray-200 rounded-lg p-4"
                >
                  <!-- Module Header -->
                  <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100">
                    <div>
                      <h3 class="text-sm font-semibold text-gray-900">{{ group.name }}</h3>
                      <p class="text-xs text-gray-500 mt-0.5">
                        {{ group.permissions.length }} permission{{ group.permissions.length !== 1 ? 's' : '' }}
                      </p>
                    </div>
                  </div>

                  <!-- Permission Badges -->
                  <div class="flex flex-wrap gap-2">
                    <GlobalBadge
                      v-for="permission in group.permissions"
                      :key="permission.id"
                      color="blue"
                      size="sm"
                    >
                      {{ permission.display_name || permission.name }}
                    </GlobalBadge>
                  </div>
                </div>
              </div>
            </div>

            <!-- Modal Footer -->
            <div class="card-footer flex items-center justify-end">
              <button
                @click="handleClose"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border-2 border-gray-300 rounded-lg hover:bg-gray-50 transition-base"
              >
                Close
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>
