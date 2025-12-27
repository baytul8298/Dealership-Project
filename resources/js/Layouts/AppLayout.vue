<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <Link href="/" class="text-xl font-bold text-gray-800">
                                Dealership
                            </Link>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div v-if="$page.props.auth.user" class="flex items-center space-x-4">
                            <Link href="/dashboard" class="text-gray-700 hover:text-gray-900">
                                Dashboard
                            </Link>
                            <span class="text-gray-700">{{ $page.props.auth.user.name }}</span>
                            <form @submit.prevent="logout">
                                <button type="submit" class="text-gray-700 hover:text-gray-900">
                                    Logout
                                </button>
                            </form>
                        </div>
                        <div v-else class="flex items-center space-x-4">
                            <Link href="/login" class="text-gray-700 hover:text-gray-900">
                                Login
                            </Link>
                            <Link href="/register" class="text-gray-700 hover:text-gray-900">
                                Register
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';

const logout = () => {
    router.post('/logout');
};
</script>
