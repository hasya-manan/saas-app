<script setup>
import { ref, computed, watch } from 'vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RegistrationWizard from '@/Components/RegistrationWizard.vue';
import PageHeader from '@/Components/PageHeader.vue';
import RoundedSelect from '@/Components/RoundedSelect.vue';
import { useIcParser } from '@/Composables/useIcParser'; 
import { useEmailValidation } from '@/Composables/useEmailValidation';
import {
    Eye, EyeOff , Plus 
     
} from 'lucide-vue-next';
const props = defineProps({
    roles: Array,
    departments: Array,
    staffList: Array, 

});

const currentStep = ref(1);
const isLastStep = computed(() => currentStep.value === steps.length);
const hasDepartments = computed(() => props.departments && props.departments.length > 0)
const isOthers = computed(() => form.department_id === 'others')
const showPassword = ref(false);
const showConfirmPassword = ref(false);

const steps = [
    { id: 1, title: 'Account Setup', desc: 'Login & System Access' },
    { id: 2, title: 'Personal Information', desc: 'Identity & Contact' },
    { id: 3, title: 'Employment Details', desc: 'Role & Department' },
    { id: 4, title: 'Financial & Statutory', desc: 'Salary, EPF, SOCSO' },
    { id: 5, title: 'Emergency Contact', desc: 'Emergency Contact' },
];

const form = useForm({
    // Step 1 : Account
    email: '',
    password: '',
    password_confirmation: '',
    // Step 2 : Personal
    name: '',// name need to change for user staff
    ic_number: '',
    user_gender: '',
    position: '',
    dob: '',
    marital_status: '',
    join_date: '',
    phone: '',
    address_line_1: '',
    address_line_2: '',
    city: '',
    postcode: '',
    state: '',
    //step 3 : employment details
    role_id: '',
    //department table 
    tenant_id: '',
    department_id: '',
    name_department: '',
    description: '',
    hod_id: '',
    is_hod: false,
    
    //step 4 : financial & statutory
    basic_salary: '',
    bank_name: '',
    bank_account_no: '',
    epf_no: '',
    socso_no: '',
    socso_type: '',
    tax_no: '',
    eis_enabled: '',
    epf_rate_employee: 11.00, 
    epf_rate_employer: 13.00, 
    //step 5 : emergency  contact 
    waris_name: '',
    waris_relationship: '',
    waris_ic: '',
    waris_phone: '',
    waris_gender: '',

});



const isValidMYIC = (ic) => /^\d{12}$/.test(ic);

// Global Email Check using Inertia
const { checkEmailUnique } = useEmailValidation(form);

const validateStep3 = () => {
    if (!form.role_id) return false

    if (hasDepartments.value) {
        if (!form.department_id) return false
        if (isOthers.value && !form.name_department) return false
        return true
    }

    if (!form.name_department) return false
    return true
}


const stepValidation = computed(() => ({
    // Step 1: No errors from backend + basic length check
    1: form.email.includes('@') && !form.errors.email && form.password.length >= 8,

    // Step 2: User IC
    2: !!form.name && 
       !!form.dob && 
       !!form.user_gender && 
       !!form.phone && 
       !!form.marital_status && 
       isValidMYIC(form.ic_number),

    // Step 3: Role and Department (role_id moved here)
    3: validateStep3(), // ← call a function instead


    // Step 4: Financial
    4: form.basic_salary > 0 && form.bank_name !== '' && form.bank_account_no !== '',

    // Step 5: Emergency Contact + IC Check
    //5: form.waris_name !== '' && form.waris_phone !== '' && isValidMYIC(form.waris_ic),
    5: (() => {
        // 1. If the name is empty, consider the whole step valid (it's skipped)
        if (!form.waris_name) return true;

        // 2. If they entered a name, require a phone number
        const hasPhone = !!form.waris_phone;

        // 3. If they selected "Other", require the specification text
        const hasOtherSpecified = form.waris_relationship === 'other' 
            ? !!form.waris_relationship_other 
            : true;

        // 4. (Optional) Check IC only if they actually typed something in the IC box
        const isIcValid = form.waris_ic ? isValidMYIC(form.waris_ic) : true;

        return hasPhone && hasOtherSpecified && isIcValid;
    })(),
    
}));

