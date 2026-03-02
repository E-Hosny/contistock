<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps({ customers: Object, filters: Object });
</script>

<template>
    <Head title="Customers" />
    <DashboardLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">{{ $t('nav.customers') }}</h2>
                <Link :href="route('customers.create')"><PrimaryButton>{{ $t('common.add') }}</PrimaryButton></Link>
            </div>
        </template>
        <Card>
            <table class="data-table min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('auth.name') }}</th>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.phone') }}</th>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('nav.sales') }}</th>
                        <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="c in customers.data" :key="c.id">
                        <td class="px-4 py-2">
                            <Link :href="route('customers.show', c.id)" class="text-primary-navy hover:underline">{{ c.name }}</Link>
                        </td>
                        <td class="px-4 py-2 text-sm">{{ c.phone }}</td>
                        <td class="px-4 py-2 text-sm">{{ c.sales_count }}</td>
                        <td class="px-4 py-2">
                            <Link :href="route('customers.edit', c.id)" class="text-sm text-blue-600 hover:underline">{{ $t('common.edit') }}</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="customers.data && !customers.data.length" class="p-4 text-center text-gray-500">{{ $t('pages.customers.no_customers') }}</div>
        </Card>
    </DashboardLayout>
</template>
