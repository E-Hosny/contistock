<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Badge from '@/Components/Badge.vue';

defineProps({ sales: Object, filters: Object });
</script>

<template>
    <Head title="Sales" />
    <DashboardLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">{{ $t('nav.sales') }}</h2>
                <Link :href="route('sales.create')"><PrimaryButton>{{ $t('common.new_sale') }}</PrimaryButton></Link>
            </div>
        </template>
        <Card>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ $t('common.date') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ $t('common.customer') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ $t('common.total') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ $t('common.paid') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ $t('common.status') }}</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">{{ $t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="s in sales.data" :key="s.id">
                        <td class="px-4 py-2 text-sm">{{ s.sale_date }}</td>
                        <td class="px-4 py-2">{{ s.customer?.name }}</td>
                        <td class="px-4 py-2">{{ s.total }}</td>
                        <td class="px-4 py-2">{{ s.paid_amount }}</td>
                        <td class="px-4 py-2"><Badge>{{ s.status }}</Badge></td>
                        <td class="px-4 py-2 text-right">
                            <Link :href="route('sales.show', s.id)" class="text-sm text-blue-600 hover:underline">{{ $t('common.view') }}</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="sales.data && !sales.data.length" class="p-4 text-center text-gray-500">{{ $t('pages.sales.no_sales') }}</div>
        </Card>
    </DashboardLayout>
</template>
