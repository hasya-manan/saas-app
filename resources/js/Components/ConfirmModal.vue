<script setup>
import { X, AlertTriangle, Loader2} from 'lucide-vue-next'; // Using the icons you already have

defineProps({
    show: Boolean,
    title: String,
    message: String,
    // RATIONALE: We add a 'note' prop to separate general context from 
    // critical technical warnings (like the system kill-switch).
    note: {
        type: String,
        default: null
    },
    confirmText: {
        type: String,
        default: 'Confirm'
    },
    variant: {
        type: String,
        default: 'danger' 
    },
    loading: {
        type: Boolean,
        default: false
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
                   <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-900">{{ title }}</h3>
                        <p class="text-gray-500 text-sm mt-1 leading-relaxed">{{ message }}</p>

                        <div v-if="note" class="mt-4 p-3 rounded-xl border bg-gray-50 border-gray-200 flex gap-3 items-start">
                            <Info :size="16" class="text-gray-400 mt-0.5 shrink-0" />
                            <p class="text-[12px] text-gray-600 leading-snug">
                                <span class="font-bold text-gray-900 uppercase text-[10px] block mb-1">Important Note</span>
                                {{ note }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                <button 
                    @click="emit('close')"
                    :disabled="loading" 
                    class="px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200 rounded-lg transition disabled:opacity-50"
                >
                    Cancel
                </button>
                <button 
                    @click="emit('confirm')" 
                    :disabled="loading"
                    :class="[
                        'px-4 py-2 text-sm font-semibold text-white rounded-lg shadow-sm transition',
                        variant === 'danger' ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700', 'disabled:opacity-70 disabled:cursor-not-allowed'
                    ]"
                >
                    
                    <Loader2 v-if="loading" class="w-4 h-4 animate-spin" />
                    {{ loading ? 'Processing...' : confirmText }}
                </button>
            </div>
        </div>
    </div>
</template>