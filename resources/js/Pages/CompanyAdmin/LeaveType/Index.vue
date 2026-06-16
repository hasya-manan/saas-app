<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import PageHeader from '@/Components/PageHeader.vue'; 
import GlobalFilter from '@/Components/GlobalFilter.vue';
import { Edit2, X } from 'lucide-vue-next';

defineProps({
    leaveTypes: Object,
    filters: Object
});
</script>

<template>
    <Head title="Leave Types" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader 
                title="Leave Type Management" 
                subtitle="Define leave policies and entitlement rules for your company"
            />
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8">
             <GlobalFilter routeName="admin_company.leavetypes.index" :filters="filters" dataKey="leaveTypes" :leaveTypes="leaveTypes.data"
                :showRoleaveTypes="true" placeholder="Search staff by name or email..." />

             <div class="flex flex-col lg:flex-row items-start gap-6">

                <!--TODO:: if have a transition for open slide effect side by side table-->
                <div class="w-full lg:w-[100%] transition-all duration-500">
                 <div class="bg-white overflow-hidden shadow-xl shadow-primary/5 border border-primary-border rounded-[2.5rem] p-8">
                     <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-y-2">
                            <thead>
                                <tr class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">
                                    <th class="px-4 py-2 text-left">Leave Policy</th>
                                    <th class="px-4 py-2 text-center">Probation</th>
                                    <th class="px-4 py-2 text-center">Max Carryover</th>
                                    <th class="px-4 py-2 text-center">Pro-Rata</th>
                                    <th class="px-4 py-2 text-right">Actions</th>
                                </tr>
                               </thead>
                                <tbody>
                                    <tr v-for="leave in leaveTypes.data" :key="leave.id"
                                        class="group bg-white hover:bg-primary-light/5 transition-all duration-300">

                                        <td
                                            class="w-[40%] px-6 py-4 rounded-l-2xl border-y border-l border-transparent group-hover:border-primary-border">
                                            <div class="font-bold text-gray-900">{{ leave.name }}</div>
                                        </td>
                                        <td
                                            class="w-[15%] px-6 py-4 border-y border-transparent group-hover:border-primary-border"">
                                            <div class="font-bold text-gray-900">{{ leave.probation_period_months }}
                                                Months</div>
                                        </td>
                                        <td class="w-[15%] px-6 py-4 border-y border-transparent group-hover:border-primary-border">
                                            <span v-if="leave.tiers && leave.tiers.length > 0 && leave.tiers[0].max_carry_forward_days > 0"
                                                class="font-medium text-primary">
                                                {{ leave.tiers[0].max_carry_forward_days }} Days
                                            </span>
                                            <span v-else class="text-gray-400 italic">None</span>
                                        </td>
                                       <td class="w-[15%] px-6 py-4 border-y border-transparent group-hover:border-primary-border"">
                                           <span :class="leave.is_pro_rata
                                            ? 'bg-green-50 text-green-600 border-green-100' : 'bg-gray-50 text-gray-600 border-gray-100'"
                                            class=" px-3 py-1 text-[10px] font-black rounded-full uppercase border">
                                                {{ leave.is_pro_rata ? 'Yes' : 'No' }}
                                             </span>
                                        </td>
                                        <td class="w-[15%] px-6 py-4 rounded-r-2xl border-y border-r border-transparent group-hover:border-primary-border text-right space-x-2">
                                            <BaseButton @click="openEditPanel(leave)">Edit</BaseButton>
                                        </td>
                                    </tr>
                                </tbody>

                            </table>
                        </div>
                        <div class="mt-8">
                            <Pagination :links="leaveTypes.links" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>