<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import { Plus, X, Inbox, Calendar, Paperclip } from 'lucide-vue-next';

const props = defineProps({
    leaves: Object,
    leaveTypes: Array,
    filters: Object,
});

// Inertia form for submitting a new leave request
// const form = useForm({
//     leave_type_id: '',
//     start_date: '',
//     end_date: '',
//     leave_duration: 'full',
//     reason: '',
//     attachment: null,
// });

const isEditPanelOpen = ref(false);

const openCreatePanel = () => {
    form.reset();
    isEditPanelOpen.value = true;
};

const closeEditPanel = () => {
    isEditPanelOpen.value = false;
    form.reset();
};

const handleFileChange = (e) => {
    form.attachment = e.target.files[0];
};

const submitForm = () => {
    form.post(route('leave.store'), {
        forceFormData: true,
        onSuccess: () => closeEditPanel(),
    });
};

// Status badge styling helper
const getStatusBadge = (status) => {
    switch (status) {
        case 'approved': return 'bg-green-50 text-green-600 border-green-100';
        case 'rejected': return 'bg-red-50 text-red-600 border-red-100';
        default: return 'bg-amber-50 text-amber-600 border-amber-100';
    }
};
</script>

<template>
    <Head title="My Leave Applications" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="My Leave Requests" subtitle="Track your submitted time-off and apply for new leave">
                <template #actions>
                    <button @click="openCreatePanel"
                        class="group flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-2xl text-sm font-bold shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all duration-300">
                        <Plus :size="20" class="group-hover:rotate-90 transition-transform duration-300" />
                        Apply Leave hello
                    </button>
                </template>
            </PageHeader>
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-start gap-6">
                
                <!-- Leave History Table -->
                <div :class="[isEditPanelOpen ? 'lg:w-[60%] w-full' : 'w-full']" class="transition-all duration-500">
                    <div class="bg-white overflow-hidden shadow-xl shadow-primary/5 border border-primary-border rounded-[2.5rem] p-8">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-separate border-spacing-y-2">
                                <thead>
                                    <tr class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">
                                        <th class="px-6 py-3">Leave Type & Reason</th>
                                        <th class="px-6 py-3">Dates & Duration</th>
                                        <th class="px-6 py-3">Status</th>
                                        <th class="px-6 py-3 text-right">Attachment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="leave in (leaves.data || leaves)" :key="leave.id"
                                        class="group bg-white hover:bg-primary-light/5 transition-all duration-300">
                                        
                                        <td class="px-6 py-4 rounded-l-2xl border-y border-l border-transparent group-hover:border-primary-border">
                                            <div class="flex items-center gap-3">
                                                <div class="h-10 w-10 rounded-xl bg-blue-50 flex items-center justify-center text-primary font-bold">
                                                    <Calendar :size="20" />
                                                </div>
                                                <div>
                                                    <div class="font-bold text-gray-900">{{ leave.leave_type?.name || 'Leave' }}</div>
                                                    <div class="text-xs text-gray-400 font-medium italic line-clamp-1">{{ leave.reason }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                            <div class="text-xs font-bold text-gray-800">{{ leave.start_date }} to {{ leave.end_date }}</div>
                                            <div class="text-[10px] text-gray-400 uppercase tracking-widest mt-0.5">
                                                {{ leave.total_days }} Days ({{ leave.leave_duration }})
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                            <span class="px-3 py-1 text-[10px] font-black rounded-full uppercase border" :class="getStatusBadge(leave.status)">
                                                {{ leave.status }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 rounded-r-2xl border-y border-r border-transparent group-hover:border-primary-border text-right">
                                            <a v-if="leave.attachment" :href="`/storage/${leave.attachment}`" target="_blank"
                                                class="inline-flex items-center gap-1 text-xs font-semibold text-primary bg-primary/5 px-3 py-1.5 rounded-xl hover:bg-primary/10 transition-colors">
                                                <Paperclip :size="14" /> View File
                                            </a>
                                            <span v-else class="text-xs text-gray-300 italic">None</span>
                                        </td>
                                    </tr>

                                    <tr v-if="(leaves.data || leaves).length === 0">
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">
                                            <div class="flex flex-col items-center justify-center gap-2">
                                                <Inbox :size="40" class="text-gray-200" />
                                                <p>You haven't submitted any leave requests yet.</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-8">
                            <Pagination :links="leaves.links" />
                        </div>
                    </div>
                </div>

                <!-- Apply Leave Slide-over Form -->
                <div v-if="isEditPanelOpen" class="w-full lg:w-[40%] sticky top-6 z-10 animate-in slide-in-from-right duration-500">
                    <div class="bg-white border border-primary/10 rounded-[2.5rem] shadow-xl p-8">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="text-xl font-bold text-gray-800">Apply for Leave</h2>
                                <p class="text-xs text-gray-400 font-medium italic">Submit time-off request</p>
                            </div>
                            <button @click="closeEditPanel" class="p-2 bg-gray-50 rounded-xl text-gray-400 hover:text-gray-600">
                                <X :size="20" />
                            </button>
                        </div>

                        <form @submit.prevent="submitForm" class="space-y-6">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Leave Type</label>
                                <select v-model="form.leave_type_id" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4" required>
                                    <option value="">Select Leave Type</option>
                                    <option v-for="type in leaveTypes" :key="type.id" :value="type.id">{{ type.name }}</option>
                                </select>
                                <div v-if="form.errors.leave_type_id" class="text-red-500 text-xs mt-1">{{ form.errors.leave_type_id }}</div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Start Date</label>
                                <input v-model="form.start_date" type="date" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4" required>
                                <div v-if="form.errors.start_date" class="text-red-500 text-xs mt-1">{{ form.errors.start_date }}</div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">End Date</label>
                                <input v-model="form.end_date" type="date" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4" required>
                                <div v-if="form.errors.end_date" class="text-red-500 text-xs mt-1">{{ form.errors.end_date }}</div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Duration</label>
                                <select v-model="form.leave_duration" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4" required>
                                    <option value="full">Full Day</option>
                                    <option value="am">Half Day (AM)</option>
                                    <option value="pm">Half Day (PM)</option>
                                </select>
                                <div v-if="form.errors.leave_duration" class="text-red-500 text-xs mt-1">{{ form.errors.leave_duration }}</div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Reason</label>
                                <textarea v-model="form.reason" rows="3" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4" placeholder="State your reason..." required></textarea>
                                <div v-if="form.errors.reason" class="text-red-500 text-xs mt-1">{{ form.errors.reason }}</div>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Attachment (MC if applicable)</label>
                                <input type="file" @change="handleFileChange" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                                <div v-if="form.errors.attachment" class="text-red-500 text-xs mt-1">{{ form.errors.attachment }}</div>
                            </div>

                            <button type="submit" :disabled="form.processing"
                                class="w-full bg-primary text-white py-4 rounded-[1.5rem] font-bold shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all">
                                {{ form.processing ? 'Submitting...' : 'Submit Leave Request' }}
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>