// Auto-fill Date of Birth (DOB) based on IC Number
const { startWatchingIc } = useIcParser(form);
startWatchingIc();

const submit = () => {
    form.post(route('admin_company.users.store'), {
        onSuccess: () => {
            form.reset()
            currentStep.value = 1
        },

        onError: (errors) => {
            if (errors.email || errors.password) currentStep.value = 1
            if (errors.name || errors.ic_number) currentStep.value = 2
            if (errors.role_id || errors.department_id) currentStep.value = 3
            if (errors.basic_salary || errors.bank_name) currentStep.value = 4
        }
    })
}

const nextStep = () => {
    if (isLastStep.value) {
        submit() 
    } else {
        currentStep.value++
    }
}

const prevStep = () => {
    if (currentStep.value > 1) currentStep.value--;
};


// 2. Auto Check Confirmation
const passwordMismatch = computed(() => {
    // Only show mismatch if both fields have values
    if (form.password && form.password_confirmation) {
        return form.password !== form.password_confirmation;
    }
    return false;
});
</script>

<template>

    <Head title="Add New Staff" />

    <AuthenticatedLayout>
       
      <template #header>
            <PageHeader :back-route="route('admin_company.users.index')" title="Staff Registration"
                subtitle="Onboard new team members and configure their professional profile" />
        </template>

       <div
            class="flex flex-col lg:flex-row min-h-[calc(100vh-200px)] bg-white rounded-2xl overflow-hidden shadow-sm border border-surface-100">

            <RegistrationWizard v-model:currentStep="currentStep" :steps="steps" :validationSchema="stepValidation">
                <template #title>New Staff</template>
            </RegistrationWizard>


            <main class="flex-1 bg-white flex flex-col">
               <!-- DEBUG:: form -->
                <!-- <pre style="background: #f1f1f1; padding: 10px; font-size: 11px;">
                {{ form.data() }}
                </pre>
                <pre style="background: #ffe0e0; padding: 10px; font-size: 11px;">
                {{ form.errors }}
                </pre> -->
                <div class="p-12 max-w-2xl mx-auto w-full flex-1">
                    <div class="mb-10">
                        <span class="text-xs font-bold text-primary tracking-widest uppercase">Step {{ currentStep }} of
                            5</span>
                        <h1 class="text-3xl font-extrabold text-slate-900 mt-1">{{ steps[currentStep - 1].title }}</h1>
                    </div>

                    <div class="min-h-[300px]">
                        <!-- Step 1 : Account Setup -->
                        <div v-if="currentStep === 1" class="space-y-6 animate-fade-in">
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                   <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address <span class="text-red-500">*</span></label>
                                    <input v-model="form.email" type="email" @blur="checkEmailUnique"
                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                        :class="{ 'border-red-500': form.errors.email }"
                                        placeholder="e.g. staff@company.com">
                                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-500 font-medium">
                                        {{ form.errors.email }}
                                    </p>
                                    
                                </div>
                                <!--TODO:: need to add view icon eye the password field-->
                               <div class="relative"> <label
                                        class="block text-sm font-semibold text-slate-700 mb-2">Password<span class="text-red-500">*</span></label>

                                    <div class="relative"> <input v-model="form.password"
                                            :type="showPassword ? 'text' : 'password'"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 pr-12 transition-all"
                                            placeholder="e.g. need to have at least 8 characters">

                                        <button type="button" @click="showPassword = !showPassword"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                            <Eye v-if="!showPassword" :size="20" />
                                            <EyeOff v-else :size="20" />
                                        </button>
                                    </div>

                                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">
                                        {{ form.errors.password }}
                                    </p>
                                </div>

                                <div class="mt-4">
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Confirm
                                        Password<span class="text-red-500">*</span></label>

                                    <div class="relative">
                                        <input v-model="form.password_confirmation"
                                            :type="showConfirmPassword ? 'text' : 'password'"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 pr-12 transition-all"
                                            :class="{ 'border-red-500 focus:ring-red-500 focus:border-red-500': passwordMismatch }"
                                            placeholder="Confirm your password">

                                        <button type="button" @click="showConfirmPassword = !showConfirmPassword"
                                            class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition-colors">
                                            <Eye v-if="!showConfirmPassword" :size="20" />
                                            <EyeOff v-else :size="20" />
                                        </button>
                                    </div>

                                    <p v-if="passwordMismatch" class="mt-1 text-xs text-red-500">
                                        Passwords do not match.
                                    </p>
                                </div>

                            </div>
                        </div>
                        <!-- Step 2:Personal Information -->
                        <div v-if="currentStep === 2" class="space-y-8 animate-fade-in">

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name (as per
                                        IC)<span class="text-red-500">*</span></label>
                                    <input v-model="form.name" type="text"
                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                        placeholder="e.g. Ahmad bin Ibrahim">
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-2 gap-6">
                                    <div>
                                       <label class="block text-sm font-semibold text-slate-700 mb-2">IC Number<span class="text-red-500">*</span></label>
                                        <input v-model="form.ic_number" type="text" maxlength="12"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                            placeholder="eg. 000308010000">
                                        <p v-if="form.errors.ic_number" class="mt-1 text-xs text-red-500">{{
                                            form.errors.ic_number }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Date of
                                            Birth<span class="text-red-500">*</span></label>
                                        <input v-model="form.dob" type="date"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary bg-slate-50 px-4 py-3 transition-all"
                                            :class="{ 'opacity-70': form.dob }">
                                        <p class="mt-1 text-[10px] text-slate-400">Auto-filled based on IC Number</p>
                                        <p v-if="form.errors.dob" class="mt-1 text-xs text-red-500">{{ form.errors.dob
                                            }}</p>
                                    </div>
                                     <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Gender<span class="text-red-500">*</span></label>
                                        <RoundedSelect v-model="form.user_gender" variant="form" label="Select Gender"
                                            :options="$page.props.lookups.genders" option-label="label"
                                            option-value="key" />

                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Phone
                                            Number<span class="text-red-500">*</span></label>
                                        <input v-model="form.phone" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                            placeholder="0123456789">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Marital
                                            Status<span class="text-red-500">*</span></label>

                                        <RoundedSelect v-model="form.marital_status" variant="form"
                                            label="Select Status" :options="$page.props.lookups.marital_statuses"
                                            option-label="label" option-value="key" />
                                    </div>
                                   

                                </div>
                            </div>


                            <div class="pt-6 border-t border-slate-100">
                                <h3 class="text-xs font-bold text-primary uppercase tracking-widest mb-6">Permanent
                                    Address</h3>

                                <div class="grid grid-cols-1 gap-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Addres Line 1</label>

                                        <input v-model="form.address_line_1" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                            placeholder="Street address, P.O. box">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Address
                                            Line 2 (Optional)</label>
                                        <input v-model="form.address_line_2" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                            placeholder="Apartment, suite, unit, building, floor">
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 mb-2">City</label>
                                            <input v-model="form.city" type="text"
                                                class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                                placeholder="Kota Bharu">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-semibold text-slate-700 mb-2">Postcode</label>
                                            <input v-model="form.postcode" type="text"
                                                class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                                placeholder="15000">
                                        </div>
                                        <div>

                                            <div>
                                                <label
                                                    class="block text-sm font-semibold text-slate-700 mb-2">State</label>
                                                <RoundedSelect v-model="form.state" variant="form" label="Select State"
                                                    :options="$page.props.lookups.states" option-label="label"
                                                    option-value="key" />

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--employement details-->
                        <!--Step 3 :: Employment Details-->
                        <div v-if="currentStep === 3" class="space-y-8 animate-fade-in">

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Staff Role <span class="text-red-500">*</span></label>
                                    <RoundedSelect v-model="form.role_id" variant="form" label="Select a role..."
                                        :options="roles" option-label="display_name" option-value="id" />
                               </div>
                            </div>
                         <div class="pt-6 border-t border-slate-100">
                                <h3 class="text-xs font-bold text-primary uppercase tracking-widest mb-6">
                                    Employment Details
                                </h3>

                                <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Job
                                            Position</label>
                                        <input v-model="form.position" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                            placeholder="e.g. Senior Software Engineer">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Joining Date</label>
                                        <input v-model="form.join_date" type="date"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all">
                                    </div>
                                   
                                </div>
                               <div class="grid grid-cols-1 gap-6 py-2">
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-slate-700 mb-2">Department</label>
                                        <RoundedSelect v-model="form.department_id" variant="form"
                                            label="Select a department..." :options="departments"
                                            :extra-options="[{ id: 'others', name: 'Others (Create new)' }]"
                                            option-label="name" option-value="id" />
                                    </div>
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
                                            <h4 class="text-sm font-bold text-slate-800">New Department Setup</h4>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-slate-800  mb-2">Department
                                                    Name</label>
                                                <input v-model="form.name_department" type="text"
                                                    class="w-full border-white rounded-xl shadow-sm focus:ring-primary focus:border-primary bg-white px-4 py-3 text-sm"
                                                    placeholder="Enter name" />
                                            </div>

                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-slate-800  mb-2">Description</label>
                                                <input v-model="form.description" type="text"
                                                    class="w-full border-white rounded-xl shadow-sm focus:ring-primary focus:border-primary bg-white px-4 py-3 text-sm"
                                                    placeholder="Briefly describe the unit" />
                                            </div>

                                            <div class="md:col-span-2 pt-2">
                                                <div
                                                    class="flex flex-col p-4 bg-slate-50/50 rounded-xl border border-slate-100 transition-all duration-200">

                                                    <label class="flex items-start gap-4 cursor-pointer select-none">
                                                        <div class="pt-1"> <input type="checkbox" v-model="form.is_hod"
                                                                class="w-5 h-5 rounded border-slate-300 text-primary focus:ring-primary transition-colors" />
                                                        </div>
                                                        <div class="flex-1">
                                                            <p class="text-sm font-bold text-slate-700 leading-tight">
                                                                Assign as Head of Department (HOD)
                                                            </p>
                                                            <p class="text-xs text-slate-500 mt-1">
                                                                Should this new staff manage this department?
                                                            </p>
                                                        </div>
                                                    </label>

                                                    <div v-if="!form.is_hod"
                                                        class="mt-4 pt-4 border-t border-slate-100/50">
                                                        <RoundedSelect v-model="form.hod_id" variant="form"
                                                            label="Who will this staff report to?" :options="staffList"
                                                            option-label="name" option-value="id" class="w-full" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </transition>
                            </div>


                       </div>
                        <!--Step 4 :: -->

                        <div v-if="currentStep === 4" class="space-y-8 animate-fade-in">

                            <div class="grid grid-cols-1 gap-6">
                                <h3 class="text-xs font-bold text-primary uppercase tracking-widest">Banking Information
                                </h3>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Basic Salary
                                        (MYR)</label>
                                    <input v-model="form.basic_salary" type="number"
                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                        placeholder="e.g. 3500.00">
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Bank Name</label>
                                        <RoundedSelect v-model="form.bank_name" variant="form" label="Select Bank"
                                            :options="$page.props.lookups.banks" option-label="label"
                                            option-value="key" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Account
                                            Number</label>
                                        <input v-model="form.bank_account_no" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                            placeholder="e.g. 164000123456">
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 mt-6 border-t border-slate-100">
                                <h3 class="text-xs font-bold text-primary uppercase tracking-widest mb-6">Statutory
                                    Contributions</h3>

                                <div class="space-y-8">
                                    <div class="bg-slate-50/50 p-4 rounded-2xl border border-slate-100">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-700 mb-2">EPF
                                                    Number</label>
                                                <input v-model="form.epf_no" type="text" placeholder="e.g. 123456789"
                                                    class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3 bg-white">
                                            </div>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <label
                                                        class="text-[10px] font-bold text-slate-500 uppercase mb-2 block">Employee
                                                        %</label>
                                                    <input v-model="form.epf_rate_employee" type="number"
                                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3 bg-white">
                                                </div>
                                                <div>
                                                    <label
                                                        class="text-[10px] font-bold text-slate-500 uppercase mb-2 block">Employer
                                                        %</label>
                                                    <input v-model="form.epf_rate_employer" type="number"
                                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3 bg-white">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="space-y-4 px-1">
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-700 mb-2">SOCSO
                                                    Number</label>
                                                <input v-model="form.socso_no" type="text"
                                                    placeholder="e.g. 850101145522"
                                                    class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-semibold text-slate-700 mb-2">SOCSO
                                                    Category</label>
                                                <select v-model="form.socso_type"
                                                    class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3 bg-white">
                                                    <option value="" disabled>Select Category</option>
                                                    <option v-for="item in $page.props.lookups.socso_types"
                                                        :key="item.key" :value="item.key">
                                                        {{ item.label }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
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

                                    <div class="px-1">
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Tax (LHDN)
                                            Number</label>
                                        <input v-model="form.tax_no" type="text" placeholder="e.g. SG 1234567890"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3">
                                    </div>

                                    <label
                                        class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 rounded-xl cursor-pointer transition-all border border-slate-200/50">
                                        <div class="flex items-center gap-4">
                                            <div class="bg-white p-2 rounded-lg shadow-sm border border-slate-100">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-900">EIS Contribution</p>
                                                <p class="text-xs text-slate-600 leading-tight">Employment Insurance
                                                    System</p>
                                            </div>
                                        </div>
                                        <input v-model="form.eis_enabled" type="checkbox"
                                            class="w-6 h-6 text-primary rounded-md border-slate-300 focus:ring-primary cursor-pointer transition-all">
                                    </label>
                                </div>
                            </div>
                        </div>
                          <!-- Step 5: Emergency Contact  -->
                        <div v-if="currentStep === 5" class="space-y-8 animate-fade-in">

                            <div class="grid grid-cols-1 gap-6">
                                <h3 class="text-xs font-bold text-primary uppercase tracking-widest">Next of Kin
                                    Information
                                </h3>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name as
                                        per
                                        IC</label>
                                    <input v-model="form.waris_name" type="text"
                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                        placeholder="e.g. Ali Bin Abu">
                                </div>
                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-slate-700 mb-2">Relationship</label>
                                        <RoundedSelect v-model="form.waris_relationship" variant="form"
                                            label="Select Relationship" :options="$page.props.lookups.relationships"
                                            option-label="label" option-value="key" />

                                        <div v-if="form.waris_relationship === 'other'" class="mt-3">
                                            <label class="block text-xs font-medium text-slate-500 mb-1">Please
                                                specify
                                                relationship</label>
                                            <input v-model="form.waris_relationship_other" type="text"
                                                class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary px-4 py-3"
                                                placeholder="e.g. Uncle, Guardian, Cousin">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Gender</label>
                                        <RoundedSelect v-model="form.waris_gender" variant="form" label="Select Gender"
                                            :options="$page.props.lookups.genders" option-label="label"
                                            option-value="key" />

                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-slate-100">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Number
                                            IC</label>
                                        <input v-model="form.waris_ic" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm px-4 py-3 focus:ring-primary focus:border-primary">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Phone
                                            Number</label>
                                        <input v-model="form.waris_phone" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm px-4 py-3 focus:ring-primary focus:border-primary">
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- <div v-if="currentStep > 4" class="animate-fade-in text-slate-400 italic flex items-center justify-center h-48 border-2 border-dashed border-slate-100 rounded-2xl">
                            Configuration for {{ steps[currentStep-1].title }} is in progress...
                        </div> -->
                    </div>

                    <div
                        class="mt-16 flex items-center justify-end gap-4 border-t border-surface-100 pt-10 px-4 sm:px-0">
                        <button v-if="currentStep > 1" @click="prevStep"
                            class="px-6 py-3 text-sm font-bold text-slate-400 hover:text-primary transition-colors active:scale-95">
                            Back
                        </button>

                        <button @click="nextStep" :disabled="!stepValidation[currentStep]" :class="[
                            'px-10 py-3 rounded-xl font-bold transition-all active:scale-95 shadow-lg',
                            stepValidation[currentStep]
                                ? 'bg-primary hover:bg-primary-dark text-white shadow-primary-light'
                                : 'bg-slate-200 text-slate-400 cursor-not-allowed shadow-none'
                        ]">
                            <!-- Show loading when submitting -->
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>{{ isLastStep ? 'Complete Registration' : 'Next Step' }}</span>
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
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>