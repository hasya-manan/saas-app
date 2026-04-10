<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RegistrationWizard from '@/Components/RegistrationWizard.vue';

const props = defineProps({
    roles: Array
});

const currentStep = ref(1);

const steps = [
    { id: 1, title: 'Account Setup', desc: 'Login & System Access' },
    { id: 2, title: 'Personal Information', desc: 'Identity & Contact' },
    { id: 3, title: 'Employment Details', desc: 'Role & Department' },
    { id: 4, title: 'Financial & Statutory', desc: 'Salary, EPF, SOCSO' },
];

const form = useForm({
    // Account
    email: '',
    role_id: '',
    // Personal
    full_name: '',
    ic_number: '',
    phone: '',
    // Add other fields as needed for later steps
});

/**
 * Validation Schema Logic
 * This tells the Wizard when to show the green checkmark
 */
const stepValidation = computed(() => ({
    1: form.email.includes('@') && form.role_id !== '',
    2: form.full_name.length > 3 && form.ic_number.length >= 12,
    3: false, // Pending implementation
    4: false  // Pending implementation
}));

const isLastStep = computed(() => currentStep.value === steps.length);

const nextStep = () => {
    if (currentStep.value < steps.length) currentStep.value++;
};

const prevStep = () => {
    if (currentStep.value > 1) currentStep.value--;
};
</script>

