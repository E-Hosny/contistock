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
                    <div><dt class="text-sm text-gray-500">{{ $t('common.total_cost') }}</dt><dd>{{ container?.total_cost }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.paid') }}</dt><dd>{{ container?.paid_amount }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.remaining') }}</dt><dd>{{ container?.remaining_amount }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.status') }}</dt><dd><Badge>{{ container?.status }}</Badge></dd></div>
                </dl>
            </Card>
            <Card>
                <template #title>
                    <Link :href="route('containers.supplier-payments.index', container.id)">{{ $t('common.supplier_payments') }}</Link>
                </template>
                <ul class="space-y-1">
                    <li v-for="p in container?.supplier_payments" :key="p.id">{{ p.amount }} — {{ p.payment_date }} — {{ p.method }}</li>
                    <li v-if="!container?.supplier_payments?.length" class="text-gray-500">{{ $t('common.no_payments') }}</li>
                </ul>
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
