<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { ChevronDown } from 'lucide-vue-next';

const props = defineProps({
    modelValue: [String, Number, null],
    options: Array,
    label: String,
    icon: Object, // Pass the Lucide icon component
    optionLabel: { type: String, default: 'name' },
    optionValue: { type: String, default: 'id' }
});

const emit = defineEmits(['update:modelValue']);
const isOpen = ref(false);
const container = ref(null);

const toggle = () => (isOpen.value = !isOpen.value);
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
        <button @click="toggle" type="button"
            class="flex items-center gap-2 px-4 py-2.5 bg-gray-50/50 rounded-2xl border border-transparent hover:border-primary-border transition-all text-sm font-medium text-gray-600 focus:outline-none">
            <component :is="icon" :size="16" class="text-gray-400" v-if="icon" />
            <span class="truncate max-w-[120px]">{{ getSelectedLabel() }}</span>
            <ChevronDown :size="14" class="ml-1 transition-transform duration-200" :class="{ 'rotate-180': isOpen }" />
        </button>

        <Transition
            enter-active-class="transition duration-100 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-75 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div v-if="isOpen" 
                class="absolute top-full mt-2 w-56 bg-white border border-gray-100 shadow-xl rounded-[1.5rem] py-2 z-50 overflow-hidden">
                
                <div @click="selectOption(null)" 
                    class="px-4 py-2.5 hover:bg-primary/5 cursor-pointer text-sm text-gray-500 italic">
                    {{ label }}
                </div>

                <div v-for="opt in options" :key="opt[optionValue]" 
                    @click="selectOption(opt)"
                    class="px-4 py-2.5 hover:bg-primary/5 cursor-pointer text-sm text-gray-700 font-medium transition-colors"
                    :class="{ 'bg-primary/5 text-primary': modelValue === opt[optionValue] }">
                    {{ opt[optionLabel] }}
                </div>
            </div>
        </Transition>
    </div>
</template>