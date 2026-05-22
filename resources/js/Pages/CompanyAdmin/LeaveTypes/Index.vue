<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue'; // Adjust path if needed

defineProps({
    leaveTypes: Array
});
</script>

<template>
    <Head title="Leave Types" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader 
                title="Leave Type Management" 
                subtitle="Define leave policies and entitlement rules for your company"
            />
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto">
                <!-- Data Table will go here -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        Leave types list will be displayed here.
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>