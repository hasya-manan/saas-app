<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'outline' // Matches your "Edit" button look
    },
    size: {
        type: String,
        default: 'md'
    }
    
});

const classes = computed(() => {
    const base = "inline-flex items-center justify-center gap-1.5 font-medium transition-all duration-200 rounded-full active:scale-95 disabled:opacity-50";
    
   const variants = {
    // MAIN: Save / Onboard
    primary: "bg-primary text-white hover:bg-primary-dark hover:shadow-md transition-all duration-300 ease-out border border-transparent",

    // SECONDARY: Edit / Manage (The "Pinkish" vibe)
    outline: "bg-white border border-primary-border text-primary hover:bg-primary-light/50 hover:border-primary transition-all duration-200",

    // NEUTRAL: Cancel / Go Back / View
    ghost: "text-gray-500 hover:bg-gray-100 hover:text-gray-700 border border-transparent transition-colors duration-200",

    // CAUTION: Archive / Trash (The "Reddish" hover)
    warning: "text-gray-400 hover:bg-red-50 hover:text-red-600 hover:border-red-100 border border-transparent transition-all duration-200",

    // DESTRUCTIVE: Delete Forever / Hard Delete
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
    <button :class="classes">
        <slot />
    </button>
</template>