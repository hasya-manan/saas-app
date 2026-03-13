<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Trash2, RotateCcw, Edit2, ShieldAlert, X } from 'lucide-vue-next';

const props = defineProps({
    tenants: Object,        // Changed from Array to Object
    deletedTenants: Object,  // Changed from Array to Object
    statusOptions: Array,
});

// --- State Management ---
const currentTab = ref('active');
const confirmingDeletion = ref(false);
const selectedTenant = ref(null);
const confirmationInput = ref('');
const isEditPanelOpen = ref(false);


// --- The Form Helper ---
const form = useForm({
    id: null,
    company_name: '',
    email: '',
    status: '',
});

const displayList = computed(() => {
    return currentTab.value === 'active'
        ? props.tenants.data
        : props.deletedTenants.data;
});

// --- Actions ---

const openEditPanel = (tenant) => {
    // Fill the form helper with the clicked tenant's data
    form.id = tenant.id;
    form.company_name = tenant.company_name;
    form.email = tenant.email;
    form.status = tenant.status;

    isEditPanelOpen.value = true;
};
const closeEditPanel = () => {
    isEditPanelOpen.value = false;
    form.reset(); // Clears inputs and any error messages
    form.clearErrors();
};

const submitUpdate = () => {
    // Note: form.put handles the processing state and errors for you
    form.put(route('tenants.update', form.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeEditPanel();
            console.log('Update successful');
        },
        onError: (errors) => {
            console.error('Update failed:', errors);
        },
    });
};
// --- Delete/Restore Logic ---
const softDeleteTenant = (tenant) => {
    if (confirm(`Archive ${tenant.company_name}?`)) {
        router.delete(route('tenants.destroy', tenant.id));
    }
};

const restoreTenant = (tenant) => {
    router.put(route('tenants.restore', tenant.id));
};

const openHardDeleteModal = (tenant) => {
    selectedTenant.value = tenant;
    confirmingDeletion.value = true;
};

const hardDeleteTenant = () => {
    router.delete(route('tenants.force-delete', selectedTenant.value.id), {
        onSuccess: () => closeModal(),
    });
};

const closeModal = () => {
    confirmingDeletion.value = false;
    confirmationInput.value = '';
    selectedTenant.value = null;
};
//split 40 :60

// const openEditPanel = (tenant) => {
//     // We use { ... } to photocopy the data so the table doesn't move while we type
//     editingTenant.value = { ...tenant };
//     isEditPanelOpen.value = true;
// };

// 3. The function to handle closing
// const closeEditPanel = () => {
//     isEditPanelOpen.value = false;
//     editingTenant.value = null; // Clear the memory
// };


</script>

