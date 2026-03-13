<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        required: true
    }
});

const statusConfig = computed(() => {
    const map = {
        'active': 'bg-emerald-50 text-emerald-600 border-emerald-100',
       'inactive': 'bg-gray-800 text-white border-transparent',
        'suspended': 'bg-amber-50 text-amber-600 border-amber-100',
        'pending': 'bg-blue-50 text-blue-600 border-blue-100',
        'archived': 'bg-red-50 text-red-600 border-red-100',
    };
    
    return map[props.status.toLowerCase()] || 'bg-gray-100 text-gray-500 border-gray-200';
});
</script>

<template>
    <span 
        :class="statusConfig" 
        class="px-2 py-0.5 text-[10px] font-bold rounded-full border uppercase tracking-wide inline-block"
    >
        {{ status }}
    </span>
</template>