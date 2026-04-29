<script setup>
import { watch, ref, nextTick } from 'vue';
const props = defineProps({
    steps: Array,
    currentStep: Number,
    validationSchema: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['update:currentStep']);

const goToStep = (id) => {
    emit('update:currentStep', id);
};

const stepperRef = ref(null);
// Create a ref for the scrollable container
const scrollContainer = ref(null);
watch(() => props.currentStep, () => {
    // 2. Wait for Vue to finish updating the DOM
    nextTick(() => {
        // 3. Find the element that is "active"
        const activeElement = scrollContainer.value?.querySelector('.text-primary-dark');
        
        if (activeElement) {
            activeElement.scrollIntoView({
                behavior: 'smooth', // Smooth animation
                inline: 'center',   // Centers it horizontally
                block: 'nearest'    // Prevents the whole page from jumping
            });
        }
    });
}, { immediate: true });

</script>

<template>
    <aside class="w-full lg:w-80 bg-surface-50 border-b lg:border-b-0 lg:border-r border-surface-100 p-6 lg:p-8 flex flex-col">
        
        <div class="mb-6 lg:mb-10">
            <h2 class="text-xl font-bold text-slate-800">
                <slot name="title">Registration</slot>
            </h2>
        </div>

        <nav class="flex flex-row lg:flex-col items-center lg:items-start gap-6 lg:gap-0 lg:space-y-8 relative overflow-x-auto lg:overflow-visible no-scrollbar pb-2 lg:pb-0">
            
            <div class="hidden lg:block absolute left-4 top-2 bottom-2 w-0.5 bg-primary-light"></div>

          <div v-for="step in steps" :key="step.id" @click="goToStep(step.id)"
                class="relative flex flex-row items-center lg:items-start group cursor-pointer flex-shrink-0">
                <div :class="[
                    'relative z-10 w-8 h-8 flex flex-shrink-0 items-center justify-center rounded-full border-2 transition-all duration-300 text-xs font-bold',
                    currentStep === step.id
                        ? 'bg-primary border-primary text-white shadow-md scale-110'
                        : validationSchema[step.id]
                            ? 'bg-green-500 border-green-500 text-white'
                            : 'bg-white border-primary-border text-primary'
                ]">
                    <span v-if="validationSchema[step.id]">✓</span>
                    <span v-else>{{ step.id }}</span>
                </div>

                <div :class="[
                    'ml-3 lg:ml-4 group-hover:translate-x-1 transition-all duration-200',
                    currentStep === step.id ? 'block' : 'hidden lg:block'
                ]">
                    <p :class="[
                        'text-xs lg:text-sm font-semibold transition-colors whitespace-nowrap',
                        currentStep === step.id ? 'text-primary-dark' : 'text-slate-400'
                    ]">
                        {{ step.title }}
                    </p>
                    <p
                        class="hidden lg:block text-[9px] lg:text-[10px] uppercase tracking-wider text-slate-400 font-medium whitespace-nowrap">
                        {{ step.desc }}
                    </p>
                </div>

                <div v-if="step.id !== steps.length" class="lg:hidden w-6 h-0.5 bg-slate-200 mx-2"></div>
                
            </div>
           
        </nav>
    </aside>
</template>

<style scoped>
/* Hide scrollbar for Chrome, Safari and Opera */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
/* Hide scrollbar for IE, Edge and Firefox */
.no-scrollbar {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>