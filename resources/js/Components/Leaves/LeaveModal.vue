<script setup>
import { ref, watch, computed } from 'vue';
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';


const emit = defineEmits(['close', 'saved']);
const activeTab = ref('general');
const props = defineProps({
    show: Boolean,
    leave: Object,
    initialTab: String
});
const form = useForm({
    id: null,
    name: '',
    code: '',
    default_days: 0,
    allows_carry_forward: false,
    is_active: true,
    is_pro_rata: false,
 
    max_carry_forward_days: 0,

    //tier table
    tiers: [],
  
});


watch(() => props.leave, (newVal) => {
    if (newVal) {
        // Just assign the values directly to the form object
        form.id = newVal.id;
        form.name = newVal.name;
        form.code = newVal.code;
        form.default_days = newVal.default_days;
        form.allows_carry_forward = !!newVal.allows_carry_forward;
        form.is_active = !!newVal.is_active;
        form.is_pro_rata = !!newVal.is_pro_rata;
        
        // Handle the nested tier data
        form.tiers = newVal.tiers ? newVal.tiers.map(tier => ({
            id: tier.id,
            min_years: tier.min_years,
            max_years: tier.max_years,
            allowed_days: tier.allowed_days,
            max_carry_forward_days: tier.max_carry_forward_days
        })) : [];
    }
}, { immediate: true, deep: true });

const isInactive = computed(() => !form.is_active);

// Determine the warning/status message
const statusMessage = computed(() => {
    return form.is_dirty 
        ? 'Warning: You are about to deactivate this leave type. This will disable all associated entitlement tiers.'
        : 'Status: This leave type is currently inactive. All associated entitlement tiers are disabled.';
});

const submitUpdate = () => {
    form.put(route('admin_company.leavetypes.update', form.id), {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => emit('close'),
    });
};
</script>

<template>
  <Modal :show="show" maxWidth="2xl" @close="$emit('close')">
    <!-- Main Flex Container for Vertical Layout -->
    <div class="flex h-[450px]">
      
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
            
            <div>
              <label class="block text-xs font-bold text-gray-700">Default Days</label>
              <input v-model="form.default_days" type="number" class="mt-1 w-full rounded-xl border-gray-200 shadow-sm focus:border-primary focus:ring-primary" />
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="flex items-center justify-between p-3 border rounded-xl bg-gray-50">
                <label class="text-xs font-bold text-gray-700">Allow Carry Forward</label>
                <input v-model="form.allows_carry_forward" type="checkbox" class="rounded text-primary focus:ring-primary" />
              </div>
              <div class="flex items-center justify-between p-3 border rounded-xl bg-gray-50">
                <label class="text-xs font-bold text-gray-700">Status (Active)</label>
                <input v-model="form.is_active" type="checkbox" class="rounded text-primary focus:ring-primary" />
              </div>
            </div>
          </div>

          <!-- TAB 2: ENTITLEMENT TIERS -->
          <div v-else class="space-y-4">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="text-xs text-gray-500 uppercase">
                <th class="p-2">Tier</th>
                <th class="p-2">Min Years</th>
                <th class="p-2">Max Years</th>
                <th class="p-2">Allowed Days</th>
                <th class="p-2">Max Carryover</th>
                <th class="p-2">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(tier, index) in form.tiers" :key="index" class="border-b">
                <td class="p-2 font-bold text-sm">Tier {{ index + 1 }}</td>
                <td><input v-model="tier.min_years" type="number" class="w-16 p-1 text-sm rounded border-gray-200" /></td>
                <td><input v-model="tier.max_years" type="number" class="w-16 p-1 text-sm rounded border-gray-200" /></td>
                <td><input v-model="tier.allowed_days" type="number" class="w-16 p-1 text-sm rounded border-gray-200" /></td>
                <td><input v-model="tier.max_carry_forward_days" type="number" class="w-16 p-1 text-sm rounded border-gray-200" /></td>
                <td class="p-2">
                    <button @click="form.tiers.splice(index, 1)" class="text-red-500 text-xs">Remove</button>
                </td>
              </tr>
            </tbody>
          </table>
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
  </Modal>
</template>

