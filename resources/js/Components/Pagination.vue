<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: Array,
    meta: Object, // Optional: for "Showing 1 to 10..." text
});
</script>

<template>
    <div v-if="links.length > 3" class="flex items-center justify-between px-6 py-4 bg-gray-50/50 border-t border-gray-100">
        <div v-if="meta" class="hidden sm:block text-xs text-gray-500 font-medium">
            Showing {{ meta.from }} to {{ meta.to }} of {{ meta.total }}
        </div>

        <div class="flex gap-1 ml-auto">
            <Link 
                v-for="(link, k) in links" 
                :key="k"
                :href="link.url || '#'"
                v-html="link.label"
                class="px-3 py-1 text-xs rounded-md border transition-all"
                :class="[
                    link.active 
                        ? 'bg-primary text-white border-primary font-bold' 
                        : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50',
                    !link.url ? 'opacity-40 cursor-not-allowed' : ''
                ]"
                preserve-scroll
            />
        </div>
    </div>
</template>