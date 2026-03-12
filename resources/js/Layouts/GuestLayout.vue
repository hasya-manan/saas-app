<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-primary-light/30 pt-6 sm:justify-center sm:pt-0 px-4">
        
       <div class="mb-8 mt-20 sm:mt-0">
            <Link href="/" class="flex flex-col items-center gap-3 group">
                <!-- <div class="p-4 bg-white rounded-2xl shadow-sm border border-primary-border group-hover:shadow-md transition-all duration-300">
                    <ApplicationLogo class="h-12 w-12 fill-current text-primary" />
                </div>
                <span class="text-xl font-bold tracking-tight text-gray-900">HR System</span> -->
            </Link>
        </div>

        <div class="w-full overflow-hidden bg-white px-8 py-10 shadow-xl shadow-primary/5 sm:max-w-md rounded-[2.5rem] border border-primary-border">
            <slot />
        </div>

        <div class="mt-8 text-xs text-gray-400 font-medium tracking-widest uppercase">
            © 2026 Built By Hasya A.M
        </div>
    </div>
</template>