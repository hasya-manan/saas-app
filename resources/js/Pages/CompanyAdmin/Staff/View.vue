<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

import RegistrationWizard from '@/Components/RegistrationWizard.vue'; 
import { Head, useForm} from '@inertiajs/vue3';
import RoundedSelect from '@/Components/RoundedSelect.vue';
import BaseInput from '@/Components/BaseInput.vue';
import HODStatusWarning  from '@/Components/Staff/HODStatusWarning.vue';

import { ref, computed, watch} from 'vue';
import {Pencil, CircleCheck , Plus} from 'lucide-vue-next';
import { useIcParser } from '@/Composables/useIcParser';
import { useMasking } from '@/Composables/useMasking';


const props = defineProps({
    user: Object,
    roles: Array,
    departments: Array,
    staffList: Array,
});

const currentStep = ref(1);
const isOthers = computed(() => form.department_id === 'others')

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
    role_id: props.user?.role_id || '',
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

    //segment employment/department
    position: props.user.profile?.position || '',
    department_id: props.user?.department_id || '',
    supervisor_id: props.user.supervisor_id || null, 
   // This one field handles BOTH existing and new descriptions
    description: props.user?.department?.description || '', 
    
    is_hod: props.user?.id === props.user?.department?.hod_id,
    
    // Only name needs a separate "new" field because the dropdown 
    // already holds the "existing" name
    name_department: '', 
    hod_id: '',
    
    // financial
    basic_salary: props.user.finance?.basic_salary || '',
    bank_name: props.user.finance?.bank_name || '',
    bank_account_no: props.user.finance?.bank_account_no || '',
    epf_no: props.user.finance?.epf_no || '',
    epf_rate_employee: props.user.finance?.epf_rate_employee || '',
    epf_rate_employer: props.user.finance?.epf_rate_employer || '',
    socso_no: props.user.finance?.socso_no || '',
    socso_type: props.user.finance?.socso_type || '',
    tax_no: props.user.finance?.tax_no || '',
    eis_enabled: props.user.finance?.eis_enabled || '',
   
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

watch(() => form.department_id, (newId) => {
    if (newId === 'others') {
      
        form.description = ''; 
        form.is_hod = false;
        form.name_department = '';
    } else {
        // Find the existing department's data
        const selectedDept = props.departments.find(d => d.id === newId);
        if (selectedDept) {
            form.description = selectedDept.description || '';
            form.is_hod = props.user.id === selectedDept.hod_id;
            form.name_department = ''; // Hide/ignore the "New Name" field
        }
    }
});

const selectedDeptHOD = computed(() => {
    // 1. Find the department using the Department ID
    const dept = props.departments.find(d => d.id === form.department_id);
    
    // 2. Return the HOD object (which contains the user uuid, name, etc.)
    return dept ? dept.hod : null; 
});

// Get the name of the selected department 
const selectedDeptName = computed(() => {
    // 1. Handle "null" or "empty" state early
    if (!form.department_id) return 'the Department';
    // 2. Find with type-safety
    const targetId = Number(form.department_id);
    const foundDept = props.departments.find(dept => Number(dept.id) === targetId);
    return foundDept ? foundDept.name : 'Unknown Department';
});
// Filter the staff list to show ONLY members of the chosen department
const departmentStaff = computed(() => {
   
    if (!form.department_id) return [];
    const targetId = Number(form.department_id);
    return props.staffList.filter(staff => Number(staff.department_id) === targetId);
});

