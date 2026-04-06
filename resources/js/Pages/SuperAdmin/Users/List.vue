<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import GlobalFilter from '@/Components/GlobalFilter.vue';
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Edit2, X } from 'lucide-vue-next';
import CreateUserModal from '@/Components/CreateUserModal.vue';
const props = defineProps({
    users: Object,
    tenants: Array,
    roles: Array,
    filters: Object,
});

// State side-by-side
const isEditPanelOpen = ref(false);
const selectedUser = ref(null);
const isCreateModalOpen = ref(false);

const form = useForm({
    id: null,
    name: '',
    email: '',
    role_id: '',
    tenant_id: null,
});

const openEditPanel = (user) => {
    // Safety check to prevent the "undefined" error
    if (!user) return;

    selectedUser.value = user;
    isEditPanelOpen.value = true;
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;

    form.company_name = user.tenant?.company_name || '';
    form.role_id = user.role_id || '';
};
const closeEditPanel = () => {
    isEditPanelOpen.value = false;
    selectedUser.value = null;
    form.reset();
};

// Added the missing submit function
const submitUpdate = () => {
    // route('users.update', form.id) generates: /users/5
    // console.log("Updating user with ID:", form.id);

    if (!form.id) {
        // console.error("Error: form.id is missing!");
        return;
    }
    form.put(route('superadmin.users.update', form.id), {
    preserveState: true,  // Keeps your form open so you can see errors
    preserveScroll: true,
    onSuccess: () => {
        // console.log("Success! Role is now:", form.role_id);
        closeEditPanel();
    },
    onError: (errors) => {
        // If it redirects to the landing page, look here!
        // console.log("Backend said NO:", errors);
    }
});
};


</script>

<template>

    <Head title="Platform Users" />

    <AuthenticatedLayout>
        <!--TODO:: make a button to become a function.-->
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Global User Directory</h2>
                    <p class="text-sm text-gray-500">Overview of all active accounts across tenants</p>
                </div>
                <button @click="isCreateModalOpen = true" type="button"
                    class="bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm shadow-primary/20">
                    + New User
                </button>
            </div>
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8">
            <GlobalFilter routeName="users.list" :filters="filters" dataKey="users" :roles="roles" :tenants="tenants"
                :showRole="true" :showTenant="true" placeholder="Search users..." />
            <div class="flex flex-col lg:flex-row items-start gap-6">

                <div :class="[isEditPanelOpen ? 'lg:w-[60%] w-full' : 'w-full']"
                    class="transition-all duration-500 order-2 lg:order-1">

                    <div
                        class="bg-white overflow-hidden shadow-xl shadow-primary/5 border border-primary-border rounded-[2.5rem] p-8">
                        <div class="mb-6 flex justify-between items-center px-2">
                            <h3 class="text-lg font-bold text-gray-900">Total Users</h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-separate border-spacing-y-2">
                                <thead>
                                    <tr class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">
                                        <th class="px-6 py-3">Full Name</th>
                                        <th class="px-6 py-3">Tenant / Company</th>
                                        <th class="px-6 py-3">Role</th>
                                        <th class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in users.data" :key="user.id"
                                        class="group bg-white hover:bg-primary-light/10 transition-all duration-300">

                                        <td
                                            class="px-6 py-4 rounded-l-2xl border-y border-l border-transparent group-hover:border-primary-border">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-bold">
                                                    {{ user.name ? user.name.charAt(0) : '?' }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900">{{ user.name }}</div>
                                                    <div class="text-xs text-gray-400 font-medium">{{ user.email }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td
                                            class="px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold text-gray-700">
                                                    {{ user.tenant?.company_name || 'System Internal' }}
                                                </span>

                                                
                                               <div class="flex flex-col items-start gap-1">
                                                    <span
                                                        class="text-[10px] text-gray-400 font-medium tracking-widest uppercase">
                                                        ID: {{ user.tenant_id }}
                                                    </span>

                                                    <span v-if="user.tenant?.deleted_at"
                                                        class="px-2 py-0.5 rounded-full bg-red-50 text-red-500 text-[8px] font-black uppercase border border-red-100">
                                                        Archived Company
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td
                                            class="px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                            <span
                                                :class="user.role?.name === 'admin_company' ? 'bg-primary text-white' : 'bg-pink-100 text-pink-700'"
                                                class="px-3 py-1 text-[10px] font-black rounded-full uppercase">
                                                {{ user.role?.name }}
                                            </span>
                                        </td>

                                        <td
                                            class="px-6 py-4 rounded-r-2xl border-y border-r border-transparent group-hover:border-primary-border text-right">

                                            <BaseButton variant="outline" size="sm" @click="openEditPanel(user)"
                                                class="p-2 lg:px-3">
                                                <Edit2 :size="14" /><span class="hidden lg:inline ml-1">Edit</span>
                                            </BaseButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-8">
                            <Pagination :links="users.links" />
                        </div>
                    </div>
                </div>

                <div v-if="isEditPanelOpen"
                    class="w-full lg:w-[40%] sticky top-6 z-10 animate-in slide-in-from-right duration-500 order-1 lg:order-2">

                    <div class="bg-white border border-primary/10 rounded-[2.5rem] shadow-xl p-8">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Edit User Access</h2>
                                <p class="text-xs text-gray-400 font-medium text-wrap">
                                    Updating permissions for {{ selectedUser?.name }}
                                </p>
                            </div>
                            <button @click="closeEditPanel"
                                class="p-2 bg-gray-50 rounded-xl text-gray-400 hover:text-gray-600 transition-colors">
                                <X :size="20" />
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">User
                                    Name</label>
                                <input v-model="form.name" type="text"
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4"
                                    placeholder="Enter user name">
                                <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">User
                                    Email</label>
                                <input v-model="form.email" type="text"
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Assign
                                    Role</label>
                                <select v-model.number="form.role_id"
                                    class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4">
                                    <option v-for="r in roles" :key="r.id" :value="r.id">
                                        {{ r.name }}
                                    </option>
                                </select>
                            </div>

                            <button @click="submitUpdate" :disabled="form.processing"
                                class="w-full bg-primary text-white py-4 rounded-[1.5rem] font-bold shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all">
                                {{ form.processing ? 'Saving...' : 'Update Permissions' }}
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--modal-->
        <CreateUserModal :show="isCreateModalOpen" title="Add New User"
            description="Create an account and assign a user." :tenants="tenants" :roles="roles"
            @close="isCreateModalOpen = false" />

    </AuthenticatedLayout>
</template>