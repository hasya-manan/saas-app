<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import GlobalFilter from '@/Components/GlobalFilter.vue';
import { ref } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { Edit2, X, UserCheck, UserX, Eye } from 'lucide-vue-next';
import BaseButton from '@/Components/BaseButton.vue';

const props = defineProps({
    employees: Object, // Changed from 'users' to match your controller
    roles: Array,
    filters: Object,
});

const isEditPanelOpen = ref(false);
const selectedUser = ref(null);

const form = useForm({
    id: null,
    name: '',
    email: '',
    role_id: '',
});

const openEditPanel = (user) => {
    if (!user) return;
    selectedUser.value = user;
    isEditPanelOpen.value = true;
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
    form.role_id = user.role_id || '';
};

const closeEditPanel = () => {
    isEditPanelOpen.value = false;
    selectedUser.value = null;
    form.reset();
};

const submitUpdate = () => {
    form.put(route('admin_company.users.update', form.id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => closeEditPanel(),
    });
};
</script>

<template>
    <Head title="Staff Directory" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">Staff Directory</h2>
                    <p class="text-sm text-gray-500">Manage your company's team members and access levels</p>
                </div>
                <Link :href="route('admin_company.users.create')"
                    class="bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm shadow-primary/20">
                    + Add Staff Member
                </Link>
            </div>
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8">
            <GlobalFilter routeName="admin_company.users.index" :filters="filters" dataKey="employees" :roles="roles"
                :showRole="true" :showTenant="false" placeholder="Search staff by name or email..." />

            <div class="flex flex-col lg:flex-row items-start gap-6">
                
                <div :class="[isEditPanelOpen ? 'lg:w-[60%] w-full' : 'w-full']" class="transition-all duration-500">
                    <div class="bg-white overflow-hidden shadow-xl shadow-primary/5 border border-primary-border rounded-[2.5rem] p-8">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-separate border-spacing-y-2">
                                <thead>
                                    <tr class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">
                                        <th class="px-6 py-3">Employee</th>
                                        <th class="px-6 py-3">Role</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user in employees.data" :key="user.id"
                                        class="group bg-white hover:bg-primary-light/5 transition-all duration-300">
                                        
                                        <td class="px-6 py-4 rounded-l-2xl border-y border-l border-transparent group-hover:border-primary-border">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-xl bg-gray-100 flex items-center justify-center text-primary font-bold">
                                                    {{ user.name ? user.name.charAt(0) : '?' }}
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900">{{ user.name }}</div>
                                                    <div class="text-xs text-gray-400 font-medium">{{ user.email }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                            <span class="px-3 py-1 text-[10px] font-black rounded-full uppercase bg-blue-50 text-blue-600 border border-blue-100">
                                                {{ user.role?.name || 'Staff' }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                            <div v-if="!user.deleted_at" class="flex items-center text-green-600 text-xs font-bold uppercase tracking-wider">
                                                <UserCheck :size="14" class="mr-1"/> Active
                                            </div>
                                            <div v-else class="flex items-center text-red-400 text-xs font-bold uppercase tracking-wider">
                                                <UserX :size="14" class="mr-1"/> Deactivated
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 rounded-r-2xl border-y border-r border-transparent group-hover:border-primary-border text-right space-x-2">
                                            <Link :href="route('admin_company.users.show', user.id)" 
                                                class="inline-flex items-center p-2 text-gray-400 hover:text-primary transition-colors">
                                                <Eye :size="18" />
                                            </Link>
                                            
                                            <BaseButton variant="outline" size="sm" @click="openEditPanel(user)" class="p-2">
                                                <Edit2 :size="14" />
                                            </BaseButton>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-8">
                            <Pagination :links="employees.links" />
                        </div>
                    </div>
                </div>

                <div v-if="isEditPanelOpen" class="w-full lg:w-[40%] sticky top-6 z-10 animate-in slide-in-from-right duration-500">
                    <div class="bg-white border border-primary/10 rounded-[2.5rem] shadow-xl p-8">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Quick Edit</h2>
                                <p class="text-xs text-gray-400 font-medium italic">Settings for {{ selectedUser?.name }}</p>
                            </div>
                            <button @click="closeEditPanel" class="p-2 bg-gray-50 rounded-xl text-gray-400 hover:text-gray-600">
                                <X :size="20" />
                            </button>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Full Name</label>
                                <input v-model="form.name" type="text" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Role Type</label>
                                <select v-model.number="form.role_id" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4">
                                    <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.name }}</option>
                                </select>
                            </div>

                            <button @click="submitUpdate" :disabled="form.processing"
                                class="w-full bg-primary text-white py-4 rounded-[1.5rem] font-bold shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all">
                                {{ form.processing ? 'Saving...' : 'Save Changes' }}
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>