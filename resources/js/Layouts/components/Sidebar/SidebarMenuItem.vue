<script setup lang="ts">
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import { ChevronDown, ChevronRight } from 'lucide-vue-next'
import type { NavigationItem } from '@/config/navigation'

interface Props {
  item: NavigationItem
  active?: boolean
}

const props = defineProps<Props>()

const expanded = ref(false)

const toggleExpanded = () => {
  if (props.item.children) {
    expanded.value = !expanded.value
  }
}

const isChildActive = (child: NavigationItem) => {
  return window.location.pathname === child.href
}
</script>

<template>
  <div class="relative">
    <!-- Menu Item -->
    <component
      :is="item.href && !item.children ? Link : 'button'"
      :href="item.href"
      @click="toggleExpanded"
      :class="[
        'w-full flex items-center justify-between px-4 py-3 text-sm font-medium transition-colors',
        active
          ? 'text-orange-500 bg-orange-50 border-l-4 border-orange-500'
          : 'text-gray-700 hover:bg-gray-50 hover:text-orange-500 border-l-4 border-transparent',
      ]"
    >
      <div class="flex items-center gap-3">
        <component :is="item.icon" v-if="item.icon" class="w-5 h-5" />
        <span>{{ item.name }}</span>
      </div>

      <!-- Chevron for expandable items -->
      <ChevronDown
        v-if="item.children"
        :class="['w-4 h-4 transition-transform', expanded ? 'rotate-180' : '']"
      />
    </component>

    <!-- Submenu -->
    <div
      v-if="item.children && expanded"
      class="bg-gray-50"
    >
      <Link
        v-for="child in item.children"
        :key="child.name"
        :href="child.href || '#'"
        :class="[
          'block px-4 py-2.5 pl-14 text-sm transition-colors',
          isChildActive(child)
            ? 'text-orange-500 font-medium bg-orange-50'
            : 'text-gray-600 hover:text-orange-500 hover:bg-gray-100',
        ]"
      >
        {{ child.name }}
      </Link>
    </div>
  </div>
</template>
