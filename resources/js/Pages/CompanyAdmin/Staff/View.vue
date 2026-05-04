<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PageHeader from '@/Components/PageHeader.vue';
import RegistrationWizard from '@/Components/RegistrationWizard.vue'; // Import your component
import { ref } from 'vue';

const props = defineProps({
    user: Object
});

const currentStep = ref(1);

// Define steps for the Profile View
const steps = [
    { id: 1, title: 'Personal Info', desc: 'Identity & Contact' },
    { id: 2, title: 'Address & Identity', desc: 'Location & Staff ID' },
    { id: 3, title: 'Employment', desc: 'Roles & Department' },
];
</script>

<template>
    <Head title="Staff Profile" />

    <AuthenticatedLayout>
          <template #header>
            <PageHeader :back-route="route('admin_company.users.index')" title="Staff Profile"
                subtitle="Management for this staff member" />
        </template>
        
        <!-- Use a flex layout to hold the sidebar and the content -->
        <div class="flex flex-col lg:flex-row min-h-screen bg-white">
            
            <!-- Sidebar Wizard Navigation -->
            <RegistrationWizard 
                v-model:currentStep="currentStep" 
                :steps="steps" 
                :validationSchema="{}" 
            >
                <template #title>Profile Detail</template>
            </RegistrationWizard>

            <!-- Main Content Area -->
            <main class="flex-1">
                

                <div class="p-6 lg:p-12 max-w-4xl space-y-8">
                    
                    <!-- 1. Top Header Card (Always visible or part of Step 1) -->
                    <div class="bg-white border border-primary-border rounded-[2.5rem] p-8 flex flex-col md:flex-row items-center justify-between shadow-xl shadow-primary/5">
                        <div class="flex items-center gap-6">
                            <div class="h-24 w-24 rounded-[1.5rem] bg-primary-light border-2 border-primary-border flex items-center justify-center overflow-hidden">
                                <span class="text-primary text-3xl font-bold">{{ user?.name?.charAt(0) || 'S' }}</span>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 leading-tight">{{ user?.name || 'Staff Member' }}</h2>
                                <div class="mt-1">
                                    <span class="px-3 py-1 text-[10px] font-black rounded-full uppercase bg-blue-50 text-blue-600 border border-blue-100">
                                        {{ user?.role?.display_name || 'Technical Staff' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Dynamic Content Based on Wizard Step -->
                    <transition name="fade" mode="out-in">
                        <div :key="currentStep">
                            
                            <!-- Step 1: Personal Information -->
                            <div v-if="currentStep === 1" class="bg-white border border-primary-border rounded-[2.5rem] p-10 shadow-xl shadow-primary/5">
                                <div class="flex justify-between items-center mb-10">
                                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Personal Information</h3>
                                    <button class="px-5 py-2 border border-primary-border text-primary font-bold rounded-2xl hover:bg-primary-light transition-all">
                                        Edit <span class="text-xs">✎</span>
                                    </button>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-16">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Full Name</label>
                                        <p class="text-gray-900 font-semibold text-lg">{{ user?.name || '-' }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Email Address</label>
                                        <p class="text-gray-900 font-semibold text-lg">{{ user?.email || '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Address & Identity -->
                            <div v-if="currentStep === 2" class="bg-white border border-primary-border rounded-[2.5rem] p-10 shadow-xl shadow-primary/5">
                                <div class="flex justify-between items-center mb-10">
                                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Address & Identity</h3>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-16">
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Country</label>
                                        <p class="text-gray-900 font-semibold text-lg">Malaysia</p>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Staff ID</label>
                                        <p class="text-gray-900 font-semibold text-lg">STAFF-{{ user?.id || '001' }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </transition>
                </div>
            </main>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.2s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
</style>