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
            </div>
        </div>
    </AuthenticatedLayout>
</template>