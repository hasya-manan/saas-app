<script setup>

import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import GlobalFilter from '@/Components/GlobalFilter.vue';
import { Edit2, X, UserCheck, UserX, Eye } from 'lucide-vue-next';
import BaseButton from '@/Components/BaseButton.vue';


const props = defineProps({
    departments: Object, // Changed to Object if you are using pagination (.data)
    users: Array,       // Staff list for HOD dropdown
    filters: Object,    // For the GlobalFilter
});

const form = useForm({
    id: null,
    name: '',
    description: '',
    hod_id: '',
   
});

// 1. Setup our visibility and mode toggles
const isEditPanelOpen = ref(false);
const isCreating = ref(false);


// 2. The CREATE Trigger
const openCreatePanel = () => {
    isCreating.value = true;
    form.reset();
    isEditPanelOpen.value = true;
};

// 3. The EDIT Trigger
const openEditPanel = (dept) => {
    isCreating.value = false; // Tell the UI we are in "Edit Mode"

    // Fill the form with the data of the row we clicked
    form.id = dept.id;
    form.name = dept.name;
    form.description = dept.description;
    form.hod_id = dept.hod_id;
    isEditPanelOpen.value = true;
};

// 4. The Combined Submit Logic
const closeEditPanel = () => {
    isEditPanelOpen.value = false;
    form.reset();
};

const submitForm = () => {
    if (isCreating.value) {
        form.post(route('admin_company.departments.store'), {
            onSuccess: () => closeEditPanel(),
        });
    } else {
        form.put(route('admin_company.departments.update', form.id), {
            onSuccess: () => closeEditPanel(),
        });
    }
};
</script>

<template>
    <Head title="Department Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black text-gray-800 tracking-tight">Manage Departments</h2>
                    <p class="text-sm text-gray-500 font-medium">Organize your company structure and assign leadership</p>
                </div>
                
                <button @click="openCreatePanel" 
                    class="group flex items-center gap-2 bg-primary text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all duration-300">
                    <Plus :size="20" class="group-hover:rotate-90 transition-transform duration-300" />
                    Add New Department
                </button>
            </div>
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-start gap-6">
                
                <div :class="[isEditPanelOpen ? 'lg:w-[60%] w-full' : 'w-full']" class="transition-all duration-500">
                    <div class="bg-white overflow-hidden shadow-xl shadow-primary/5 border border-primary-border rounded-[2.5rem] p-8">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-separate border-spacing-y-2">
                                <thead>
                                    <tr class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">
                                        <th class="px-6 py-3">Department Details</th>
                                        <th class="px-6 py-3">Head of Dept</th>
                                        <th class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="dept in (departments.data || departments)" :key="dept.id"
                                        class="group bg-white hover:bg-primary-light/5 transition-all duration-300">
                                        
                                        <td class="px-6 py-4 rounded-l-2xl border-y border-l border-transparent group-hover:border-primary-border">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-xl bg-gray-100 flex items-center justify-center text-primary font-bold">
                                                    {{ dept.name.charAt(0) }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900">{{ dept.name }}</div>
                                                    <div class="text-xs text-gray-400 font-medium italic line-clamp-1">{{ dept.description || 'No description provided' }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                            <span v-if="dept.hod" class="px-3 py-1 text-[10px] font-black rounded-full uppercase bg-blue-50 text-blue-600 border border-blue-100">
                                                {{ dept.hod.name }}
                                            </span>
                                            <span v-else class="text-xs text-gray-300 italic">Not Assigned</span>
                                        </td>

                                        <td class="px-6 py-4 rounded-r-2xl border-y border-r border-transparent group-hover:border-primary-border text-right">
                                            <button @click="openEditPanel(dept)" class="p-2 text-gray-400 hover:text-primary transition-colors">
                                                <Edit2 :size="18" />
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div v-if="isEditPanelOpen" class="w-full lg:w-[40%] sticky top-6 z-10 animate-in slide-in-from-right duration-500">
                    <div class="bg-white border border-primary/10 rounded-[2.5rem] shadow-xl p-8">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">{{ isCreating ? 'Create' : 'Quick Edit' }}</h2>
                                <p class="text-xs text-gray-400 font-medium italic">Department Configuration</p>
                            </div>
                            <button @click="closeEditPanel" class="p-2 bg-gray-50 rounded-xl text-gray-400 hover:text-gray-600">
                                <X :size="20" />
                            </button>
                        </div>

                        <form @submit.prevent="submitForm" class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Department Name</label>
                                <input v-model="form.name" type="text" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4" required>
                                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Head of Department (HOD)</label>
                                <select v-model="form.hod_id" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4">
                                    <option value="">No HOD Assigned</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Description</label>
                                <textarea v-model="form.description" rows="3" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4" placeholder="Briefly describe department responsibilities..."></textarea>
                            </div>

                            <button type="submit" :disabled="form.processing"
                                class="w-full bg-primary text-white py-4 rounded-[1.5rem] font-bold shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all">
                                {{ form.processing ? 'Syncing...' : (isCreating ? 'Create Department' : 'Update Department') }}
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

