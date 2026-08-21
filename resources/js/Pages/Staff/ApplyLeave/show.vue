<script setup>
import { ref } from 'vue';
import { Head, useForm, Link} from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import { Plus, X, Inbox, Calendar, Paperclip } from 'lucide-vue-next';
import GlobalFilter from '@/Components/GlobalFilter.vue';

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



const closeEditPanel = () => {
    isEditPanelOpen.value = false;
    form.reset();
};

const handleFileChange = (e) => {
    form.attachment = e.target.files[0];
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
             <GlobalFilter routeName="staff.applyLeave.show" :filters="filters" dataKey="leaveTypes" :leaveTypes="leaveTypes" :departments="allDepartments" 
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

                <!-- Update Leave Slide-over Form / withdrawn leave -->
                

            </div>
        </div>
    </AuthenticatedLayout>
</template>