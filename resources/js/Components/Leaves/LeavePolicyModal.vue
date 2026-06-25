<script setup>
import { ref, watch } from 'vue';
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
    allowed_days: 0,
    max_carry_forward_days: 0
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
        form.allowed_days = newVal.tiers?.[0]?.allowed_days || 0;
        form.max_carry_forward_days = newVal.tiers?.[0]?.max_carry_forward_days || 0;
    }
}, { immediate: true, deep: true });
const save = () => {
    form.put(route('leave-types.update', form.id), {
        onSuccess: () => emit('close'),
    });
};
</script>

<template>
  <Modal :show="show" maxWidth="2xl" @close="$emit('close')">
    <div class="p-8">
      <div class="flex gap-4 border-b border-gray-100 mb-8">
        <BaseButton 
            variant="ghost" 
            size="sm"
            @click="activeTab = 'general'"
            :class="activeTab === 'general' ? 'text-primary border-b-2 border-primary rounded-none' : 'text-gray-400'"
        >
            General Leave Type
        </BaseButton>

        <BaseButton 
            variant="ghost" 
            size="sm"
            @click="activeTab = 'tiers'"
            :class="activeTab === 'tiers' ? 'text-primary border-b-2 border-primary rounded-none' : 'text-gray-400'"
        >
            Entitlement Tiers
        </BaseButton>
    </div>

      <div class="min-h-[300px]">
       <div v-if="activeTab === 'general'" class="space-y-6">
       
        
        <div class="grid grid-cols-2 gap-4">
          <div>
            
            <label class="block text-xs font-bold uppercase text-gray-700">Leave Name</label>
            <input v-model="form.name" type="text" class="mt-1 w-full rounded-xl border-gray-200 shadow-sm focus:border-primary focus:ring-primary" />
          </div>
          <div>
            <label class="block text-xs font-bold uppercase text-gray-700">Code</label>
            <input v-model="form.code" type="text" class="mt-1 w-full rounded-xl border-gray-200 shadow-sm focus:border-primary focus:ring-primary" />
          </div>
        </div>

        <!-- Default Days -->
        <div>
          <label class="block text-xs font-bold uppercase text-gray-700">Default Days</label>
          <input v-model="form.default_days" type="number" class="mt-1 w-full rounded-xl border-gray-200 shadow-sm focus:border-primary focus:ring-primary" />
        </div>

        <!-- Toggles (Switches) -->
        <div class="grid grid-cols-2 gap-4">
          <div class="flex items-center justify-between p-3 border rounded-xl bg-gray-50">
            <label class="text-xs font-bold uppercase text-gray-700">Allow Carry Forward</label>
            <input v-model="form.allows_carry_forward" type="checkbox" class="rounded text-primary focus:ring-primary" />
          </div>
          <div class="flex items-center justify-between p-3 border rounded-xl bg-gray-50">
            <label class="text-xs font-bold uppercase text-gray-700">Pro Rate?</label>
            <input v-model="form.is_active" type="checkbox" class="rounded text-primary focus:ring-primary" />
          </div>
        </div>
      </div>
        
        <div v-else>
          <p class="text-gray-400">Entitlement tiers table...</p>
        </div>
      </div>

      <div class="mt-8 flex justify-end gap-3 pt-6 border-t">
        <BaseButton 
        variant="ghost" 
        @click="$emit('close')"
    >
        Cancel
    </BaseButton>
    
    <BaseButton 
        variant="primary" 
        @click="save"
    >
        Save Changes
    </BaseButton>
      </div>
    </div>
  </Modal>
</template>

