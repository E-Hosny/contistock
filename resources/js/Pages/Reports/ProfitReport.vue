<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';

const props = defineProps({ report: Object, containers: Array, filterContainerId: [Number, String] });
</script>

<template>
    <Head title="Profit report" />
    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">{{ $t('pages.reports.profit_report') }}</h2>
        </template>
        <div class="space-y-4">
            <form :action="route('reports.profit')" method="get" class="flex gap-2">
                <select name="container_id" class="rounded border-gray-300 shadow-sm">
                    <option value="">{{ $t('common.all_containers') }}</option>
                    <option v-for="c in containers" :key="c.id" :value="c.id" :selected="filterContainerId == c.id">{{ c.product_name }}</option>
                </select>
                <button type="submit" class="rounded bg-gray-200 px-3 py-1">{{ $t('common.filter') }}</button>
            </form>
            <Card>
                <table class="data-table min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.container') }}</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.total_cost') }}</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.sold_profit') }}</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.expected_profit') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="row in report?.by_container" :key="row.container_id">
                            <td class="px-4 py-2" :data-label="$t('common.container')">{{ row.container_name }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.total_cost')">{{ row.total_cost }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.sold_profit')">{{ row.total_sold_profit }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.expected_profit')">{{ row.total_profit_expected }}</td>
                        </tr>
                    </tbody>
                </table>
            </Card>
        </div>
    </DashboardLayout>
</template>
