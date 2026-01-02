<script setup lang="ts">
import { Button, FormInput, SearchableDropdown, TextArea } from '@/Components/shared'
import type { Role, User } from '@/types/models/User'
import { useForm } from '@inertiajs/vue3'
import { X } from 'lucide-vue-next'
import { computed, watch } from 'vue'

interface Props {
  open: boolean
  user?: User | null
  mode: 'create' | 'edit'
  roles: Role[]
  userTypeOptions: Array<{ value: string; label: string }>
  statusOptions: Array<{ value: string; label: string }>
  languageOptions: Array<{ value: string; label: string }>
}

const props = defineProps<Props>()
const emit = defineEmits<{
  close: []
  success: []
}>()

const form = useForm({
  name: '',
  username: '',
  email: '',
  password: '',
  user_type: '',
  contact_no: '',
  address: '',
  language: '',  // Changed from 'en' to empty string
  status: '',    // Changed from '1' to empty string
  role_id: '',
})

const roleOptions = computed(() => {
  return [
    { value: '', label: 'No Role' },
    ...props.roles.map((role) => ({
      value: role.id.toString(),
      label: role.display_name || role.name,
    })),
  ]
})

// User type options for form (same pattern as RoleModal - include empty option)
const formUserTypeOptions = computed(() => {
  return [
    { value: '', label: 'Select User Type' },
    ...props.userTypeOptions.filter((option) => option.value !== ''),
  ]
})

// Language options with empty default
const formLanguageOptions = computed(() => {
  return [
    { value: '', label: 'Select Language' },
    ...props.languageOptions.filter((option) => option.value !== ''),
  ]
})

// Status options for form (convert to proper format)
const formStatusOptions = computed(() => {
  return [
    { value: '', label: 'Select Status' },
    { value: '1', label: 'Active' },
    { value: '0', label: 'Inactive' },
  ]
})

// Watch for user changes to populate form
watch(
  () => props.user,
  (newUser) => {
    if (newUser && props.mode === 'edit') {
      form.name = newUser.name
      form.username = newUser.username
      form.email = newUser.email
      form.password = '' // Don't populate password for security
      form.user_type = newUser.user_type || ''
      form.contact_no = newUser.contact_no || ''
      form.address = newUser.address || ''
      form.language = newUser.language || ''
      form.status = newUser.status ? '1' : '0' // Convert boolean to string
      form.role_id = newUser.roles?.[0]?.id ? String(newUser.roles[0].id) : ''
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
      if (props.mode === 'edit' && props.user) {
        form.name = props.user.name
        form.username = props.user.username
        form.email = props.user.email
        form.password = ''
        form.user_type = props.user.user_type || ''
        form.contact_no = props.user.contact_no || ''
        form.address = props.user.address || ''
        form.language = props.user.language || ''
        form.status = props.user.status ? '1' : '0'
        form.role_id = props.user.roles?.[0]?.id ? String(props.user.roles[0].id) : ''
      } else {
        // Reset to empty values for create mode
        form.reset()
      }
    } else {
      // Reset form when closing
      form.reset()
      form.clearErrors()
    }
  }
)

const modalTitle = computed(() => {
  return props.mode === 'create' ? 'Create User' : 'Edit User'
})

const submitButtonText = computed(() => {
  return props.mode === 'create' ? 'Create' : 'Update'
})

function handleSubmit() {
  // Transform data before submission
  const submitData = {
    ...form.data(),
    status: form.status === '1', // Convert string to boolean
    role_id: form.role_id && form.role_id !== '' ? parseInt(form.role_id) : null, // Convert string to number
    language: form.language || 'en', // Default to 'en' if empty
  }

  if (props.mode === 'create') {
    form.transform(() => submitData).post('/users', {
      preserveScroll: true,
      onSuccess: () => {
        emit('success')
        emit('close')
      },
    })
  } else if (props.user) {
    form.transform(() => submitData).put(`/users/${props.user.id}`, {
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
            class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            @click.stop
          >
            <!-- Modal Header -->
            <div class="sticky top-0 bg-white z-10 card-header border-b">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <div
                    class="w-8 h-8 rounded flex items-center justify-center"
                    style="background-color: rgba(255, 136, 0, 0.1)"
                  >
                    <svg
                      class="w-4 h-4 text-primary"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                      />
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
              <!-- Basic Information Section -->
              <div>
                <h3 class="text-sm font-medium text-gray-700 mb-4 flex items-center gap-2">
                  <svg
                    class="w-4 h-4 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                  </svg>
                  Basic Information
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <!-- Name -->
                  <FormInput
                    v-model="form.name"
                    label="Full Name"
                    placeholder="e.g., John Doe"
                    :error="form.errors.name"
                    :disabled="form.processing"
                    required
                  />

                  <!-- Username -->
                  <FormInput
                    v-model="form.username"
                    label="Username"
                    placeholder="e.g., johndoe"
                    :error="form.errors.username"
                    :disabled="form.processing"
                    required
                  />

                  <!-- Email -->
                  <FormInput
                    v-model="form.email"
                    label="Email"
                    type="email"
                    placeholder="e.g., john@example.com"
                    :error="form.errors.email"
                    :disabled="form.processing"
                    required
                  />

                  <!-- Password -->
                  <FormInput
                    v-model="form.password"
                    label="Password"
                    type="password"
                    :placeholder="mode === 'edit' ? 'Leave blank to keep current' : 'Enter password'"
                    :error="form.errors.password"
                    :disabled="form.processing"
                    :required="mode === 'create'"
                  />

                  <!-- User Type -->
                  <SearchableDropdown
                    v-model="form.user_type"
                    label="User Type"
                    :options="formUserTypeOptions"
                    placeholder="Select user type..."
                    :error="form.errors.user_type"
                    :disabled="form.processing"
                  />

                  <!-- Role -->
                  <SearchableDropdown
                    v-model="form.role_id"
                    label="Role"
                    :options="roleOptions"
                    placeholder="Select role..."
                    :error="form.errors.role_id"
                    :disabled="form.processing"
                  />

                  <!-- Contact No -->
                  <FormInput
                    v-model="form.contact_no"
                    label="Contact Number"
                    placeholder="e.g., +1 234 567 8900"
                    :error="form.errors.contact_no"
                    :disabled="form.processing"
                  />

                  <!-- Language -->
                  <SearchableDropdown
                    v-model="form.language"
                    label="Language"
                    :options="formLanguageOptions"
                    placeholder="Select language..."
                    :error="form.errors.language"
                    :disabled="form.processing"
                  />

                  <!-- Status -->
                  <SearchableDropdown
                    v-model="form.status"
                    label="Status"
                    :options="formStatusOptions"
                    placeholder="Select status..."
                    :error="form.errors.status"
                    :disabled="form.processing"
                  />
                </div>

                <!-- Address (Full Width) -->
                <div class="mt-4">
                  <TextArea
                    v-model="form.address"
                    label="Address"
                    placeholder="Enter full address..."
                    :error="form.errors.address"
                    :disabled="form.processing"
                    :rows="2"
                  />
                </div>
              </div>

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