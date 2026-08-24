<script setup>
import { ref } from 'vue';
import { Head, useForm, Link} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import { Plus, X, Inbox, Calendar, Paperclip, Edit2 } from 'lucide-vue-next';
import GlobalFilter from '@/Components/GlobalFilter.vue';


const props = defineProps({
    leaves: Object,
    leaveTypes: Array,
    filters: Object,
});


const isEditPanelOpen = ref(false);
const selectedleave = ref(null);

const editForm = useForm({
    id: null,
    leave_type_id: '',
    start_date: '',
    end_date: '',
    leave_duration: 'full',
    reason: '',
    attachment: null,
});
const openEditPanel = (leave) => {
    if (!leave) return;
    
    // 1. Assign the selected leave ID (UUID) to the form
    editForm.id = leave.id;
    
    // 2. Populate the form fields with the existing leave data
    editForm.leave_type_id = leave.leave_type_id;
    editForm.start_date = leave.start_date;
    editForm.end_date = leave.end_date;
    editForm.leave_duration = leave.leave_duration;
    editForm.reason = leave.reason;
    editForm.attachment = null; // Reset file input so it doesn't resend old data accidentally
    
    // 3. Open the panel
    isEditPanelOpen.value = true;
};



const closeEditPanel = () => {
    isEditPanelOpen.value = false;
    editForm.reset();
    editForm.clearErrors();
};




const updateLeave = () => {
    editForm.post(route('staff.applyLeave.update', editForm.id), {
        forceFormData: true, // Required because we are handling file attachments
        onSuccess: () => {
            closeEditPanel();
        },
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
                <!-- Use Inertia's Link component to navigate to the create page -->
               

                <Link :href="route('staff.applyLeave.store')"
                    class="group flex items-center gap-2 bg-primary hover:bg-primary-dark text-white px-5 py-2.5 rounded-xl text-sm font-bold transition-all shadow-sm shadow-primary/20">
                    <Plus :size="20" class="group-hover:rotate-90 transition-transform duration-300" /> 
                    <span>Apply Leave</span>
                </Link>
            </template>
        </PageHeader>
    </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8">
             <GlobalFilter routeName="staff.applyLeave.show" :filters="filters" dataKey="leaveTypes" :leaveTypes="leaveTypes" " 
                :showRole="false" :showRoleaveTypes="true" placeholder="Search staff by name leaves Type..." />
            
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
                                        <th class="px-6 py-3">Action</th>
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

                                      <!-- 4. Middle Cell (Attachment) - REMOVED rounded-r-2xl from here! -->
                                        <td class="px-6 py-4 border-y border-transparent group-hover:border-primary-border text-right">
                                            <a v-if="leave.attachment" :href="`/storage/${leave.attachment}`" target="_blank"
                                                class="inline-flex items-center gap-1 text-xs font-semibold text-primary bg-primary/5 px-3 py-1.5 rounded-xl hover:bg-primary/10 transition-colors">
                                                <Paperclip :size="14" /> View File
                                            </a>
                                            <span v-else class="text-xs text-gray-300 italic">None</span>
                                        </td>

                                        <!-- 5. Last Cell (Action - Right Rounded) -->
                                        <td class="px-6 py-4 rounded-r-2xl border-y border-r border-transparent group-hover:border-primary-border text-center">
                                            <div class="flex items-center justify-center gap-2" v-if="leave.status === 'pending'">
                                                <BaseButton variant="outline" size="sm" @click="openEditPanel(leave)">
                                                    <Edit2 :size="14" />
                                                </BaseButton>

                                                <!-- Withdraw BaseButton -->
                                                <BaseButton variant="danger" size="sm" @click="withdrawLeave(leave.id)">
                                                    Withdraw
                                                </BaseButton>
                                            </div>
                                            <span v-else class="text-xs text-gray-300 italic">Locked</span>
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

                <!-- Update Leave Slide-over Form / withdrawn leave -->
                 <!-- Side-by-side Edit Panel -->
<div v-if="isEditPanelOpen" class="w-full lg:w-[40%] sticky top-6 z-10 animate-in slide-in-from-right duration-500">
    <div class="bg-white border border-primary/10 rounded-[2.5rem] shadow-xl p-8">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Edit Leave Application</h2>
                <p class="text-xs text-gray-400 font-medium italic">Modify your pending time-off request</p>
            </div>
            <button @click="closeEditPanel" class="p-2 bg-gray-50 rounded-xl text-gray-400 hover:text-gray-600 transition-colors">
                <X :size="20" />
            </button>
        </div>

        <form @submit.prevent="updateLeave" class="space-y-6">
            <!-- Leave Type Selection -->
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Leave Type</label>
                <select v-model="editForm.leave_type_id" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4">
                    <option v-for="type in leaveTypes" :key="type.id" :value="type.id">
                        {{ type.name }}
                    </option>
                </select>
                <div v-if="editForm.errors.leave_type_id" class="text-red-500 text-xs mt-1 ml-1">{{ editForm.errors.leave_type_id }}</div>
            </div>

            <!-- Start Date -->
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Start Date</label>
                <input v-model="editForm.start_date" type="date" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4">
                <div v-if="editForm.errors.start_date" class="text-red-500 text-xs mt-1 ml-1">{{ editForm.errors.start_date }}</div>
            </div>

            <!-- End Date -->
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">End Date</label>
                <input v-model="editForm.end_date" type="date" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4">
                <div v-if="editForm.errors.end_date" class="text-red-500 text-xs mt-1 ml-1">{{ editForm.errors.end_date }}</div>
            </div>

            <!-- Leave Duration -->
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Duration Type</label>
                <select v-model="editForm.leave_duration" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4">
                    <option value="full">Full Day</option>
                    <option value="am">Morning (AM)</option>
                    <option value="pm">Afternoon (PM)</option>
                </select>
                <div v-if="editForm.errors.leave_duration" class="text-red-500 text-xs mt-1 ml-1">{{ editForm.errors.leave_duration }}</div>
            </div>

            <!-- Reason -->
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Reason</label>
                <textarea v-model="editForm.reason" rows="3" class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4"></textarea>
                <div v-if="editForm.errors.reason" class="text-red-500 text-xs mt-1 ml-1">{{ editForm.errors.reason }}</div>
            </div>

            <!-- Attachment -->
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Attachment (Optional)</label>
                <input type="file" @input="editForm.attachment = $event.target.files[0]" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                <div v-if="editForm.errors.attachment" class="text-red-500 text-xs mt-1 ml-1">{{ editForm.errors.attachment }}</div>
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2">
                <button type="button" @click="closeEditPanel"
                    class="w-1/2 bg-gray-100 text-gray-600 py-4 rounded-[1.5rem] font-bold hover:bg-gray-200 transition-all">
                    Cancel
                </button>
                <button type="submit" :disabled="editForm.processing"
                    class="w-1/2 bg-primary text-white py-4 rounded-[1.5rem] font-bold shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all">
                    {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                </button>
            </div>
        </form>
    </div>
</div>
                

            </div>
        </div>
    </AuthenticatedLayout>
</template>