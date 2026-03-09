<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';

defineProps({ receipt: Object });
</script>

<template>
    <Head title="Warehouse Receipt" />
    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Receipt {{ receipt?.receipt_date }}</h2>
        </template>
        <div class="space-y-4">
            <Card>
                <dl class="grid gap-2 sm:grid-cols-2">
                    <div><dt class="text-sm text-gray-500">{{ $t('common.container') }}</dt><dd><Link :href="route('containers.show', receipt?.container?.id)" class="text-primary-navy hover:underline">{{ receipt?.container?.product_name }}</Link></dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.received_by') }}</dt><dd>{{ receipt?.received_by_user?.name }}</dd></div>
                </dl>
            </Card>
            <Card>
                <template #title>{{ $t('common.items') }}</template>
                <table class="data-table min-w-full">
                    <thead><tr><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.product') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.qty') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.buy') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.sale') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.profit_per_unit') }}</th></tr></thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="item in receipt?.receipt_items" :key="item.id">
                            <td class="px-4 py-2" :data-label="$t('common.product')">{{ item.product?.name }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.qty')">{{ item.qty_received }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.buy')">{{ item.buy_price }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.sale')">{{ item.sale_price }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.profit_per_unit')">{{ (item.sale_price - item.buy_price).toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </Card>
        </div>
    </DashboardLayout>
</template>
