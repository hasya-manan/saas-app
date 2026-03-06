<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue'; 

import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Trash2, RotateCcw, Edit2, ShieldAlert } from 'lucide-vue-next';

const props = defineProps({
    tenants: Array,      // Active tenants
    deletedTenants: Array // Soft-deleted tenants
});

const currentTab = ref('active'); 
const confirmingDeletion = ref(false);
const selectedTenant = ref(null);
const confirmationInput = ref('');

// Computed list based on tab
const displayList = computed(() => {
    return currentTab.value === 'active' ? props.tenants : props.deletedTenants;
});

const closeModal = () => {
    confirmingDeletion.value = false;
    confirmationInput.value = '';
    selectedTenant.value = null;
};

// Open Modal for Hard Delete only
const openHardDeleteModal = (tenant) => {
    selectedTenant.value = tenant;
    confirmingDeletion.value = true;
};

// Logic for Soft Delete (Archive)
const softDeleteTenant = (tenant) => {
    if (confirm(`Archive ${tenant.company_name}? You can restore it from Trash later.`)) {
        router.delete(route('tenants.destroy', tenant.id));
    }
};

// Logic for Restore
const restoreTenant = (tenant) => {
    router.put(route('tenants.restore', tenant.id));
};

// Logic for Hard Delete (Forever)
const hardDeleteTenant = () => {
    router.delete(route('tenants.force-delete', selectedTenant.value.id), {
        onSuccess: () => closeModal(),
    });
};
</script>

<template>
    <Head title="Manage Companies" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Manage Companies</h2>
                    <p class="text-sm text-gray-500">Overview of all client workspaces</p>
                </div>
                <Link :href="route('tenants.create')" class="bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm shadow-primary/20">
                    + Onboard New
                </Link>
            </div>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex gap-6 mb-6 border-b border-gray-100">
                <button @click="currentTab = 'active'" 
                    :class="currentTab === 'active' ? 'border-b-2 border-primary text-primary' : 'text-gray-400 hover:text-gray-600'"
                    class="pb-4 px-2 font-bold text-sm transition-all">
                    Active ({{ tenants.length }})
                </button>
                <button @click="currentTab = 'trash'" 
                    :class="currentTab === 'trash' ? 'border-b-2 border-primary text-primary' : 'text-gray-400 hover:text-gray-600'"
                    class="pb-4 px-2 font-bold text-sm transition-all flex items-center gap-2">
                    Trash ({{ deletedTenants.length }})
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm border border-primary-border/20 sm:rounded-2xl">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-primary-light/20">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Company</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Admin Email</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-widest">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        <tr v-for="tenant in displayList" :key="tenant.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ tenant.company_name }}</div>
                                <div class="text-xs text-gray-400">UUID: {{ tenant.id }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ tenant.email }}</td>
                            <td class="px-6 py-4">
                                <span v-if="currentTab === 'active'" class="px-2 py-1 text-[10px] font-bold rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase">
                                    Active
                                </span>
                                <span v-else class="px-2 py-1 text-[10px] font-bold rounded-full bg-red-50 text-red-600 border border-red-100 uppercase">
                                    Archived
                                </span>
                            </td>
                           <td class="px-6 py-4 text-right">
                                <div v-if="currentTab === 'active'" class="flex justify-end gap-2">
                                    <BaseButton variant="outline" size="sm">
                                        <Edit2 :size="14" /> Edit
                                    </BaseButton>

                                    <BaseButton @click="softDeleteTenant(tenant)" variant="danger" size="sm">
                                        <Trash2 :size="14" /> Archive
                                    </BaseButton>
                                </div>

                                <div v-else class="flex justify-end gap-2">
                                    <BaseButton @click="restoreTenant(tenant)" variant="primary" size="sm">
                                         <RotateCcw :size="iconSize" /> Restore
                                    </BaseButton>

                                    <BaseButton @click="openHardDeleteModal(tenant)" variant="danger" size="sm">
                                        Delete Forever
                                    </BaseButton>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="displayList.length === 0">
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400 text-sm">
                                No companies found in {{ currentTab }}.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <Modal :show="confirmingDeletion" @close="closeModal">
            <div class="p-8">
                <div class="flex items-center gap-3 text-red-600 mb-4">
                    <ShieldAlert :size="28" />
                    <h2 class="text-xl font-bold">Final Confirmation Required</h2>
                </div>
                
                <p class="text-sm text-gray-500 leading-relaxed">
                    You are about to <span class="text-red-600 font-bold uppercase">Permanently Delete</span> 
                    <span class="font-bold text-gray-900">{{ selectedTenant?.company_name }}</span>.
                    All employee records, payroll history, and files will be erased from our servers immediately.
                </p>
                
                <div class="mt-6 bg-red-50 p-4 rounded-xl border border-red-100">
                    <InputLabel value="To verify, type the company name below:" class="text-red-800 font-bold mb-2" />
                    <TextInput 
                        v-model="confirmationInput"
                        type="text"
                        class="block w-full border-red-200 focus:ring-red-500 focus:border-red-500"
                        :placeholder="selectedTenant?.company_name"
                    />
                </div>

                <div class="mt-8 flex justify-end gap-4">
                    <button @click="closeModal" class="text-sm font-semibold text-gray-500 hover:text-gray-700">Go Back</button>
                    <button 
                        @click="hardDeleteTenant"
                        :disabled="confirmationInput !== selectedTenant?.company_name"
                        class="px-8 py-3 bg-red-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-red-200 disabled:opacity-30 disabled:cursor-not-allowed transition-all hover:bg-red-700"
                    >
                        Delete Everything Forever
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>