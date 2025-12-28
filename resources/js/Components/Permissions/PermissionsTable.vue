<script setup lang="ts">
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import { MoreVertical, Edit, Trash2 } from 'lucide-vue-next'
import type { Permission } from '@/types/models/User'
import { format } from 'date-fns'

interface Props {
  permissions: {
    data: Permission[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

const props = defineProps<Props>()
const emit = defineEmits<{
  edit: [permission: Permission]
  delete: [permission: Permission]
}>()

const selectedPermissions = ref<number[]>([])
const openDropdownId = ref<number | null>(null)

const allSelected = computed({
  get: () => {
    return (
      props.permissions.data.length > 0 &&
      props.permissions.data.every((p) => selectedPermissions.value.includes(p.id))
    )
  },
  set: (value: boolean) => {
    if (value) {
      selectedPermissions.value = props.permissions.data.map((p) => p.id)
    } else {
      selectedPermissions.value = []
    }
  },
})

function toggleDropdown(permissionId: number, event: Event) {
  event.stopPropagation()
  openDropdownId.value = openDropdownId.value === permissionId ? null : permissionId
}

function closeDropdown() {
  openDropdownId.value = null
}

// Close dropdown when clicking outside
document.addEventListener('click', (e) => {
  const target = e.target as HTMLElement
  if (!target.closest('.dropdown-menu') && !target.closest('.dropdown-trigger')) {
    closeDropdown()
  }
})

function handleEdit(permission: Permission) {
  closeDropdown()
  emit('edit', permission)
}

function handleDelete(permission: Permission) {
  closeDropdown()
  emit('delete', permission)
}

function formatDate(dateString: string) {
  try {
    return format(new Date(dateString), 'yyyy-MM-dd HH:mm')
  } catch {
    return dateString
  }
}
</script>

<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full">
        <thead class="bg-gray-50 border-b border-gray-200">
          <tr>
            <th class="w-12 px-6 py-3 text-left">
              <input
                v-model="allSelected"
                type="checkbox"
                class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500"
              />
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Name
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Display Name
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Description
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Created at
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Updated at
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr
            v-for="permission in permissions.data"
            :key="permission.id"
            class="hover:bg-gray-50 transition-colors"
          >
            <!-- Checkbox -->
            <td class="px-6 py-4 whitespace-nowrap">
              <input
                v-model="selectedPermissions"
                :value="permission.id"
                type="checkbox"
                class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500"
              />
            </td>

            <!-- Name -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                  </div>
                  <div>
                    <div class="text-sm font-medium text-gray-900">
                      {{ permission.name }}
                    </div>
                    <div class="text-xs text-gray-500">
                      {{ permission.guard_name }}
                    </div>
                  </div>
                </div>
              </div>
            </td>

            <!-- Display Name -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ permission.display_name || '-' }}</div>
            </td>

            <!-- Description -->
            <td class="px-6 py-4">
              <div class="text-sm text-gray-500 max-w-xs truncate">
                {{ permission.description || '-' }}
              </div>
            </td>

            <!-- Created at -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-500">
                {{ formatDate(permission.created_at) }}
              </div>
            </td>

            <!-- Updated at -->
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-500">
                {{ formatDate(permission.updated_at) }}
              </div>
            </td>

            <!-- Actions -->
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="relative inline-block">
                <button
                  @click="(e) => toggleDropdown(permission.id, e)"
                  class="dropdown-trigger text-gray-400 hover:text-gray-600 transition-colors p-1"
                >
                  <MoreVertical class="w-5 h-5" />
                </button>

                <!-- Dropdown Menu -->
                <Transition
                  enter-active-class="transition ease-out duration-100"
                  enter-from-class="transform opacity-0 scale-95"
                  enter-to-class="transform opacity-100 scale-100"
                  leave-active-class="transition ease-in duration-75"
                  leave-from-class="transform opacity-100 scale-100"
                  leave-to-class="transform opacity-0 scale-95"
                >
                  <div
                    v-if="openDropdownId === permission.id"
                    class="dropdown-menu absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10"
                  >
                    <div class="py-1">
                      <button
                        @click.stop="handleEdit(permission)"
                        class="flex items-center gap-2 w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors"
                      >
                        <Edit class="w-4 h-4 text-blue-500" />
                        Edit Permission
                      </button>
                      <button
                        @click.stop="handleDelete(permission)"
                        class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
                      >
                        <Trash2 class="w-4 h-4" />
                        Delete Permission
                      </button>
                    </div>
                  </div>
                </Transition>
              </div>
            </td>
          </tr>

          <!-- Empty State -->
          <tr v-if="permissions.data.length === 0">
            <td colspan="7" class="px-6 py-12 text-center">
              <div class="flex flex-col items-center justify-center">
                <svg class="w-12 h-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="text-gray-500 text-sm font-medium">No permissions found</p>
                <p class="text-gray-400 text-xs mt-1">Create your first permission to get started</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div
      v-if="permissions.last_page > 1"
      class="flex items-center justify-between px-6 py-3 bg-gray-50 border-t border-gray-200"
    >
      <div class="flex items-center gap-2 text-sm text-gray-500">
        Showing
        <span class="font-medium text-gray-900">
          {{ (permissions.current_page - 1) * permissions.per_page + 1 }}
        </span>
        to
        <span class="font-medium text-gray-900">
          {{ Math.min(permissions.current_page * permissions.per_page, permissions.total) }}
        </span>
        of
        <span class="font-medium text-gray-900">{{ permissions.total }}</span>
        results
      </div>

      <div class="flex items-center gap-2">
        <button
          @click="router.visit(`/permissions?page=${permissions.current_page - 1}`)"
          :disabled="permissions.current_page === 1"
          class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          Previous
        </button>
        <button
          @click="router.visit(`/permissions?page=${permissions.current_page + 1}`)"
          :disabled="permissions.current_page === permissions.last_page"
          class="px-3 py-1 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>

