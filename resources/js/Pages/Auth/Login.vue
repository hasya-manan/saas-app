<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>
<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="text-center mb-8">
            
            <h1 class="text-2xl font-bold text-gray-900">Welcome Back</h1>
            <p class="text-gray-500 text-sm mt-1">Please enter your details to sign in</p>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Email" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full rounded-xl border-gray-100 focus:border-primary focus:ring focus:ring-primary/10 transition-all"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="name@company.com"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <div class="flex justify-between items-center">
                    <InputLabel for="password" value="Password" class="text-xs font-bold text-gray-400 uppercase tracking-widest ml-1" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs font-semibold text-primary hover:text-primary-dark transition-colors"
                    >
                        Forgot?
                    </Link>
                </div>

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full rounded-xl border-gray-100 focus:border-primary focus:ring focus:ring-primary/10 transition-all"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center ml-1">
                    <Checkbox name="remember" v-model:checked="form.remember" class="rounded border-gray-300 text-primary focus:ring-primary/20" />
                    <span class="ms-2 text-sm text-gray-500">Stay signed in</span>
                </label>
            </div>

            <div class="mt-6">
                <BaseButton
                    variant="primary"
                    class="w-full py-4 rounded-xl shadow-lg shadow-primary/20"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing">Signing in...</span>
                    <span v-else>Log in</span>
                </BaseButton>
            </div>
        </form>

        <p class="text-center mt-8 text-sm text-gray-500">
            Don't have an account? <span class="font-bold text-primary cursor-pointer hover:underline">Contact Sales</span>
        </p>
    </GuestLayout>
</template>
