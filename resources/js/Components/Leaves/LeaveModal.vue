<script setup>

/**
 * DOCUMENTATION: LeaveModal.vue
 * ----------------------------
 * This component uses a 'watch' to synchronize props.leave into the Inertia useForm object.
 * Logic: 
 * 1. Tracks leave_type configurations (Global).
 * 2. Handles tenure-based logic via 'is_calculated_by_experience'.
 * Refer to /docs/leave-module/leave-modal-logic.md for full details.
 */

import { ref, watch, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import { useForm, router } from '@inertiajs/vue3';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import {Info, Plus}from 'lucide-vue-next';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    show: Boolean,
    leave: Object,
    initialTab: {
        type: String,
        default: 'general'
    }
});
const emit = defineEmits(['close', 'saved']);
const activeTab = ref(props.initialTab);
const showModal = ref(false);
const isProcessing = ref(false);
const showDeleteModal = ref(false);
const tierToDeleteIndex = ref(null);


const form = useForm({
    id: null,
    name: '',
    code: '',
    is_calculated_by_experience: false,
    default_days: 0,
    allows_carry_forward: false,
    probation_period_months:'',
    is_active: true,
    is_pro_rata: false,
   

    //tier table
    tiers: [],
  
});


watch(() => props.show, (newVal) => {
    if (newVal) {
        activeTab.value = props.initialTab;
    }
});

// Sync internal tab state with parent prop whenever the modal is opened
watch(() => props.leave, (newVal) => {
    // Only update if the form is empty OR if we are loading a DIFFERENT leave type
    if (newVal && (form.id !== newVal.id)) {
       
        form.id = newVal.id;
        form.name = newVal.name;
        form.code = newVal.code;
        form.default_days = newVal.default_days;
        form.allows_carry_forward = !!newVal.allows_carry_forward;
        form.is_active = !!newVal.is_active;
        form.is_pro_rata = !!newVal.is_pro_rata;
        form.is_calculated_by_experience = !!newVal.is_calculated_by_experience;
        form.probation_period_months = newVal.probation_period_months;
        
        //nested tier
        form.tiers = newVal.tiers ? newVal.tiers.map(tier => ({
            id: tier.id,
            min_years: tier.min_years,
            max_years: tier.max_years,
            allowed_days: tier.allowed_days,
            max_carry_forward_days: tier.max_carry_forward_days
        })) : [];
    }
}, { immediate: true, deep: true });


