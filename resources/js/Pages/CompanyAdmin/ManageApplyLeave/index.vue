<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue'; // Adjust path if needed
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import RoundedSelect from '@/Components/RoundedSelect.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

</script>
<template>
    <Head title="Apply Leave" />
    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Apply Leave" subtitle="Manage Leave Applications" />
        </template>
        <!-- Your form content goes here -->
         
    </AuthenticatedLayout>
</template>