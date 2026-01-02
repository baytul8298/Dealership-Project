<script setup lang="ts">
import { DataTableNew, DatePicker, SearchableDropdown } from '@/Components/shared'
import type { Role } from '@/types/models/User'
import { format } from 'date-fns'
import { Edit, Trash2 } from 'lucide-vue-next'
import { ref } from 'vue'
import PermissionViewModal from './PermissionViewModal.vue'

interface Props {
  roles: {
    data: Role[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  roleType: string
  roleTypeOptions: Array<{ value: string; label: string }>
  createdFromDate: Date | null
  createdToDate: Date | null
}

const props = defineProps<Props>()
const emit = defineEmits<{
  edit: [role: Role]
  delete: [role: Role]
  'apply-filters': []
  'reset-filters': []
  'update:roleType': [value: string]
  'update:createdFromDate': [value: Date | null]
  'update:createdToDate': [value: Date | null]
}>()

const showPermissionsModal = ref(false)
const selectedRoleForPermissions = ref<Role | null>(null)

const columns = [
  { key: 'icon', label: '' },
  { key: 'name', label: 'Name' },
  { key: 'role_type', label: 'Role Type' },
  { key: 'permissions_count', label: 'Permissions' },
  { key: 'created_at', label: 'Created at' },
  { key: 'updated_at', label: 'Updated at' },
]

function handleEdit(role: Role) {
  emit('edit', role)
}

function handleDelete(role: Role) {
  emit('delete', role)
}

function formatDate(dateString: string) {
  try {
    return format(new Date(dateString), 'yyyy-MM-dd HH:mm')
  } catch {
    return dateString
  }
}

function handleApplyFilters() {
  emit('apply-filters')
}

function handleResetFilters() {
  emit('reset-filters')
}

function openPermissionsModal(role: Role) {
  selectedRoleForPermissions.value = role
  showPermissionsModal.value = true
}

function closePermissionsModal() {
  showPermissionsModal.value = false
  selectedRoleForPermissions.value = null
}
</script>

<template>
  <DataTableNew
    :data="roles.data"
    :columns="columns"
    search-placeholder="Search here..."
    @action-edit="handleEdit"
    @action-delete="handleDelete"
    @apply-filters="handleApplyFilters"
    @reset-filters="handleResetFilters"
  >
    <!-- Filters Slot -->
    <template #filters>
      <div class="space-y-4">
        <SearchableDropdown
          label="Role Type"
          :model-value="roleType"
          :options="roleTypeOptions"
          placeholder="Select role type..."
          @update:model-value="$emit('update:roleType', $event)"
        />

        <DatePicker
          label="Created From"
          :model-value="createdFromDate"
          placeholder="Select start date"
          @update:model-value="$emit('update:createdFromDate', $event)"
        />

        <DatePicker
          label="Created To"
          :model-value="createdToDate"
          placeholder="Select end date"
          @update:model-value="$emit('update:createdToDate', $event)"
        />
      </div>
    </template>

    <!-- Icon Column -->
    <template #cell-icon="{ row }">
      <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: rgba(255, 136, 0, 0.1);">
        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
      </div>
    </template>

    <!-- Name Column -->
    <template #cell-name="{ row }">
      <div>
        <div class="text-sm font-medium text-gray-900">
          {{ row.display_name || row.name }}
        </div>
        <div class="text-xs text-gray-500">
          {{ row.name }}
        </div>
      </div>
    </template>

    <!-- Role Type Column (Badge) -->
    <template #cell-role_type="{ row }">
      <GlobalBadge color="blue" size="xs">
        {{ row.role_type || 'Admin' }}
      </GlobalBadge>
    </template>

    <!-- Permissions Count Column -->
    <template #cell-permissions_count="{ row }">
      <button
        @click.stop="openPermissionsModal(row)"
        class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-700 hover:bg-primary hover:text-white transition-base cursor-pointer"
      >
        {{ row.permissions_count || 0 }} permissions
      </button>
    </template>

    <!-- Created at Column -->
    <template #cell-created_at="{ value }">
      <div class="text-sm text-gray-500">
        {{ formatDate(value) }}
      </div>
    </template>

    <!-- Updated at Column -->
    <template #cell-updated_at="{ value }">
      <div class="text-sm text-gray-500">
        {{ formatDate(value) }}
      </div>
    </template>

    <!-- Custom Actions -->
    <template #actions="{ row, close }">
      <div class="py-1">
        <button
          @click.stop="handleEdit(row); close()"
          class="dropdown-item flex items-center gap-2 w-full"
        >
          <Edit class="w-4 h-4 text-blue-500" />
          Edit Role
        </button>
        <button
          @click.stop="handleDelete(row); close()"
          class="dropdown-item flex items-center gap-2 w-full text-error"
        >
          <Trash2 class="w-4 h-4" />
          Delete Role
        </button>
      </div>
    </template>
  </DataTableNew>

  <!-- Permissions View Modal -->
  <PermissionViewModal
    :open="showPermissionsModal"
    :role="selectedRoleForPermissions"
    @close="closePermissionsModal"
  />
</template>