<template>
    <Head title="Add New Staff" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 tracking-tight">Staff Registration</h2>
                    <p class="text-sm text-gray-500">Onboard new team members and configure their professional profile</p>
                </div>
                
                <Link :href="route('admin_company.users.index')" class="text-sm font-semibold text-primary hover:text-primary-dark transition-colors">
                    &larr; Back to Directory
                </Link>
            </div>
        </template>

        <div class="flex min-h-[calc(100vh-200px)] bg-white rounded-2xl overflow-hidden shadow-sm border border-surface-100">
            
            <RegistrationWizard 
                v-model:currentStep="currentStep" 
                :steps="steps" 
                :validationSchema="stepValidation"
            >
                <template #title>New Staff</template>
            </RegistrationWizard>

            <main class="flex-1 bg-white flex flex-col">
                <div class="p-12 max-w-2xl mx-auto w-full flex-1">
                    <div class="mb-10">
                        <span class="text-xs font-bold text-primary tracking-widest uppercase">Step {{ currentStep }} of 4</span>
                        <h1 class="text-3xl font-extrabold text-slate-900 mt-1">{{ steps[currentStep-1].title }}</h1>
                    </div>

                    <div class="min-h-[300px]">
                    <!-- Account Setup -->
                        <div v-if="currentStep === 1" class="space-y-6 animate-fade-in">
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                                    <input v-model="form.email" type="email" 
                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all" 
                                        placeholder="e.g. staff@company.com">
                                </div>
                                <!--TODO:: need to add view icon eye the password field-->
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                                    <input v-model="form.password" type="password" 
                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all" 
                                        placeholder="e.g. need to have at least 8 characters">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">System Role</label>
                                    <select v-model="form.role_id" class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3 transition-all">
                                        <option value="" disabled selected>Select a role...</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.display_name }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- Personal Information -->
                       <div v-if="currentStep === 2" class="space-y-8 animate-fade-in">
    
                        <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name (as per
                                        IC)</label>
                                    <input v-model="form.full_name" type="text"
                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                        placeholder="e.g. Ahmad bin Ibrahim">
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">IC Number</label>
                                        <input v-model="form.ic_number" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                            placeholder="000308000000">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Phone
                                            Number</label>
                                        <input v-model="form.phone" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                            placeholder="0123456789">
                                    </div>
                                </div>
                        </div>

                    

                        <div class="pt-6 border-t border-slate-100">
                                <h3 class="text-xs font-bold text-primary uppercase tracking-widest mb-6">Permanent
                                    Address</h3>

                                <div class="grid grid-cols-1 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase">Address
                                            Line 1</label>
                                        <input v-model="form.address_line_1" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                            placeholder="Street address, P.O. box">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase">Address
                                            Line 2 (Optional)</label>
                                        <input v-model="form.address_line_2" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                            placeholder="Apartment, suite, unit, building, floor">
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label
                                                class="block text-[11px] font-bold text-slate-500 mb-1 uppercase">City</label>
                                            <input v-model="form.city" type="text"
                                                class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                                placeholder="Kota Bharu">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[11px] font-bold text-slate-500 mb-1 uppercase">Postcode</label>
                                            <input v-model="form.postcode" type="text"
                                                class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                                placeholder="15000">
                                        </div>
                                        <div>
                                            
                                           <div>
                                                <label
                                                    class="block text-[11px] font-bold text-slate-500 mb-1 uppercase">State</label>
                                                <select v-model="form.state"
                                                    class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3 bg-white transition-all">
                                                    <option value="" disabled>Select State</option>

                                                    <option v-for="state in states" :key="state.id" :value="state.name">
                                                        {{ state.name }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--employement details-->
                        <!--TODO:: need to update the UI / UX for this step-->
                         <div v-if="currentStep === 3" class="space-y-8 animate-fade-in">
    
                        <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name (as per
                                        IC)</label>
                                    <input v-model="form.full_name" type="text"
                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                        placeholder="e.g. Ahmad bin Ibrahim">
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">IC Number</label>
                                        <input v-model="form.ic_number" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                            placeholder="000308000000">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Phone
                                            Number</label>
                                        <input v-model="form.phone" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                            placeholder="0123456789">
                                    </div>
                                </div>
                        </div>

                    

                        <div class="pt-6 border-t border-slate-100">
                                <h3 class="text-xs font-bold text-primary uppercase tracking-widest mb-6">Permanent
                                    Address</h3>

                                <div class="grid grid-cols-1 gap-5">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase">Address
                                            Line 1</label>
                                        <input v-model="form.address_line_1" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                            placeholder="Street address, P.O. box">
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-500 mb-1 uppercase">Address
                                            Line 2 (Optional)</label>
                                        <input v-model="form.address_line_2" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                            placeholder="Apartment, suite, unit, building, floor">
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label
                                                class="block text-[11px] font-bold text-slate-500 mb-1 uppercase">City</label>
                                            <input v-model="form.city" type="text"
                                                class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                                placeholder="Kota Bharu">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-[11px] font-bold text-slate-500 mb-1 uppercase">Postcode</label>
                                            <input v-model="form.postcode" type="text"
                                                class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                                placeholder="15000">
                                        </div>
                                        <div>
                                            
                                           <div>
                                                <label
                                                    class="block text-[11px] font-bold text-slate-500 mb-1 uppercase">State</label>
                                                <select v-model="form.state"
                                                    class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3 bg-white transition-all">
                                                    <option value="" disabled>Select State</option>

                                                    <option v-for="state in states" :key="state.id" :value="state.name">
                                                        {{ state.name }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div v-if="currentStep > 2" class="animate-fade-in text-slate-400 italic flex items-center justify-center h-48 border-2 border-dashed border-slate-100 rounded-2xl">
                            Configuration for {{ steps[currentStep-1].title }} is in progress...
                        </div>
                    </div>

                    <div class="mt-16 flex items-center justify-end gap-4 border-t border-surface-100 pt-10 px-4 sm:px-0">
                        <button 
                            v-if="currentStep > 1" 
                            @click="prevStep"
                            class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-primary transition-colors active:scale-95"
                        >
                            Back
                        </button>

                        <button 
                            @click="nextStep"
                            :disabled="!stepValidation[currentStep]"
                            :class="[
                                'px-10 py-3 rounded-xl font-bold transition-all active:scale-95 shadow-lg',
                                stepValidation[currentStep] 
                                    ? 'bg-primary hover:bg-primary-dark text-white shadow-primary-light' 
                                    : 'bg-slate-200 text-slate-400 cursor-not-allowed shadow-none'
                            ]"
                        >
                            {{ isLastStep ? 'Complete Registration' : 'Next Step' }}
                        </button>
                    </div>
                </div>
            </main>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in {
    animation: fadeIn 0.4s ease-out;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>