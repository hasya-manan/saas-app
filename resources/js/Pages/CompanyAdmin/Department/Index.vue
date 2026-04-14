<script setup>

import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
const props = defineProps({
    departments: Array,
    users: Array // Existing staff to choose as HOD
});

const form = useForm({
    name: '',
    description: '',
    manager_id: '',
    join_date: '',
});
</script>

<template>
        <Head title="Department Management" />
    <AuthenticatedLayout>

         <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 tracking-tight">Manage Departments</h2>
                    <p class="text-sm text-gray-500">Onboard new departments and configure their professional profile</p>
                </div>
                
                <Link :href="route('admin_company.users.index')" class="text-sm font-semibold text-primary hover:text-primary-dark transition-colors">
                    &larr; Back to Directory
                </Link>
            </div>
        </template>
        
        

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-8">
            <form @submit.prevent="form.post(route('departments.store'))" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Department Name</label>
                    <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Assign HOD (Head of Dept)</label>
                    <select v-model="form.manager_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Select a User...</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                </div>

                <div class="md:col-span-2 text-right">
                    <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg font-bold hover:bg-primary-dark transition-colors">
                        Create Department
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-slate-50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Dept Name</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">HOD / Manager</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">Established</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="dept in departments" :key="dept.id" class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium">{{ dept.name }}</td>
                        <td class="px-6 py-4 text-slate-600">{{ dept.manager?.name || 'Not Assigned' }}</td>
                        <td class="px-6 py-4 text-slate-500">{{ dept.join_date || 'N/A' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    


    </AuthenticatedLayout>
    
</template>

