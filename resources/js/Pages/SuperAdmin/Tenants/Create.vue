<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

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
    <Head title="Create Tenant" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Onboard New Company</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!--flash message -->
                <div v-if="flashMessage" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ flashMessage }}
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit" class="max-w-xl">
                        
                        <div>
                            <InputLabel for="company_name" value="Company Name" />
                            <TextInput id="company_name" type="text" class="mt-1 block w-full" 
                                v-model="form.company_name" required autofocus />
                            <InputError class="mt-2" :message="form.errors.company_name" />
                        </div>

                        <hr class="my-8 border-gray-200" />

                        <h3 class="text-lg font-medium text-gray-900 mb-4">Company Admin Account</h3>
                        
                        <div class="mt-4">
                            <InputLabel for="admin_name" value="Admin Full Name" />
                            <TextInput id="admin_name" type="text" class="mt-1 block w-full" 
                                v-model="form.admin_name" required />
                            <InputError class="mt-2" :message="form.errors.admin_name" />
                        </div>

                        <div class="mt-4">
                            <InputLabel for="admin_email" value="Admin Email Address" />
                            <TextInput id="admin_email" type="email" class="mt-1 block w-full" 
                                v-model="form.admin_email" required />
                            <InputError class="mt-2" :message="form.errors.admin_email" />
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                Create Company & Admin
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>