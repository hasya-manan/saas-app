<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Trash2, RotateCcw, Edit2, ShieldAlert, X, Archive } from 'lucide-vue-next';
import GlobalFilter from '@/Components/GlobalFilter.vue';

const props = defineProps({
    tenants: Object,        // Changed from Array to Object
    deletedTenants: Object,  // Changed from Array to Object
    filters: Object,
    statusOptions: Array,
});

// --- State Management ---
const currentTab = ref('active');
const confirmingDeletion = ref(false);
const selectedTenant = ref(null);
const confirmationInput = ref('');
const isEditPanelOpen = ref(false);
const isModalOpen = ref(false);
const tenantToArchive = ref(null);


// --- The Form Helper ---
const form = useForm({
    id: null,
    company_name: '',
    company_email: '',
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
    form.company_email = tenant.email;
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
    tenantToArchive.value = tenant; // Save the tenant details
    isModalOpen.value = true;
};


const executeDelete = () => {
    router.delete(route('tenants.destroy', tenantToArchive.value.id), {
        onSuccess: () => {
            isModalOpen.value = false;
            // Your AuthenticatedLayout watcher will trigger the Toast automatically!
        },
    });
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

        <div class="py-12 mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="mb-8">
                <div class="flex gap-6 mb-6 border-b border-gray-200">
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

                <GlobalFilter routeName="tenants.list" :filters="filters" dataKey="tenants"
                    :status-options="statusOptions" :show-role="false" :show-tenant="false" :show-status="true"
                    placeholder="Search companies by name or email..." />
            </div>

            <div class="flex flex-col lg:flex-row items-start gap-6">

                <div :class="[isEditPanelOpen ? 'lg:w-[60%] w-full' : 'w-full']"
                    class="transition-all duration-500 order-2 lg:order-1">
                    <div
                        class="bg-white shadow-xl shadow-primary/5 border border-primary-border rounded-[2.5rem] overflow-hidden">
                        <div class="overflow-x-auto p-4 lg:p-8">
                            <table class="w-full text-left border-separate border-spacing-y-2">
                                <thead class="bg-primary-light/10">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                            Company</th>
                                        <th
                                            class="hidden md:table-cell px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                            Email</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">
                                            Status</th>
                                        <th
                                            class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-widest">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <tr v-for="tenant in displayList" :key="tenant.id"
                                        class="group hover:bg-primary-light/5 transition-all duration-300">
                                        <td
                                            class="px-6 py-4 rounded-l-2xl border-y border-l border-transparent group-hover:border-primary-border">
                                            <div class="text-sm font-bold text-gray-900">{{ tenant.company_name }}</div>
                                            <div class="text-[10px] text-gray-400">ID: {{ tenant.id }}</div>
                                        </td>
                                        <td
                                            class="hidden md:table-cell px-6 py-4 text-sm text-gray-600 border-y border-transparent group-hover:border-primary-border">
                                            {{ tenant.email }}
                                        </td>
                                        <td
                                            class="px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                            <StatusBadge v-if="currentTab === 'trash'" status="archived" />
                                            <StatusBadge v-else :status="tenant.status" />
                                        </td>
                                        <td
                                            class="px-6 py-4 text-right rounded-r-2xl border-y border-r border-transparent group-hover:border-primary-border">
                                            <div class="flex justify-end gap-2">

                                                <template v-if="currentTab === 'active'">
                                                    <BaseButton variant="outline" size="sm"
                                                        @click="openEditPanel(tenant)">
                                                        <Edit2 :size="14" />
                                                        <span class="hidden lg:inline ml-1">Edit</span>
                                                    </BaseButton>

                                                    <BaseButton variant="warning" size="sm"
                                                        @click="softDeleteTenant(tenant)">
                                                        <Archive :size="14" />
                                                        <span class="hidden lg:inline ml-1">Archive</span>
                                                    </BaseButton>
                                                </template>

                                                <template v-else>
                                                    <BaseButton variant="outline" size="sm"
                                                        @click="restoreTenant(tenant)">
                                                        <RotateCcw :size="14" />
                                                        <span class="hidden lg:inline ml-1">Restore</span>
                                                    </BaseButton>

                                                    <BaseButton variant="danger" size="sm"
                                                        @click="openHardDeleteModal(tenant)">
                                                        <Trash2 :size="14" />
                                                        <span class="hidden lg:inline ml-1">Delete</span>
                                                    </BaseButton>
                                                </template>

                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="bg-gray-50/50 border-t border-gray-100 px-8">
                            <Pagination v-if="currentTab === 'active'" :links="tenants.links"
                                :meta="{ from: tenants.from, to: tenants.to, total: tenants.total }" />
                            <Pagination v-else :links="deletedTenants.links"
                                :meta="{ from: deletedTenants.from, to: deletedTenants.to, total: deletedTenants.total }" />
                        </div>
                    </div>
                </div>

                <div v-if="isEditPanelOpen"
                    class="w-full lg:w-[40%] sticky top-6 z-10 animate-in slide-in-from-right duration-500 order-1 lg:order-2">
                    <div class="bg-white border border-primary/10 rounded-[2.5rem] shadow-xl p-8">
                        <div class="flex items-center justify-between mb-8">
                            <h2 class="text-xl font-bold text-gray-800">Edit Company</h2>
                            <button @click="closeEditPanel"
                                class="p-2 -mr-2 text-gray-400 hover:bg-gray-100 rounded-full transition-colors">
                                <X :size="20" />
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Company
                                    Name</label>
                                <input v-model="form.company_name" type="text"
                                    class="w-full rounded-xl border-gray-200 focus:ring-primary p-3"
                                    :class="{ 'border-red-500': form.errors.company_name }" />
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Contact
                                    Email</label>
                                <input v-model="form.company_email" type="email"
                                    class="w-full rounded-xl border-gray-200 focus:ring-primary p-3"
                                    :class="{ 'border-red-500': form.errors.email }" />
                            </div>

                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Status</label>
                                <select v-model="form.status"
                                    class="w-full rounded-xl border-gray-200 focus:ring-primary p-3 bg-white">
                                    <option v-for="option in statusOptions" :key="option.key" :value="option.key">{{
                                        option.label }}</option>
                                </select>
                            </div>

                            <div class="flex flex-col sm:flex-row justify-end gap-3 pt-6 border-t border-gray-50">
                                <BaseButton variant="outline" class="flex-1" @click="closeEditPanel">Cancel</BaseButton>
                                <BaseButton variant="primary" class="flex-1" :disabled="form.processing"
                                    @click="submitUpdate">
                                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
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

        <!--Modal:: Soft Delete -->
        <ConfirmModal :show="isModalOpen" title="Archive Tenant"
            :message="`Are you sure you want to archive ${tenantToArchive?.company_name}? This will disable their access.`"
            confirmText="Yes, Archive" @close="isModalOpen = false" @confirm="executeDelete" />
    </AuthenticatedLayout>


</template>