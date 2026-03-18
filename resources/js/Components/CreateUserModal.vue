<script setup>
import { useForm } from '@inertiajs/vue3';
import { X, UserPlus } from 'lucide-vue-next';

const props = defineProps({
    show: Boolean,
    title: { type: String, default: 'Modal Title' },
    description: { type: String, default: 'Modal Description' },
    tenants: Array,
    roles: Array
});

const emit = defineEmits(['close']);
//TODO:: the password need to change instead of this:: maybe can use no fon first or need to think about it
const form = useForm({
    name: '',
    email: '',
    password: 'password123', // Default for now
    tenant_id: '',
    role: '',
});

const submit = () => {
    form.post(route('users.store'), {
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
};
</script>


<template>
    <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0 scale-95"
        enter-to-class="opacity-100 scale-100" leave-active-class="ease-in duration-200"
        leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
        <div v-if="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-900/70 backdrop-blur-md transition-opacity" @click="emit('close')"></div>

            <div
                class="relative w-full max-w-2xl max-h-[90vh] bg-gray-50 rounded-[2.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-gray-100 flex flex-col overflow-hidden">

                <!-- Header -->
                <div class="px-10 py-8 flex items-center justify-between border-b border-gray-100 bg-white">
                    <div class="flex items-center gap-5 mr-10"> <!-- Increased gap & margin -->
                        <div class="p-3.5 bg-primary/10 rounded-2xl text-primary">
                            <UserPlus :size="24" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 leading-tight">{{ title }}</h3>
                            <p class="text-sm text-gray-500 font-medium">{{ description }}</p>
                        </div>
                    </div>
                    <button @click="emit('close')"
                        class="p-2 hover:bg-gray-100 rounded-xl transition-colors text-gray-400">
                        <X :size="22" />
                    </button>
                </div>

                <!-- Scrollable Form -->
                <div class="flex-1 overflow-y-auto p-10 pt-8 custom-scrollbar">
                    <form id="createUserForm" @submit.prevent="submit" class="space-y-7">

                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <label
                                    class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2.5 ml-1">Full
                                    Name</label>
                                <input v-model="form.name" type="text" placeholder="Ahmad Abdullah"
                                    class="w-full px-5 py-4 bg-white border border-gray-300 rounded-2xl shadow-[inset_0_1px_2px_rgba(0,0,0,0.07)] placeholder:text-gray-500 placeholder:font-medium focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm text-gray-900 font-medium outline-none">
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2.5 ml-1">Email
                                    Address</label>
                                <input v-model="form.email" type="email" placeholder="ahmad@email.com"
                                    class="w-full px-5 py-4 bg-white border border-gray-300 rounded-2xl shadow-[inset_0_1px_2px_rgba(0,0,0,0.07)] placeholder:text-gray-500 placeholder:font-mediumfocus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm text-gray-900 font-medium outline-none">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <label
                                    class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2.5 ml-1">Phone
                                    Number</label>
                                <input v-model="form.phone" type="text" placeholder="+6012-3456789"
                                    class="w-full px-5 py-4 bg-white border border-gray-300 rounded-2xl shadow-[inset_0_1px_2px_rgba(0,0,0,0.07)]  placeholder:text-gray-500 placeholder:font-mediumfocus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm text-gray-900 font-medium outline-none">
                            </div>
                            <div>
                                <label
                                    class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2.5 ml-1">Tenant/Company</label>
                                <select v-model="form.tenant_id"
                                    class="w-full px-5 py-4 bg-white border border-gray-300 rounded-2xl shadow-[inset_0_1px_2px_rgba(0,0,0,0.07)]  placeholder:text-gray-500 placeholder:font-medium focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm text-gray-900 font-medium outline-none">
                                    <option value="">Select Company</option>
                                    <option v-for="t in tenants" :key="t.id" :value="t.id">{{ t.company_name }}</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label
                                class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-2.5 ml-1">Home
                                Address</label>
                            <textarea v-model="form.address" rows="3" placeholder="Enter complete address"
                                class="w-full px-5 py-4 bg-white border border-gray-300 rounded-2xl shadow-[inset_0_1px_2px_rgba(0,0,0,0.07)] placeholder:text-gray-500 placeholder:font-medium focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all text-sm text-gray-900 font-medium outline-none"></textarea>
                        </div>

                        <!-- Emergency Contact Section -->
                        <div class="bg-white p-8 rounded-[2.5rem] border border-gray-200 shadow-sm">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-primary mb-6 ml-1">
                                Emergency Contact (Waris)
                            </p>

                            <div class="grid grid-cols-2 gap-6">
                                <!-- Contact Name -->
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1">
                                        Contact Name
                                    </label>
                                    <input v-model="form.waris_name" type="text" placeholder="e.g. Siti Aminah"
                                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-300 rounded-xl shadow-[inset_0_1px_2px_rgba(0,0,0,0.05)] placeholder:text-gray-500 text-sm text-gray-900 font-medium focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                                </div>

                                <!-- Relationship -->
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-2 ml-1">
                                        Relationship
                                    </label>
                                    <input v-model="form.waris_relationship" type="text" placeholder="Spouse / Parent"
                                        class="w-full px-5 py-3.5 bg-gray-50 border border-gray-300 rounded-xl shadow-[inset_0_1px_2px_rgba(0,0,0,0.05)] placeholder:text-gray-500 text-sm text-gray-900 font-medium focus:bg-white focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all outline-none">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Footer -->
                <div class="p-10 py-7 bg-white border-t border-gray-100 flex items-center gap-4">
                    <BaseButton variant="ghost"
                        class="flex-1 py-4 rounded-2xl font-bold uppercase tracking-widest text-[10px]"
                        @click="emit('close')">
                        Cancel
                    </BaseButton>

                    <button type="submit" form="createUserForm" :disabled="form.processing"
                        class="flex-[2] py-4 bg-primary text-white rounded-2xl font-bold shadow-lg shadow-primary/20 hover:bg-primary-dark transition-all disabled:opacity-50">
                        {{ form.processing ? 'Saving...' : 'Create Account' }}
                    </button>
                </div>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #E5E7EB;
    border-radius: 10px;
}
</style>