<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue'; // Adjust path if needed
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import RoundedSelect from '@/Components/RoundedSelect.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';


const props = defineProps({
    leaveTypes: {
        type: Array,
        required: true,
    },
    leaveBalances: {
        type: Array,
        required: true,
    }
});

const form = useForm({
    leave_type_id: '',
    start_date: '',
    end_date: '',
    leave_duration: 'full',
    half_day_session: 'am',
    reason: '',
});

const selectedBalance = computed(() => {
    if (!form.leave_type_id) return null;
    // Matches the leave_type_id from the dropdown selection to the user's balances
    return props.leaveBalances.find(b => b.leave_type_id === form.leave_type_id);
});

const submit = () => {
    form.post(route('staff.applyLeave.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Apply Leave" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader 
                title="Apply Leave"
                subtitle="Submit a new leave application for management review and tracking." 
            />
        </template>

        <div class="max-w-4xl">
            <form @submit.prevent="submit" class="bg-white rounded-2xl shadow-sm border border-primary-border/30 overflow-hidden">
                
                <!-- Section 1: Leave Selection & Dates -->
                <div class="p-8">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="h-8 w-1 bg-primary rounded-full"></div>
                        <h3 class="text-lg font-bold text-gray-800">Leave Details</h3>
                    </div>

                    <div class="space-y-6">
                        <!-- Leave Type Selection -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                            <div class="md:col-span-1">
                                <InputLabel for="leave_type_id" value="Leave Type" class="font-semibold text-gray-700" />
                                <p class="text-xs text-gray-400 mt-1 leading-relaxed">Select the category of leave you wish to take.</p>
                            </div>
                           <div class="md:col-span-2">
                            <RoundedSelect 
                                v-model="form.leave_type_id" 
                                variant="form"
                                label="Select a leave type..." 
                                :options="leaveTypes"
                                option-label="name" 
                                option-value="id" 
                            />
                            <InputError class="mt-2" :message="form.errors.leave_type_id" />
                        </div>
                        </div>

                        <!-- Live Balance Indicator (Conditional) -->
                        <div v-if="selectedBalance" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-1"></div>
                            <div class="md:col-span-2">
                                <div class="bg-gray-50/70 p-4 rounded-xl border border-gray-200/80 flex justify-between items-center text-sm">
                                    <span class="text-gray-600 font-medium">Available Balance:</span>
                                    <span class="font-bold text-gray-900 bg-white px-3 py-1.5 rounded-lg border border-gray-200 shadow-xs">
                                        {{ selectedBalance.allotted_days - selectedBalance.taken_days }} Days Remaining
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Start Date -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-1">
                                <InputLabel for="start_date" value="Start Date" class="font-semibold text-gray-700" />
                                <p class="text-xs text-gray-400 mt-1 leading-relaxed">First day of your absence.</p>
                            </div>
                            <div class="md:col-span-2">
                                <TextInput id="start_date" type="date" 
                                    class="block w-full border-gray-200 bg-gray-50/30 focus:bg-white focus:ring-primary-border focus:border-primary transition-all rounded-xl shadow-sm" 
                                    v-model="form.start_date" required />
                                <InputError class="mt-2" :message="form.errors.start_date" />
                            </div>
                        </div>

                        <!-- End Date -->
                       
                        <!-- End Date & Timing -->
                       
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-1">
                                <InputLabel for="end_date" value="End Date & Timing" class="font-semibold text-gray-700" />
                                <p class="text-xs text-gray-400 mt-1 leading-relaxed">Last day of your absence and timing details.</p>
                            </div>
                            
                            <div class="md:col-span-2 space-y-4">
                                <!-- Date Input -->
                                <div>
                                    <TextInput id="end_date" type="date" 
                                        class="block w-full border-gray-200 bg-gray-50/30 focus:bg-white focus:ring-primary-border focus:border-primary transition-all rounded-xl shadow-sm" 
                                        v-model="form.end_date" required />
                                    <InputError class="mt-2" :message="form.errors.end_date" />
                                </div>

                                <!-- Main Options: Full Day vs Half Day Toggle -->
                                <div class="flex items-center space-x-8 pt-1">
                                    <!-- Full Day Option -->
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" value="full" v-model="form.leave_duration" 
                                            class="h-4 w-4 rounded-full border-gray-300 text-primary focus:ring-primary" />
                                        <span class="text-sm font-medium text-gray-700">Full Day</span>
                                    </label>

                                    <!-- Half Day Toggle (Defaults to 'am' when selected) -->
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" value="am" v-model="form.leave_duration" 
                                            class="h-4 w-4 rounded-full border-gray-300 text-primary focus:ring-primary" />
                                        <span class="text-sm font-medium text-gray-700">Half Day</span>
                                    </label>
                                </div>

                                <!-- Conditional AM/PM Session Options (Rendered dynamically from database lookups) -->
                                <div v-if="form.leave_duration === 'am' || form.leave_duration === 'pm'" 
                                    class="flex items-center space-x-6 pl-3 py-2.5 border-l-2 border-primary/30 bg-gray-50/50 rounded-r-xl transition-all">
                                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Session:</span>
                                    
                                    <template v-for="lookup in $page.props.lookups.leave_duration" :key="lookup.key">
                                        <label v-if="lookup.key === 'am' || lookup.key === 'pm'" class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" :value="lookup.key" v-model="form.leave_duration" 
                                                class="h-4 w-4 rounded-full border-gray-300 text-primary focus:ring-primary" />
                                            <span class="text-sm text-gray-700">{{ lookup.label }}</span>
                                        </label>
                                    </template>
                                </div>

                                <InputError class="mt-2" :message="form.errors.leave_duration" />
                            </div>
                        </div>
                        
                    </div>
                </div>

                <div class="px-8">
                    <hr class="border-primary-border/10" />
                </div>

                <!-- Section 2: Reason & Remarks -->
                <div class="p-8">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="h-8 w-1 bg-primary rounded-full opacity-80"></div>
                        <h3 class="text-lg font-bold text-gray-800">Additional Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <InputLabel for="reason" value="Reason for Leave" class="font-semibold text-gray-700" />
                            <p class="text-xs text-gray-400 mt-1 leading-relaxed">Provide details or context for management approval.</p>
                        </div>
                        <div class="md:col-span-2">
                            <textarea 
                                id="reason"
                                v-model="form.reason" 
                                rows="4"
                                placeholder="e.g. Family vacation, medical appointment..."
                                class="block w-full border-gray-200 bg-gray-50/30 focus:bg-white focus:ring-primary-border focus:border-primary transition-all rounded-xl shadow-sm text-sm text-gray-700 p-3"
                                required
                            ></textarea>
                            <InputError class="mt-2" :message="form.errors.reason" />
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="bg-primary-light/20 p-8 border-t border-primary-border/20 flex items-center justify-end gap-6">
                    <button type="button" @click="form.reset()" 
                        class="text-sm font-semibold text-gray-500 hover:text-primary transition-colors">
                        Clear Form
                    </button>
                    <PrimaryButton 
                        class="px-8 py-3 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold shadow-lg shadow-primary/20 transition-all transform active:scale-95 border-none" 
                        :class="{ 'opacity-50': form.processing }" 
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Submitting...' : 'Submit Application' }}
                    </PrimaryButton>
                </div>

            </form>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>