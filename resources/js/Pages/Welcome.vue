<script setup>
import { Link, Head } from '@inertiajs/vue3';

defineProps({
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="Welcome" />
    <div class="min-h-screen bg-surface-50 font-sans selection:bg-primary/20">
        <nav class="flex items-center justify-between px-8 py-8 max-w-6xl mx-auto">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-primary rounded-2xl flex items-center justify-center shadow-xl shadow-primary/20 rotate-3">
                    <span class="text-white font-black text-xl italic -rotate-3">H</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-black text-gray-900 leading-none tracking-tighter">H.A.M</span>
                    <span class="text-[10px] font-bold text-primary uppercase tracking-[0.2em]">Hasya Abdul Manan</span>
                </div>
            </div>
            
            <div class="flex items-center gap-6">
                <Link :href="route('login')" class="text-xs font-black uppercase tracking-widest text-gray-400 hover:text-primary transition-colors">
                    Login
                </Link>
                <Link :href="route('register')" class="bg-gray-900 text-white px-6 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-primary transition-all shadow-xl shadow-gray-900/10">
                    Register
                </Link>
            </div>
        </nav>

        <main class="max-w-6xl mx-auto px-8 pt-20">
            <div class="grid lg:grid-cols-5 gap-12 items-center">
                <div class="lg:col-span-3 space-y-8">
                    <div class="inline-block px-4 py-1.5 bg-primary-light border border-primary-border rounded-full">
                        <span class="text-[10px] font-black uppercase tracking-[0.15em] text-primary-dark">Personal Research Project</span>
                    </div>
                    
                    <h1 class="text-6xl lg:text-7xl font-black text-gray-900 leading-[1.05] tracking-tight">
                        Deep-Diving into <br/>
                        <span class="text-primary italic">Multi-Tenancy.</span>
                    </h1>
                    
                    <p class="text-gray-500 font-medium text-lg leading-relaxed max-w-xl">
                        A technical sandbox built by <strong>Hasya</strong> to explore complex Laravel 11 architectures, shared-database isolation, and advanced Role-Based Access Control (RBAC).
                    </p>

                    <div class="flex items-center gap-4 pt-4">
                        <Link :href="route('login')" class="bg-primary text-white px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-sm shadow-2xl shadow-primary/30 hover:bg-primary-dark transition-all">
                            View Demo
                        </Link>
                        <div class="h-1 w-12 bg-primary-border rounded-full"></div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Laravel 11 • Vue 3 • Inertia</span>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white border border-primary-border p-8 rounded-[2.5rem] shadow-2xl shadow-primary/5">
                        <h3 class="text-sm font-black uppercase tracking-widest text-gray-800 mb-6 border-b border-surface-100 pb-4">Architectural Goals</h3>
                        <ul class="space-y-4">
                            <li v-for="(goal, index) in ['Shared-Database Isolation', 'Dynamic Tenant Middleware', 'Inertia.js SPA Bridge', 'Custom RBAC Logic']" 
                                :key="index"
                                class="flex items-center gap-3 text-sm font-bold text-gray-500">
                                <div class="w-1.5 h-1.5 bg-primary rounded-full"></div>
                                {{ goal }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
        
        <footer class="mt-32 pb-12 text-center">
            <p class="text-[10px] font-bold text-gray-300 uppercase tracking-[0.3em]">
                &copy; 2026 HASYA ABDUL MANAN — ALL RIGHTS RESERVED
            </p>
        </footer>
    </div>
</template>

