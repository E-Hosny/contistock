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
                <table class="min-w-full">
                    <thead><tr><th class="text-left text-xs text-gray-500">{{ $t('common.product') }}</th><th class="text-left text-xs text-gray-500">{{ $t('common.qty') }}</th><th class="text-left text-xs text-gray-500">{{ $t('common.buy') }}</th><th class="text-left text-xs text-gray-500">{{ $t('common.sale') }}</th></tr></thead>
                    <tbody>
                        <tr v-for="item in summary?.receipt_items" :key="item.id" class="border-t">
                            <td class="py-1">{{ item.product?.name }}</td>
                            <td class="py-1">{{ item.qty_received }}</td>
                            <td class="py-1">{{ item.buy_price }}</td>
                            <td class="py-1">{{ item.sale_price }}</td>
                        </tr>
                    </tbody>
                </table>
            </Card>
        </div>
    </DashboardLayout>
</template>