const { maskAccount } = useMasking();
const displayAccount = computed(() => maskAccount(props.user.finance?.bank_account_no));
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
                                                        <CircleCheck :size="16" />
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
                                       <label class="block text-sm font-semibold text-slate-700 mb-1">Full Name</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.personal" :key="'view-name'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user?.name || '-' }}
                                            </p>
                                            <div v-else :key="'edit-name'">
                                                <BaseInput v-model="form.name" placeholder="Enter full name"
                                                    :error="form.errors.name" />
                                            </div>
                                        </transition>
                                    </div>
                                    <!-- Email Address (Username - Locked) -->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Email
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
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">IC Number</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.personal" :key="'view-ic'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.profile?.ic_number || 'Not Set' }}
                                            </p>
                                            <div v-else :key="'edit-ic'">

                                                <BaseInput v-model="form.ic_number" placeholder="IC Number"
                                                    :error="form.errors.ic_number" />
                                            </div>
                                        </transition>
                                    </div>

                                    <!--dob-->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Date of Birth</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.personal" :key="'view-dob'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.profile?.dob || 'Not Set' }}
                                            </p>
                                            
                                            <div v-else :key="'edit-dob'">
                                                <BaseInput v-model="form.dob" placeholder="Date of Birth" type="date"
                                                    :error="form.errors.dob" />
                                                <p class="mt-1 text-[10px] text-slate-400">Auto-filled based on IC Number</p>

                                            </div>
                                        </transition>
                                    </div>
                                    <!-- Gender  -->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Gender</label>
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
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Phone
                                            Number</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.personal" :key="'view-phone'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.profile?.phone || 'Not Set' }}
                                            </p>
                                            <div v-else :key="'edit-phone'">
                                                <BaseInput v-model="form.phone" placeholder="0190909090"
                                                    :error="form.errors.phone" />
                                            </div>
                                        </transition>
                                    </div>
                                    <!--Marital Status-->
                                     <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Marital Status</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.personal" :key="'view-marital'"
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
                                                        <CircleCheck :size="16" />
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
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Address 
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
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Address 
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
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">City</label>
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
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Postcode</label>
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
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">State</label>
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
                                                        <CircleCheck :size="16" />
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
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Staff
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
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!--Position-->
                                        <div class="space-y-1">
                                            <label
                                                class="block text-sm font-semibold text-slate-700 mb-1">Job Position</label>
                                            <transition name="fade" mode="out-in">
                                                <p v-if="!editingSegment.roles" :key="'view-position'"
                                                    class="text-gray-900 font-semibold text-md py-3">
                                                    {{ user.profile?.position || 'Not Set' }}
                                                </p>
                                                <div v-else :key="'edit-position'">
                                                    <BaseInput v-model="form.position" type="text"
                                                        placeholder="Position" :error="form.errors.position" />
                                                </div>
                                            </transition>
                                        </div>
                                        <!--Joining Date-->

                                        <div class="space-y-1">
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">Joining
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
                                    <!--Department-->
                                        <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Department</label>
                                        
                                        <transition name="fade" mode="out-in">
                                            <div v-if="!editingSegment.roles" :key="'view-mode'">
                                                
                                                <div class="grid grid-cols-2 gap-4 py-3">
                                                    <div>
                                                        <p class="text-gray-900 font-semibold text-md">
                                                            {{ user.department?.name || 'No Department' }}
                                                        </p>
                                                        <p class="text-gray-500 text-xs">
                                                            {{ user.department?.description || 'No description available' }}
                                                        </p>
                                                    </div>

                                                    <div v-if="user.id === user.department?.hod_id" class="pt-1">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200 uppercase tracking-wider">
                                                            Head of Department (HOD)
                                                        </span>
                                                    </div>
                                                </div>
                                                  <!-- Report to who? in view mode-->
                                                <div class="mt-3 pt-3 border-t border-slate-100">
                                                    <p class="text-[10px] uppercase tracking-wider font-bold text-slate-400 mb-2">Reports To</p>
                                                    
                                                    <div v-if="user.supervisor" class="flex items-center gap-3">
                                                        <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs border border-primary/20">
                                                            {{ user.supervisor.name ? user.supervisor.name.charAt(0) : '?' }}
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-semibold text-slate-700 leading-tight">{{ user.supervisor.name }}</p>
                                                            <p class="text-[11px] text-slate-500 italic">
                                                                {{ user.supervisor?.profile?.position || 'Primary Approver' }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div v-else class="flex items-center gap-3 opacity-60">
                                                        <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-xs border border-slate-200">
                                                            HR
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-semibold text-slate-700 leading-tight">Company Management</p>
                                                            <p class="text-[11px] text-slate-500 italic">Managed by HR/Admin</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                          
                                             
                                             

                                            <div v-else :key="'edit-dept'" class="space-y-4">
                                                    <RoundedSelect v-model="form.department_id" variant="form"
                                                        label="Select a department..." :options="departments"
                                                        :extra-options="[{ id: 'others', name: 'Others (Create new)' }]"
                                                        option-label="name" option-value="id" />

                                                     <div class="space-y-1">
                                                    <label
                                                        class="block text-sm font-semibold text-slate-700 mb-1">Description</label>
                                                    <transition name="fade" mode="out-in">

                                                        <div :key="'edit-dept'">
                                                            <BaseInput v-model="form.description" type="text"
                                                                placeholder="Description"
                                                                :error="form.errors.description" />
                                                        </div>
                                                   </transition>
                                                </div>
                                                <!-- assign supervisor -->
                                               <div v-if="form.department_id" class="mt-4">
                                                    <label
                                                        class="block text-[10px] uppercase tracking-wider font-bold text-slate-500 mb-1">
                                                        Assign Supervisor <span class="text-red-500">*</span>
                                                    </label>

                                                    <select v-model="form.supervisor_id" 
                                                        class="w-full px-4 py-2 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm">

                                                        <option value="" disabled>Select a supervisor</option>

                                                        <optgroup :label="`Staff in ${selectedDeptName}`">
                                                            <template v-if="departmentStaff.length > 0">
                                                                <template v-for="staff in departmentStaff"
                                                                    :key="staff.id">
                                                                    <option v-if="staff.uuid !== user.uuid"
                                                                        :value="staff.id">
                                                                        {{ staff.name }}
                                                                    </option>
                                                                </template>
                                                            </template>

                                                            <option v-else disabled value="">
                                                                No staff members found in this department
                                                            </option>
                                                        </optgroup>
                                                    </select>
                                                    <p class="mt-2 text-[11px] text-slate-400 italic">
                                                        * Assign who is responsible for approving this staff member's
                                                        claims and OT.
                                                        * If unassigned, approval requests will automatically route to HR/Admin management.
                                                    </p>
                                                </div>
                                                <!--current HOD status -->

                                                <div v-if="form.department_id && !isOthers" class="mt-3">
                                                    <div
                                                        class="flex items-center gap-3 p-3 rounded-xl border border-slate-100 bg-slate-50/50">
                                                        <div class="flex-1">
                                                            <p
                                                                class="text-[10px] uppercase tracking-wider font-bold text-slate-500">
                                                                Current HOD Status</p>
                                                            <p class="text-sm font-semibold text-slate-700">
                                                                <template v-if="selectedDeptHOD">
                                                                    <span v-if="selectedDeptHOD.id === user.uuid"
                                                                        class="text-emerald-600">
                                                                        You are currently the Head
                                                                    </span>
                                                                    <span v-else class="text-slate-700">
                                                                        Managed by: {{ selectedDeptHOD.name }}
                                                                    </span>
                                                                </template>
                                                                <template v-else>
                                                                    <span class="text-slate-400 italic">No HOD
                                                                        assigned</span>
                                                                </template>
                                                            </p>
                                                        </div>
                                                        <label
                                                            class="relative inline-flex items-center cursor-pointer group">
                                                           <input type="checkbox" v-model="form.is_hod"
                                                                class="sr-only peer">

                                                            <div
                                                                class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                                                            </div>

                                                            <span
                                                                class="ml-3 text-xs font-bold uppercase tracking-tight">
                                                                <template v-if="selectedDeptHOD">
                                                                    <span v-if="selectedDeptHOD.uuid !== user.uuid"
                                                                        class="text-amber-600">
                                                                        Replace {{ selectedDeptHOD.name }}
                                                                    </span>
                                                                    <span v-else class="text-emerald-600">
                                                                        Current HOD
                                                                    </span>
                                                                </template>
                                                                <span v-else class="text-slate-500">
                                                                    Assign as HOD
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>

                                                    <!--confirm modal if user wants to replace HOD-->
                                                    <HODStatusWarning :is-hod="form.is_hod"
                                                                :selected-hod="selectedDeptHOD"
                                                                :current-user-uuid="user.uuid"
                                                                :current-user-name="user.name" />
                                                </div>

                                                <transition enter-active-class="transition duration-200 ease-out"
                                                    enter-from-class="transform scale-95 opacity-0"
                                                    enter-to-class="transform scale-100 opacity-100"
                                                    leave-active-class="transition duration-150 ease-in"
                                                    leave-from-class="transform scale-100 opacity-100"
                                                    leave-to-class="transform scale-95 opacity-0">
                                                    <div v-if="isOthers"
                                                        class="bg-primary/5 border border-primary/10 rounded-2xl p-6 relative overflow-hidden">

                                                        <div class="flex items-center gap-2 mb-4">
                                                            <div class="p-2 bg-primary text-white rounded-lg">
                                                                <Plus :size="16" />
                                                            </div>
                                                            <h4 class="text-sm font-bold text-slate-800">New Department
                                                                Setup</h4>
                                                        </div>

                                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                                            <div>
                                                                <label
                                                                    class="block text-xs font-bold text-slate-800  mb-1">Department
                                                                    Name</label>
                                                                <input v-model="form.name_department" type="text"
                                                                    class="w-full border-white rounded-xl shadow-sm focus:ring-primary focus:border-primary bg-white px-4 py-3 text-sm"
                                                                    placeholder="Enter name" />
                                                            </div>

                                                            <div>
                                                                <label
                                                                    class="block text-xs font-bold text-slate-800  mb-1">Description</label>
                                                                <input v-model="form.description" type="text"
                                                                    class="w-full border-white rounded-xl shadow-sm focus:ring-primary focus:border-primary bg-white px-4 py-3 text-sm"
                                                                    placeholder="Briefly describe the unit" />
                                                            </div>

                                                            <div class="md:col-span-2 pt-2">
                                                            <div
                                                                class="flex flex-col p-4 bg-slate-50/50 rounded-xl border border-slate-100 transition-all duration-200">

                                                                <label
                                                                    class="flex items-start gap-4 cursor-pointer select-none">
                                                                    <div class="pt-1"> <input type="checkbox"
                                                                            v-model="form.is_hod"
                                                                            class="w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary transition-colors" />
                                                                    </div>
                                                                    <div class="flex-1">
                                                                        <p
                                                                            class="text-sm font-bold text-slate-700 leading-tight">
                                                                            Assign as Head of Department (HOD)
                                                                        </p>
                                                                        <p class="text-xs text-slate-500 mt-1">
                                                                            Should this new staff manage this
                                                                            department?
                                                                        </p>
                                                                    </div>
                                                                </label>

                                                                <div v-if="!form.is_hod"
                                                                    class="mt-4 pt-4 border-t border-slate-100/50">
                                                                    <RoundedSelect v-model="form.hod_id"
                                                                        variant="form"
                                                                        label="Who will be the HOD?"
                                                                        :options="staffList" option-label="name"
                                                                        option-value="id" class="w-full" />
                                                                </div>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </transition>
                                            </div>
                                        </transition>
                                        </div>
                                </div>
                            </div>
                            <!-- Step 4: Financial & Statutory -->
                             <div v-if="currentStep === 4" class="bg-white border border-primary-border rounded-[2.5rem] p-10 shadow-xl shadow-primary/5">
                                <div class="flex justify-between items-center mb-10">
                                     <h3 class="text-xl font-bold text-gray-900 tracking-tight">Financial & Statutory
                                    </h3>
                                    <!--Button-->
                                    <div class="flex gap-2">
                                        <transition name="fade" mode="out-in">
                                            <div :key="editingSegment.financial" class="flex gap-2">
                                                <template v-if="editingSegment.financial">
                                                    <button @click="cancelEdit('financial')"
                                                        class="px-4 py-2 text-slate-500 font-bold hover:bg-slate-50 rounded-2xl transition-all">
                                                        Cancel
                                                    </button>
                                                    <button @click="saveSegment('financial')" :disabled="form.processing"
                                                        class="px-5 py-2 bg-primary text-white font-bold rounded-2xl hover:bg-primary/90 transition-all flex items-center gap-2">
                                                        Save
                                                        <CircleCheck  :size="16" />
                                                    </button>
                                                </template>
                                                <button v-else @click="editingSegment.financial = true"
                                                    class="px-5 py-2 border border-primary-border text-primary font-bold rounded-2xl hover:bg-primary-light transition-all flex items-center gap-2">
                                                    Edit
                                                    <Pencil :size="14" />
                                                </button>
                                            </div>
                                        </transition>
                                    </div>
                                </div>
                                   <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-16">
                                 <!-- Section Header: Uses col-span-full to sit above both columns -->
                                  
                                    <h3 class="col-span-full text-xs font-bold text-primary uppercase tracking-widest ">
                                        Banking Information
                                    </h3>
                                    <!--Basic Salary-->
                                    <div class="space-y-1 ">
                                      
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Basic Salary</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.financial" :key="'view-salary'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.finance?.basic_salary ?
                                                    Number(user.finance?.basic_salary).toLocaleString('en-MY', {
                                                minimumFractionDigits: 2 }) : '0.00' }}
                                            </p>

                                            <div v-else :key="'edit-salary'">
                                                <BaseInput v-model="form.basic_salary" type="number" step="0.01"
                                                    placeholder="0.00" :error="form.errors.basic_salary" />
                                                <p class="text-[10px] text-slate-500 mt-1">Enter amount in RM (e.g.
                                                    2500.50)</p>
                                            </div>
                                        </transition>
                                    </div>
                                   <!--bank name-->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Bank  Name</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.financial" :key="'view-bank-name'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                          
                                                  <!-- Find label by comparing the key to the user's stored gender -->
                                                {{$page.props.lookups.banks.find(g => g.key ===
                                                    user.finance?.bank_name)?.label || 'Not Set'}}
                                            </p>
                                            <div v-else :key="'edit-bank-name'">
                                               
                                                    <RoundedSelect v-model="form.bank_name" variant="form"
                                                    label="Select bank name" :options="$page.props.lookups.banks"
                                                    option-label="label" option-value="key" />
                                                <p v-if="form.errors.bank_name" class="text-red-500 text-xs mt-1">
                                                    {{ form.errors.bank_name }}
                                                </p>
                                            </div>
                                        </transition>
                                    </div>
                                   <!--Account Number-->
                                    <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Account Number</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.financial" :key="'view-accountnum'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                              {{ displayAccount }}
                                            </p>
                                               <div v-else :key="'edit-accountnum'">
                                                <BaseInput v-model="form.bank_account_no" placeholder="Enter bank account number"
                                                    :error="form.errors.bank_account_no" />
                                            </div>
                                        </transition>
                                    </div>
                                    <!-- Statutory Contributions Section -->
                                   <h3 class="col-span-full text-xs font-bold text-primary uppercase tracking-widest ">
                                        Statutory Contributions
                                    </h3>
                                    <!--3 column -->
                                    <div class="col-span-full grid grid-cols-1 md:grid-cols-3 gap-6">

                                        <!-- EPF Number -->
                                        <div class="space-y-1">
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">EPF
                                                Number</label>
                                            <transition name="fade" mode="out-in">
                                                <p v-if="!editingSegment.financial" :key="'view-epf'"
                                                    class="text-gray-900 font-semibold text-md py-3">
                                                    {{ user.finance?.epf_no || 'Not Set' }}
                                                </p>
                                                <div v-else :key="'edit-epf'">
                                                    <BaseInput v-model="form.epf_no" type="number"
                                                        :error="form.errors.epf_no" />
                                                </div>
                                            </transition>
                                        </div>

                                        <!-- Employee % -->
                                        <div class="space-y-1">
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">Employee
                                                %</label>
                                            <transition name="fade" mode="out-in">
                                                <p v-if="!editingSegment.financial" :key="'view-emp-pct'"
                                                    class="text-gray-900 font-semibold text-md py-3">
                                                    {{ user.finance?.epf_rate_employee  }}%
                                                </p>
                                                <div v-else :key="'edit-emp-pct'">
                                                    <BaseInput v-model="form.epf_rate_employee" type="number"
                                                        :error="form.errors.epf_rate_employee" />
                                                </div>
                                            </transition>
                                        </div>

                                        <!-- Employer % -->
                                        <div class="space-y-1">
                                            <label class="block text-sm font-semibold text-slate-700 mb-1">Employer
                                                %</label>
                                            <transition name="fade" mode="out-in">
                                                <p v-if="!editingSegment.financial" :key="'view-empr-pct'"
                                                    class="text-gray-900 font-semibold text-md py-3">
                                                    {{ user.finance?.epf_rate_employer }}%
                                                </p>
                                                <div v-else :key="'edit-empr-pct'">
                                                    <BaseInput v-model="form.epf_rate_employer" type="number"
                                                        :error="form.errors.epf_rate_employer" />
                                                </div>
                                            </transition>
                                        </div>
                                    </div>
                                    <!--socso number-->
                                     <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Socso Number</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.financial" :key="'view-socso'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.finance?.socso_no || 'Not Set' }}
                                            </p>
                                               <div v-else :key="'edit-socso'">
                                                <BaseInput v-model="form.socso_no" placeholder="Enter socso  number"
                                                    :error="form.errors.socso_no" />
                                            </div>
                                        </transition>
                                    </div>
                                    <!-- Socso Category-->

                                     <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Socso Category</label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.financial" :key="'view-socso'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                               
                                                  {{$page.props.lookups.socso_types.find(g => g.key ===
                                                    user.finance?.socso_type)?.label || 'Not Set'}}
                                            </p>
                                               <div v-else :key="'edit-socso'">
                                                <RoundedSelect v-model="form.socso_type" variant="form"
                                                    label="Select Catehory" :options="$page.props.lookups.socso_types"
                                                    option-label="label" option-value="key" />
                                                <p v-if="form.errors.socso_type" class="text-red-500 text-xs mt-1">
                                                    {{ form.errors.socso_type }}
                                                </p>
                                            </div>
                                        </transition>
                                          <transition enter-active-class="transition duration-200 ease-out"
                                            enter-from-class="opacity-0 -translate-y-1"
                                            enter-to-class="opacity-100 translate-y-0">
                                            <div v-if="form.socso_type"
                                                class="bg-blue-50/50 border border-blue-100 p-3 rounded-xl">
                                                <p class="text-[11px] text-blue-700 leading-tight">
                                                    <span class="font-bold uppercase mr-1">Coverage:</span>
                                                    {{$page.props.lookups.socso_types.find(i => i.key ===
                                                    form.socso_type)?.description }}
                                                </p>
                                            </div>
                                        </transition>
                                    </div>
                                    <!--tax no-->
                                     <div class="space-y-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Tax (LHDN) Number
                                        </label>
                                        <transition name="fade" mode="out-in">
                                            <p v-if="!editingSegment.financial" :key="'view-tax'"
                                                class="text-gray-900 font-semibold text-md py-3">
                                                {{ user.finance?.tax_no || 'Not Set' }}
                                            </p>
                                               <div v-else :key="'edit-tax'">
                                                <BaseInput v-model="form.tax_no" placeholder="Enter tax (LHDN) number"
                                                    :error="form.errors.tax_no" />
                                            </div>
                                       </transition>
                                    </div>

                                    <!-- EIS Enabled -->
                                    
                                    <div class="space-y-3 col-span-full">
                                        <label class="block text-sm font-semibold text-slate-700">
                                            EIS Contribution
                                        </label>

                                        <transition name="fade" mode="out-in">
                                            <!-- VIEW MODE: Logic for 1 (True) or 0 (False) -->
                                            <div v-if="!editingSegment.financial" :key="'view-eis'" class="py-2">
                                                <span
                                                    :class="(form.eis_enabled ?? 0) == 1 ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-slate-50 text-slate-600 border-slate-100'"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-sm font-medium">
                                                    <div :class="(form.eis_enabled ?? 0) == 1 ? 'bg-emerald-500' : 'bg-slate-400'"
                                                        class="w-1.5 h-1.5 rounded-full"></div>
                                                    {{(form.eis_enabled ?? 0) == 1 ? 'Enabled' : 'Disabled' }}
                                                </span>
                                            </div>

                                            <!-- EDIT MODE -->
                                            <div v-else :key="'edit-eis'">
                                                <label
                                                    class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 rounded-xl cursor-pointer transition-all border border-slate-200/50">
                                                    <div class="flex items-center gap-4">
                                                        <div
                                                            class="bg-white p-2 rounded-lg shadow-sm border border-slate-100">
                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                class="h-5 w-5 text-primary" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="text-sm font-bold text-slate-900">EIS Contribution
                                                            </p>
                                                            <p class="text-xs text-slate-600">Employment Insurance
                                                                System</p>
                                                        </div>
                                                    </div>
                                                    <!-- Vue checkbox will bind true/false to form.eis_enabled -->
                                                    <input v-model="form.eis_enabled" :true-value="1" :false-value="0"
                                                        type="checkbox"
                                                        class="w-6 h-6 text-primary rounded-md border-slate-600 focus:ring-primary cursor-pointer transition-all">
                                                </label>
                                            </div>
                                        </transition>
                                    </div>

                                </div>

                            </div>

                            <!-- Step 5: Emergency Contact -->
                            <div v-if="currentStep === 5" class="bg-white border border-primary-border rounded-[2.5rem] p-10 shadow-xl shadow-primary/5">
                            <div class="flex justify-between items-center mb-10">
                                <h3 class="text-xl font-bold text-gray-900 tracking-tight">Emergency Contact</h3>
                                <!--Button-->
                                    <div class="flex gap-2">
                                        <transition name="fade" mode="out-in">
                                            <div :key="editingSegment.emergency" class="flex gap-2">
                                                <template v-if="editingSegment.emergency">
                                                    <button @click="cancelEdit('emergency')"
                                                        class="px-4 py-2 text-slate-500 font-bold hover:bg-slate-50 rounded-2xl transition-all">
                                                        Cancel
                                                    </button>
                                                    <button @click="saveSegment('emergency')" :disabled="form.processing"
                                                        class="px-5 py-2 bg-primary text-white font-bold rounded-2xl hover:bg-primary/90 transition-all flex items-center gap-2">
                                                        Save
                                                        <CircleCheck  :size="16" />
                                                    </button>
                                                </template>
                                                <button v-else @click="editingSegment.emergency = true"
                                                    class="px-5 py-2 border border-primary-border text-primary font-bold rounded-2xl hover:bg-primary-light transition-all flex items-center gap-2">
                                                    Edit
                                                    <Pencil :size="14" />
                                                </button>
                                            </div>
                                        </transition>
                                    </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-10 gap-x-16">
                                  
                                <h3 class="col-span-full text-xs font-bold text-primary uppercase tracking-widest ">
                                    Next of Kin Information

                                </h3>
                                <!--Full Name IC-->
                                <div class="space-y-1 ">
                                        <label class="block text-sm font-semibold text-slate-700 mb-1">Full Name as per IC
                                    </label>
                                    <transition name="fade" mode="out-in">
                                        <p v-if="!editingSegment.emergency" :key="'view-waris-name'"
                                            class="text-gray-900 font-semibold text-md py-3">
                                            {{ user.profile?.waris_name || 'Not Set' }}
                                        </p>

                                        <div v-else :key="'edit-waris-name'">
                                            <BaseInput v-model="form.waris_name" type="text" 
                                                placeholder="Ali Bin Abu" :error="form.errors.waris_name" />
                                            
                                        </div>
                                    </transition>
                                </div>
                                <!--IC Number -->
                                <div class="space-y-1">
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">IC Number</label>
                                    <transition name="fade" mode="out-in">
                                        <p v-if="!editingSegment.emergency" :key="'view-waris-ic'"
                                            class="text-gray-900 font-semibold text-md py-3">
                                            {{ user.profile?.waris_ic || 'Not Set' }}
                                        </p>
                                        <div v-else :key="'edit-waris-ic'">
                                            <BaseInput v-model="form.waris_ic" type="text" 
                                                placeholder="560XXX01XXXX" :error="form.errors.waris_ic" />
                                        </div>
                                    </transition>
                                </div>
                                <!--Relationship -->
                                <div class="space-y-1">
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Relationship</label>
                                    <transition name="fade" mode="out-in">
                                        <p v-if="!editingSegment.emergency" :key="'view-waris-relationship'"
                                            class="text-gray-900 font-semibold text-md py-3">
                                           
                                             {{$page.props.lookups.relationships.find(g => g.key ===
                                                    user.profile?.waris_relationship)?.label || 'Not Set'}}
                                        </p>
                                        <div v-else :key="'edit-waris-relationship'">
                                           <RoundedSelect v-model="form.waris_relationship" variant="form"
                                                    label="Select Relationship" :options="$page.props.lookups.relationships"
                                                    option-label="label" option-value="key" />
                                                <p v-if="form.errors.waris_relationship" class="text-red-500 text-xs mt-1">
                                                    {{ form.errors.waris_relationship }}
                                                </p>
                                        </div>
                                    </transition>
                                </div>
                                <!--Gender-->
                                <div class="space-y-1">
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Gender</label>
                                    <transition name="fade" mode="out-in">
                                        <p v-if="!editingSegment.emergency" :key="'view-waris-gender'"
                                            class="text-gray-900 font-semibold text-md py-3">
                                           
                                             {{$page.props.lookups.genders.find(g => g.key ===
                                                    user.profile?.waris_gender)?.label || 'Not Set'}}
                                        </p>
                                        <div v-else :key="'edit-waris-gender'">
                                           <RoundedSelect v-model="form.waris_gender" variant="form"
                                                    label="Select Gender" :options="$page.props.lookups.genders"
                                                    option-label="label" option-value="key" />
                                                <p v-if="form.errors.waris_gender" class="text-red-500 text-xs mt-1">
                                                    {{ form.errors.waris_gender }}
                                                </p>
                                        </div>
                                    </transition>
                                </div>
                                <!--Phone Number   -->
                                <div class="space-y-1">
                                    <label class="block text-sm font-semibold text-slate-700 mb-1">Phone Number</label>
                                    <transition name="fade" mode="out-in">
                                        <p v-if="!editingSegment.emergency" :key="'view-waris-phone'"
                                            class="text-gray-900 font-semibold text-md py-3">
                                            {{ user.profile?.waris_phone || 'Not Set' }}
                                        </p>
                                        <div v-else :key="'edit-waris-phone'">
                                            <BaseInput v-model="form.waris_phone" type="text" 
                                                placeholder="0123456789" :error="form.errors.waris_phone" />
                                        </div>
                                    </transition>
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