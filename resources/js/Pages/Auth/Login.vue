<script setup lang="ts">
import { ref } from 'vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import AuthLayout from '@/Layouts/AuthLayout.vue'
import { Eye, EyeOff, Lock } from 'lucide-vue-next'

const form = useForm({
  email: '',
  password: '',
  remember: false,
})

const showPassword = ref(false)

const submit = () => {
  form.post('/login', {
    onFinish: () => form.reset('password'),
  })
}

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value
}
</script>

<template>
  <Head title="Login" />

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
      <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome to Dealership</h2>
      <p class="text-gray-600 text-sm">Welcome back! Sign in to access Dealership.</p>
    </div>

    <!-- Login Form -->
    <form @submit.prevent="submit" class="space-y-5">
      <!-- Email/Username Field -->
      <div>
        <input
          id="email"
          v-model="form.email"
          type="email"
          placeholder="E-mail/Username"
          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all text-gray-700 placeholder-gray-400"
          :class="{ 'border-red-500': form.errors.email }"
          required
          autofocus
        />
        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5 ml-1">
          {{ form.errors.email }}
        </p>
      </div>

      <!-- Password Field -->
      <div>
        <div class="relative">
          <input
            id="password"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            placeholder="Password"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all text-gray-700 placeholder-gray-400 pr-12"
            :class="{ 'border-red-500': form.errors.password }"
            required
          />
          <button
            type="button"
            @click="togglePasswordVisibility"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors"
          >
            <Eye v-if="!showPassword" class="w-5 h-5" />
            <EyeOff v-else class="w-5 h-5" />
          </button>
        </div>
        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5 ml-1">
          {{ form.errors.password }}
        </p>
      </div>

      <!-- Remember Me & Forgot Password -->
      <div class="flex items-center justify-between">
        <label class="flex items-center cursor-pointer">
          <input
            v-model="form.remember"
            type="checkbox"
            class="w-4 h-4 text-orange-500 border-gray-300 rounded focus:ring-orange-500 focus:ring-2"
          />
          <span class="ml-2 text-sm text-gray-600">Remember me</span>
        </label>
        <Link href="/forgot-password" class="text-sm text-orange-500 hover:text-orange-600 font-medium">
          Forgot Password?
        </Link>
      </div>

      <!-- Login Button -->
      <button
        type="submit"
        :disabled="form.processing"
        class="w-full bg-gradient-to-r from-orange-400 to-orange-500 hover:from-orange-500 hover:to-orange-600 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 shadow-md hover:shadow-lg flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <Lock class="w-4 h-4" />
        <span>{{ form.processing ? 'Logging in...' : 'Login' }}</span>
      </button>

      <!-- Register Link -->
      <div class="text-center pt-4 border-t border-gray-200">
        <p class="text-sm text-gray-600">
          Don't have an account?
          <Link href="/register" class="text-orange-500 hover:text-orange-600 font-semibold ml-1">
            Sign up
          </Link>
        </p>
      </div>
    </form>
  </AuthLayout>
</template>
