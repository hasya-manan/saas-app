<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '@/Components/PageHeader.vue';

// Assuming you pass the staff data as a prop from your Laravel controller
const props = defineProps({
    user: Object
});
</script>

<template>
    <Head title="Staff Profile" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader :back-route="route('admin_company.users.index')" title="Staff Profile" subtitle="Detailed information and management for this staff member">
                <template #actions>
                    
                </template>
            </PageHeader>
        </template>

       <div class="py-12 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- 1. Top Header Card (Avatar & Basic Info) -->
            <div
                class="bg-white border border-primary-border rounded-[2.5rem] p-8 flex flex-col md:flex-row items-center justify-between shadow-xl shadow-primary/5">
                <div class="flex items-center gap-6">
                    <div
                        class="h-24 w-24 rounded-[1.5rem] bg-primary-light border-2 border-primary-border flex items-center justify-center overflow-hidden">
                        <!-- Staff Initial Placeholder -->
                        <span class="text-primary text-3xl font-bold">{{ user?.name?.charAt(0) || 'S' }}</span>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 leading-tight">{{ user?.name || 'Staff Member' }}
                        </h2>
                        <div class="mt-1">
                            <span
                                class="px-3 py-1 text-[10px] font-black rounded-full uppercase bg-blue-50 text-blue-600 border border-blue-100">
                                {{ user?.role_name || 'Technical Staff' }}
                            </span>
                        </div>
                        <p class="text-gray-400 text-xs font-medium mt-2 tracking-wide">Kuala Lumpur, Malaysia</p>
                    </div>
                </div>
                <button
                    class="mt-4 md:mt-0 px-6 py-2.5 border border-primary-border text-primary font-bold rounded-2xl hover:bg-primary-light transition-all active:scale-95 flex items-center gap-2">
                    Edit Profile <span class="text-xs">✎</span>
                </button>
            </div>

            <!-- 2. Personal Information Card -->
            <div class="bg-white border border-primary-border rounded-[2.5rem] p-10 shadow-xl shadow-primary/5">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Personal Information</h3>
                    <button
                        class="px-5 py-2 border border-primary-border text-primary font-bold rounded-2xl hover:bg-primary-light transition-all active:scale-95 flex items-center gap-2">
                        Edit <span class="text-xs">✎</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-16">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Full Name</label>
                        <p class="text-gray-900 font-semibold text-lg">{{ user?.name || '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Email
                            Address</label>
                        <p class="text-gray-900 font-semibold text-lg">{{ user?.email || '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Phone</label>
                        <p class="text-gray-900 font-semibold text-lg">{{ user?.phone || '+60 12-345 6789' }}</p>
                    </div>
                    <div class="space-y-1">
                        <label
                            class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Department</label>
                        <p class="text-gray-900 font-semibold text-lg">Engineering</p>
                    </div>
                </div>
            </div>

            <!-- 3. Address / Work Details Card -->
            <div class="bg-white border border-primary-border rounded-[2.5rem] p-10 shadow-xl shadow-primary/5">
                <div class="flex justify-between items-center mb-10">
                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Address & Identity</h3>
                    <button
                        class="px-5 py-2 border border-primary-border text-primary font-bold rounded-2xl hover:bg-primary-light transition-all active:scale-95 flex items-center gap-2">
                        Edit <span class="text-xs">✎</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-16">
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Country</label>
                        <p class="text-gray-900 font-semibold text-lg">Malaysia</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">City /
                            State</label>
                        <p class="text-gray-900 font-semibold text-lg">Kuala Lumpur</p>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Staff ID</label>
                        <p class="text-gray-900 font-semibold text-lg">STAFF-{{ user?.id || '001' }}</p>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>