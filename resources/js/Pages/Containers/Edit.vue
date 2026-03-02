<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({ container: Object, suppliers: Array });

const form = useForm({
    supplier_id: props.container?.supplier_id ?? '',
    product_name: props.container?.product_name ?? '',
    description: props.container?.description ?? '',
    total_weight_kg: props.container?.total_weight_kg ?? '',
    total_cost: props.container?.total_cost ?? '',
    purchase_date: props.container?.purchase_date ?? '',
    invoice_ref: props.container?.invoice_ref ?? '',
    status: props.container?.status ?? 'draft',
});
</script>

<template>
    <Head title="Edit Container" />
    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">{{ $t('pages.containers.edit') }}</h2>
        </template>
        <form @submit.prevent="form.put(route('containers.update', container.id))" class="max-w-xl space-y-4">
            <div>
                <InputLabel for="supplier_id" :value="$t('common.supplier')" />
                <select id="supplier_id" v-model="form.supplier_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
            </div>
            <div>
                <InputLabel for="product_name" :value="$t('common.product')" />
                <TextInput id="product_name" v-model="form.product_name" class="mt-1 block w-full" required />
            </div>
            <div>
                <InputLabel for="total_cost" :value="$t('common.total_cost')" />
                <TextInput id="total_cost" v-model="form.total_cost" type="number" step="0.01" class="mt-1 block w-full" required />
            </div>
            <div>
                <InputLabel for="status" :value="$t('common.status')" />
                <select id="status" v-model="form.status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    <option value="draft">Draft</option>
                    <option value="purchased">Purchased</option>
                    <option value="received_to_warehouse">Received</option>
                    <option value="closed">Closed</option>
                </select>
            </div>
            <div class="flex gap-2">
                <PrimaryButton type="submit" :disabled="form.processing">{{ $t('common.update') }}</PrimaryButton>
                <Link :href="route('containers.show', container.id)"><SecondaryButton type="button">{{ $t('common.cancel') }}</SecondaryButton></Link>
            </div>
        </form>
    </DashboardLayout>
</template>
