<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { X } from 'lucide-vue-next'
import type { Permission } from '@/types/models/User'

interface Props {
  open: boolean
  permission?: Permission | null
  mode: 'create' | 'edit'
}

const props = defineProps<Props>()
const emit = defineEmits<{
  close: []
  success: []
}>()

const form = useForm({
  name: '',
  display_name: '',
  description: '',
})

// Watch for permission changes to populate form
watch(
  () => props.permission,
  (newPermission) => {
    if (newPermission && props.mode === 'edit') {
      form.name = newPermission.name
      form.display_name = newPermission.display_name || ''
      form.description = newPermission.description || ''
    } else {
      form.reset()
    }
  },
  { immediate: true }
)

// Watch for modal close to reset form
watch(
  () => props.open,
  (isOpen) => {
    if (!isOpen) {
      form.reset()
      form.clearErrors()
    }
  }
)

const modalTitle = computed(() => {
  return props.mode === 'create' ? 'Create Permission' : 'Edit Permission'
})

const submitButtonText = computed(() => {
  return props.mode === 'create' ? 'Create' : 'Update'
})

function handleSubmit() {
  if (props.mode === 'create') {
    form.post('/permissions', {
      preserveScroll: true,
      onSuccess: () => {
        emit('success')
        emit('close')
      },
    })
  } else if (props.permission) {
    form.put(`/permissions/${props.permission.id}`, {
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
            class="bg-white rounded-lg shadow-xl w-full max-w-md"
            @click.stop
          >
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-orange-100 rounded flex items-center justify-center">
                  <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                  </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">{{ modalTitle }}</h2>
              </div>
              <button
                type="button"
                @click="handleClose"
                :disabled="form.processing"
                class="text-gray-400 hover:text-gray-600 transition-colors"
              >
                <X class="w-5 h-5" />
              </button>
            </div>

            <!-- Modal Body -->
            <form @submit.prevent="handleSubmit" class="p-6 space-y-4">
              <!-- Permission Name -->
              <div>
                <label for="permission-name" class="block text-sm font-medium text-gray-700 mb-1">
                  Permission Name <span class="text-red-500">*</span>
                </label>
                <input
                  id="permission-name"
                  v-model="form.name"
                  type="text"
                  placeholder="e.g., users.create, products.view"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                  :class="{ 'border-red-500': form.errors.name }"
                  :disabled="form.processing"
                  required
                />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                  {{ form.errors.name }}
                </p>
                <p class="mt-1 text-xs text-gray-500">
                  Use lowercase letters, numbers, hyphens, and dots only
                </p>
              </div>

              <!-- Display Name -->
              <div>
                <label for="display-name" class="block text-sm font-medium text-gray-700 mb-1">
                  Display Name <span class="text-red-500">*</span>
                </label>
                <input
                  id="display-name"
                  v-model="form.display_name"
                  type="text"
                  placeholder="e.g., Create Users, View Products"
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                  :class="{ 'border-red-500': form.errors.display_name }"
                  :disabled="form.processing"
                  required
                />
                <p v-if="form.errors.display_name" class="mt-1 text-sm text-red-600">
                  {{ form.errors.display_name }}
                </p>
              </div>

              <!-- Description -->
              <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                  Description
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  placeholder="Brief description of what this permission allows..."
                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent resize-none"
                  :class="{ 'border-red-500': form.errors.description }"
                  :disabled="form.processing"
                />
                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                  {{ form.errors.description }}
                </p>
              </div>

              <!-- Modal Footer -->
              <div class="flex items-center justify-end gap-3 pt-4">
                <button
                  type="button"
                  @click="handleClose"
                  :disabled="form.processing"
                  class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  Cancel
                </button>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="px-4 py-2 text-sm font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                >
                  <span v-if="!form.processing">{{ submitButtonText }}</span>
                  <span v-else>Processing...</span>
                </button>
              </div>
            </form>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>
