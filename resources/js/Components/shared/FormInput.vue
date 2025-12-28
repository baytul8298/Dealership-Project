<script setup lang="ts">
import { computed } from 'vue'

interface Props {
  label: string
  modelValue: string | number
  type?: string
  placeholder?: string
  required?: boolean
  error?: string
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  placeholder: '',
  required: false,
})

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
}>()

const hasValue = computed(() => {
  return props.modelValue !== '' && props.modelValue !== null && props.modelValue !== undefined
})

function handleInput(event: Event) {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.value)
}
</script>

<template>
  <div class="relative">
    <input
      :type="type"
      :value="modelValue"
      @input="handleInput"
      :placeholder="placeholder"
      :required="required"
      class="w-full px-3 py-2.5 pt-4 border-2 border-gray-300 rounded-lg focus:outline-none focus:ring-0 focus:border-orange-500 transition-colors peer placeholder-transparent"
      :class="{ 'border-red-500 focus:border-red-500': error }"
    />
    <label
      class="absolute left-3 -top-2.5 bg-white px-1 text-sm font-medium transition-all peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-2.5 peer-focus:-top-2.5 peer-focus:text-sm peer-focus:text-orange-500"
      :class="hasValue ? 'text-orange-500 -top-2.5 text-sm' : 'text-gray-600'"
    >
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
    </label>
    <p v-if="error" class="mt-1 text-sm text-red-600">
      {{ error }}
    </p>
  </div>
</template>
