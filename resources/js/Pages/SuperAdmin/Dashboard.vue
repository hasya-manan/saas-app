<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    stats: Object
});
</script>

<template>
    <Head title="SuperAdmin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Platform Overview</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                        <div class="text-sm font-medium text-gray-500 truncate">Total Companies</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.total_tenants }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                        <div class="text-sm font-medium text-gray-500 truncate">Active Clients</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.active_tenants }}</div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                        <div class="text-sm font-medium text-gray-500 truncate">Pending Approval</div>
                        <div class="mt-1 text-3xl font-semibold text-gray-900">{{ stats.pending_tenants }}</div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        Welcome back, Boss! Here you can manage the whole pakitangan platform.
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>