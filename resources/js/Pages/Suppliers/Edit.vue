<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({ supplier: Object });

const form = useForm({
    name: props.supplier?.name ?? '',
    phone: props.supplier?.phone ?? '',
    email: props.supplier?.email ?? '',
    address: props.supplier?.address ?? '',
    notes: props.supplier?.notes ?? '',
    is_active: props.supplier?.is_active ?? true,
});
</script>

<template>
    <Head title="Edit Supplier" />
    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">{{ $t('pages.suppliers.edit') }}</h2>
        </template>
        <form @submit.prevent="form.put(route('suppliers.update', supplier.id))" class="max-w-xl space-y-4">
            <div>
                <InputLabel for="name" :value="$t('auth.name')" />
                <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required />
                <InputError :message="form.errors.name" />
            </div>
            <div>
                <InputLabel for="phone" :value="$t('common.phone')" />
                <TextInput id="phone" v-model="form.phone" class="mt-1 block w-full" />
            </div>
            <div>
                <InputLabel for="email" :value="$t('auth.email')" />
                <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" />
            </div>
            <div>
                <InputLabel for="address" :value="$t('common.address')" />
                <textarea id="address" v-model="form.address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            </div>
            <div>
                <InputLabel for="notes" :value="$t('common.notes')" />
                <textarea id="notes" v-model="form.notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
            </div>
            <div class="flex gap-2">
                <PrimaryButton type="submit" :disabled="form.processing">{{ $t('common.update') }}</PrimaryButton>
                <Link :href="route('suppliers.show', supplier.id)"><SecondaryButton type="button">{{ $t('common.cancel') }}</SecondaryButton></Link>
            </div>
        </form>
    </DashboardLayout>
</template>
