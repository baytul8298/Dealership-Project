<script setup lang="ts">
import UserModal from '@/Components/Users/UserModal.vue'
import UsersTable from '@/Components/Users/UsersTable.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import type { Role, User } from '@/types/models/User'
import { router } from '@inertiajs/vue3'
import { Plus } from 'lucide-vue-next'
import { ref } from 'vue'
import { Button } from '@/Components/shared'

interface Props {
  users: {
    data: User[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  roles: Role[]
  userTypeOptions: Array<{ value: string; label: string }>
  statusOptions: Array<{ value: string; label: string }>
  languageOptions: Array<{ value: string; label: string }>
  filters: {
    search: string | null
    per_page: number
    user_type: string | null
    status: string | null
    created_from: string | null
    created_to: string | null
  }
}

const props = defineProps<Props>()

const showModal = ref(false)
const modalMode = ref<'create' | 'edit'>('create')
const selectedUser = ref<User | null>(null)
const showDeleteConfirm = ref(false)
const userToDelete = ref<User | null>(null)

// Filter states
const userType = ref(props.filters.user_type || '')
const status = ref(props.filters.status || '')
const createdFromDate = ref<Date | null>(
  props.filters.created_from ? new Date(props.filters.created_from) : null
)
const createdToDate = ref<Date | null>(
  props.filters.created_to ? new Date(props.filters.created_to) : null
)

function openCreateModal() {
  modalMode.value = 'create'
  selectedUser.value = null
  showModal.value = true
}

function openEditModal(user: User) {
  modalMode.value = 'edit'
  selectedUser.value = user
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedUser.value = null
}

function handleDeleteClick(user: User) {
  userToDelete.value = user
  showDeleteConfirm.value = true
}

function confirmDelete() {
  if (userToDelete.value) {
    router.delete(`/users/${userToDelete.value.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteConfirm.value = false
        userToDelete.value = null
      },
    })
  }
}

function cancelDelete() {
  showDeleteConfirm.value = false
  userToDelete.value = null
}

function applyFilters() {
  router.get(
    '/users',
    {
      user_type: userType.value || undefined,
      status: status.value || undefined,
      created_from: createdFromDate.value
        ? createdFromDate.value.toISOString().split('T')[0]
        : undefined,
      created_to: createdToDate.value
        ? createdToDate.value.toISOString().split('T')[0]
        : undefined,
    },
    {
      preserveState: true,
      preserveScroll: true,
    }
  )
}

function resetFilters() {
  userType.value = ''
  status.value = ''
  createdFromDate.value = null
  createdToDate.value = null

  router.get('/users', {}, {
    preserveState: true,
    preserveScroll: true,
  })
}
</script>

<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Page Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Users</h1>
          <p class="text-sm text-gray-500 mt-1">
            Manage your application users and their roles
          </p>
        </div>
        <button
          @click="openCreateModal"
          class="inline-flex items-center gap-2 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-hover transition-base"
        >
          <Plus class="w-5 h-5" />
          Add New User
        </button>
      </div>

      <!-- Users Table -->
      <UsersTable
        :users="users"
        :user-type="userType"
        :user-type-options="userTypeOptions"
        :status="status"
        :status-options="statusOptions"
        :created-from-date="createdFromDate"
        :created-to-date="createdToDate"
        @edit="openEditModal"
        @delete="handleDeleteClick"
        @apply-filters="applyFilters"
        @reset-filters="resetFilters"
        @update:user-type="userType = $event"
        @update:status="status = $event"
        @update:created-from-date="createdFromDate = $event"
        @update:created-to-date="createdToDate = $event"
      />
    </div>

    <!-- User Modal -->
    <UserModal
      :open="showModal"
      :mode="modalMode"
      :user="selectedUser"
      :roles="roles"
      :user-type-options="userTypeOptions"
      :status-options="statusOptions"
      :language-options="languageOptions"
      @close="closeModal"
      @success="closeModal"
    />

    <!-- Delete Confirmation Modal -->
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
          v-if="showDeleteConfirm"
          class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
          @click.self="cancelDelete"
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
              v-if="showDeleteConfirm"
              class="bg-white rounded-lg shadow-xl w-full max-w-md p-6"
              @click.stop
            >
              <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete User</h3>
              <p class="text-sm text-gray-600 mb-6">
                Are you sure you want to delete the user
                <span class="font-semibold">{{ userToDelete?.name }}</span>?
                This action cannot be undone.
              </p>
              <div class="flex items-center justify-end gap-3">
                <Button variant="secondary" @click="cancelDelete">
                  Cancel
                </Button>
                <Button variant="danger" @click="confirmDelete">
                  Delete
                </Button>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </AppLayout>
</template>
