<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({ products: Object, filters: Object });
</script>

<template>
    <Head title="Products" />
    <DashboardLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">{{ $t('nav.products') }}</h2>
                <Link :href="route('products.create')"><PrimaryButton>{{ $t('common.add') }}</PrimaryButton></Link>
            </div>
        </template>
        <Card>
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('auth.name') }}</th>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('pages.products.sku') }}</th>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="p in products.data" :key="p.id">
                        <td class="px-4 py-2">{{ p.name }}</td>
                        <td class="px-4 py-2 text-sm">{{ p.sku }}</td>
                        <td class="px-4 py-2">
                            <Link :href="route('products.edit', p.id)" class="text-sm text-blue-600 hover:underline">{{ $t('common.edit') }}</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="products.data && !products.data.length" class="p-4 text-center text-gray-500">{{ $t('pages.products.no_products') }}</div>
        </Card>
    </DashboardLayout>
</template>
