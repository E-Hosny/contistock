<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({ customer: Object });
const form = useForm({
    name: props.customer?.name ?? '',
    phone: props.customer?.phone ?? '',
    email: props.customer?.email ?? '',
    address: props.customer?.address ?? '',
    notes: props.customer?.notes ?? '',
    status: props.customer?.status ?? 'active',
});
</script>

<template>
    <Head title="Edit Customer" />
    <DashboardLayout>
        <template #header><h2 class="text-xl font-semibold text-gray-800">{{ $t('pages.customers.edit') }}</h2></template>
        <form @submit.prevent="form.put(route('customers.update', customer.id))" class="max-w-xl space-y-4">
            <div>
                <InputLabel for="name" :value="$t('auth.name')" />
                <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required />
            </div>
            <div>
                <InputLabel for="phone" :value="$t('common.phone')" />
                <TextInput id="phone" v-model="form.phone" class="mt-1 block w-full" />
            </div>
            <div>
                <InputLabel for="email" :value="$t('auth.email')" />
                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" />
            </div>
            <div class="flex gap-2">
                <PrimaryButton type="submit" :disabled="form.processing">{{ $t('common.update') }}</PrimaryButton>
                <Link :href="route('customers.show', customer.id)"><SecondaryButton type="button">{{ $t('common.cancel') }}</SecondaryButton></Link>
            </div>
        </form>
    </DashboardLayout>
</template>
