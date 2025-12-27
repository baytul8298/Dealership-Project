<template>
    <AppLayout>
        <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                        Email
                    </label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        :class="{ 'border-red-500': form.errors.email }"
                        required
                        autofocus
                    />
                    <p v-if="form.errors.email" class="text-red-500 text-xs italic mt-1">
                        {{ form.errors.email }}
                    </p>
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                        Password
                    </label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                        :class="{ 'border-red-500': form.errors.password }"
                        required
                    />
                    <p v-if="form.errors.password" class="text-red-500 text-xs italic mt-1">
                        {{ form.errors.password }}
                    </p>
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input
                            v-model="form.remember"
                            type="checkbox"
                            class="mr-2"
                        />
                        <span class="text-sm text-gray-700">Remember me</span>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <button
                        type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Logging in...' : 'Login' }}
                    </button>
                    <Link href="/register" class="text-blue-500 hover:text-blue-700 text-sm">
                        Need an account?
                    </Link>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>
