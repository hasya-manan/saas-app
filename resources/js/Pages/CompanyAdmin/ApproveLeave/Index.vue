<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Check, X, Undo2, Inbox, Paperclip } from 'lucide-vue-next';

const props = defineProps({
    directApprovals: { type: Array, default: () => [] },
    companyApprovals: { type: Array, default: () => [] },
    tab: { type: String, default: 'pending' },
});

const page = usePage();
const flash = computed(() => page.props.flash || {});
const processingId = ref(null);

const tabs = [
    { key: 'pending', label: 'Pending' },
    { key: 'approved', label: 'Approved' },
    { key: 'history', label: 'All History' },
];

const switchTab = (key) => {
    router.get(route('admin_company.leave.approvals'), { tab: key }, { preserveState: true, replace: true });
};

// One modal for both Reject and Withdraw
const modal = ref(null); // { leave, status }
const remarks = ref('');

const sections = computed(() => {
    const list = [
        { key: 'direct', title: 'My Team', subtitle: 'Staff who report directly to you', rows: props.directApprovals ?? [] },
    ];
    if ((props.companyApprovals ?? []).length > 0) {
        list.push({ key: 'company', title: 'Company Wide', subtitle: 'Other requests in your company', rows: props.companyApprovals });
    }
    return list;
});

const durationLabel = (duration) => ({
    full: 'Full day',
    am: 'Half day (AM)',
    pm: 'Half day (PM)',
}[duration] ?? duration);

const formatDate = (v) => new Date(v).toLocaleDateString('en-MY', { day: '2-digit', month: 'short', year: 'numeric' });
const dateRange = (leave) => {
    const start = String(leave.start_date).slice(0, 10);
    const end = String(leave.end_date).slice(0, 10);

    return start === end
        ? formatDate(start)
        : `${formatDate(start)} - ${formatDate(end)}`;
};

const statusClass = (s) => ({
    pending:   'bg-yellow-50 text-yellow-700 border-yellow-100',
    approved:  'bg-green-50 text-green-700 border-green-100',
    rejected:  'bg-red-50 text-red-600 border-red-100',
    withdrawn: 'bg-gray-100 text-gray-500 border-gray-200',
}[s] ?? 'bg-gray-50 text-gray-500 border-gray-100');

const decide = (leave, status, note = null) => {
    processingId.value = leave.id;
    router.put(
        route('admin_company.leave.update', leave.id),
        { status, remarks: note },
        {
            preserveScroll: true,
            onFinish: () => {
                processingId.value = null;
                closeModal();
            },
        }
    );
};

const approve = (leave) => decide(leave, 'approved');
const openModal = (leave, status) => { modal.value = { leave, status }; remarks.value = ''; };
const closeModal = () => { modal.value = null; remarks.value = ''; };
const confirmModal = () => decide(modal.value.leave, modal.value.status, remarks.value || null);
</script>

