<script setup>
/**
 * ARCHITECTURAL DESIGN PATTERN: The Flexible Page Header
 * Purpose: Standardizes the top section of all administrative pages.
 * * Why this is a "Best Practice":
 * 1. Encapsulation: Handles responsive stacking (iPad/Mobile) in one place.
 * 2. Slot-Based Design: Uses the "Inversion of Control" principle, allowing 
 * the parent page to decide what actions (buttons/links) to display.
 */

import { Link } from '@inertiajs/vue3';
defineProps({
    title: String,     
    subtitle: String,
    backRoute: String,
    backLabel: { type: String, default: 'Back to Directory' }
});
import { ArrowBigLeftDash } from 'lucide-vue-next';
</script>

<template>
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 py-4 mb-4 border-b border-transparent">
    
    <div class="flex-1 min-w-0">
      <h2 class="text-lg lg:text-2xl font-bold text-slate-800 tracking-tight">
        {{ title }}
      </h2>
      <p v-if="subtitle" class="text-sm text-slate-500 mt-1 hidden lg:block">
        {{ subtitle }}
      </p>
    </div>

    <div class="flex shrink-0 items-center">
      <slot name="actions">
        <Link v-if="backRoute" :href="backRoute" class="group flex items-center gap-3">
          
          <div class="flex items-center justify-center w-10 h-10 rounded-full bg-white border border-slate-200 shadow-sm transition-all duration-300 group-hover:bg-primary group-hover:border-primary group-hover:text-white">
            <ArrowBigLeftDash 
                :size="20" 
                class="transition-transform group-hover:-translate-x-0.5" 
            />
          </div>

          <span class="hidden lg:block text-sm font-bold text-slate-600 group-hover:text-primary transition-colors">
            {{ backLabel }}
          </span>
          
        </Link>
      </slot>
    </div>
  </div>
</template>

