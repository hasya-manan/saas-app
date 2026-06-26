<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import PageHeader from '@/Components/PageHeader.vue'; 
import LeaveModal from '@/Components/Leaves/LeaveModal.vue';
import GlobalFilter from '@/Components/GlobalFilter.vue';
import { Edit2, X, ChevronRight, ChevronDown, Plus }from 'lucide-vue-next';
import { ref } from 'vue';

defineProps({
    leaveTypes: Object,
    filters: Object
});

const expandedRows = ref([]);

const toggleRow = (id) => {
    if (expandedRows.value.includes(id)) {
        expandedRows.value = expandedRows.value.filter(rowId => rowId !== id);
    } else {
        expandedRows.value.push(id);
    }
};

const isModalOpen = ref(false);
const editingLeave = ref(null); 
const activeTab = ref('general'); 

const openEditModal = (data, tab = 'general') => {
  console.log("Button clicked, data received:", data); // Add this line
  
  editingLeave.value = JSON.parse(JSON.stringify(data));
  activeTab.value = tab;
  isModalOpen.value = true;
  
  console.log("Modal status:", isModalOpen.value); // Add this line
};
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

              
                <div class="w-full lg:w-[100%] transition-all duration-500">
                 <div class="bg-white overflow-hidden shadow-xl shadow-primary/5 border border-primary-border rounded-[2.5rem] p-8">
                     <div class="overflow-x-auto">
                        <table class="w-full text-left border-separate border-spacing-y-2">
                            <thead>
                                <tr class="text-xs font-bold text-gray-400 uppercase tracking-[0.2em]">
                                    <th class="px-4 py-2 text-left">Leave Type Name</th>
                                    <th class="px-4 py-2 text-center">Probation</th>
                                    <th class="px-4 py-2 text-center">Max Carryover</th>
                                    <th class="px-4 py-2 text-center">Pro-Rata</th>
                                    <th class="px-4 py-2 text-right">Actions</th>
                                </tr>
                               </thead>
                                <tbody>
                                <template v-for="leave in leaveTypes.data" :key="leave.id">
                                    <!-- Summary Row -->
                                       <tr class="group bg-white hover:bg-primary-light/5 transition-all duration-300">
                                            <td class="px-6 py-4 rounded-l-2xl border-y border-transparent">
                                                <BaseButton variant="ghost" size="sm" @click="toggleRow(leave.id)"
                                                    class="flex items-center gap-2 ">
                                                    <span class="text-[10px] text-gray-400 font-mono">[{{ leave.code
                                                        }}]</span>
                                                    {{ leave.name }}
                                                </BaseButton>
                                            </td>
  
                                        <td class="text-center">{{ leave.probation_period_months }} Months</td>
                                        <td class="text-center text-primary font-medium">
                                            {{ leave.tiers?.length > 0 ? (leave.tiers[0].max_carry_forward_days + ' Days') : 'None' }}
                                        </td>
                                        <td class="text-center">
                                            <span :class="leave.is_pro_rata ? 'bg-green-50 text-green-600' : 'bg-gray-50 text-gray-600'" 
                                                class="px-3 py-1 text-[10px] font-black rounded-full border uppercase">
                                                {{ leave.is_pro_rata ? 'Yes' : 'No' }}
                                            </span>
                                        </td>
                                        <td class="text-right px-6">
                                        <BaseButton variant="ghost" size="sm" @click="toggleRow(leave.id)">
                                                <ChevronDown v-if="expandedRows.includes(leave.id)" class="w-5 h-5" />
                                                <ChevronRight v-else class="w-5 h-5" />
                                            </BaseButton>
                                        </td>
                                    </tr>

                                    <!-- Expanded Tier List -->
                                       <tr v-if="expandedRows.includes(leave.id)">
                                            <td colspan="5" class="bg-gray-50 px-12 pb-6 border-b border-gray-200">
                                                <div class="flex justify-between items-center mb-4 mt-2 px-1">
                                                    <div
                                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                                        <span class="text-gray-300">└─</span> Entitlement Tiers
                                                    </div>

                                                  <div class="flex items-center gap-2">
                                                        <BaseButton variant="ghost" size="sm"
                                                            @click="openEditModal(leave)">
                                                            <Edit2 class="w-3.5 h-3.5" />
                                                            Edit Leave Details
                                                        </BaseButton>

                                                        <BaseButton variant="primary" size="sm"
                                                            @click="addNewTier(leave.id)">
                                                            <Plus class="w-3.5 h-3.5" />
                                                            Add New Tier
                                                        </BaseButton>
                                                    </div>
                                                </div>
                                            
                                            <table class="w-full text-sm">
                                                <thead>
                                                    <tr class="text-[10px] uppercase text-gray-400">
                                                        <th class="py-2 text-left">Min Years</th>
                                                        <th class="py-2 text-left">Max Years</th>
                                                        <th class="py-2 text-left">Allowed Days</th>
                                                        <th class="py-2 text-left">Max Carryover</th>
                                                       
                                                    </tr>
                                                </thead>
                                               <tbody class="border-t border-gray-200">
                                              <template v-if="Array.isArray(leave.tiers) && leave.tiers.length > 0">
                                                    <tr v-for="tier in leave.tiers" :key="tier.id" class="text-gray-600">
                                                        <td class="py-3">{{ tier.min_years }}</td>
                                                        <td class="py-3">{{ tier.max_years >= 99 ? '∞' : tier.max_years }}</td>
                                                        <td class="py-3 font-bold text-gray-900">{{ tier.allowed_days }} Days</td>
                                                        <td class="py-3">{{ tier.max_carry_forward_days }} Days</td>
                                                        
                                                    </tr>
                                                </template>

                                                <template v-else>
                                                    <tr>
                                                        <td colspan="5" class="py-6 text-center text-gray-400 italic text-sm">
                                                            No entitlement tiers defined.
                                                        </td>
                                                    </tr>
                                                </template>

                                               
                                            </tbody>
                                                
                                            </table>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            </table>
                        </div>
                        <div class="mt-8">
                            <Pagination :links="leaveTypes.links" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- modal edit -->
            
            <LeaveModal 
                :show="isModalOpen" 
                :leave="editingLeave" 
                :initialTab="activeTab"
                @close="isModalOpen = false"
            />
          
        </div>
    </AuthenticatedLayout>
</template>