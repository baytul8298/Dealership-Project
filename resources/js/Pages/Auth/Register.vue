<script setup lang="ts">
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Eye, EyeOff, UserPlus } from 'lucide-vue-next'
import { ref } from 'vue'

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
})

const showPassword = ref(false)
const showPasswordConfirmation = ref(false)
const nameFocused = ref(false)
const emailFocused = ref(false)
const passwordFocused = ref(false)
const passwordConfirmationFocused = ref(false)

const submit = () => {
  form.post('/register', {
    onFinish: () => form.reset('password', 'password_confirmation'),
  })
}

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}

const togglePasswordConfirmationVisibility = () => {
  showPasswordConfirmation.value = !showPasswordConfirmation.value
}
</script>

<template>
  <Head title="Register" />

  <AuthLayout>
    <!-- Logo and Brand -->
    <div class="flex flex-col items-center mb-8">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
          <span class="text-white font-bold text-2xl">D</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800">Dealership</h1>
      </div>
    </div>

    <!-- Welcome Text -->
    <div class="text-center mb-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-2">Create Account</h2>
      <p class="text-gray-600 text-sm">Join us today! Create your account to get started.</p>
    </div>

    <!-- Register Form -->
    <form @submit.prevent="submit" class="space-y-5">
      <!-- Name Field -->
      <div class="relative">
        <input
          id="name"
          v-model="form.name"
          type="text"
          @focus="nameFocused = true"
          @blur="nameFocused = false"
          :placeholder="nameFocused || form.name ? 'Enter your full name' : ''"
          class="w-full px-4 py-3 border rounded-md transition-all duration-200 outline-none bg-white"
          :class="{
            'border-orange-500 border-2': nameFocused && !form.errors.name,
            'border-red-500 border-2': form.errors.name,
            'border-gray-300': !nameFocused && !form.errors.name
          }"
          required
          autofocus
        />
        
        <label
          class="absolute left-4 transition-all duration-200 pointer-events-none bg-white px-1"
          :class="{
            '-top-2.5 text-xs font-medium': nameFocused || form.name,
            'top-1/2 -translate-y-1/2 text-base': !nameFocused && !form.name,
            'text-red-500': form.errors.name,
            'text-orange-500': nameFocused && !form.errors.name,
            'text-gray-500': !nameFocused && !form.errors.name
          }"
        >
          Full Name
          <span class="text-red-500 ml-0.5">*</span>
        </label>

        <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">
          {{ form.errors.name }}
        </p>
      </div>

      <!-- Email Field -->
      <div class="relative">
        <input
          id="email"
          v-model="form.email"
          type="email"
          @focus="emailFocused = true"
          @blur="emailFocused = false"
          :placeholder="emailFocused || form.email ? 'Enter your email address' : ''"
          class="w-full px-4 py-3 border rounded-md transition-all duration-200 outline-none bg-white"
          :class="{
            'border-orange-500 border-2': emailFocused && !form.errors.email,
            'border-red-500 border-2': form.errors.email,
            'border-gray-300': !emailFocused && !form.errors.email
          }"
          required
        />
        
        <label
          class="absolute left-4 transition-all duration-200 pointer-events-none bg-white px-1"
          :class="{
            '-top-2.5 text-xs font-medium': emailFocused || form.email,
            'top-1/2 -translate-y-1/2 text-base': !emailFocused && !form.email,
            'text-red-500': form.errors.email,
            'text-orange-500': emailFocused && !form.errors.email,
            'text-gray-500': !emailFocused && !form.errors.email
          }"
        >
          E-mail
          <span class="text-red-500 ml-0.5">*</span>
        </label>

        <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">
          {{ form.errors.email }}
        </p>
      </div>

      <!-- Password Field -->
      <div class="relative">
        <input
          id="password"
          v-model="form.password"
          :type="showPassword ? 'text' : 'password'"
          @focus="passwordFocused = true"
          @blur="passwordFocused = false"
          :placeholder="passwordFocused || form.password ? 'Enter your password' : ''"
          class="w-full px-4 py-3 border rounded-md transition-all duration-200 outline-none bg-white pr-12"
          :class="{
            'border-orange-500 border-2': passwordFocused && !form.errors.password,
            'border-red-500 border-2': form.errors.password,
            'border-gray-300': !passwordFocused && !form.errors.password
          }"
          required
        />
        
        <label
          class="absolute left-4 transition-all duration-200 pointer-events-none bg-white px-1"
          :class="{
            '-top-2.5 text-xs font-medium': passwordFocused || form.password,
            'top-1/2 -translate-y-1/2 text-base': !passwordFocused && !form.password,
            'text-red-500': form.errors.password,
            'text-orange-500': passwordFocused && !form.errors.password,
            'text-gray-500': !passwordFocused && !form.errors.password
          }"
        >
          Password
          <span class="text-red-500 ml-0.5">*</span>
        </label>

        <button
          type="button"
          @click="togglePasswordVisibility"
          class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500 transition-colors"
        >
          <Eye v-if="!showPassword" class="w-5 h-5" />
          <EyeOff v-else class="w-5 h-5" />
        </button>

        <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">
          {{ form.errors.password }}
        </p>
      </div>

      <!-- Confirm Password Field -->
      <div class="relative">
        <input
          id="password_confirmation"
          v-model="form.password_confirmation"
          :type="showPasswordConfirmation ? 'text' : 'password'"
          @focus="passwordConfirmationFocused = true"
          @blur="passwordConfirmationFocused = false"
          :placeholder="passwordConfirmationFocused || form.password_confirmation ? 'Re-enter your password' : ''"
          class="w-full px-4 py-3 border rounded-md transition-all duration-200 outline-none bg-white pr-12"
          :class="{
            'border-orange-500 border-2': passwordConfirmationFocused,
            'border-gray-300': !passwordConfirmationFocused
          }"
          required
        />
        
        <label
          class="absolute left-4 transition-all duration-200 pointer-events-none bg-white px-1"
          :class="{
            '-top-2.5 text-xs font-medium': passwordConfirmationFocused || form.password_confirmation,
            'top-1/2 -translate-y-1/2 text-base': !passwordConfirmationFocused && !form.password_confirmation,
            'text-orange-500': passwordConfirmationFocused,
            'text-gray-500': !passwordConfirmationFocused
          }"
        >
          Confirm Password
          <span class="text-red-500 ml-0.5">*</span>
        </label>

        <button
          type="button"
          @click="togglePasswordConfirmationVisibility"
          class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-orange-500 transition-colors"
        >
          <Eye v-if="!showPasswordConfirmation" class="w-5 h-5" />
          <EyeOff v-else class="w-5 h-5" />
        </button>
      </div>

      <!-- Register Button -->
      <button
        type="submit"
        :disabled="form.processing"
        class="w-full bg-gradient-to-r from-orange-400 to-orange-500 hover:from-orange-500 hover:to-orange-600 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <UserPlus class="w-4 h-4" />
        <span>{{ form.processing ? 'Creating Account...' : 'Create Account' }}</span>
      </button>

      <!-- Login Link -->
      <div class="text-center pt-4 border-t border-gray-200">
        <p class="text-sm text-gray-600">
          Already have an account?
          <Link href="/login" class="text-orange-500 hover:text-orange-600 font-semibold ml-1 transition-colors">
            Sign in
          </Link>
        </p>
      </div>
    </form>
  </AuthLayout>
</template>