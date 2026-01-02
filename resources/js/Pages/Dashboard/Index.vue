<script setup lang="ts">
import StatCard from '@/Components/shared/StatCard.vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head } from '@inertiajs/vue3'
import {
  CheckCircle,
  DollarSign,
  FolderOpen,
  Package,
  ShoppingCart,
  TrendingUp,
  Users,
  Utensils,
} from 'lucide-vue-next'

// Mock data - in real app, this would come from props
const stats = {
  totalSales: 'JOD 53,568.858',
  totalOrders: 404,
  totalActiveOrders: 193,
  averageOrder: 'JOD 132',
  totalUsers: 121,
  totalMenus: 12,
  totalProducts: 238,
  totalCategories: 366,
}
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout>
    <div class="space-y-6">
      <!-- Stats Grid - Row 1 -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
          title="Total Sales"
          :value="stats.totalSales"
          :icon="DollarSign"
          icon-color="text-blue-500"
          icon-bg="bg-blue-100"
        />
        <StatCard
          title="Total Orders"
          :value="stats.totalOrders"
          :icon="ShoppingCart"
          icon-color="text-green-500"
          icon-bg="bg-green-100"
        />
        <StatCard
          title="Total Active Orders"
          :value="stats.totalActiveOrders"
          :icon="CheckCircle"
          icon-color="text-purple-500"
          icon-bg="bg-purple-100"
        />
        <StatCard
          title="Average Order"
          :value="stats.averageOrder"
          :icon="TrendingUp"
          icon-color="text-orange-500"
          icon-bg="bg-orange-100"
        />
      </div>

      <!-- Stats Grid - Row 2 -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <StatCard
          title="Total Users"
          :value="stats.totalUsers"
          :icon="Users"
          icon-color="text-yellow-500"
          icon-bg="bg-yellow-100"
        />
        <StatCard
          title="Total Menus"
          :value="stats.totalMenus"
          :icon="Utensils"
          icon-color="text-cyan-500"
          icon-bg="bg-cyan-100"
        />
        <StatCard
          title="Total Products"
          :value="stats.totalProducts"
          :icon="Package"
          icon-color="text-pink-500"
          icon-bg="bg-pink-100"
        />
        <StatCard
          title="Total Categories"
          :value="stats.totalCategories"
          :icon="FolderOpen"
          icon-color="text-indigo-500"
          icon-bg="bg-indigo-100"
        />
      </div>

      <!-- Charts Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Hourly Sales Trend -->
        <div class="bg-white rounded-lg p-6 border border-gray-200">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">📊 Hourly Sales Trend</h3>
          </div>
          <div class="h-64 flex items-center justify-center text-gray-400">
            <p>Chart will be implemented with Chart.js</p>
          </div>
        </div>

        <!-- Sales Analytics -->
        <div class="bg-white rounded-lg p-6 border border-gray-200">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">📈 Sales Analytics</h3>
            <select class="text-sm border border-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
              <option>Weekly</option>
              <option>Monthly</option>
              <option>Yearly</option>
            </select>
          </div>
          <div class="h-64 flex items-center justify-center text-gray-400">
            <p>Chart will be implemented with Chart.js</p>
          </div>
        </div>
      </div>

      <!-- Top Selling Products -->
      <div class="bg-white rounded-lg p-6 border border-gray-200">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">🏆 Top Selling Products</h3>
          <select class="text-sm border border-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
            <option>All The Time</option>
            <option>This Week</option>
            <option>This Month</option>
          </select>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
          <!-- Sample Product Card -->
          <div class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 hover:border-orange-300 transition-colors">
            <div class="w-16 h-16 bg-gray-200 rounded-lg flex-shrink-0"></div>
            <div class="flex-1 min-w-0">
              <p class="font-medium text-gray-900 truncate">Sample Product</p>
              <p class="text-sm text-gray-500">Quantity Sold: 142</p>
              <p class="text-sm font-semibold text-orange-600 mt-0.5">BDT 10,799.043</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
