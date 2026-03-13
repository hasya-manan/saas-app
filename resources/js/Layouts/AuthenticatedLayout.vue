<script setup>
import { ref, watch } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useNotifications } from '@/Composables/useNotifications';
// Example using Lucide icons (install via npm install lucide-vue-next)
import {
    LayoutDashboard,
    Users,
    Settings,
    LogOut,
    ChevronLeft,
    ChevronRight,
    PlusCircle,
    Menu, 
    X,
    List      
} from 'lucide-vue-next';

const isCollapsed = ref(false);
const showingMobileMenu = ref(false); 
const page = usePage();
const { notifySuccess, notifyError, notifyWarning } = useNotifications();
const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value;
};

watch(
    () => page.props.flash, 
    (flash) => {
        console.log("Flash Data Detected:", flash); // <--- Add this
        if (flash?.success) {
            notifySuccess(flash.success);
        }
        if (flash?.error) {
            notifyError(flash.error);
        }
        
        if (flash?.message) {
            notifySuccess(flash.message);
        }
    }, 
    { deep: true }
);
</script>

<template>
    <div class="flex h-screen bg-gray-50 overflow-hidden font-sans">
        
        <div v-if="showingMobileMenu" class="fixed inset-0 z-50 flex md:hidden">
            <div @click="showingMobileMenu = false" class="fixed inset-0 bg-gray-600/75"></div>

            <aside class="relative flex w-full max-w-xs flex-1 flex-col bg-white transition-transform duration-300">
                <div class="p-6 flex items-center justify-between border-b">
                    <!-- TODO:: change title later on -->
                    <span class="font-bold text-gray-800">HR System</span>
                    <button @click="showingMobileMenu = false"><X :size="24" /></button>
                </div>
                
                <nav class="flex-1 px-4 py-4 space-y-2">
                    <Link :href="route('dashboard')" class="block px-3 py-2 text-gray-600 font-medium">Dashboard</Link>
                    <Link v-if="$page.props.auth.user.role_id === 1" :href="route('tenants.create')" class="block px-3 py-2 text-gray-600 font-medium">Onboard Company</Link>
                    <Link v-if="$page.props.auth.user.role_id === 1" :href="route('tenants.list')" class="block px-3 py-2 text-gray-600 font-medium">List Company</Link>

                </nav>
            </aside>
        </div>

        <aside :class="isCollapsed ? 'w-20' : 'w-64'"
            class="relative hidden md:flex flex-col bg-white border-r border-gray-200 transition-all duration-300 ease-in-out">
            <button @click="toggleSidebar"
                class="absolute -right-3 top-10 z-50 bg-white border border-gray-200 rounded-full p-1 shadow-sm hover:bg-gray-50">
                <ChevronLeft v-if="!isCollapsed" :size="16" />
                <ChevronRight v-else :size="16" />
            </button>

            <div class="p-6 flex items-center gap-3 border-b border-gray-50">
                <div class="h-10 w-10 rounded-full bg-gray-200 shrink-0 overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name=Super+Admin" alt="Avatar" />
                </div>
                <div v-if="!isCollapsed" class="overflow-hidden transition-opacity duration-300">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-tighter">SuperAdmin</p>
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $page.props.auth.user.name }}</p>
                </div>
            </div>

            <nav class="flex-1 px-3 py-4 space-y-1">
                <p v-if="!isCollapsed" class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Main</p>

                <Link v-if="$page.props.auth.user.role_id === 1" :href="route('superadmin.dashboard')"
                    :class="[route().current('superadmin.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50']"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors group relative">
                    <LayoutDashboard :size="20" />
                    <span v-if="!isCollapsed" class="text-sm font-medium">Platform Dashboard</span>
                </Link>

                <Link v-if="$page.props.auth.user.role_id === 3" :href="route('dashboard')"
                    :class="[route().current('dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50']"
                    class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors group relative">
                    <LayoutDashboard :size="20" />
                    <span v-if="!isCollapsed" class="text-sm font-medium">My Dashboard</span>
                </Link>

                <div v-if="$page.props.auth.user.role_id === 1">
                    <p v-if="!isCollapsed" class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-6 mb-2">System Admin</p>
                    <Link :href="route('tenants.create')"
                        :class="[route().current('tenants.create') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50']"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors group relative">
                        <PlusCircle :size="20" />
                        <span v-if="!isCollapsed" class="text-sm font-medium">Onboard Company</span>
                    </Link>
                    <Link :href="route('tenants.list')"
                        :class="[route().current('tenants.list') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50']"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors group relative">
                        <List  :size="20" />
                        <span v-if="!isCollapsed" class="text-sm font-medium">List Company</span>
                    </Link>
                </div>
            </nav>

            <div class="p-4 border-t border-gray-100">
                <Link :href="route('logout')" method="post" as="button" class="w-full flex items-center gap-3 px-3 py-2 text-red-500 hover:bg-red-50 rounded-lg group">
                    <LogOut :size="20" />
                    <span v-if="!isCollapsed" class="text-sm font-medium">Logout Account</span>
                </Link>
            </div>
        </aside>

        <div class="flex flex-1 flex-col overflow-hidden">
            <header class="flex h-16 items-center justify-between border-b bg-white px-4 md:hidden">
                <button @click="showingMobileMenu = true" class="text-gray-600">
                    <Menu :size="24" />
                </button>
                <span class="font-bold text-gray-800">HR System</span>
                <div class="w-6"></div> </header>

            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                <header v-if="$slots.header" class="mb-8">
                    <slot name="header" />
                </header>
                <slot />
            </main>
        </div>
    </div>
</template>