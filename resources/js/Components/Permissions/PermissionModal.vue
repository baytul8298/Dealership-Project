<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { X } from 'lucide-vue-next'
import type { Permission } from '@/types/models/User'
import type { Module } from '@/types/models/Module'
import { FormInput, TextArea, Button, SearchableDropdown } from '@/Components/shared'

interface Props {
  open: boolean
  permission?: Permission | null
  mode: 'create' | 'edit'
  modules: Module[]
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
  module_id: '',
})

// Watch for permission changes to populate form
watch(
  () => props.permission,
  (newPermission) => {
    if (newPermission && props.mode === 'edit') {
      form.name = newPermission.name
      form.display_name = newPermission.display_name || ''
      form.description = newPermission.description || ''
      form.module_id = newPermission.module_id
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

const moduleOptions = computed(() => {
  return props.modules.map(module => ({
    value: module.id,
    label: module.module_name
  }))
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
            <div class="card-header">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div class="w-8 h-8 rounded flex items-center justify-center" style="background-color: rgba(255, 136, 0, 0.1);">
                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
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
            <form @submit.prevent="handleSubmit" class="card-body space-y-4">
              <!-- Permission Name -->
              <div>
                <FormInput
                  v-model="form.name"
                  label="Permission Name"
                  placeholder="e.g., users.create, products.view"
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
                placeholder="e.g., Create Users, View Products"
                :error="form.errors.display_name"
                :disabled="form.processing"
                required
              />

              <!-- Module Selection -->
              <SearchableDropdown
                v-model="form.module_id"
                label="Module"
                :options="moduleOptions"
                placeholder="Select module..."
                :error="form.errors.module_id"
                :disabled="form.processing"
                required
              />

              <!-- Description -->
              <TextArea
                v-model="form.description"
                label="Description"
                placeholder="Brief description of what this permission allows..."
                :error="form.errors.description"
                :disabled="form.processing"
                :rows="3"
              />

              <!-- Modal Footer -->
              <div class="flex items-center justify-end gap-3 pt-4">
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