<template>

    <Head title="Manage Companies" />

    <AuthenticatedLayout>
        <template #header>
            <!--Button on board-->
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Manage Companies</h2>
                    <p class="text-sm text-gray-500">Overview of all client workspaces</p>
                </div>
                <Link :href="route('tenants.create')"
                    class="bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm shadow-primary/20">
                    + Onboard New
                </Link>
            </div>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex gap-6 mb-6 border-b border-gray-100">
                <button @click="currentTab = 'active'"
                    :class="currentTab === 'active' ? 'border-b-2 border-primary text-primary' : 'text-gray-400 hover:text-gray-600'"
                    class="pb-4 px-2 font-bold text-sm transition-all">
                    Active ({{ tenants.total ?? 0 }})
                </button>
                <button @click="currentTab = 'trash'"
                    :class="currentTab === 'trash' ? 'border-b-2 border-primary text-primary' : 'text-gray-400 hover:text-gray-600'"
                    class="pb-4 px-2 font-bold text-sm transition-all flex items-center gap-2">
                    Trash ({{ deletedTenants.total ?? 0 }})
                </button>
            </div>

            <div class="flex flex-col lg:flex-row items-start gap-6">
                <div :class="[isEditPanelOpen ? 'lg:w-[60%] w-full' : 'w-full']"
                    class="transition-all duration-500 order-2 lg:order-1">
                    <div
                        class="bg-white overflow-x-auto shadow-sm border border-primary-border/20 rounded-xl lg:rounded-2xl">
                        <table class="min-w-full divide-y divide-gray-100">
                            <thead class="bg-primary-light/20">
                                <tr>
                                    <th
                                        class="px-4 lg:px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                        Company</th>
                                    <th
                                        class="hidden md:table-cell px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                        Admin Email</th>
                                    <th
                                        class="px-4 lg:px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                        Status</th>
                                    <th
                                        class="px-4 lg:px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-widest">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                <tr v-for="tenant in displayList" :key="tenant.id"
                                    class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-4 lg:px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ tenant.company_name }}</div>
                                        <div
                                            class="text-[10px] text-gray-400 truncate max-w-[100px] lg:max-w-none lg:text-xs">
                                            ID: {{ tenant.id }}</div>
                                    </td>
                                    <td class="hidden md:table-cell px-6 py-4 text-sm text-gray-600">{{ tenant.email }}
                                    </td>
                                    <td class="px-4 lg:px-6 py-4">
                                        <StatusBadge v-if="currentTab === 'trash'" status="archived" />

                                        <StatusBadge v-else :status="tenant.status" />
                                    </td>
                                    <td class="px-4 lg:px-6 py-4 text-right">
                                        <div class="flex justify-end gap-1 lg:gap-2">
                                            <template v-if="currentTab === 'active'">
                                                <BaseButton variant="outline" size="sm" @click="openEditPanel(tenant)"
                                                    class="p-2 lg:px-3">
                                                    <Edit2 :size="14" /><span class="hidden lg:inline ml-1">Edit</span>
                                                </BaseButton>
                                                <BaseButton @click="softDeleteTenant(tenant)" variant="danger" size="sm"
                                                    class="p-2 lg:px-3">
                                                    <Trash2 :size="14" /><span
                                                        class="hidden lg:inline ml-1">Archive</span>
                                                </BaseButton>
                                            </template>
                                            <template v-else>
                                                <BaseButton @click="restoreTenant(tenant)" variant="primary" size="sm"
                                                    class="p-2 lg:px-3">
                                                    <RotateCcw :size="14" /><span
                                                        class="hidden lg:inline ml-1">Restore</span>
                                                </BaseButton>
                                                <BaseButton @click="openHardDeleteModal(tenant)" variant="danger"
                                                    size="sm" class="p-2 lg:px-3">
                                                    <Trash2 :size="14" /><span
                                                        class="hidden lg:inline ml-1">Delete</span>
                                                </BaseButton>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <!--pagination-->
                        <div class="bg-white border-t border-gray-100">
                            <Pagination v-if="currentTab === 'active'" :links="tenants.links"
                                :meta="{ from: tenants.from, to: tenants.to, total: tenants.total }" />
                            <Pagination v-else :links="deletedTenants.links"
                                :meta="{ from: deletedTenants.from, to: deletedTenants.to, total: deletedTenants.total }" />
                        </div>
                    </div>
                </div>

                <div v-if="isEditPanelOpen"
                    class="w-full lg:w-[40%] sticky top-0 lg:top-6 z-10 animate-in slide-in-from-top lg:slide-in-from-right duration-500 order-1 lg:order-2">
                    <div class="bg-white border border-primary/10 rounded-xl lg:rounded-2xl shadow-lg p-5 lg:p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-bold text-gray-800">Edit Company</h2>
                            <button @click="closeEditPanel" class="p-2 -mr-2 text-gray-400 hover:text-gray-600">
                                <X :size="20" />
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Company
                                    Name</label>
                                <input v-model="form.company_name" type="text"
                                    class="w-full rounded-lg border-gray-200 focus:ring-primary text-sm p-2.5"
                                    :class="{ 'border-red-500': form.errors.company_name }" />
                                <p v-if="form.errors.company_name" class="text-xs text-red-500 mt-1">{{
                                    form.errors.company_name
                                    }}</p>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Admin
                                    Email</label>
                                <input v-model="form.email" type="email"
                                    class="w-full rounded-lg border-gray-200 focus:ring-primary text-sm p-2.5"
                                    :class="{ 'border-red-500': form.errors.email }" />
                                <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">
                                    Status</label>
                                <select v-model="form.status"
                                    class="w-full rounded-lg border-gray-200 focus:ring-primary text-sm p-2.5 bg-white"
                                    :class="{ 'border-red-500': form.errors.status }">
                                    <option v-for="option in statusOptions" :key="option.key" :value="option.key">
                                        {{ option.label }}
                                    </option>
                                </select>
                                <p v-if="form.errors.status" class="text-xs text-red-500 mt-1">{{ form.errors.status }}
                                </p>
                            </div>

                            <div class="flex flex-col sm:flex-row justify-end gap-2 pt-4 border-t border-gray-50 mt-4">
                                <BaseButton variant="outline" class="w-full sm:w-auto" @click="closeEditPanel">Cancel
                                </BaseButton>
                                <BaseButton variant="primary" class="w-full sm:w-auto" :disabled="processing"
                                    @click="submitUpdate">
                                    <template v-if="processing">
                                        Saving...
                                    </template>
                                    <template v-else>
                                        Save Changes
                                    </template>
                                </BaseButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Modal::Delete forever-->
        <Modal :show="confirmingDeletion" @close="closeModal">
            <div class="p-8">
                <div class="flex items-center gap-3 text-red-600 mb-4">
                    <ShieldAlert :size="28" />
                    <h2 class="text-xl font-bold">Final Confirmation Required</h2>
                </div>

                <p class="text-sm text-gray-500 leading-relaxed">
                    You are about to <span class="text-red-600 font-bold uppercase">Permanently Delete</span>&nbsp;
                    <span class="font-bold text-gray-900">{{ selectedTenant?.company_name }}</span>.
                    All employee records, payroll history, and files will be erased from our servers immediately.
                </p>

                <div class="mt-6 bg-red-50 p-4 rounded-xl border border-red-100">
                    <InputLabel value="To verify, type the company name below:" class="text-red-800 font-bold mb-2" />
                    <TextInput v-model="confirmationInput" type="text"
                        class="block w-full border-red-200 focus:ring-red-500 focus:border-red-500"
                        :placeholder="selectedTenant?.company_name" />
                </div>

                <div class="mt-8 flex justify-end gap-4">
                    <button @click="closeModal" class="text-sm font-semibold text-gray-500 hover:text-gray-700">
                        Go Back
                    </button>
                    <button @click="hardDeleteTenant" :disabled="confirmationInput !== selectedTenant?.company_name"
                        class="px-8 py-3 bg-red-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-red-200 disabled:opacity-30 disabled:cursor-not-allowed transition-all hover:bg-red-700">
                        Delete Everything Forever
                    </button>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>


</template>