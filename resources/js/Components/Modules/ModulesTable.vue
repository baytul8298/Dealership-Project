<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { Edit, Trash2 } from 'lucide-vue-next'
import type { Module } from '@/types/models/Module'
import { format } from 'date-fns'
import { DataTableNew } from '@/Components/shared'

interface Props {
  modules: {
    data: Module[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

const props = defineProps<Props>()
const emit = defineEmits<{
  edit: [module: Module]
  delete: [module: Module]
}>()

const columns = [
  { key: 'icon', label: '' },
  { key: 'module_name', label: 'Module Name' },
  { key: 'created_at', label: 'Created at' },
  { key: 'updated_at', label: 'Updated at' },
]

function handleEdit(module: Module) {
  emit('edit', module)
}

function handleDelete(module: Module) {
  emit('delete', module)
}

function formatDate(dateString: string) {
  try {
    return format(new Date(dateString), 'yyyy-MM-dd HH:mm')
  } catch {
    return dateString
  }
}
</script>

<template>
  <DataTableNew
    :data="modules.data"
    :columns="columns"
    search-placeholder="Search modules..."
    @action-edit="handleEdit"
    @action-delete="handleDelete"
  >
    <!-- Icon Column -->
    <template #cell-icon="{ row }">
      <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: rgba(255, 136, 0, 0.1);">
        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
        </svg>
      </div>
    </template>

    <!-- Module Name Column -->
    <template #cell-module_name="{ row }">
      <div>
        <div class="text-sm font-medium text-gray-900">
          {{ row.module_name }}
        </div>
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
          Edit Module
        </button>
        <button
          @click.stop="handleDelete(row); close()"
          class="dropdown-item flex items-center gap-2 w-full text-error"
        >
          <Trash2 class="w-4 h-4" />
          Delete Module
        </button>
      </div>
    </template>
  </DataTableNew>
</template>
