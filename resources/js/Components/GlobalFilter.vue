<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Search, X, Users, Building2, Activity } from 'lucide-vue-next';

const props = defineProps({
    routeName: String,
    filters: { type: Object, default: () => ({}) },
    tenants: { type: Array, default: () => [] },
    roles: { type: Array, default: () => [] },
    statusOptions: { type: Array, default: () => [] },
    placeholder: String,
    showRole: Boolean,
    showTenant: Boolean,
    showStatus: { type: Boolean, default: false },
    dataKey: { type: String, default: 'data' }
});

const search = ref(props.filters.search || '');
const role = ref(props.filters.role || null);
const tenant_id = ref(props.filters.tenant_id || null);
const status = ref(props.filters.status || null);

let timeout = null;

const performFilter = () => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        // We only send the data that is currently active
        const query = { search: search.value };
        if (props.showRole) query.role = role.value;
        if (props.showTenant) query.tenant_id = tenant_id.value;
        if (props.showStatus) query.status = status.value;

        router.get(route(props.routeName), query, {
            preserveState: true,
            replace: true,
            only: [props.dataKey],
        });
    }, 300);
};

watch([search, role, tenant_id, status], () => performFilter());

const formatRoleName = (name) => {
    if (!name) return '';
    return name.replace(/_/g, ' ').replace(/\b\w/g, char => char.toUpperCase());
};
</script>

<template>
    <div class="flex flex-wrap items-center gap-4 mb-8 bg-white p-5 rounded-[2.5rem] border border-primary-border">

        <div class="relative flex-1 min-w-[280px]">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                <Search :size="18" />
            </span>
            <input v-model="search" type="text" :placeholder="placeholder"
                class="w-full pl-12 pr-4 py-3 bg-gray-50/50 border-transparent rounded-[1.5rem] focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm outline-none" />
        </div>

        <div v-if="showRole"
            class="flex items-center gap-2 px-4 py-2 bg-gray-50/50 rounded-2xl border border-transparent hover:border-primary-border transition-colors">
            <Users :size="16" class="text-gray-400" />
            <select v-model="role"
                class="bg-transparent border-none text-sm font-medium text-gray-600 focus:ring-0 cursor-pointer p-0 pr-8">
                <option :value="null">All Roles</option>

                <option v-for="r in roles" :key="r.id" :value="r.name">
                    {{ formatRoleName(r.name) }}
                </option>
            </select>
        </div>

        <div v-if="showTenant && tenants.length > 0"
            class="flex items-center gap-2 px-4 py-2 bg-gray-50/50 rounded-2xl border border-transparent hover:border-primary-border transition-colors">
            <Building2 :size="16" class="text-gray-400" />
            <select v-model="tenant_id"
                class="bg-transparent border-none text-sm font-medium text-gray-600 focus:ring-0 cursor-pointer p-0 pr-8">
                <option :value="null">All Companies</option>
                <option v-for="t in tenants" :key="t.id" :value="t.id">{{ t.company_name }}</option>

            </select>
        </div>

        <div v-if="showStatus && statusOptions.length > 0"
            class="flex items-center gap-2 px-4 py-2 bg-gray-50/50 rounded-2xl border border-transparent hover:border-primary-border transition-colors">
            <Activity :size="16" class="text-gray-400" />
            <select v-model="status"
                class="bg-transparent border-none text-sm font-medium text-gray-600 focus:ring-0 cursor-pointer p-0 pr-8">
                <option :value="null">All Status</option>
                <option v-for="opt in statusOptions" :key="opt.key" :value="opt.key">
                    {{ opt.label }}
                </option>
            </select>
        </div>
    </div>
</template>