<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { ChevronDown } from 'lucide-vue-next';

const props = defineProps({
    modelValue: [String, Number, null],
    options: Array,
    label: String,
    icon: {
        type: [Object, Function],
        default: null
    },
    optionLabel: { type: String, default: 'name' },
    optionValue: { type: String, default: 'id' },
    variant: { type: String, default: 'search' } // 'search' or 'form'
});

const emit = defineEmits(['update:modelValue']);
const isOpen = ref(false);
const container = ref(null);
const dropUp = ref(false);

const toggle = () => {
    if (!isOpen.value && container.value) {
        const rect = container.value.getBoundingClientRect();
        const spaceBelow = window.innerHeight - rect.bottom;
        // Flip if there's less than 300px of space below
        dropUp.value = spaceBelow < 300; 
    }
    isOpen.value = !isOpen.value;
};
const close = () => (isOpen.value = false);

const selectOption = (option) => {
    emit('update:modelValue', option ? option[props.optionValue] : null);
    close();
};

// Close when clicking outside
const handleClickOutside = (e) => {
    if (container.value && !container.value.contains(e.target)) close();
};

onMounted(() => document.addEventListener('click', handleClickOutside));
onUnmounted(() => document.removeEventListener('click', handleClickOutside));

const getSelectedLabel = () => {
    const selected = props.options.find(opt => opt[props.optionValue] === props.modelValue);
    return selected ? selected[props.optionLabel] : props.label;
};
</script>

<template>
   <div class="relative" ref="container">
        <button @click="toggle" type="button" :class="[
            // Base classes
            'flex items-center justify-between gap-2 rounded-2xl border transition-all text-sm font-medium focus:outline-none',

            // Variant logic
            variant === 'form'
                ? 'w-full px-4 py-3 bg-white border-primary-border text-slate-700 shadow-sm'
                : 'px-4 py-2.5 bg-gray-50/50 border-transparent hover:border-primary-border text-gray-600'
        ]">
            <component :is="icon" :size="16" class="text-gray-400" v-if="icon" />
            <span class="truncate max-w-[120px]">{{ getSelectedLabel() }}</span>
            <ChevronDown :size="14" class="ml-1 transition-transform duration-200" :class="{ 'rotate-180': isOpen }" />
        </button>
       <Transition enter-active-class="transition duration-100 ease-out"
            :enter-from-class="dropUp ? 'transform scale-95 opacity-0 translate-y-2' : 'transform scale-95 opacity-0 -translate-y-2'"
            enter-to-class="transform scale-100 opacity-100 translate-y-0"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100 translate-y-0"
            :leave-to-class="dropUp ? 'transform scale-95 opacity-0 translate-y-2' : 'transform scale-95 opacity-0 -translate-y-2'">
            <div v-if="isOpen" :class="[
                // Base Styles
                'absolute bg-white border border-gray-100 shadow-xl rounded-[1.5rem] py-2 z-50 overflow-hidden',

                // 1. Smart Positioning (Top vs Bottom)
                dropUp ? 'bottom-full mb-2' : 'top-full mt-2',

                // 2. Responsive Width (Full for Forms, fixed for Search)
                variant === 'form' ? 'w-full left-0' : 'w-56 right-0'
            ]">
                <div @click="selectOption(null)"
                    class="px-4 py-2.5 hover:bg-primary/5 cursor-pointer text-sm text-gray-500 italic">
                    {{ label }}
                </div>

                <div class="max-h-60 overflow-y-auto custom-scrollbar">
                    <div v-for="opt in options" :key="opt[optionValue]" @click="selectOption(opt)"
                        class="px-4 py-2.5 hover:bg-primary/5 cursor-pointer text-sm text-gray-700 font-medium transition-colors"
                        :class="{ 'bg-primary/5 text-primary': modelValue === opt[optionValue] }">
                        {{ opt[optionLabel] }}
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>