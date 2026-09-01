<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue'; // Adjust path if needed
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import RoundedSelect from '@/Components/RoundedSelect.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

</script>
<template>
    <Head title="Approve Leave" />
    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Approve Leave" subtitle="Manage Leave Applications" />
        </template>
        <!-- Your form content goes here -->
         <!-- Tabs Navigation -->
        <div class="flex border-b border-gray-200 mb-6">
            <button @click="activeTab = 'direct'" 
                :class="['pb-3 px-4 text-sm font-semibold border-b-2 transition-colors', activeTab === 'direct' ? 'border-primary-dark text-primary-dark' : 'border-transparent text-gray-400 hover:text-gray-600']">
                Direct Reports <span class="ml-1 px-2 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">{{ directApprovals.length }}</span>
            </button>
            
            <button v-if="$page.props.auth.user.role_id === 2" @click="activeTab = 'company'" 
                :class="['pb-3 px-4 text-sm font-semibold border-b-2 transition-colors', activeTab === 'company' ? 'border-primary-dark text-primary-dark' : 'border-transparent text-gray-400 hover:text-gray-600']">
                Company-Wide Requests <span class="ml-1 px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">{{ companyApprovals.length }}</span>
            </button>
        </div>

        <!-- Tab 1: Direct Priority Approvals -->
        <div v-if="activeTab === 'direct'">
            <!-- Render your leave table using directApprovals -->
        </div>

        <!-- Tab 2: Company-Wide Overview (Admin Only) -->
        <div v-if="activeTab === 'company'">
            <!-- Render your leave table using companyApprovals -->
        </div>
         
    </AuthenticatedLayout>
</template>