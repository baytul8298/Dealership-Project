<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { X } from 'lucide-vue-next'
import type { Role, Permission } from '@/types/models/User'
import type { Module } from '@/types/models/Module'
import { FormInput, TextArea, Button, SearchableDropdown } from '@/Components/shared'

interface Props {
  open: boolean
  role?: Role | null
  mode: 'create' | 'edit'
  permissions: Permission[]
}

const props = defineProps<Props>()
const emit = defineEmits<{
  close: []
  success: []
}>()

const form = useForm({
  name: '',
  display_name: '',
  guard_name: 'web',
  role_type: '',
  description: '',
  permissions: [] as number[],
})

const guardOptions = [
  { value: 'web', label: 'Web' },
  { value: 'api', label: 'API' },
]

const roleTypeOptions = [
  { value: '', label: 'Select Role Type' },
  { value: 'Admin', label: 'Admin' },
  { value: 'Manager', label: 'Manager' },
  { value: 'User', label: 'User' },
]

// Group permissions by module
const permissionsByModule = computed(() => {
  const grouped = new Map<string, { module: Module | null; permissions: Permission[] }>()

  props.permissions.forEach(permission => {
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

// Check if all permissions are selected
const allSelected = computed(() => {
  return form.permissions.length === props.permissions.length
})

// Check if all permissions in a module are selected
function isModuleSelected(moduleName: string) {
  const modulePerms = permissionsByModule.value.find(m => m.name === moduleName)
  if (!modulePerms) return false

  return modulePerms.permissions.every(p => form.permissions.includes(p.id))
}

// Toggle all permissions
function toggleAll() {
  if (allSelected.value) {
    form.permissions = []
  } else {
    form.permissions = props.permissions.map(p => p.id)
  }
}

// Toggle all permissions in a module
function toggleModule(moduleName: string) {
  const modulePerms = permissionsByModule.value.find(m => m.name === moduleName)
  if (!modulePerms) return

  const modulePermIds = modulePerms.permissions.map(p => p.id)

  if (isModuleSelected(moduleName)) {
    // Remove all module permissions
    form.permissions = form.permissions.filter(id => !modulePermIds.includes(id))
  } else {
    // Add all module permissions
    const newPerms = new Set([...form.permissions, ...modulePermIds])
    form.permissions = Array.from(newPerms)
  }
}

// Toggle individual permission
function togglePermission(permissionId: number) {
  const index = form.permissions.indexOf(permissionId)
  if (index > -1) {
    form.permissions.splice(index, 1)
  } else {
    form.permissions.push(permissionId)
  }
}

// Watch for role changes to populate form
watch(
  () => props.role,
  (newRole) => {
    if (newRole && props.mode === 'edit') {
      form.name = newRole.name
      form.display_name = newRole.display_name || ''
      form.guard_name = newRole.guard_name || 'web'
      form.role_type = newRole.role_type || ''
      form.description = newRole.description || ''
      form.permissions = newRole.permissions?.map(p => p.id) || []
    } else {
      form.reset()
    }
  },
  { immediate: true }
)

// Watch for modal open/close
watch(
  () => props.open,
  (isOpen) => {
    if (isOpen) {
      // Repopulate form when opening in edit mode
      if (props.mode === 'edit' && props.role) {
        form.name = props.role.name
        form.display_name = props.role.display_name || ''
        form.guard_name = props.role.guard_name || 'web'
        form.role_type = props.role.role_type || ''
        form.description = props.role.description || ''
        form.permissions = props.role.permissions?.map(p => p.id) || []
      }
    } else {
      // Reset form when closing
      form.reset()
      form.clearErrors()
    }
  }
)

const modalTitle = computed(() => {
  return props.mode === 'create' ? 'Create Role' : 'Edit Role'
})

const submitButtonText = computed(() => {
  return props.mode === 'create' ? 'Create' : 'Update'
})

function handleSubmit() {
  if (props.mode === 'create') {
    form.post('/roles', {
      preserveScroll: true,
      onSuccess: () => {
        emit('success')
        emit('close')
      },
    })
  } else if (props.role) {
    form.put(`/roles/${props.role.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        emit('success')
        emit('close')
      },
    })
  }
}

function handleClose() {
  if (!form.processing) {
    emit('close')
  }
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
            class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto"
            @click.stop
          >
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white z-10 card-header border-b">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 rounded flex items-center justify-center" style="background-color: rgba(255, 136, 0, 0.1);">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                  </div>
                  <h2 class="text-lg font-semibold text-gray-900">{{ modalTitle }}</h2>
                </div>
                <button
                  type="button"
                  @click="handleClose"
                  :disabled="form.processing"
                  class="text-gray-400 hover:text-gray-600 transition-base"
                >
                  <X class="w-5 h-5" />
                </button>
              </div>
            </div>

            <!-- Modal Body -->
            <form @submit.prevent="handleSubmit" class="card-body space-y-6">
              <!-- Role Information Section -->
              <div>
                <h3 class="text-sm font-medium text-gray-700 mb-4 flex items-center gap-2">
                  <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  Role Information
                </h3>

                <div class="space-y-4">
                  <!-- Role Name -->
                  <div>
                    <FormInput
                      v-model="form.name"
                      label="Name (English)"
                      placeholder="e.g., admin, manager, cashier"
                      :error="form.errors.name"
                      :disabled="form.processing"
                      required
                    />
                    <p class="mt-1 text-xs text-gray-500">
                      Use lowercase letters, numbers, hyphens, and dots only
                    </p>
                  </div>

                  <!-- Display Name -->
                  <FormInput
                    v-model="form.display_name"
                    label="Display Name"
                    placeholder="e.g., Administrator, Manager"
                    :error="form.errors.display_name"
                    :disabled="form.processing"
                    required
                  />

                  <!-- Guard Name -->
                  <SearchableDropdown
                    v-model="form.guard_name"
                    label="Guard Name"
                    :options="guardOptions"
                    placeholder="Select guard..."
                    :error="form.errors.guard_name"
                    :disabled="form.processing"
                    required
                  />

                  <!-- Role Type -->
                  <SearchableDropdown
                    v-model="form.role_type"
                    label="Role Type"
                    :options="roleTypeOptions"
                    placeholder="Select role type..."
                    :error="form.errors.role_type"
                    :disabled="form.processing"
                  />

                  <!-- Description -->
                  <TextArea
                    v-model="form.description"
                    label="Description"
                    placeholder="Brief description of this role..."
                    :error="form.errors.description"
                    :disabled="form.processing"
                    :rows="2"
                  />
                </div>
              </div>

              <!-- Permissions Section -->
              <div>
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-sm font-medium text-gray-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Permissions
                  </h3>

                  <!-- Select All Toggle -->
                  <label class="inline-flex items-center cursor-pointer">
                    <input
                      type="checkbox"
                      class="sr-only peer"
                      :checked="allSelected"
                      @change="toggleAll"
                      :disabled="form.processing"
                    />
                    <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-orange-500"></div>
                  </label>
                </div>

                <!-- Permissions Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div
                    v-for="group in permissionsByModule"
                    :key="group.name"
                    class="border border-gray-200 rounded-lg p-4"
                  >
                    <!-- Module Header -->
                    <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100">
                      <h4 class="text-sm font-medium text-gray-900">{{ group.name }}</h4>
                      <label class="inline-flex items-center cursor-pointer">
                        <input
                          type="checkbox"
                          class="sr-only peer"
                          :checked="isModuleSelected(group.name)"
                          @change="toggleModule(group.name)"
                          :disabled="form.processing"
                        />
                        <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-orange-500"></div>
                      </label>
                    </div>

                    <!-- Permission Items -->
                    <div class="space-y-2">
                      <label
                        v-for="permission in group.permissions"
                        :key="permission.id"
                        class="flex items-center justify-between cursor-pointer group"
                      >
                        <span class="text-sm text-gray-700 group-hover:text-gray-900">
                          {{ permission.display_name || permission.name }}
                        </span>
                        <input
                          type="checkbox"
                          class="sr-only peer"
                          :checked="form.permissions.includes(permission.id)"
                          @change="togglePermission(permission.id)"
                          :disabled="form.processing"
                        />
                        <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-orange-500"></div>
                      </label>
                    </div>
                  </div>
                </div>

                <p v-if="form.errors.permissions" class="mt-2 text-sm text-red-600">
                  {{ form.errors.permissions }}
                </p>
              </div>

              <!-- Modal Footer -->
              <div class="flex items-center justify-end gap-3 pt-4 border-t">
                <Button
                  type="button"
                  variant="secondary"
                  @click="handleClose"
                  :disabled="form.processing"
                >
                  Cancel
                </Button>
                <Button
                  type="submit"
                  variant="primary"
                  :loading="form.processing"
                  :disabled="form.processing"
                >
                  {{ submitButtonText }}
                </Button>
              </div>
            </form>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>
