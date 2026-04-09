<script setup>
defineProps({
    steps: Array,
    currentStep: Number,
    // The parent sends: { 1: true, 2: false, ... }
    validationSchema: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['update:currentStep']);

const goToStep = (id) => {
    emit('update:currentStep', id);
};
</script>

<template>
    <aside class="w-80 bg-surface-50 border-r border-surface-100 p-8 flex flex-col">
        <div class="mb-10">
            <h2 class="text-xl font-bold text-slate-800">
                <slot name="title">Registration</slot>
            </h2>
        </div>

        <nav class="space-y-8 relative">
            <div class="absolute left-4 top-2 bottom-2 w-0.5 bg-primary-light"></div>

            <div v-for="step in steps" :key="step.id" 
                @click="goToStep(step.id)"
                class="relative flex items-start group cursor-pointer"
            >
                <div :class="[
                    'relative z-10 w-8 h-8 flex items-center justify-center rounded-full border-2 transition-all duration-300 text-xs font-bold',
                    currentStep === step.id 
                        ? 'bg-primary border-primary text-white shadow-md scale-110' 
                        : validationSchema[step.id] 
                            ? 'bg-green-500 border-green-500 text-white' 
                            : 'bg-white border-primary-border text-primary'
                ]">
                    <span v-if="validationSchema[step.id]">✓</span>
                    <span v-else>{{ step.id }}</span>
                </div>

                <div class="ml-4 group-hover:translate-x-1 transition-transform duration-200">
                    <p :class="[
                        'text-sm font-semibold transition-colors', 
                        currentStep === step.id ? 'text-primary-dark' : 'text-slate-400'
                    ]">
                        {{ step.title }}
                    </p>
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 font-medium">
                        {{ step.desc }}
                    </p>
                </div>
            </div>
        </nav>
    </aside>
</template>