<template>
    <Head title="Approve Leave" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Approve Leave" subtitle="Review, approve and withdraw leave applications" />
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8 space-y-8">

            <!-- Flash messages -->
            <div v-if="flash.success" class="rounded-2xl bg-green-50 border border-green-100 text-green-700 text-sm font-medium px-5 py-3">
                {{ flash.success }}
            </div>
            <div v-if="flash.error" class="rounded-2xl bg-red-50 border border-red-100 text-red-600 text-sm font-medium px-5 py-3">
                {{ flash.error }}
            </div>

            <!-- Tabs -->
            <div class="flex gap-2">
                <button v-for="t in tabs" :key="t.key" @click="switchTab(t.key)"
                    :class="tab === t.key
                        ? 'bg-primary text-white shadow-lg shadow-primary/20'
                        : 'bg-white text-gray-500 hover:bg-gray-50 border border-gray-100'"
                    class="px-4 py-2 rounded-2xl text-sm font-bold transition-all">
                    {{ t.label }}
                </button>
            </div>

            <div v-for="section in sections" :key="section.key"
                class="bg-white overflow-hidden shadow-xl shadow-primary/5 border border-primary-border rounded-[2.5rem] p-8">

                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-800">
                        {{ section.title }}
                        <span class="ml-1 px-2 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">{{ section.rows?.length ?? 0 }}</span>
                    </h2>
                    <p class="text-xs text-gray-400 font-medium italic">{{ section.subtitle }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-y-2">
                        <thead>
                            <tr class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">
                                <th class="px-6 py-3">Staff</th>
                                <th class="px-6 py-3">Leave Type &amp; Reason</th>
                                <th class="px-6 py-3">Dates &amp; Duration</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Attachment</th>
                                <th class="px-6 py-3 text-right">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="leave in section.rows" :key="leave.id"
                                class="group bg-white hover:bg-primary-light/5 transition-all duration-300">

                                <!-- Staff -->
                                <td class="px-6 py-4 rounded-l-2xl border-y border-l border-transparent group-hover:border-primary-border">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-gray-100 flex items-center justify-center text-primary font-bold">
                                            {{ leave.user?.name?.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900">{{ leave.user?.name }}</div>
                                            <div class="text-xs text-gray-400">{{ leave.user?.email }}</div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Leave type + reason -->
                                <td class="px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                    <div class="font-bold text-gray-900">{{ leave.leave_type?.name ?? 'Leave' }}</div>
                                    <div class="text-xs text-gray-400 italic line-clamp-1">{{ leave.reason }}</div>
                                </td>

                                <!-- Dates + duration -->
                                <td class="px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                    <div class="text-sm font-medium text-gray-800">{{ dateRange(leave) }}</div>
                                    <div class="text-xs text-gray-400">
                                        {{ durationLabel(leave.leave_duration) }} &middot; {{ leave.total_days }} day(s)
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                    <span :class="statusClass(leave.status)"
                                        class="px-3 py-1 text-[10px] font-black rounded-full uppercase border">
                                        {{ leave.status }}
                                    </span>
                                    <div v-if="leave.remarks" class="text-[11px] text-gray-400 italic mt-1 line-clamp-1">
                                        {{ leave.remarks }}
                                    </div>
                                </td>

                                <!-- Attachment -->
                                <td class="px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                    <a v-if="leave.attachment" :href="`/storage/${leave.attachment}`" target="_blank"
                                        class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:underline">
                                        <Paperclip :size="14" /> View
                                    </a>
                                    <span v-else class="text-xs text-gray-300 italic">None</span>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 rounded-r-2xl border-y border-r border-transparent group-hover:border-primary-border text-right">
                                    <!-- Pending: approve / reject -->
                                    <div v-if="leave.status === 'pending'" class="inline-flex items-center gap-2">
                                        <button @click="approve(leave)" :disabled="processingId === leave.id"
                                            class="flex items-center gap-1 px-3 py-2 rounded-xl text-xs font-bold bg-green-50 text-green-700 hover:bg-green-100 disabled:opacity-50 transition-colors">
                                            <Check :size="16" /> Approve
                                        </button>
                                        <button @click="openModal(leave, 'rejected')" :disabled="processingId === leave.id"
                                            class="flex items-center gap-1 px-3 py-2 rounded-xl text-xs font-bold bg-red-50 text-red-600 hover:bg-red-100 disabled:opacity-50 transition-colors">
                                            <X :size="16" /> Reject
                                        </button>
                                    </div>

                                    <!-- Approved: withdraw -->
                                    <button v-else-if="leave.status === 'approved'"
                                        @click="openModal(leave, 'withdrawn')" :disabled="processingId === leave.id"
                                        class="inline-flex items-center gap-1 px-3 py-2 rounded-xl text-xs font-bold bg-orange-50 text-orange-600 hover:bg-orange-100 disabled:opacity-50 transition-colors">
                                        <Undo2 :size="16" /> Withdraw
                                    </button>

                                    <span v-else class="text-xs text-gray-300 italic">-</span>
                                </td>
                            </tr>

                            <tr v-if="!section.rows?.length">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-400 italic">
                                    <div class="flex flex-col items-center justify-center gap-2">
                                        <Inbox :size="40" class="text-gray-200" />
                                        <p>No leave requests here.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Reject / Withdraw modal -->
        <div v-if="modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 px-4">
            <div class="w-full max-w-md bg-white rounded-[2rem] shadow-xl p-8">
                <h3 class="text-lg font-bold text-gray-800">
                    {{ modal.status === 'withdrawn' ? 'Withdraw approved leave' : 'Reject leave request' }}
                </h3>
                <p class="text-xs text-gray-400 italic mb-5">
                    {{ modal.leave.user?.name }} &middot; {{ dateRange(modal.leave) }}
                </p>

                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">
                    Remarks (optional)
                </label>
                <textarea v-model="remarks" rows="3" maxlength="500"
                    class="w-full rounded-2xl border-gray-100 bg-gray-50/50 focus:ring-primary text-sm p-4"
                    :placeholder="modal.status === 'withdrawn' ? 'Reason for withdrawing...' : 'Reason for rejection...'"></textarea>

                <div class="flex justify-end gap-3 mt-6">
                    <button @click="closeModal"
                        class="px-4 py-2 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button @click="confirmModal" :disabled="processingId === modal.leave.id"
                        class="px-4 py-2 rounded-xl text-sm font-bold text-white disabled:opacity-50"
                        :style="{ backgroundColor: modal.status === 'withdrawn' ? '#f97316' : '#ef4444' }">
                        {{ modal.status === 'withdrawn' ? 'Confirm Withdraw' : 'Confirm Reject' }}
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>