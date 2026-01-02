<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import { Plus, Search, Filter, X, ChevronDown } from 'lucide-vue-next'
import AppLayout from '@/Layouts/AppLayout.vue'
import ModulesTable from '@/Components/Modules/ModulesTable.vue'
import ModuleModal from '@/Components/Modules/ModuleModal.vue'
import { Button } from '@/Components/shared'
import type { Module } from '@/types/models/Module'

interface Props {
  modules: {
    data: Module[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
  filters: {
    search: string | null
    per_page: number
  }
}

const props = defineProps<Props>()

const showModal = ref(false)
const modalMode = ref<'create' | 'edit'>('create')
const selectedModule = ref<Module | null>(null)
const searchQuery = ref(props.filters.search || '')
const showDeleteConfirm = ref(false)
const moduleToDelete = ref<Module | null>(null)
const perPage = ref(props.filters.per_page || 15)

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
  selectedModule.value = null
  showModal.value = true
}

function openEditModal(module: Module) {
  modalMode.value = 'edit'
  selectedModule.value = module
  showModal.value = true
}

function closeModal() {
  showModal.value = false
  selectedModule.value = null
}

function handleSearch() {
  const params = new URLSearchParams()
  if (searchQuery.value) {
    params.append('search', searchQuery.value)
  }
  params.append('per_page', perPage.value.toString())

  const url = `/modules${params.toString() ? '?' + params.toString() : ''}`
  router.visit(url, {
    preserveState: true,
    preserveScroll: true,
  })
}

function clearSearch() {
  searchQuery.value = ''
}

function handleDeleteClick(module: Module) {
  moduleToDelete.value = module
  showDeleteConfirm.value = true
}

function confirmDelete() {
  if (moduleToDelete.value) {
    router.delete(`/modules/${moduleToDelete.value.id}`, {
      preserveScroll: true,
      onSuccess: () => {
        showDeleteConfirm.value = false
        moduleToDelete.value = null
      },
    })
  }
}

function cancelDelete() {
  showDeleteConfirm.value = false
  moduleToDelete.value = null
}
</script>

<template>
  <AppLayout>
    <Head title="Modules" />

    <div class="space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Modules</h1>
          <p class="mt-1 text-sm text-gray-500">
            Manage system modules
          </p>
        </div>
        <Button variant="primary" @click="openCreateModal">
          <Plus class="w-5 h-5" />
          Create Module
        </Button>
      </div>

      <!-- Stats Summary -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">Total Modules</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">{{ modules.total }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">Current Page</p>
              <p class="text-2xl font-bold text-gray-900 mt-1">{{ modules.current_page }} / {{ modules.last_page }}</p>
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
              <p class="text-2xl font-bold text-gray-900 mt-1">{{ Math.min(10, modules.data.length) }}</p>
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

      <!-- Modules Table -->
      <ModulesTable
        :modules="modules"
        @edit="openEditModal"
        @delete="handleDeleteClick"
      />

      <!-- Module Modal -->
      <ModuleModal
        :open="showModal"
        :mode="modalMode"
        :module="selectedModule"
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
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Delete Module</h3>
                <p class="text-sm text-gray-600 mb-6">
                  Are you sure you want to delete the module
                  <span class="font-semibold">{{ moduleToDelete?.module_name }}</span>?
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
