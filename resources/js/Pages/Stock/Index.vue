<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';

const props = defineProps({ stock: Array, containers: Array, filterContainerId: [Number, String] });
</script>

<template>
    <Head title="Stock" />
    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">{{ $t('nav.stock') }}</h2>
        </template>
        <div class="space-y-4">
            <form :action="route('stock.index')" method="get" class="flex gap-2">
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
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.product') }}</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('pages.products.sku') }}</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.available_qty') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="s in stock" :key="s.product?.id">
                            <td class="px-4 py-2" :data-label="$t('common.product')">{{ s.product?.name }}</td>
                            <td class="px-4 py-2 text-sm" :data-label="$t('pages.products.sku')">{{ s.product?.sku }}</td>
                            <td class="px-4 py-2 font-medium" :data-label="$t('common.available_qty')">{{ s.available_qty }}</td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="!stock?.length" class="p-4 text-center text-gray-500">{{ $t('pages.stock.no_stock') }}</div>
            </Card>
        </div>
    </DashboardLayout>
</template>
