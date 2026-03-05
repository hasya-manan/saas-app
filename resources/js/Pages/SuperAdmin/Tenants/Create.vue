<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

//for flash 
const page = usePage();
const form = useForm({
    company_name: '',
    admin_name: '',
    admin_email: '',
});

const submit = () => {
    form.post(route('tenants.store'), {
        onFinish: () => form.reset('admin_email'),
    });
};

const flashMessage = computed(() => page.props.flash.message);
</script>

<template>
    <Head title="Onboard New Company" />

    <AuthenticatedLayout>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Onboard New Company</h1>
            <p class="text-sm text-gray-500">Register a new client organization and assign their first system administrator.</p>
        </div>

        <div class="max-w-4xl">
            <form @submit.prevent="submit" class="bg-white rounded-2xl shadow-sm border border-primary-border/30 overflow-hidden">
                
                <div class="p-8">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="h-8 w-1 bg-primary rounded-full"></div>
                        <h3 class="text-lg font-bold text-gray-800">Organization Details</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-1">
                            <InputLabel for="company_name" value="Company Legal Name" class="font-semibold text-gray-700" />
                            <p class="text-xs text-gray-400 mt-1 leading-relaxed">This name will be used for all official correspondence and system-generated reports.</p>
                        </div>
                        <div class="md:col-span-2">
                            <TextInput id="company_name" type="text" 
                                class="block w-full border-gray-200 bg-gray-50/30 focus:bg-white focus:ring-primary-border focus:border-primary transition-all rounded-xl shadow-sm" 
                                v-model="form.company_name" required autofocus placeholder="e.g. Acme Corporation Pty Ltd" />
                            <InputError class="mt-2" :message="form.errors.company_name" />
                        </div>
                    </div>
                </div>

                <div class="px-8">
                    <hr class="border-primary-border/10" />
                </div>

                <div class="p-8">
                    <div class="flex items-center gap-2 mb-6">
                        <div class="h-8 w-1 bg-primary rounded-full opacity-80"></div>
                        <h3 class="text-lg font-bold text-gray-800">Primary Administrator</h3>
                    </div>

                    <div class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-1">
                                <InputLabel for="admin_name" value="Full Name" class="font-semibold text-gray-700" />
                            </div>
                            <div class="md:col-span-2">
                                <TextInput id="admin_name" type="text" 
                                    class="block w-full border-gray-200 bg-gray-50/30 focus:bg-white focus:ring-primary-border focus:border-primary rounded-xl shadow-sm" 
                                    v-model="form.admin_name" required placeholder="Enter admin's full name" />
                                <InputError class="mt-2" :message="form.errors.admin_name" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="md:col-span-1">
                                <InputLabel for="admin_email" value="Work Email Address" class="font-semibold text-gray-700" />
                                <p class="text-xs text-gray-400 mt-1">An invitation link will be sent to this email to set up their password.</p>
                            </div>
                            <div class="md:col-span-2">
                                <TextInput id="admin_email" type="email" 
                                    class="block w-full border-gray-200 bg-gray-50/30 focus:bg-white focus:ring-primary-border focus:border-primary rounded-xl shadow-sm" 
                                    v-model="form.admin_email" required placeholder="admin@company.com" />
                                <InputError class="mt-2" :message="form.errors.admin_email" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-primary-light/20 p-8 border-t border-primary-border/20 flex items-center justify-end gap-6">
                    <button type="button" @click="form.reset()" 
                        class="text-sm font-semibold text-gray-500 hover:text-primary transition-colors">
                        Clear Form
                    </button>
                    <PrimaryButton 
                        class="px-8 py-3 bg-primary hover:bg-primary-dark text-white rounded-xl font-bold shadow-lg shadow-primary/20 transition-all transform active:scale-95 border-none" 
                        :class="{ 'opacity-50': form.processing }" 
                        :disabled="form.processing"
                    >
                        {{ form.processing ? 'Registering...' : 'Confirm Registration' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.5s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>