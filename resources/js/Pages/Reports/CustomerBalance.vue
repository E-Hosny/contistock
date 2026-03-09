<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';

defineProps({ balances: Array });
</script>

<template>
    <Head title="Customer balance" />
    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">{{ $t('pages.reports.customer_balance') }}</h2>
        </template>
        <Card>
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.customer') }}</th>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.total_sales') }}</th>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.total_paid') }}</th>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.balance') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="b in balances" :key="b.customer?.id">
                        <td class="px-4 py-2" :data-label="$t('common.customer')">
                            <Link :href="route('customers.show', b.customer?.id)" class="text-primary-navy hover:underline">{{ b.customer?.name }}</Link>
                        </td>
                        <td class="px-4 py-2" :data-label="$t('common.total_sales')">{{ b.total_sales }}</td>
                        <td class="px-4 py-2" :data-label="$t('common.total_paid')">{{ b.total_paid }}</td>
                        <td class="px-4 py-2 font-medium" :data-label="$t('common.balance')">{{ b.balance }}</td>
                    </tr>
                </tbody>
            </table>
        </Card>
    </DashboardLayout>
</template>
