<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Edit, Trash2 } from 'lucide-vue-next'
import type { Permission } from '@/types/models/User'
import type { Module } from '@/types/models/Module'
import { format } from 'date-fns'
import { DataTableNew, SearchableDropdown, DatePicker } from '@/Components/shared'

interface Props {
  permissions: {
    data: Permission[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  guardName: string
  guardOptions: Array<{ value: string; label: string }>
  moduleId: string
  moduleOptions: Array<{ value: string | number; label: string }>
  createdFromDate: Date | null
  createdToDate: Date | null
}

const props = defineProps<Props>()
const emit = defineEmits<{
  edit: [permission: Permission]
  delete: [permission: Permission]
  'apply-filters': []
  'reset-filters': []
  'update:guardName': [value: string]
  'update:moduleId': [value: string | number]
  'update:createdFromDate': [value: Date | null]
  'update:createdToDate': [value: Date | null]
}>()

const columns = [
  { key: 'icon', label: '' },
  { key: 'name', label: 'Name' },
  { key: 'display_name', label: 'Display Name' },
  { key: 'module', label: 'Module' },
  { key: 'description', label: 'Description' },
  { key: 'created_at', label: 'Created at' },
  { key: 'updated_at', label: 'Updated at' },
]

function handleEdit(permission: Permission) {
  emit('edit', permission)
}

function handleDelete(permission: Permission) {
  emit('delete', permission)
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
    :data="permissions.data"
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
          label="Guard Name"
          :model-value="guardName"
          :options="guardOptions"
          placeholder="Select guard..."
          @update:model-value="$emit('update:guardName', $event)"
        />

        <SearchableDropdown
          label="Module"
          :model-value="moduleId"
          :options="moduleOptions"
          placeholder="Select module..."
          @update:model-value="$emit('update:moduleId', $event)"
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
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
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
          {{ row.guard_name }}
        </div>
      </div>
    </template>

    <!-- Display Name Column -->
    <template #cell-display_name="{ value }">
      <div class="text-sm text-gray-900">{{ value || '-' }}</div>
    </template>

    <!-- Module Column -->
    <template #cell-module="{ row }">
      <div class="text-sm text-gray-900">
        {{ row.module?.module_name || 'N/A' }}
      </div>
    </template>

    <!-- Description Column -->
    <template #cell-description="{ value }">
      <div class="text-sm text-gray-500 max-w-xs truncate">
        {{ value || '-' }}
      </div>
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
          Edit Permission
        </button>
        <button
          @click.stop="handleDelete(row); close()"
          class="dropdown-item flex items-center gap-2 w-full text-error"
        >
          <Trash2 class="w-4 h-4" />
          Delete Permission
        </button>
      </div>
    </template>

  </DataTableNew>
</template>

