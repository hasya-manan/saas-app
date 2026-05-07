<script setup>
import { AlertTriangle } from 'lucide-vue-next'; // Or your preferred icon library

const props = defineProps({
    isHod: Boolean,          // form.is_hod
    selectedHod: Object,     // selectedDeptHOD
    currentUserUuid: String, // user.uuid
    currentUserName: String  // user.name
});
</script>

<template>
    <transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="transform scale-95 opacity-0"
        enter-to-class="transform scale-100 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="transform scale-100 opacity-100"
        leave-to-class="transform scale-95 opacity-0"
    >
        <div v-if="isHod && selectedHod && selectedHod.uuid !== currentUserUuid" 
             class="mt-3 p-4 rounded-xl bg-amber-50 border border-amber-100 flex gap-3 shadow-sm">
            
            <div class="p-2 bg-amber-100 text-amber-600 rounded-lg h-fit">
                <AlertTriangle :size="18" />
            </div>

            <div>
                <h4 class="text-[11px] font-bold text-amber-900 uppercase tracking-wider">
                    Confirm Replacement
                </h4>
                <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                    Saving will replace <span class="font-bold underline">{{ selectedHod.name }}</span> 
                    with <span class="font-bold text-amber-900">{{ currentUserName }}</span> 
                    as the Head of Department.
                </p>
            </div>
        </div>
    </transition>
</template>