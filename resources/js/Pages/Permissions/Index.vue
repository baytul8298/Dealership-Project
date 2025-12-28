<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { Plus, Search, Filter, X, ChevronDown } from 'lucide-vue-next'
import AppLayout from '@/Layouts/AppLayout.vue'
import PermissionsTable from '@/Components/Permissions/PermissionsTable.vue'
import PermissionModal from '@/Components/Permissions/PermissionModal.vue'
import FormInput from '@/Components/shared/FormInput.vue'
import FormSelect from '@/Components/shared/FormSelect.vue'
import type { Permission } from '@/types/models/User'

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
const createdFrom = ref(props.filters.created_from || '')
const createdTo = ref(props.filters.created_to || '')

const guardOptions = [
  { value: '', label: 'All Guards' },
  { value: 'web', label: 'Web' },
  { value: 'api', label: 'API' },
]

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
}

function resetFilters() {
  guardName.value = ''
  createdFrom.value = ''
  createdTo.value = ''
  handleSearch()
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
        <button
          @click="openCreateModal"
          class="flex items-center gap-2 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors shadow-sm"
        >
          <Plus class="w-5 h-5" />
          Create Permission
        </button>
      </div>

      <!-- Search and Filters Bar -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col md:flex-row gap-4">
          <!-- Search Input -->
          <div class="flex-1 relative">
            <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search by name, display name or description..."
              class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
            />
            <button
              v-if="searchQuery"
              @click="clearSearch"
              class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
            >
              <X class="w-5 h-5" />
            </button>
          </div>

          <!-- Per Page Selector -->
          <div class="flex items-center gap-2">
            <label class="text-sm text-gray-600 whitespace-nowrap">Show:</label>
            <select
              v-model="perPage"
              class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent"
            >
              <option :value="10">10</option>
              <option :value="15">15</option>
              <option :value="25">25</option>
              <option :value="50">50</option>
              <option :value="100">100</option>
            </select>
          </div>

          <!-- Filter Button -->
          <button
            @click="showFilters = !showFilters"
            class="flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
            :class="{ 'bg-orange-50 border-orange-300': showFilters }"
          >
            <Filter class="w-5 h-5" />
            Filters
            <ChevronDown class="w-4 h-4 transition-transform" :class="{ 'rotate-180': showFilters }" />
          </button>
        </div>

        <!-- Expandable Filters -->
        <Transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="transform opacity-0 -translate-y-2"
          enter-to-class="transform opacity-100 translate-y-0"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="transform opacity-100 translate-y-0"
          leave-to-class="transform opacity-0 -translate-y-2"
        >
          <div v-if="showFilters" class="mt-4 pt-4 border-t border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              <!-- Guard Name Filter -->
              <FormSelect
                v-model="guardName"
                label="Guard Name"
                :options="guardOptions"
                placeholder="Select guard"
              />

              <!-- Created Date Filter -->
              <FormInput
                v-model="createdFrom"
                label="Created From"
                type="date"
                placeholder="Select start date"
              />

              <!-- Created Date To -->
              <FormInput
                v-model="createdTo"
                label="Created To"
                type="date"
                placeholder="Select end date"
              />
            </div>

            <!-- Filter Actions -->
            <div class="flex items-center justify-end gap-3 mt-4">
              <button
                @click="resetFilters"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
              >
                Reset Filters
              </button>
              <button
                @click="applyFilters"
                class="px-4 py-2 text-sm font-medium text-white bg-orange-500 rounded-lg hover:bg-orange-600 transition-colors"
              >
                Apply Filters
              </button>
            </div>
          </div>
        </Transition>
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
              <p class="text-2xl font-bold text-gray-900 mt-1">{{ permissions.data.length }}</p>
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
              <p class="text-2xl font-bold text-gray-900 mt-1">{{ perPage }}</p>
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
        @edit="openEditModal"
        @delete="handleDeleteClick"
      />

      <!-- Permission Modal -->
      <PermissionModal
        :open="showModal"
        :mode="modalMode"
        :permission="selectedPermission"
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
                  <button
                    @click="cancelDelete"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    Cancel
                  </button>
                  <button
                    @click="confirmDelete"
                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors"
                  >
                    Delete
                  </button>
                </div>
              </div>
            </Transition>
          </div>
        </Transition>
      </Teleport>
    </div>
  </AppLayout>
</template>
