<script setup lang="ts">
import PermissionModal from '@/Components/Permissions/PermissionModal.vue'
import PermissionsTable from '@/Components/Permissions/PermissionsTable.vue'
import { Button } from '@/Components/shared'
import AppLayout from '@/Layouts/AppLayout.vue'
import type { Module } from '@/types/models/Module'
import type { Permission } from '@/types/models/User'
import { Head, router } from '@inertiajs/vue3'
import { Plus } from 'lucide-vue-next'
import { computed, onMounted, ref, watch } from 'vue'

interface Props {
  permissions: {
    data: Permission[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  filters: {
    search: string | null
    per_page: number
    guard_name?: string | null
    module_id?: string | null
    created_from?: string | null
    created_to?: string | null
  }
}

const props = defineProps<Props>()

const showModal = ref(false)
const modalMode = ref<'create' | 'edit'>('create')
const selectedPermission = ref<Permission | null>(null)
const searchQuery = ref(props.filters.search || '')
const showDeleteConfirm = ref(false)
const permissionToDelete = ref<Permission | null>(null)
const showFilters = ref(false)
const perPage = ref(props.filters.per_page || 15)

// Filter states
const guardName = ref(props.filters.guard_name || '')
const moduleId = ref(props.filters.module_id || '')
const createdFrom = ref(props.filters.created_from || '')
const createdTo = ref(props.filters.created_to || '')
const modules = ref<Module[]>([])

const guardOptions = [
  { value: '', label: 'All Guards' },
  { value: 'web', label: 'Web' },
  { value: 'api', label: 'API' },
]

const moduleOptions = computed(() => {
  return [
    { value: '', label: 'All Modules' },
    ...modules.value.map(module => ({
      value: module.id,
      label: module.module_name
    }))
  ]
})

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

function openCreateModal() {
  modalMode.value = 'create'
  selectedPermission.value = null
  showModal.value = true
}

function openEditModal(permission: Permission) {
  modalMode.value = 'edit'
  selectedPermission.value = permission
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedPermission.value = null
}

function handleSearch() {
  const params = new URLSearchParams()
  if (searchQuery.value) {
    params.append('search', searchQuery.value)
  }
  params.append('per_page', perPage.value.toString())

  // Add filters
  if (guardName.value) {
    params.append('guard_name', guardName.value)
  }
  if (moduleId.value) {
    params.append('module_id', moduleId.value.toString())
  }
  if (createdFrom.value) {
    params.append('created_from', createdFrom.value)
  }
  if (createdTo.value) {
    params.append('created_to', createdTo.value)
  }

  const url = `/permissions${params.toString() ? '?' + params.toString() : ''}`
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
  guardName.value = ''
  moduleId.value = ''
  createdFrom.value = ''
  createdTo.value = ''
  createdFromDate.value = null
  createdToDate.value = null
  handleSearch()
  showFilters.value = false
}

function handleDeleteClick(permission: Permission) {
  permissionToDelete.value = permission
  showDeleteConfirm.value = true
}

function confirmDelete() {
  if (permissionToDelete.value) {
    router.delete(`/permissions/${permissionToDelete.value.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteConfirm.value = false
        permissionToDelete.value = null
      },
    })
  }
}

function cancelDelete() {
  showDeleteConfirm.value = false
  permissionToDelete.value = null
}

async function fetchModules() {
  try {
    const response = await fetch('/modules/all')
    const data = await response.json()
    modules.value = data.data
  } catch (error) {
    console.error('Failed to fetch modules:', error)
  }
}

// Fetch modules on component mount
onMounted(() => {
  fetchModules()
})
</script>

<template>
  <AppLayout>
    <Head title="Permissions" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Permissions</h1>
          <p class="mt-1 text-sm text-gray-500">
            Manage system permissions and access controls
          </p>
        </div>
        <Button variant="primary" @click="openCreateModal">
          <Plus class="w-5 h-5" />
          Create Permission
        </Button>
      </div>


      <!-- Stats Summary -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">Total Permissions</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">{{ permissions.total }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">Current Page</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">{{ permissions.current_page }} / {{ permissions.last_page }}</p>
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
              <p class="text-2xl font-bold text-gray-900 mt-1">{{ Math.min(10, permissions.data.length) }}</p>
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

      <!-- Permissions Table -->
      <PermissionsTable
        :permissions="permissions"
        :guard-name="guardName"
        :guard-options="guardOptions"
        :module-id="moduleId"
        :module-options="moduleOptions"
        :created-from-date="createdFromDate"
        :created-to-date="createdToDate"
        @edit="openEditModal"
        @delete="handleDeleteClick"
        @apply-filters="applyFilters"
        @reset-filters="resetFilters"
        @update:guard-name="guardName = $event"
        @update:module-id="moduleId = $event"
        @update:created-from-date="createdFromDate = $event"
        @update:created-to-date="createdToDate = $event"
      />

      <!-- Permission Modal -->
      <PermissionModal
        :open="showModal"
        :mode="modalMode"
        :permission="selectedPermission"
        :modules="modules"
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
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Permission</h3>
                <p class="text-sm text-gray-600 mb-6">
                  Are you sure you want to delete the permission
                  <span class="font-semibold">{{ permissionToDelete?.name }}</span>?
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
