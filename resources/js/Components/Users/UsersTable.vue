<script setup lang="ts">
import { DataTableNew, DatePicker, SearchableDropdown } from '@/Components/shared'
import type { User } from '@/types/models/User'
import { format } from 'date-fns'
import { Edit, Trash2 } from 'lucide-vue-next'
import { ref } from 'vue'

interface Props {
  users: {
    data: User[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  userType: string
  userTypeOptions: Array<{ value: string; label: string }>
  status: string
  statusOptions: Array<{ value: string; label: string }>
  createdFromDate: Date | null
  createdToDate: Date | null
}

const props = defineProps<Props>()
const emit = defineEmits<{
  edit: [user: User]
  delete: [user: User]
  'apply-filters': []
  'reset-filters': []
  'update:userType': [value: string]
  'update:status': [value: string]
  'update:createdFromDate': [value: Date | null]
  'update:createdToDate': [value: Date | null]
}>()

const columns = [
  { key: 'icon', label: '' },
  { key: 'name', label: 'Name' },
  { key: 'user_type', label: 'User Type' },
  { key: 'role', label: 'Role' },
  { key: 'status', label: 'Status' },
  { key: 'created_at', label: 'Created at' },
  { key: 'updated_at', label: 'Updated at' },
]

function handleEdit(user: User) {
  emit('edit', user)
}

function handleDelete(user: User) {
  emit('delete', user)
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
</script>

<template>
  <DataTableNew
    :data="users.data"
    :columns="columns"
    search-placeholder="Search users..."
    @action-edit="handleEdit"
    @action-delete="handleDelete"
    @apply-filters="handleApplyFilters"
    @reset-filters="handleResetFilters"
  >
    <!-- Filters Slot -->
    <template #filters>
      <div class="space-y-4">
        <SearchableDropdown
          label="User Type"
          :model-value="userType"
          :options="userTypeOptions"
          placeholder="Select user type..."
          @update:model-value="$emit('update:userType', $event)"
        />

        <SearchableDropdown
          label="Status"
          :model-value="status"
          :options="statusOptions"
          placeholder="Select status..."
          @update:model-value="$emit('update:status', $event)"
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
      <div
        class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0"
        style="background-color: rgba(255, 136, 0, 0.1)"
      >
        <svg
          class="w-5 h-5 text-primary"
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
    </template>

    <!-- Name Column -->
    <template #cell-name="{ row }">
      <div>
        <div class="text-sm font-medium text-gray-900">
          {{ row.name }}
        </div>
        <div class="text-xs text-gray-500">
          {{ row.email }}
        </div>
        <div class="text-xs text-gray-400">
          @{{ row.username }}
        </div>
      </div>
    </template>

    <!-- User Type Column (Badge) -->
    <template #cell-user_type="{ row }">
      <GlobalBadge color="cyan" size="xs">
        {{ row.user_type || 'N/A' }}
      </GlobalBadge>
    </template>

    <!-- Role Column -->
    <template #cell-role="{ row }">
      <div v-if="row.roles && row.roles.length > 0">
        <GlobalBadge
          v-for="role in row.roles"
          :key="role.id"
          color="purple"
          size="xs"
        >
          {{ role.display_name || role.name }}
        </GlobalBadge>
      </div>
      <span v-else class="text-xs text-gray-400">No role</span>
    </template>

    <!-- Status Column -->
    <template #cell-status="{ row }">
      <GlobalBadge :color="row.status ? 'green' : 'red'" size="xs">
        {{ row.status ? 'Active' : 'Inactive' }}
      </GlobalBadge>
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
          Edit User
        </button>
        <button
          @click.stop="handleDelete(row); close()"
          class="dropdown-item flex items-center gap-2 w-full text-error"
        >
          <Trash2 class="w-4 h-4" />
          Delete User
        </button>
      </div>
    </template>
  </DataTableNew>
</template>
