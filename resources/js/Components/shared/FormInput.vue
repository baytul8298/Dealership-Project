<script setup lang="ts">
import { computed, ref } from 'vue'

interface Props {
  label: string
  modelValue: string | number
  type?: string
  placeholder?: string
  required?: boolean
  error?: string
  disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  placeholder: '',
  required: false,
  disabled: false,
})

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
}>()

const isFocused = ref(false)
const touched = ref(false)

const hasValue = computed(() => {
  return props.modelValue !== '' && props.modelValue !== null && props.modelValue !== undefined
})

const isActive = computed(() => {
  return isFocused.value || hasValue.value
})

function handleInput(event: Event) {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.value)
}

function handleFocus() {
  touched.value = true
  isFocused.value = true
}

function handleBlur() {
  isFocused.value = false
}
</script>

<template>
  <div class="relative">
    <input
      :type="type"
      :value="modelValue"
      @input="handleInput"
      @focus="handleFocus"
      @blur="handleBlur"
      :placeholder="isActive ? placeholder : ''"
      :required="required"
      :disabled="disabled"
      class="w-full px-4 py-3 border rounded-md transition-all duration-200 outline-none bg-white peer"
      :class="{
        'border-orange-500 border-2': touched && isFocused && !error,
        'border-red-500 border-2': error,
        'border-gray-300': (!touched || !isFocused) && !error,
        'opacity-60 cursor-not-allowed': disabled
      }"
    />
    <label
      class="absolute left-4 transition-all duration-200 pointer-events-none bg-white px-1"
      :class="{
        '-top-2.5 text-xs font-medium': isActive,
        'top-1/2 -translate-y-1/2 text-base': !isActive,
        'text-red-500': error,
        'text-orange-500': touched && isFocused && !error,
        'text-gray-500': (!touched || !isFocused) && !error
      }"
    >
      {{ label }}
      <span v-if="required" class="text-red-500 ml-0.5">*</span>
    </label>
    <p v-if="error" class="mt-1 text-sm text-red-500">
      {{ error }}
    </p>
  </div>
</template>