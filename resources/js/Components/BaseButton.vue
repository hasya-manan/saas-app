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
    outline: "bg-white border border-primary text-primary hover:bg-primary-light hover:shadow-sm",
    ghost: "text-gray-500 hover:bg-red-50 hover:text-red-600 hover:border-red-100 border border-transparent",
    primary: "bg-primary text-white hover:opacity-80 hover:shadow-md border border-transparent",
    danger: "bg-red-600 text-white hover:bg-red-800 hover:shadow-md border border-transparent",
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