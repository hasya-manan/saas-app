<script setup>
import { X, AlertTriangle } from 'lucide-vue-next'; // Using the icons you already have

defineProps({
    show: Boolean,
    title: String,
    message: String,
    confirmText: {
        type: String,
        default: 'Confirm'
    },
    variant: {
        type: String,
        default: 'danger' 
    }
});

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        
        <div class="bg-white rounded-2xl shadow-xl max-w-md w-full overflow-hidden transition-all transform scale-100">
            
            <div class="p-6">
                <div class="flex items-center gap-4">
                    <div :class="[
                        'p-3 rounded-full',
                        variant === 'danger' ? 'bg-red-100 text-red-600' : 'bg-emerald-100 text-emerald-600'
                    ]">
                        <AlertTriangle class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">{{ title }}</h3>
                        <p class="text-gray-500 text-sm mt-1">{{ message }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button 
                    @click="emit('close')" 
                    class="px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200 rounded-lg transition"
                >
                    Cancel
                </button>
                <button 
                    @click="emit('confirm')" 
                    :class="[
                        'px-4 py-2 text-sm font-semibold text-white rounded-lg shadow-sm transition',
                        variant === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700'
                    ]"
                >
                    {{ confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>