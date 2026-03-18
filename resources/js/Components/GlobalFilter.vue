<script setup>
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { Search, X, Users, Building2, Activity } from 'lucide-vue-next';
import RoundedSelect from '@/Components/RoundedSelect.vue';

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
const isRoleOpen = ref(false);
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
        <!--TODO:: make a dropdown more to the theme-->
       <RoundedSelect 
            v-if="showRole"
            v-model="role"
            :options="roles"
            label="All Roles"
            :icon="Users"
            option-label="name"
            option-value="name"
        />

       <RoundedSelect 
            v-if="showTenant"
            v-model="tenant_id"
            :options="tenants"
            label="All Companies"
            :icon="Building2"
            option-label="company_name" 
            option-value="id"
        />

        <RoundedSelect 
            v-if="showStatus"
            v-model="status"
            :options="statusOptions"
            label="All Status"
            :icon="Activity"
            option-label="label"
            option-value="key"
        />
       
    </div>
</template>