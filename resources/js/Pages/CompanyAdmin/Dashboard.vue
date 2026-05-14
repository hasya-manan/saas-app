<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

// Define the props coming from your Controller
const props = defineProps({
    tenant: Object,
    stats: Object
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard - {{ tenant?.company_name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Existing Functional Header Card -->
                <div class="mb-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Managing Company</p>
                            <h3 class="text-2xl font-bold text-blue-600">{{ tenant?.company_name }}</h3>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Tenant ID</p>
                            <code class="bg-gray-100 px-2 py-1 rounded text-sm font-mono">{{ tenant?.id }}</code>
                        </div>
                    </div>
                    
                    <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-50/50">
                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-100">
                            <p class="text-gray-500 text-sm">Total Employees</p>
                            <p class="text-3xl font-bold">{{ stats.total_employees }}</p>
                        </div>
                    </div>
                </div>

              <!-- NEW: "In Progress" Section -->
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center bg-white/50">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-blue-600 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 animate-pulse" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>

                    <h4 class="text-lg font-medium text-gray-900">Advanced Analytics Coming Soon</h4>

                    <p class="mt-2 text-sm text-gray-500 max-w-xs mx-auto">
                        <span class="font-semibold text-gray-700 italic">Hasya</span> is currently crafting the
                        attendance and payroll charts for **{{ tenant?.company_name }}**.
                    </p>

                    <div class="mt-6 flex flex-col items-center gap-3">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Development Phase: In Progress
                        </span>

                        <!-- Optional: A tiny subtle "Made by" signature at the bottom -->
                        <span class="text-[10px] text-gray-400 uppercase tracking-widest">
                            Build by Hasya &middot; 2026
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>