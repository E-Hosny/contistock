<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';

const props = defineProps({
    stats: { type: Object, default: () => ({}) },
    supplierBalances: { type: Array, default: () => [] },
    customerBalances: { type: Array, default: () => [] },
});
</script>

<template>
    <Head :title="$t('nav.dashboard')" />

    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('nav.dashboard') }}
            </h2>
        </template>

        <div class="space-y-6">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-primary-navy">
                    {{ $t('dashboard.welcome') }}
                </h3>
                <p class="mt-1 text-gray-600">{{ $t('dashboard.subtitle') }}</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <Card>
                    <template #title>{{ $t('dashboard.suppliers_balance') }}</template>
                    <p class="text-2xl font-semibold text-gray-800">{{ stats.suppliers_balance ?? 0 }}</p>
                </Card>
                <Card>
                    <template #title>{{ $t('dashboard.customers_balance') }}</template>
                    <p class="text-2xl font-semibold text-gray-800">{{ stats.customers_balance ?? 0 }}</p>
                </Card>
                <Card>
                    <template #title>{{ $t('dashboard.inventory_value') }}</template>
                    <p class="text-2xl font-semibold text-gray-800">{{ stats.inventory_value ?? 0 }}</p>
                </Card>
                <Card>
                    <template #title>{{ $t('dashboard.total_profit') }}</template>
                    <p class="text-2xl font-semibold text-gray-800">{{ stats.total_profit ?? 0 }}</p>
                </Card>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                <Card>
                    <template #title>
                        <Link :href="route('reports.supplier-balance')" class="hover:underline">
                            {{ $t('dashboard.suppliers_balance') }} ({{ supplierBalances.length }})
                        </Link>
                    </template>
                    <ul class="space-y-1 text-sm text-gray-600">
                        <li v-for="b in supplierBalances" :key="b.supplier?.id">
                            {{ b.supplier?.name }}: {{ b.balance }}
                        </li>
                        <li v-if="!supplierBalances.length">{{ $t('common.no_data') }}</li>
                    </ul>
                </Card>
                <Card>
                    <template #title>
                        <Link :href="route('reports.customer-balance')" class="hover:underline">
                            {{ $t('dashboard.customers_balance') }} ({{ customerBalances.length }})
                        </Link>
                    </template>
                    <ul class="space-y-1 text-sm text-gray-600">
                        <li v-for="b in customerBalances" :key="b.customer?.id">
                            {{ b.customer?.name }}: {{ b.balance }}
                        </li>
                        <li v-if="!customerBalances.length">{{ $t('common.no_data') }}</li>
                    </ul>
                </Card>
            </div>
        </div>
    </DashboardLayout>
</template>
