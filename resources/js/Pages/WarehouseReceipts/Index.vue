<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({ receipts: Object, filters: Object });
</script>

<template>
    <Head title="Warehouse Receipts" />
    <DashboardLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">{{ $t('nav.warehouse_receipts') }}</h2>
                <Link :href="route('warehouse-receipts.create')"><PrimaryButton>{{ $t('common.new_receipt') }}</PrimaryButton></Link>
            </div>
        </template>
        <Card>
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.date') }}</th>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.container') }}</th>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.received_by') }}</th>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="r in receipts.data" :key="r.id">
                        <td class="px-4 py-2 text-sm">{{ r.receipt_date }}</td>
                        <td class="px-4 py-2">{{ r.container?.product_name }}</td>
                        <td class="px-4 py-2 text-sm">{{ r.received_by_user?.name }}</td>
                        <td class="px-4 py-2">
                            <Link :href="route('warehouse-receipts.show', r.id)" class="text-sm text-blue-600 hover:underline">{{ $t('common.view') }}</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="receipts.data && !receipts.data.length" class="p-4 text-center text-gray-500">{{ $t('pages.warehouse_receipts.no_receipts') }}</div>
        </Card>
    </DashboardLayout>
</template>
