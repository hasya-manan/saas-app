<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import PageHeader from '@/Components/PageHeader.vue';
import RegistrationWizard from '@/Components/RegistrationWizard.vue'; // Import your component
import { ref } from 'vue';
import {Pencil, Check, X} from 'lucide-vue-next';
import RoundedSelect from '@/Components/RoundedSelect.vue';
import BaseInput from '@/Components/BaseInput.vue';
import { useIcParser } from '@/Composables/useIcParser';
const props = defineProps({
    user: Object,
    roles: Array,
    departments: Array,
});

const currentStep = ref(1);

// Define steps for the Profile View
const steps = [
    { id: 1, title: 'Personal Information', desc: 'Identity & Contact' },
    { id: 2, title: 'Address', desc: 'Location' },
    { id: 3, title: 'Employment Details', desc: 'Roles & Department' },
    { id: 4, title: 'Financial & Statutory', desc: 'Salary, EPF, SOCSO' },
    { id: 5, title: 'Emergency Contact', desc: 'Contact Person' },

];

// Inertia Form for handling updates
const form = useForm({
    name: props.user?.name || '',

    //table user_profiles
    ic_number: props.user.profile?.ic_number || '',
    user_gender: props.user.profile?.user_gender || '',
    phone: props.user.profile?.phone || '',   
    marital_status: props.user.profile?.marital_status || '',
    join_date: props.user.profile?.join_date || '',
    dob: props.user.profile?.dob || '',

    //segment address
    address_line_1: props.user.profile?.address_line_1 || '',
    address_line_2: props.user.profile?.address_line_2 || '',
    city: props.user.profile?.city || '',
    postcode: props.user.profile?.postcode || '',
    state: props.user.profile?.state || '',
    waris_name: props.user.profile?.waris_name || '',
    waris_gender: props.user.profile?.waris_gender || '',
    waris_ic: props.user.profile?.waris_ic || '',
    waris_relationship: props.user.profile?.waris_relationship || '',
    waris_phone: props.user.profile?.waris_phone || '',

    //segment employment
    position: props.user.profile?.position || '',
    department_id: props.user?.department_id || '',
    
    // Add other fields for steps 2 and 3 here
});


// Tracking edit state for each segment independently
const editingSegment = ref({
    personal: false,    
    address: false,     
    employment: false,
    roles: false,
    financial: false,
    emergency: false 
});

const saveSegment = (segment) => {
    form.put(route('admin_company.users.update', props.user.uuid), {
        preserveScroll: true,
        onSuccess: () => {
            editingSegment.value[segment] = false;
        },
    });
};

// Function to cancel editing
const cancelEdit = (segment) => {
    editingSegment.value[segment] = false;
    form.reset(); // Reset form to original props
};

