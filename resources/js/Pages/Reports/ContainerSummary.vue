<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';

defineProps({ summary: Object });
</script>

<template>
    <Head title="Container summary" />
    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">Container: {{ summary?.container?.product_name }}</h2>
        </template>
        <div class="space-y-4">
            <Card>
                <dl class="grid gap-2 sm:grid-cols-2">
                    <div><dt class="text-sm text-gray-500">{{ $t('common.total_cost') }}</dt><dd>{{ summary?.total_cost }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.paid') }}</dt><dd>{{ summary?.paid_amount }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.remaining') }}</dt><dd>{{ summary?.remaining_amount }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.sold_profit') }}</dt><dd>{{ summary?.total_sold_profit }}</dd></div>
                </dl>
            </Card>
            <Card>
                <template #title>{{ $t('common.receipt_items') }}</template>
                <table class="data-table min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.product') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.qty') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.buy') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.sale') }}</th></tr></thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="item in summary?.receipt_items" :key="item.id">
                            <td class="px-4 py-2">{{ item.product?.name }}</td>
                            <td class="px-4 py-2">{{ item.qty_received }}</td>
                            <td class="px-4 py-2">{{ item.buy_price }}</td>
                            <td class="px-4 py-2">{{ item.sale_price }}</td>
                        </tr>
                    </tbody>
                </table>
            </Card>
        </div>
    </DashboardLayout>
</template>
