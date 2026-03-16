<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import GlobalFilter from '@/Components/GlobalFilter.vue';

defineProps({
    users: Object,
    filters: Object,    
    tenants: Array,     
    roles: Array,
    filters: Object,
});
</script>

<template>

    <Head title="Platform Users" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold leading-tight text-gray-800">
                Global User Directory
            </h2>
            <p class="text-sm text-gray-500">Overview of all active accounts across tenants</p>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div
                    class="bg-white overflow-hidden shadow-xl shadow-primary/5 border border-primary-border rounded-[2.5rem] p-8">

                    <div class="mb-6 flex justify-between items-center px-2">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Total Users</h3>

                        </div>
                    </div>
                    <!--filtering-->
                    <GlobalFilter routeName="users.list" :filters="filters" dataKey="users" :roles="roles" :tenants="tenants"
                        :showRole="true" :showTenant="true"  placeholder="Search users..." />

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
                                                <div class="text-xs text-gray-400 font-medium">{{ user.email }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-gray-700">
                                                {{ user.tenant?.company_name || 'Individual' }}
                                            </span>
                                            <span class="text-[10px] text-gray-400  tracking-widest">
                                                ID: {{ user.tenant_id }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                        <span :class="{
                                            'bg-primary text-white': user.role?.name === 'admin_company',
                                            'bg-pink-100 text-pink-700': user.role?.name === 'Staff'
                                        }"
                                            class="px-3 py-1 text-[10px] font-black rounded-full uppercase tracking-tighter">
                                            {{ user.role?.name }}
                                        </span>
                                    </td>

                                    <td
                                        class="px-6 py-4 rounded-r-2xl border-y border-r border-transparent group-hover:border-primary-border text-right">
                                        <!-- <Link :href="route('superadmin.users.edit', user.id)" class="text-primary hover:text-primary-dark font-bold text-xs underline underline-offset-4">
                                            Edit Access
                                        </Link> -->
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
        </div>
    </AuthenticatedLayout>
</template>