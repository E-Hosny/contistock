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
                <table class="min-w-full">
                    <thead><tr><th class="text-left text-xs text-gray-500">{{ $t('common.product') }}</th><th class="text-left text-xs text-gray-500">{{ $t('common.qty') }}</th><th class="text-left text-xs text-gray-500">{{ $t('common.buy') }}</th><th class="text-left text-xs text-gray-500">{{ $t('common.sale') }}</th><th class="text-left text-xs text-gray-500">{{ $t('common.profit_per_unit') }}</th></tr></thead>
                    <tbody>
                        <tr v-for="item in receipt?.receipt_items" :key="item.id" class="border-t">
                            <td class="py-1">{{ item.product?.name }}</td>
                            <td class="py-1">{{ item.qty_received }}</td>
                            <td class="py-1">{{ item.buy_price }}</td>
                            <td class="py-1">{{ item.sale_price }}</td>
                            <td class="py-1">{{ (item.sale_price - item.buy_price).toFixed(2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </Card>
        </div>
    </DashboardLayout>
</template>