// Auto-fill Date of Birth (DOB) based on IC Number -> composables(useIcParser.js)
const { startWatchingIc } = useIcParser(form);
startWatchingIc();
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
                    <div
                        class="bg-white border border-primary-border rounded-[2.5rem] p-8 flex flex-col md:flex-row items-center justify-between shadow-xl shadow-primary/5">
                        <div class="flex items-center gap-6">
                            <div class="h-24 w-24 rounded-[1.5rem] bg-primary-light border-2 border-primary-border flex items-center justify-center overflow-hidden">
                                <span class="text-primary text-3xl font-bold">{{ user?.name?.charAt(0) || 'S' }}</span>
                            </div>
                           <div>
                             <h2 class="text-2xl font-bold text-gray-900 leading-tight">{{ user?.name || 'Staff Member' }}</h2>
                                <div class="mt-2 flex items-center gap-2">
                                <!-- System Role: High Contrast / Badge Style -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-[11px] font-bold tracking-wider uppercase bg-primary/10 text-primary border border-primary/20">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 4.946-2.597 9.181-6.5 11.5a11.954 11.954 0 01-6.5-11.5c0-.68.056-1.35.166-2.001z" clip-rule="evenodd"></path></svg>
                                        {{ user?.role?.display_name || 'Staff' }}
                                    </span>

                                    <!-- Separator Dot -->
                                    <span class="text-slate-300">•</span>

                                    <!-- Job Position: Soft Contrast / Text Style -->
                                    <span class="text-sm font-medium text-slate-500">
                                        {{ user.profile?.position || 'Position Not Set' }}
                                    </span>
                                </div>
                            </div>
                            </div>
                    </div>

                    <!-- 2. Dynamic Content Based on Wizard Step -->
                   <transition name="fade" mode="out-in">
                        <div :key="currentStep">

                            <!-- Step 1: Personal Information -->
                            <div v-if="currentStep === 1"
                                class="bg-white border border-primary-border rounded-[2.5rem] p-10 shadow-xl shadow-primary/5">
                                <div class="flex justify-between items-center mb-10">
                                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Personal Information</h3>

                                    <!-- Action Buttons with Transition -->
                                    <div class="flex gap-2">
                                        <transition name="fade" mode="out-in">
                                            <div :key="editingSegment.personal" class="flex gap-2">
                                                <template v-if="editingSegment.personal">
                                                    <button @click="cancelEdit('personal')"
                                                        class="px-4 py-2 text-slate-500 font-bold hover:bg-slate-50 rounded-2xl transition-all">
                                                        Cancel
                                                    </button>
                                                    <button @click="saveSegment('personal')" :disabled="form.processing"
                                                        class="px-5 py-2 bg-primary text-white font-bold rounded-2xl hover:bg-primary/90 transition-all flex items-center gap-2">
                                                        Save
                                                        <Check :size="16" />
                                                    </button>
                                                </template>
                                                <button v-else @click="editingSegment.personal = true"
                                                    class="px-5 py-2 border border-primary-border text-primary font-bold rounded-2xl hover:bg-primary-light transition-all flex items-center gap-2">
                                                    Edit
                                                    <Pencil :size="14" />
                                                </button>
                                            </div>
                                        </transition>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-16">
                                    <!-- Full Name -->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.personal" :key="'view-name'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user?.name || '-' }}
                                            </p>
                                            <div v-else>
                                                <BaseInput v-model="form.name" placeholder="Enter full name"
                                                    :error="form.errors.name" />
                                            </div>
                                        </transition>
                                    </div>
                                    <!-- Email Address (Username - Locked) -->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Email
                                            Address</label>
                                        <p class="text-gray-900 font-semibold text-md py-3">{{ user?.email || '-' }}</p>
                                        <transition name="fade">
                                            <span v-if="editingSegment.personal"
                                                class="text-[10px] text-slate-400 block -mt-2">
                                                Username cannot be changed
                                            </span>
                                        </transition>
                                    </div>
                                    <!--IC Number-->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">IC Number</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.personal" :key="'view-name'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.profile?.ic_number || 'Not Set' }}
                                            </p>
                                            <div v-else :key="'edit-name'">

                                                <BaseInput v-model="form.ic_number" placeholder="IC Number"
                                                    :error="form.errors.ic_number" />
                                            </div>
                                        </transition>
                                    </div>

                                    <!--dob-->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Date of Birth</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.personal" :key="'view-dob'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.profile?.dob || 'Not Set' }}
                                            </p>
                                            <div v-else :key="'edit-dob'">

                                                <BaseInput v-model="form.dob" placeholder="Date of Birth" type="date"
                                                    :error="form.errors.dob" />
                                            </div>
                                        </transition>
                                    </div>
                                    <!-- Gender  -->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Gender</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.personal" :key="'view-gender'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                <!-- Find label by comparing the key to the user's stored gender -->
                                                {{$page.props.lookups.genders.find(g => g.key ===
                                                    user.profile?.user_gender)?.label || 'Not Set'}}
                                            </p>
                                            <div v-else :key="'edit-gender'">
                                                <RoundedSelect v-model="form.user_gender" variant="form"
                                                    label="Select Gender" :options="$page.props.lookups.genders"
                                                    option-label="label" option-value="key" />
                                                <p v-if="form.errors.user_gender" class="text-red-500 text-xs mt-1">
                                                    {{ form.errors.user_gender }}
                                                </p>
                                            </div>
                                        </transition>
                                    </div>
                                    <!-- Phone Number -->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Phone
                                            Number</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.personal" :key="'view-name'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.profile?.phone || 'Not Set' }}
                                            </p>
                                            <div v-else :key="'edit-name'">

                                                <BaseInput v-model="form.phone" placeholder="0190909090"
                                                    :error="form.errors.phone" />
                                            </div>
                                        </transition>
                                    </div>
                                    <!--Marital Status-->
                                     <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Marital Status</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.personal" :key="'view-gender'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                <!-- Find label by comparing the key to the user's stored gender -->
                                                {{$page.props.lookups.genders.find(g => g.key ===
                                                    user.profile?.marital_status)?.label || 'Not Set'}}
                                            </p>
                                            <div v-else :key="'edit-marital'">
                                                <RoundedSelect v-model="form.marital_status" variant="form"
                                                    label="Select Marital Status" :options="$page.props.lookups.marital_statuses"
                                                    option-label="label" option-value="key" />
                                                <p v-if="form.errors.marital_status" class="text-red-500 text-xs mt-1">
                                                    {{ form.errors.marital_status }}
                                                </p>
                                            </div>
                                        </transition>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Address & Identity -->
                            <div v-if="currentStep === 2" class="bg-white border border-primary-border rounded-[2.5rem] p-10 shadow-xl shadow-primary/5">
                                <div class="flex justify-between items-center mb-10">
                                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Address </h3>
                                     <!-- Action Buttons step 2 -->
                                    <div class="flex gap-2">
                                        <transition name="fade" mode="out-in">
                                            <div :key="editingSegment.address" class="flex gap-2">
                                                <template v-if="editingSegment.address">
                                                    <button @click="cancelEdit('address')"
                                                        class="px-4 py-2 text-slate-500 font-bold hover:bg-slate-50 rounded-2xl transition-all">
                                                        Cancel
                                                    </button>
                                                    <button @click="saveSegment('address')" :disabled="form.processing"
                                                        class="px-5 py-2 bg-primary text-white font-bold rounded-2xl hover:bg-primary/90 transition-all flex items-center gap-2">
                                                        Save
                                                        <Check :size="16" />
                                                    </button>
                                                </template>
                                                <button v-else @click="editingSegment.address = true"
                                                    class="px-5 py-2 border border-primary-border text-primary font-bold rounded-2xl hover:bg-primary-light transition-all flex items-center gap-2">
                                                    Edit
                                                    <Pencil :size="14" />
                                                </button>
                                            </div>
                                        </transition>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-16">
                                    <!-- Address 1 -->
                                   <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Address 
                                            1</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.address" :key="'view-address1'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.profile?.address_line_1 || 'Not Set' }}
                                            </p>
                                            <div v-else :key="'edit-address1'">

                                                <BaseInput v-model="form.address_line_1" placeholder="Street address, P.O. box"
                                                    :error="form.errors.address_line_1" />
                                            </div>
                                        </transition>
                                    </div>
                                    <!-- Address 2 -->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Address 
                                            2</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.address" :key="'view-address2'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.profile?.address_line_2 || 'Not Set' }}
                                            </p>
                                            <div v-else :key="'edit-address2'">

                                                <BaseInput v-model="form.address_line_2" placeholder="Street address, P.O. box"
                                                    :error="form.errors.address_line_2" />
                                            </div>
                                        </transition>
                                    </div>
                                    <!--city-->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">City</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.address" :key="'view-city'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.profile?.city || 'Not Set' }}
                                            </p>
                                            <div v-else :key="'edit-city'">

                                                <BaseInput v-model="form.city" placeholder="City"
                                                    :error="form.errors.city" />
                                            </div>
                                        </transition>
                                     </div>
                                     <!--Postcode-->
                                     <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Postcode</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.address" :key="'view-postcode'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.profile?.postcode || 'Not Set' }}
                                            </p>
                                            <div v-else :key="'edit-postcode'">

                                                <BaseInput v-model="form.postcode" placeholder="Postcode"
                                                    :error="form.errors.postcode" />
                                            </div>
                                        </transition>
                                     </div>
                                     <!--State-->
                                       <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">State</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.address" :key="'view-state'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                <!-- Find label by comparing the key to the user's stored gender -->
                                                {{$page.props.lookups.states.find(g => g.key ===
                                                    user.profile?.state)?.label || 'Not Set'}}
                                            </p>
                                            <div v-else :key="'edit-state'">
                                                <RoundedSelect v-model="form.states" variant="form"
                                                    label="Select Marital Status" :options="$page.props.lookups.states"
                                                    option-label="label" option-value="key" />
                                                <p v-if="form.errors.states" class="text-red-500 text-xs mt-1">
                                                    {{ form.errors.states }}
                                                </p>
                                            </div>
                                        </transition>
                                    </div>
                                    
                            </div>
                            </div>

                            <!--Step 3: Roles and Departments -->
                           <div v-if="currentStep === 3"
                                class="bg-white border border-primary-border rounded-[2.5rem] p-10 shadow-xl shadow-primary/5">
                                <div class="flex justify-between items-center mb-10">
                                    <h3 class="text-xl font-bold text-gray-900 tracking-tight">Departments and Roles
                                    </h3>
                                    <!-- Action Buttons step 2 -->
                                    <div class="flex gap-2">
                                        <transition name="fade" mode="out-in">
                                            <div :key="editingSegment.roles" class="flex gap-2">
                                                <template v-if="editingSegment.roles">
                                                    <button @click="cancelEdit('roles')"
                                                        class="px-4 py-2 text-slate-500 font-bold hover:bg-slate-50 rounded-2xl transition-all">
                                                        Cancel
                                                    </button>
                                                    <button @click="saveSegment('roles')" :disabled="form.processing"
                                                        class="px-5 py-2 bg-primary text-white font-bold rounded-2xl hover:bg-primary/90 transition-all flex items-center gap-2">
                                                        Save
                                                        <Check :size="16" />
                                                    </button>
                                                </template>
                                                <button v-else @click="editingSegment.roles = true"
                                                    class="px-5 py-2 border border-primary-border text-primary font-bold rounded-2xl hover:bg-primary-light transition-all flex items-center gap-2">
                                                    Edit
                                                    <Pencil :size="14" />
                                                </button>
                                            </div>
                                        </transition>
                                    </div>
                                </div>

                                <div class="space-y-5">
                                    <!--roles -->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Staff
                                            Role</label>

                                        <transition name="fade" mode="out-in">
                                            <!-- VIEW MODE: Show the role name from the loaded relationship -->
                                            <p v-if="!editingSegment.roles" :key="'view-role'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.role?.display_name || 'No Role Assigned' }}
                                            </p>

                                            <!-- EDIT MODE: Show the dropdown using the roles prop -->
                                            <div v-else :key="'edit-role'">
                                                <RoundedSelect v-model="form.role_id" variant="form"
                                                    label="Select a role..." :options="roles"
                                                    option-label="display_name" option-value="id" />
                                                <p v-if="form.errors.role_id" class="text-red-500 text-xs mt-1">
                                                    {{ form.errors.role_id }}
                                                </p>
                                            </div>
                                        </transition>
                                    </div>
                                    <!--Joining Date-->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Joining
                                            Date</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.roles" :key="'view-joining-date'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.profile?.join_date || 'Not Set' }}
                                            </p>
                                            <div v-else :key="'edit-joining-date'">
                                                <BaseInput v-model="form.join_date" type="date"
                                                    placeholder="Joining Date" :error="form.errors.join_date" />
                                            </div>

                                        </transition>
                                    </div>
                                </div>
                            </div>
                            <!-- Step 4: Financial & Statutory -->

                            <!-- Step 5: Emergency Contact -->

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