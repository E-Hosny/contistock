<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    container: Object,
    purchasesCard: Object,
    salesCard: Object,
    stockCard: Object,
    canReceiveToWarehouse: Boolean,
});

const receiveUrl = computed(
    () => `${route('warehouse-receipts.create')}?container_id=${props.container.id}`,
);
</script>

<template>
    <Head :title="container?.product_name" />
    <DashboardLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800">{{ container?.product_name }}</h2>
                    <p class="mt-1 text-sm text-gray-500">{{ $t('pages.containers.show_subtitle') }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <Link :href="route('containers.edit', container.id)">
                        <PrimaryButton type="button">{{ $t('common.edit') }}</PrimaryButton>
                    </Link>
                    <Link
                        v-if="canReceiveToWarehouse"
                        :href="receiveUrl"
                        class="inline-flex items-center justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700"
                    >
                        {{ $t('pages.containers.receive_to_warehouse') }}
                    </Link>
                    <span
                        v-else
                        class="inline-flex cursor-not-allowed items-center justify-center rounded-md bg-gray-200 px-4 py-2 text-sm text-gray-500"
                        :title="$t('common.receive_hint')"
                    >
                        {{ $t('pages.containers.receive_to_warehouse') }}
                    </span>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <Card class="!shadow-sm">
                <dl class="flex flex-wrap gap-x-6 gap-y-2 text-sm">
                    <div>
                        <span class="text-gray-500">{{ $t('common.supplier') }}:</span>
                        <span class="ms-1 font-medium text-gray-900">{{ container?.supplier?.name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">{{ $t('common.status') }}:</span>
                        <Badge class="ms-1">{{ $t('statusOptions.' + (container?.status || 'draft')) }}</Badge>
                    </div>
                    <div v-if="container?.purchase_date">
                        <span class="text-gray-500">{{ $t('common.date') }}:</span>
                        <span class="ms-1">{{ container.purchase_date }}</span>
                    </div>
                    <div v-if="container?.invoice_ref">
                        <span class="text-gray-500">{{ $t('common.invoice_ref') }}:</span>
                        <span class="ms-1">{{ container.invoice_ref }}</span>
                    </div>
                    <div v-if="container?.total_weight_kg != null && container?.total_weight_kg !== ''">
                        <span class="text-gray-500">{{ $t('common.weight_kg') }}:</span>
                        <span class="ms-1">{{ container.total_weight_kg }}</span>
                    </div>
                </dl>
                <div v-if="container?.description" class="mt-3 border-t border-gray-100 pt-3 text-sm">
                    <span class="text-gray-500">{{ $t('common.details') }}:</span>
                    <p class="mt-1 whitespace-pre-wrap text-gray-800">{{ container.description }}</p>
                </div>
            </Card>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Link
                    :href="route('containers.purchases', container.id)"
                    class="block rounded-2xl border-2 border-gray-200 bg-white p-6 text-start shadow-sm transition hover:border-primary-navy hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-navy/40"
                >
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="text-lg font-bold text-gray-900">{{ $t('pages.containers.card_purchases') }}</h3>
                        <span class="shrink-0 text-primary-navy">→</span>
                    </div>
                    <dl class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-100">
                            <dt class="text-xs font-medium text-gray-500">{{ $t('common.total') }}</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ purchasesCard?.total }}</dd>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-100">
                            <dt class="text-xs font-medium text-gray-500">{{ $t('common.paid') }}</dt>
                            <dd class="mt-1 text-lg font-semibold text-green-700">{{ purchasesCard?.paid }}</dd>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-100">
                            <dt class="text-xs font-medium text-gray-500">{{ $t('common.remaining') }}</dt>
                            <dd class="mt-1 text-lg font-semibold text-amber-700">{{ purchasesCard?.remaining }}</dd>
                        </div>
                    </dl>
                    <p class="mt-3 text-xs text-gray-500">{{ $t('pages.containers.card_purchases_hint') }}</p>
                </Link>

                <Link
                    :href="route('containers.sales', container.id)"
                    class="block rounded-2xl border-2 border-gray-200 bg-white p-6 text-start shadow-sm transition hover:border-emerald-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-600/30"
                >
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="text-lg font-bold text-gray-900">{{ $t('pages.containers.card_sales') }}</h3>
                        <span class="shrink-0 text-emerald-700">→</span>
                    </div>
                    <dl class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-100">
                            <dt class="text-xs font-medium text-gray-500">{{ $t('common.total') }}</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ salesCard?.total }}</dd>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-100">
                            <dt class="text-xs font-medium text-gray-500">{{ $t('common.paid') }}</dt>
                            <dd class="mt-1 text-lg font-semibold text-green-700">{{ salesCard?.paid }}</dd>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-100">
                            <dt class="text-xs font-medium text-gray-500">{{ $t('common.remaining') }}</dt>
                            <dd class="mt-1 text-lg font-semibold text-amber-700">{{ salesCard?.remaining }}</dd>
                        </div>
                    </dl>
                    <p class="mt-3 text-xs text-gray-500">{{ $t('pages.containers.card_sales_hint') }}</p>
                </Link>

                <Link
                    :href="route('containers.stock', container.id)"
                    class="block rounded-2xl border-2 border-gray-200 bg-white p-6 text-start shadow-sm transition hover:border-sky-600 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500/35 md:max-lg:col-span-2"
                >
                    <div class="flex items-start justify-between gap-2">
                        <h3 class="text-lg font-bold text-gray-900">{{ $t('pages.containers.card_stock') }}</h3>
                        <span class="shrink-0 text-sky-700">→</span>
                    </div>
                    <dl class="mt-4 grid grid-cols-2 gap-3">
                        <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-100">
                            <dt class="text-xs font-medium text-gray-500">{{ $t('pages.containers.stock_skus_with_qty') }}</dt>
                            <dd class="mt-1 text-lg font-semibold text-gray-900">{{ stockCard?.skus_with_qty }}</dd>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3 ring-1 ring-gray-100">
                            <dt class="text-xs font-medium text-gray-500">{{ $t('pages.containers.stock_total_available') }}</dt>
                            <dd class="mt-1 text-lg font-semibold text-sky-800">{{ stockCard?.total_available_qty }}</dd>
                        </div>
                    </dl>
                    <p class="mt-3 text-xs text-gray-500">{{ $t('pages.containers.card_stock_hint') }}</p>
                </Link>
            </div>
        </div>
    </DashboardLayout>
</template>
