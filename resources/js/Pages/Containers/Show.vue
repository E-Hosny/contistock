<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({ container: Object });
</script>

<template>
    <Head :title="container?.product_name" />
    <DashboardLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">{{ container?.product_name }}</h2>
                <div class="flex gap-2">
                    <Link :href="route('containers.edit', container.id)"><PrimaryButton>{{ $t('common.edit') }}</PrimaryButton></Link>
                    <Link :href="route('warehouse-receipts.create') + '?container_id=' + container.id" class="rounded bg-green-600 px-3 py-2 text-white hover:bg-green-700">{{ $t('pages.containers.receive_to_warehouse') }}</Link>
                </div>
            </div>
        </template>
        <div class="space-y-4">
            <Card>
                <dl class="grid gap-2 sm:grid-cols-2">
                    <div><dt class="text-sm text-gray-500">{{ $t('common.supplier') }}</dt><dd>{{ container?.supplier?.name }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.status') }}</dt><dd><Badge>{{ $t('statusOptions.' + (container?.status || 'draft')) }}</Badge></dd></div>
                </dl>
            </Card>
            <Card>
                <template #title>{{ $t('common.financial_summary') }}</template>
                <dl class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-lg bg-gray-50 p-3">
                        <dt class="text-sm font-medium text-gray-500">{{ $t('common.total_cost') }}</dt>
                        <dd class="mt-1 text-xl font-semibold text-gray-900">{{ container?.total_cost }}</dd>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-3">
                        <dt class="text-sm font-medium text-gray-500">{{ $t('common.paid') }}</dt>
                        <dd class="mt-1 text-xl font-semibold text-green-700">{{ container?.paid_amount }}</dd>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-3">
                        <dt class="text-sm font-medium text-gray-500">{{ $t('common.remaining') }}</dt>
                        <dd class="mt-1 text-xl font-semibold text-amber-700">{{ container?.remaining_amount }}</dd>
                    </div>
                </dl>
            </Card>
            <Card>
                <template #title>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span>{{ $t('common.supplier_payments') }}</span>
                        <Link :href="route('containers.supplier-payments.index', container.id)" class="rounded bg-primary-navy px-3 py-1.5 text-sm text-white hover:bg-primary-navy/90">{{ $t('common.add_payment') }}</Link>
                    </div>
                </template>
                <table v-if="container?.supplier_payments?.length" class="data-table min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.date') }}</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.amount') }}</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.method') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="p in container?.supplier_payments" :key="p.id">
                            <td class="px-4 py-2" :data-label="$t('common.date')">{{ p.payment_date }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.amount')">{{ p.amount }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.method')">{{ p.method }}</td>
                        </tr>
                    </tbody>
                </table>
                <div v-else class="rounded-lg border border-dashed border-gray-300 bg-gray-50/50 p-6 text-center text-gray-500">
                    <p class="mb-3">{{ $t('common.no_payments') }}</p>
                    <Link :href="route('containers.supplier-payments.index', container.id)" class="inline-flex rounded bg-primary-navy px-4 py-2 text-sm text-white hover:bg-primary-navy/90">{{ $t('common.add_payment') }}</Link>
                </div>
            </Card>
            <Card>
                <template #title>{{ $t('common.warehouse_receipts') }}</template>
                <ul class="space-y-1">
                    <li v-for="r in container?.warehouse_receipts" :key="r.id">
                        <Link :href="route('warehouse-receipts.show', r.id)" class="text-primary-navy hover:underline">{{ r.receipt_date }}</Link>
                    </li>
                    <li v-if="!container?.warehouse_receipts?.length" class="text-gray-500">{{ $t('common.none') }}</li>
                </ul>
            </Card>
        </div>
    </DashboardLayout>
</template>
