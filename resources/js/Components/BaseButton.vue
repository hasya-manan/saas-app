<script setup>
import { computed } from 'vue';
import { Loader2 } from 'lucide-vue-next'; 

const props = defineProps({
    variant: {
        type: String,
        default: 'outline' 
    },
    size: {
        type: String,
        default: 'md'
    },
    disabled: {          
        type: Boolean,
        default: false
    },
    loading: { type: Boolean, default: false }
});

const classes = computed(() => {
    const base = "inline-flex items-center justify-center gap-1.5 font-medium transition-all duration-200 rounded-full active:scale-95 disabled:opacity-50 disabled:pointer-events-none disabled:cursor-not-allowed";    
   const variants = {
    // MAIN: Save / Onboard
    primary: "bg-primary text-white hover:bg-primary-dark hover:shadow-md transition-all duration-300 ease-out border border-transparent",

    // SECONDARY: Edit / Manage (The "Pinkish" vibe)
    outline: "bg-white border-2 border-primary text-primary-dark font-black px-4 py-2 rounded-xl hover:bg-primary hover:text-white transition-all duration-300",
    // NEUTRAL: Cancel / Go Back / View
    ghost: "text-gray-500 hover:bg-gray-100 hover:text-gray-700 border border-transparent transition-colors duration-200",

    // CAUTION / ARCHIVE: Soft Muted Yellow
    warning: "bg-white border-2 border-amber-400 text-amber-700 font-black px-4 py-2 rounded-xl hover:bg-amber-400 hover:text-white hover:border-amber-400 transition-all duration-300 shadow-sm shadow-amber-400/10 active:scale-95",     // DESTRUCTIVE: Delete Forever / Hard Delete
    danger: "bg-red-600 text-white hover:bg-red-700 hover:shadow-md border border-transparent transition-all duration-200",
};

    const sizes = {
        sm: "px-3 py-1 text-xs",
        md: "px-4 py-1.5 text-sm", // This matches your screenshot's height
    };

    return `${base} ${variants[props.variant]} ${sizes[props.size]}`;
});
</script>

<template>
   <button :class="classes" :disabled="disabled || loading"">
    <Loader2 v-if="loading" :size="16" class="animate-spin" />
        <slot />
    </button>
</template>