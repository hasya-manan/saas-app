<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import RegistrationWizard from '@/Components/RegistrationWizard.vue';
import RoundedSelect from '@/Components/RoundedSelect.vue';

const props = defineProps({
    roles: Array,
    departments: Array,
    staffList: Array, 

});

const currentStep = ref(1);
const isLastStep = computed(() => currentStep.value === steps.length);
const hasDepartments = computed(() => props.departments && props.departments.length > 0)
const isOthers = computed(() => form.department_id === 'others')


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
    // Step 2 : Personal
    name: '',// name need to change for user staff
    ic_number: '',
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
    epf_rate_employee: '',
    epf_rate_employer: '',
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

});

/**
 * Validation Schema Logic
 * This tells the Wizard when to show the green checkmark
 */


// Function to check email uniqueness (called on @blur)
const isValidMYIC = (ic) => /^\d{12}$/.test(ic);

// Global Email Check using Inertia
const checkEmailUnique = () => {
    if (!form.email.includes('@')) return;

    router.post(route('validation.email'), {
        email: form.email
    }, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            // Clear error manually if backend passed
            form.clearErrors('email');
        },
        onError: (errors) => {
            // This is the "Global" part: we map the response to our form
            form.setError('email', errors.email);
        }
    });
};

const stepValidation = computed(() => ({
    // Step 1: No errors from backend + basic length check
    1: form.email.includes('@') && !form.errors.email && form.password.length >= 8,

    // Step 2: User IC
    2: isValidMYIC(form.ic_number),

    // Step 3: Role and Department (role_id moved here)
    3: form.role_id !== '' && (form.department_id !== '' || form.name_department !== ''),

    // Step 4: Financial
    4: form.basic_salary > 0 && form.bank_name !== '' && form.bank_account_no !== '',

    // Step 5: Emergency Contact + IC Check
    5: form.waris_name !== '' && form.waris_phone !== '' && isValidMYIC(form.waris_ic),
}));



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
                    <p class="text-sm text-gray-500">Onboard new team members and configure their professional profile
                    </p>
                </div>

                <Link :href="route('admin_company.users.index')"
                    class="text-sm font-semibold text-primary hover:text-primary-dark transition-colors">
                    &larr; Back to Directory
                </Link>
            </div>
        </template>

        <div
            class="flex min-h-[calc(100vh-200px)] bg-white rounded-2xl overflow-hidden shadow-sm border border-surface-100">

            <RegistrationWizard v-model:currentStep="currentStep" :steps="steps" :validationSchema="stepValidation">
                <template #title>New Staff</template>
            </RegistrationWizard>

            <main class="flex-1 bg-white flex flex-col">
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
                                   <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                                    <input v-model="form.email" type="email" @blur="checkEmailUnique"
                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                        :class="{ 'border-red-500': form.errors.email }"
                                        placeholder="e.g. staff@company.com">
                                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-500 font-medium">
                                        {{ form.errors.email }}
                                    </p>
                                </div>
                                <!--TODO:: need to add view icon eye the password field-->
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                                    <input v-model="form.password" type="password"
                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                        placeholder="e.g. need to have at least 8 characters">
                                </div>

                            </div>
                        </div>
                        <!-- Step 2:Personal Information -->
                        <div v-if="currentStep === 2" class="space-y-8 animate-fade-in">

                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name (as per
                                        IC)</label>
                                    <input v-model="form.name" type="text"
                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                        placeholder="e.g. Ahmad bin Ibrahim">
                                </div>

                                <div class="grid grid-cols-2 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">IC Number</label>
                                        <input v-model="form.ic_number" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                            placeholder="000308000000">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Phone
                                            Number</label>
                                        <input v-model="form.phone" type="text"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                            placeholder="0123456789">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Marital
                                            Status</label>

                                        <RoundedSelect v-model="form.marital_status" variant="form"
                                            label="Select Status" :options="$page.props.lookups.marital_statuses"
                                            option-label="label" option-value="key" />
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-700 mb-2">Date of Birth</label>

                                        <input v-model="form.dob" type="date"
                                            class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                            >
                                    </div>
                                    <!--TODO:: add DOB and other columns-->

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
                                                <RoundedSelect v-model="form.state" variant="form" label="Select Status"
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
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Staff Role </label>
                                    <RoundedSelect v-model="form.role_id" variant="form" label="Select a role..."
                                        :options="roles" option-label="display_name" option-value="id" />
                                </div>
                            </div>

                            <div class="pt-6 border-t border-slate-100">
                                <h3 class="text-xs font-bold text-primary uppercase tracking-widest mb-6">Department
                                </h3>

                                <div class="grid grid-cols-1 gap-5">
                                    <div>
                                        <label
                                            class="block text-sm font-semibold text-slate-700 mb-2">Department</label>

                                        <!-- CASE 1: Tenant has departments → show dropdown -->
                                        <div v-if="hasDepartments">
                                            <RoundedSelect v-model="form.department_id" variant="form"
                                                label="Select a department..." :options="departments"
                                                :extra-options="[{ id: 'others', name: 'Others (Create new)' }]"
                                                option-label="name" option-value="id" />

                                            <!-- Show text input only when "Others" is picked -->
                                           <div v-if="isOthers" class="mt-2 space-y-3">
                                                <div>
                                                    <label
                                                        class="block text-sm font-semibold text-slate-700 mb-2">Department
                                                        Name</label>
                                                    <input v-model="form.name_department" type="text"
                                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                                        placeholder="Enter new department name" />
                                                </div>

                                                <div>
                                                    <label
                                                        class="block text-sm font-semibold text-slate-700 mb-2">Description
                                                        <span class="normal-case font-normal">(Optional)</span></label>
                                                    <input v-model="form.description" type="text"
                                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                                        placeholder="Department description (optional)" />
                                                </div>

                                                <!-- HOD Assignment (Optional) -->
                                                <div>
                                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Head
                                                        of Department
                                                        <span class="normal-case font-normal">(Optional)</span></label>

                                                    <!-- Toggle: assign this new staff as HOD -->
                                                    <label
                                                        class="flex items-center gap-2 mb-2 cursor-pointer select-none">
                                                        <input type="checkbox" v-model="form.is_hod"
                                                            class="rounded border-primary-border text-primary focus:ring-primary" />
                                                        <span class="text-sm text-slate-600">Assign this new staff as
                                                            HOD</span>
                                                    </label>

                                                    <!-- If not assigning new staff, pick existing staff as HOD -->
                                                    <RoundedSelect v-if="!form.is_hod" v-model="form.hod_id"
                                                        variant="form" label="Select existing staff as HOD..."
                                                        :options="staffList" option-label="name" option-value="id" />
                                                </div>
                                            </div>
                                        </div>
                                        <!-- CASE 2: No departments yet → always show text input -->
                                        <div v-else>
                                            <input v-model="form.name_department" type="text"
                                                class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                                placeholder="Enter department name" />
                                            <input v-model="form.description" type="text"
                                                class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all"
                                                placeholder="Enter description department" />
                                        </div>

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
                                        <div></div>
                                    </div>
                                </div>
                            </div>
                        </div>

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

                                <div class="grid grid-cols-2 gap-6">
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

                            <div class="pt-6 border-t border-slate-100">
                                <h3 class="text-xs font-bold text-primary uppercase tracking-widest mb-6">Statutory
                                    Contributions</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-semibold text-slate-700 mb-2">EPF
                                                Number</label>
                                            <input v-model="form.epf_no" type="text"
                                                class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all">
                                        </div>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="text-[10px] font-bold text-slate-500 uppercase">Employee
                                                    %</label>
                                                <input v-model="form.epf_rate_employee" type="number"
                                                    class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all">
                                            </div>
                                            <div>
                                                <label class="text-[10px] font-bold text-slate-500 uppercase">Employer
                                                    %</label>
                                                <input v-model="form.epf_rate_employer" type="number"
                                                    class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all" ">
                                            </div>
                                        </div>
                                    </div>

                                    <div class=" space-y-4">
                                                <div>
                                                    <label class="block text-sm font-semibold text-slate-700 mb-2">SOCSO
                                                        Number</label>
                                                    <input v-model="form.socso_no" type="text"
                                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all">
                                                </div>
                                                <div>
                                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Tax
                                                        (LHDN)
                                                        Number</label>
                                                    <input v-model="form.tax_no" type="text"
                                                        class="w-full border-primary-border rounded-xl shadow-sm focus:ring-primary focus:border-primary placeholder:text-slate-300 px-4 py-3 transition-all">
                                                </div>
                                            </div>
                                        </div>

                                        <label
                                            class="mt-6 flex items-center justify-between p-4 bg-slate-100/50 hover:bg-slate-100 rounded-xl cursor-pointer transition-colors border border-transparent hover:border-slate-200">
                                            <div>
                                                <p class="text-sm font-bold text-slate-900">EIS Contribution</p>
                                                <p class="text-xs text-slate-600">
                                                    Enable Employment Insurance System contribution
                                                </p>
                                            </div>
                                            <input v-model="form.eis_enabled" type="checkbox"
                                                class="w-6 h-6 text-primary rounded-md border-slate-400 focus:ring-primary cursor-pointer">
                                        </label>
                                    </div>
                                </div>
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
                                                    label="Select Relationship"
                                                    :options="$page.props.lookups.relationships" option-label="label"
                                                    option-value="key" />

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
                                                <label
                                                    class="block text-sm font-semibold text-slate-700 mb-2">Gender</label>
                                                <RoundedSelect v-model="form.waris_gender" variant="form"
                                                    label="Select Gender" :options="$page.props.lookups.genders"
                                                    option-label="label" option-value="key" />

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