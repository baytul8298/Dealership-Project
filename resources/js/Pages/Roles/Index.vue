<script setup lang="ts">
import { ref, computed, watch, onMounted } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { Plus, Search, Filter, X, ChevronDown } from 'lucide-vue-next'
import AppLayout from '@/Layouts/AppLayout.vue'
import RolesTable from '@/Components/Roles/RolesTable.vue'
import RoleModal from '@/Components/Roles/RoleModal.vue'
import { Button, SearchableDropdown, DatePicker } from '@/Components/shared'
import type { Role, Permission } from '@/types/models/User'

interface Props {
  roles: {
    data: Role[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  filters: {
    search: string | null
    per_page: number
    role_type?: string | null
    created_from?: string | null
    created_to?: string | null
  }
}

const props = defineProps<Props>()

const showModal = ref(false)
const modalMode = ref<'create' | 'edit'>('create')
const selectedRole = ref<Role | null>(null)
const searchQuery = ref(props.filters.search || '')
const showDeleteConfirm = ref(false)
const roleToDelete = ref<Role | null>(null)
const showFilters = ref(false)
const perPage = ref(props.filters.per_page || 15)
const permissions = ref<Permission[]>([])

// Filter states
const roleType = ref(props.filters.role_type || '')
const createdFrom = ref(props.filters.created_from || '')
const createdTo = ref(props.filters.created_to || '')

const roleTypeOptions = [
  { value: '', label: 'All Role Types' },
  { value: 'Admin', label: 'Admin' },
  { value: 'Manager', label: 'Manager' },
  { value: 'User', label: 'User' },
]

const createdFromDate = ref<Date | null>(createdFrom.value ? new Date(createdFrom.value) : null)
const createdToDate = ref<Date | null>(createdTo.value ? new Date(createdTo.value) : null)

watch(createdFromDate, (newDate) => {
  createdFrom.value = newDate ? newDate.toISOString().split('T')[0] : ''
})

watch(createdToDate, (newDate) => {
  createdTo.value = newDate ? newDate.toISOString().split('T')[0] : ''
})

// Debounced search
let searchTimeout: NodeJS.Timeout
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    handleSearch()
  }, 500)
}

watch(searchQuery, () => {
  debouncedSearch()
})

watch(perPage, () => {
  handleSearch()
})

async function fetchPermissions() {
  try {
    const response = await fetch('/permissions/all')
    const data = await response.json()
    permissions.value = data.data
  } catch (error) {
    console.error('Failed to fetch permissions:', error)
  }
}

// Fetch permissions on component mount
onMounted(() => {
  fetchPermissions()
})

function openCreateModal() {
  modalMode.value = 'create'
  selectedRole.value = null
  showModal.value = true
}

function openEditModal(role: Role) {
  modalMode.value = 'edit'
  selectedRole.value = role
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedRole.value = null
}

function handleSearch() {
  const params = new URLSearchParams()
  if (searchQuery.value) {
    params.append('search', searchQuery.value)
  }
  params.append('per_page', perPage.value.toString())

  // Add filters
  if (roleType.value) {
    params.append('role_type', roleType.value)
  }
  if (createdFrom.value) {
    params.append('created_from', createdFrom.value)
  }
  if (createdTo.value) {
    params.append('created_to', createdTo.value)
  }

  const url = `/roles${params.toString() ? '?' + params.toString() : ''}`
  router.visit(url, {
    preserveState: true,
    preserveScroll: true,
  })
}

function clearSearch() {
  searchQuery.value = ''
}

function applyFilters() {
  handleSearch()
  showFilters.value = false
}

function resetFilters() {
  roleType.value = ''
  createdFrom.value = ''
  createdTo.value = ''
  createdFromDate.value = null
  createdToDate.value = null
  handleSearch()
  showFilters.value = false
}

function handleDeleteClick(role: Role) {
  roleToDelete.value = role
  showDeleteConfirm.value = true
}

function confirmDelete() {
  if (roleToDelete.value) {
    router.delete(`/roles/${roleToDelete.value.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteConfirm.value = false
        roleToDelete.value = null
      },
    })
  }
}

function cancelDelete() {
  showDeleteConfirm.value = false
  roleToDelete.value = null
}
</script>

<template>
  <AppLayout>
    <Head title="Roles" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Roles</h1>
          <p class="mt-1 text-sm text-gray-500">
            Manage system roles and assign permissions
          </p>
        </div>
        <Button variant="primary" @click="openCreateModal">
          <Plus class="w-5 h-5" />
          Create Role
        </Button>
      </div>

      <!-- Stats Summary -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">Total Roles</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">{{ roles.total }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">Current Page</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">{{ roles.current_page }} / {{ roles.last_page }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">Showing Results</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">{{ Math.min(10, roles.data.length) }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">Per Page</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">10</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Roles Table -->
      <RolesTable
        :roles="roles"
        :role-type="roleType"
        :role-type-options="roleTypeOptions"
        :created-from-date="createdFromDate"
        :created-to-date="createdToDate"
        @edit="openEditModal"
        @delete="handleDeleteClick"
        @apply-filters="applyFilters"
        @reset-filters="resetFilters"
        @update:role-type="roleType = $event"
        @update:created-from-date="createdFromDate = $event"
        @update:created-to-date="createdToDate = $event"
      />

      <!-- Role Modal -->
      <RoleModal
        :open="showModal"
        :mode="modalMode"
        :role="selectedRole"
        :permissions="permissions"
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
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Role</h3>
                <p class="text-sm text-gray-600 mb-6">
                  Are you sure you want to delete the role
                  <span class="font-semibold">{{ roleToDelete?.display_name || roleToDelete?.name }}</span>?
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
    </div>
  </AppLayout>
</template>
