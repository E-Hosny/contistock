<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({ sale: Object, customers: Array, products: Array, containers: Array });

const form = useForm({
    customer_id: props.sale?.customer_id ?? '',
    sale_date: props.sale?.sale_date ?? '',
    invoice_no: props.sale?.invoice_no ?? '',
    discount: props.sale?.discount ?? 0,
    items: (props.sale?.sale_items ?? []).map(i => ({
        product_id: i.product_id,
        container_id: i.container_id,
        qty: i.qty,
        unit_price: i.unit_price,
    })),
});

function addItem() {
    form.items.push({ product_id: '', container_id: '', qty: 1, unit_price: 0 });
}

function removeItem(i) {
    form.items.splice(i, 1);
}
</script>

<template>
    <Head title="Edit Sale" />
    <DashboardLayout>
        <template #header><h2 class="text-xl font-semibold text-gray-800">{{ $t('pages.sales.edit') }}</h2></template>
        <form @submit.prevent="form.put(route('sales.update', sale.id))" class="space-y-4">
            <div class="max-w-xl space-y-4 rounded-lg bg-white p-4 shadow">
                <div>
                    <InputLabel for="customer_id" :value="$t('common.customer')" />
                    <select id="customer_id" v-model="form.customer_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }}</option>
                    </select>
                </div>
                <div>
                    <InputLabel for="sale_date" :value="$t('common.date')" />
                    <TextInput id="sale_date" v-model="form.sale_date" type="date" class="mt-1 block w-full" required />
                </div>
                <div>
                    <InputLabel for="discount" :value="$t('common.discount')" />
                    <TextInput id="discount" v-model="form.discount" type="number" step="0.01" class="mt-1 block w-full" />
                </div>
            </div>
            <div class="rounded-lg bg-white p-4 shadow">
                <div class="flex items-center justify-between">
                    <h3 class="font-medium">{{ $t('common.items') }}</h3>
                    <button type="button" @click="addItem" class="text-sm text-blue-600">{{ $t('common.add_row') }}</button>
                </div>
                <table class="data-table mt-2 min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.product') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.container') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.qty') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.unit_price') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.actions') }}</th></tr></thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="(item, i) in form.items" :key="i">
                            <td class="px-4 py-2">
                                <select v-model="item.product_id" class="w-full rounded border-gray-300 text-sm" required>
                                    <option value="">{{ $t('common.select') }}</option>
                                    <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }}</option>
                                </select>
                            </td>
                            <td class="px-4 py-2">
                                <select v-model="item.container_id" class="w-full rounded border-gray-300 text-sm" required>
                                    <option v-for="c in containers" :key="c.id" :value="c.id">{{ c.product_name }}</option>
                                </select>
                            </td>
                            <td class="px-4 py-2"><input v-model.number="item.qty" type="number" step="0.01" min="0.01" class="mx-auto w-20 rounded border-gray-300 text-center text-sm" required /></td>
                            <td class="px-4 py-2"><input v-model.number="item.unit_price" type="number" step="0.01" min="0" class="mx-auto w-24 rounded border-gray-300 text-center text-sm" required /></td>
                            <td class="px-4 py-2"><button type="button" @click="removeItem(i)" class="text-red-600">{{ $t('common.remove') }}</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex gap-2">
                <PrimaryButton type="submit" :disabled="form.processing">{{ $t('common.update') }}</PrimaryButton>
                <Link :href="route('sales.show', sale.id)"><SecondaryButton type="button">{{ $t('common.cancel') }}</SecondaryButton></Link>
            </div>
        </form>
    </DashboardLayout>
</template>
