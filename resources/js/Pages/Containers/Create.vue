<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({ suppliers: Array });

const form = useForm({
    supplier_id: '',
    product_name: '',
    description: '',
    total_weight_kg: '',
    total_cost: '',
    purchase_date: '',
    invoice_ref: '',
    status: 'draft',
});
</script>

<template>
    <Head title="New Container" />
    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">{{ $t('pages.containers.new') }}</h2>
        </template>
        <form @submit.prevent="form.post(route('containers.store'))" class="max-w-xl space-y-4">
            <div>
                <InputLabel for="supplier_id" :value="$t('common.supplier')" />
                <select id="supplier_id" v-model="form.supplier_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <option value="">{{ $t('common.select') }}</option>
                    <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
                <InputError :message="form.errors.supplier_id" />
            </div>
            <div>
                <InputLabel for="product_name" :value="$t('pages.containers.product_name')" />
                <TextInput id="product_name" v-model="form.product_name" class="mt-1 block w-full" required />
            </div>
            <div>
                <InputLabel for="total_cost" :value="$t('common.total_cost')" />
                <TextInput id="total_cost" v-model="form.total_cost" type="number" step="0.01" class="mt-1 block w-full" required />
            </div>
            <div>
                <InputLabel for="purchase_date" :value="$t('common.date')" />
                <TextInput id="purchase_date" v-model="form.purchase_date" type="date" class="mt-1 block w-full" />
            </div>
            <div>
                <InputLabel for="invoice_ref" value="Invoice ref" />
                <TextInput id="invoice_ref" v-model="form.invoice_ref" class="mt-1 block w-full" />
            </div>
            <div class="flex gap-2">
                <PrimaryButton type="submit" :disabled="form.processing">{{ $t('common.save') }}</PrimaryButton>
                <Link :href="route('containers.index')"><SecondaryButton type="button">{{ $t('common.cancel') }}</SecondaryButton></Link>
            </div>
        </form>
    </DashboardLayout>
</template>
