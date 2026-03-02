<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({ sale: Object });

const confirmForm = useForm({});
const cancelForm = useForm({});
</script>

<template>
    <Head title="Sale" />
    <DashboardLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Sale #{{ sale?.id }}</h2>
                <div class="flex gap-2">
                    <Link v-if="sale?.status === 'draft'" :href="route('sales.edit', sale.id)"><PrimaryButton>{{ $t('common.edit') }}</PrimaryButton></Link>
                    <form v-if="sale?.status === 'draft'" @submit.prevent="confirmForm.post(route('sales.confirm', sale.id))" class="inline">
                        <PrimaryButton type="submit" :disabled="confirmForm.processing">{{ $t('common.confirm') }}</PrimaryButton>
                    </form>
                    <form v-if="sale?.status === 'draft' || sale?.status === 'confirmed'" @submit.prevent="cancelForm.post(route('sales.cancel', sale.id))" class="inline">
                        <DangerButton type="submit" :disabled="cancelForm.processing">{{ $t('common.cancel_sale') }}</DangerButton>
                    </form>
                    <Link :href="route('sales.customer-payments.index', sale.id)" class="rounded bg-green-600 px-3 py-2 text-white hover:bg-green-700">{{ $t('common.payments') }}</Link>
                </div>
            </div>
        </template>
        <div class="space-y-4">
            <Card>
                <dl class="grid gap-2 sm:grid-cols-2">
                    <div><dt class="text-sm text-gray-500">{{ $t('common.customer') }}</dt><dd>{{ sale?.customer?.name }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.date') }}</dt><dd>{{ sale?.sale_date }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.status') }}</dt><dd><Badge>{{ sale?.status }}</Badge></dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.subtotal') }}</dt><dd>{{ sale?.subtotal }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.discount') }}</dt><dd>{{ sale?.discount }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.total') }}</dt><dd>{{ sale?.total }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.paid') }}</dt><dd>{{ sale?.paid_amount }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.remaining') }}</dt><dd>{{ sale?.remaining_amount }}</dd></div>
                </dl>
            </Card>
            <Card>
                <template #title>{{ $t('common.items') }}</template>
                <table class="data-table min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.product') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.qty') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.unit_price') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.total') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.sold_profit') }}</th></tr></thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="item in sale?.sale_items" :key="item.id">
                            <td class="px-4 py-2">{{ item.product?.name }}</td>
                            <td class="px-4 py-2">{{ item.qty }}</td>
                            <td class="px-4 py-2">{{ item.unit_price }}</td>
                            <td class="px-4 py-2">{{ item.line_total }}</td>
                            <td class="px-4 py-2">{{ item.profit_line }}</td>
                        </tr>
                    </tbody>
                </table>
            </Card>
        </div>
    </DashboardLayout>
</template>
