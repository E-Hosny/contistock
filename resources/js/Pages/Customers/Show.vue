<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({ customer: Object });
</script>

<template>
    <Head :title="customer?.name" />
    <DashboardLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">{{ customer?.name }}</h2>
                <Link :href="route('customers.edit', customer.id)"><PrimaryButton>{{ $t('common.edit') }}</PrimaryButton></Link>
            </div>
        </template>
        <div class="space-y-4">
            <Card>
                <dl class="grid gap-2 sm:grid-cols-2">
                    <div><dt class="text-sm text-gray-500">{{ $t('common.phone') }}</dt><dd>{{ customer?.phone || '—' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('auth.email') }}</dt><dd>{{ customer?.email || '—' }}</dd></div>
                </dl>
            </Card>
            <Card>
                <template #title>{{ $t('nav.sales') }}</template>
                <ul class="space-y-1">
                    <li v-for="s in customer?.sales" :key="s.id">
                        <Link :href="route('sales.show', s.id)" class="text-primary-navy hover:underline">{{ s.sale_date }} — {{ $t('common.total') }}: {{ s.total }} — {{ $t('common.paid') }}: {{ s.customer_payments_sum_amount ?? 0 }}</Link>
                    </li>
                    <li v-if="!customer?.sales?.length" class="text-gray-500">{{ $t('pages.customers.no_sales') }}</li>
                </ul>
            </Card>
        </div>
    </DashboardLayout>
</template>