const submitUpdate = () => {
  //console.log('Payload being sent to server:', form.data());
   if (form.id) {
        form.put(route('admin_company.leavetypes.update', form.id), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    } else {
        form.post(route('admin_company.leavetypes.store'), {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => emit('close'),
        });
    }
};

const handleStatusToggle = () => {
  // Use form.is_active, NOT form.value.is_active , because this is inside another Modal page
  //In useForm, the object is already "unwrapped."
  if (!form.is_active) {
    showModal.value = true;
  } else {
   
    //console.log("Activating...");
  }
};


const handleModalAction = async (isConfirmed) => {
  //console.log("Modal Action Triggered. Confirmed:", isConfirmed); 
  console.log("Current form object:", form); 
  if (!isConfirmed) {
    form.is_active = true; 
    showModal.value = false;
    return;
  }

 
  isProcessing.value = true;
  try {
    
    showModal.value = false;
  } catch (err) {
    console.error("Update failed", err);
  } finally {
    isProcessing.value = false;
  }
};


const createDefaultTier = () => ({
  min_years: 0,
  max_years: 0,
  allowed_days: 0,
  max_carry_forward_days: 0
 
});


const addTier = () => {
  form.tiers.push(createDefaultTier());
};



// open modal
const confirmRemoveTier = (index) => {
    tierToDeleteIndex.value = index;
    showDeleteModal.value = true;
};

// deleted
const executeDelete = () => {
    const tier = form.tiers[tierToDeleteIndex.value];

    // If the tier has an ID, it exists in the DB, so DELETE via API
    if (tier.id) {
        router.delete(route('admin_company.leave-tiers.destroy', tier.id), {
            onSuccess: () => {
                form.tiers.splice(tierToDeleteIndex.value, 1);
                showDeleteModal.value = false;
                tierToDeleteIndex.value = null;
            }
        });
    } else {
        // If it's a new row (no ID), just remove it from the UI (no API call needed)
        form.tiers.splice(tierToDeleteIndex.value, 1);
        showDeleteModal.value = false;
    }
};
</script>

<template>
  <Modal :show="show" maxWidth="4xl" @close="$emit('close')">
    <!-- Main Flex Container for Vertical Layout -->
    <div class="flex min-h-[500px]">
      
      <!-- LEFT SIDEBAR: Navigation -->
      <div class="w-1/4 border-r border-gray-100 p-6 space-y-2 bg-gray-50/50">
        <button 
          @click="activeTab = 'general'"
          :class="activeTab === 'general' ? 'bg-white shadow-sm border-l-4 border-primary text-primary font-bold' : 'text-gray-500 hover:text-gray-700'"
          class="w-full text-left p-3 rounded-md text-sm transition-all"
        >
          General
        </button>
        <button 
          @click="activeTab = 'tiers'"
          :class="activeTab === 'tiers' ? 'bg-white shadow-sm border-l-4 border-primary text-primary font-bold' : 'text-gray-500 hover:text-gray-700'"
          class="w-full text-left p-3 rounded-md text-sm transition-all"
        >
          Entitlement Tiers
        </button>
      </div>

      <!-- RIGHT SIDE: Content Area -->
      <div class="w-3/4 flex flex-col">
        <div class="flex-1 p-8 overflow-y-auto">
          
          <!-- TAB 1: GENERAL -->
          <div v-if="activeTab === 'general'" class="space-y-6">
            <h3 class="text-sm font-bold text-gray-900 uppercase">General Information</h3>
            
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-700">Leave Name</label>
                <input v-model="form.name" type="text" class="mt-1 w-full rounded-xl border-gray-200 shadow-sm focus:border-primary focus:ring-primary" />
              </div>
              <div>
                <label class="block text-xs font-bold text-gray-700">Code</label>
                <input v-model="form.code" type="text" class="mt-1 w-full rounded-xl border-gray-200 shadow-sm focus:border-primary focus:ring-primary" />
              </div>
            </div>

            <!-- Conditional Logic for Default Days -->
           <div class="space-y-1">
              <div class="flex items-center space-x-2">
                <label class="block text-xs font-bold text-gray-700">
                  Default Days <span class="text-red-500">*</span>
                </label>

                <div class="cursor-help text-gray-400 hover:text-primary transition-colors"
                  title="Default Days: When 'Calc. by Experience' is OFF, this is the fixed entitlement. When ON, this value serves as a fallback for employees not covered by any tenure tiers.">
                  <Info :size="14" />
                </div>
              </div>

              <input v-model="form.default_days" type="number"
                class="mt-1 w-full rounded-xl border-gray-200 shadow-sm focus:border-primary focus:ring-primary"
                :placeholder="form.is_calculated_by_experience ? 'e.g. 7 (Fallback)' : 'e.g. 14'" />

              <InputError :message="form.errors.default_days" class="mt-1" />

              <p v-if="form.is_calculated_by_experience" class="text-[10px] text-amber-600 font-medium mt-1 italic">
                * Tier-based calculation active. This value acts as a fallback.
              </p>
            </div>

            <!-- New Fields Section -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-gray-700">Probation Period (Months)</label>
                <input v-model="form.probation_period_months" placeholder="e.g. 2" type="number" min="0" class="mt-1 w-full rounded-xl border-gray-200 shadow-sm focus:border-primary focus:ring-primary" />
              </div>
            </div>

            <!-- Toggles Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              <div class="flex items-center justify-between p-3 border rounded-xl bg-gray-50">
                <label class="text-xs font-bold text-gray-700">Calc. by Experience</label>
                <input v-model="form.is_calculated_by_experience" type="checkbox" class="rounded text-primary focus:ring-primary" />
              </div>
              <div class="flex items-center justify-between p-3 border rounded-xl bg-gray-50">
                <label class="text-xs font-bold text-gray-700">Allow Carry Forward</label>
                <input v-model="form.allows_carry_forward" type="checkbox" class="rounded text-primary focus:ring-primary" />
              </div>
              <div class="flex items-center justify-between p-3 border rounded-xl bg-gray-50">
                <label class="text-xs font-bold text-gray-700">Pro-Rata</label>
                <input v-model="form.is_prorata" type="checkbox" class="rounded text-primary focus:ring-primary" />
              </div>
              <div class="flex items-center justify-between p-3 border rounded-xl bg-gray-50">
                <label class="text-xs font-bold text-gray-700">Active</label>
                <input v-model="form.is_active" type="checkbox" @change="handleStatusToggle" class="rounded text-primary focus:ring-primary" />
              </div>
            </div>
          </div>

          <!-- TAB 2: ENTITLEMENT TIERS -->
        <div v-else class="space-y-4">
          <div class="flex justify-between items-center">
            <h3 class="text-sm font-bold text-gray-900 uppercase">Tier Configuration</h3>
             <BaseButton variant="primary" size="sm" @click="addTier">
                <Plus :size="14" /> Add Tier
              </BaseButton>

          </div>

          <div class="overflow-hidden border rounded-xl">
            <table class="w-full text-left">
              <thead class="bg-gray-50 border-b">
                <tr class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                  <th class="p-3">Tier Level</th>
                  <th class="p-3">Years (Min - Max)</th>
                  <th class="p-3">Allowed Days</th>
                  <th class="p-3">Max Carryover</th>
                  <th class="p-3 text-center">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-gray-100">
                <tr v-for="(tier, index) in form.tiers" :key="index" class="hover:bg-gray-50/50">
                  <td class="p-3 font-semibold text-sm">#{{ index + 1 }}</td>
                  <td class="p-3 flex gap-2">
                    <input v-model="tier.min_years" placeholder="Min" class="w-16 p-2 text-sm rounded-lg border-gray-200" />
                    <input v-model="tier.max_years" placeholder="Max" class="w-16 p-2 text-sm rounded-lg border-gray-200" />
                  </td>
                  <td class="p-3"><input v-model="tier.allowed_days" type="number" class="w-20 p-2 text-sm rounded-lg border-gray-200" /></td>
                  <td class="p-3"><input v-model="tier.max_carry_forward_days" type="number" class="w-20 p-2 text-sm rounded-lg border-gray-200" /></td>
                  <td class="p-3 text-center">
                    <!-- <button @click="form.tiers.splice(index, 1)" class="text-red-500 hover:text-red-700">
                      <span class="text-xs font-bold">Remove</span>
                    </button> -->
                    <button @click="form.tiers[index].id ? confirmRemoveTier(index) : form.tiers.splice(index, 1)" 
                            class="text-red-500 hover:text-red-700">
                        <span class="text-xs font-bold">Remove</span>
                    </button>
                   
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        </div>

        <!-- FOOTER: Fixed to bottom of right side -->
        <div class="p-6 border-t flex justify-end gap-3 bg-white">
          <BaseButton variant="ghost" @click="$emit('close')">Cancel</BaseButton>
          <BaseButton variant="primary" @click="submitUpdate" :disabled="form.processing">
            {{ form.processing ? 'Saving...' : 'Save Changes' }}
          </BaseButton>
        </div>
      </div>
    </div>

    <!--Modal confirm-->
    <ConfirmModal
      :show="showModal"
      title="Inactive Leave Type?"
      message="Are you sure you want to inactive this leave type?"
      note="All associated entitlement tiers will be suspended and unavailable for selection."
      confirm-text="Inactive"
      variant="danger"
      :loading="isProcessing"
      @close="() => handleModalAction(false)" 
      @confirm="() => handleModalAction(true)"
    />

    <!--Delete Modal-->
    <ConfirmModal 
    :show="showDeleteModal"
    title="Remove Tier"
    message="Are you sure you want to remove this tier? This action will permanently remove this configuration once you save changes."
    note="Any employees currently linked to this specific tier may need to be reassigned."
    confirmText="Yes, Remove"
    variant="danger"
    @close="showDeleteModal = false"
    @confirm="executeDelete"
/>
  </Modal>
</template>

