<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({ containers: Array, products: Array, preselectedContainerId: [String, Number] });

const form = useForm({
    container_id: props.preselectedContainerId || '',
    receipt_date: new Date().toISOString().slice(0, 10),
    notes: '',
    items: [{ product_id: '', qty_received: 1, buy_price: 0, sale_price: 0 }],
});

function addItem() {
    form.items.push({ product_id: '', qty_received: 1, buy_price: 0, sale_price: 0 });
}

function removeItem(i) {
    form.items.splice(i, 1);
}
</script>

<template>
    <Head title="New Warehouse Receipt" />
    <DashboardLayout>
        <template #header><h2 class="text-xl font-semibold text-gray-800">{{ $t('pages.warehouse_receipts.new') }}</h2></template>
        <form @submit.prevent="form.post(route('warehouse-receipts.store'))" class="space-y-4">
            <div class="max-w-xl space-y-4 rounded-lg bg-white p-4 shadow">
                <div>
                    <InputLabel for="container_id" :value="$t('common.container')" />
                    <select id="container_id" v-model="form.container_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        <option value="">{{ $t('common.select') }}</option>
                        <option v-for="c in containers" :key="c.id" :value="c.id">{{ c.product_name }}</option>
                    </select>
                </div>
                <div>
                    <InputLabel for="receipt_date" :value="$t('common.receipt_date')" />
                    <TextInput id="receipt_date" v-model="form.receipt_date" type="date" class="mt-1 block w-full" required />
                </div>
            </div>
            <div class="rounded-lg bg-white p-4 shadow">
                <div class="flex items-center justify-between">
                    <h3 class="font-medium">{{ $t('common.items') }}</h3>
                    <button type="button" @click="addItem" class="text-sm text-blue-600">{{ $t('common.add_row') }}</button>
                </div>
                <table class="data-table mt-2 min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.product') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.qty') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.buy_price') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.sale_price') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.actions') }}</th></tr></thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="(item, i) in form.items" :key="i">
                            <td class="px-4 py-2">
                                <select v-model="item.product_id" class="w-full rounded border-gray-300 text-sm" required>
                                    <option value="">{{ $t('common.select') }}</option>
                                    <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }} ({{ p.sku }})</option>
                                </select>
                            </td>
                            <td class="px-4 py-2"><input v-model.number="item.qty_received" type="number" step="0.01" min="0.01" class="mx-auto w-20 rounded border-gray-300 text-center text-sm" required /></td>
                            <td class="px-4 py-2"><input v-model.number="item.buy_price" type="number" step="0.01" min="0" class="mx-auto w-24 rounded border-gray-300 text-center text-sm" required /></td>
                            <td class="px-4 py-2"><input v-model.number="item.sale_price" type="number" step="0.01" min="0" class="mx-auto w-24 rounded border-gray-300 text-center text-sm" required /></td>
                            <td class="px-4 py-2"><button type="button" @click="removeItem(i)" class="text-red-600">{{ $t('common.remove') }}</button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex gap-2">
                <PrimaryButton type="submit" :disabled="form.processing">{{ $t('common.create_receipt') }}</PrimaryButton>
                <Link :href="route('warehouse-receipts.index')"><SecondaryButton type="button">{{ $t('common.cancel') }}</SecondaryButton></Link>
            </div>
        </form>
    </DashboardLayout>
</template>
