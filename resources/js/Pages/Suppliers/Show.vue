<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    supplier: Object,
    supplier_total_cost: Number,
    supplier_paid_amount: Number,
    supplier_remaining_amount: Number,
});

function allPayments() {
    const containers = props.supplier?.containers ?? [];
    return containers.flatMap((c) => (c.supplier_payments ?? []).map((p) => ({ ...p, container_name: c.product_name }))).sort((a, b) => (a.payment_date > b.payment_date ? -1 : 1));
}
</script>

<template>
    <Head :title="supplier?.name" />
    <DashboardLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">{{ supplier?.name }}</h2>
                <Link :href="route('suppliers.edit', supplier.id)"><PrimaryButton>{{ $t('common.edit') }}</PrimaryButton></Link>
            </div>
        </template>
        <div class="space-y-4">
            <Card>
                <dl class="grid gap-2 sm:grid-cols-2">
                    <div><dt class="text-sm text-gray-500">{{ $t('common.phone') }}</dt><dd>{{ supplier?.phone || '—' }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('auth.email') }}</dt><dd>{{ supplier?.email || '—' }}</dd></div>
                    <div class="sm:col-span-2"><dt class="text-sm text-gray-500">{{ $t('common.address') }}</dt><dd>{{ supplier?.address || '—' }}</dd></div>
                </dl>
            </Card>
            <Card>
                <template #title>{{ $t('common.financial_summary') }}</template>
                <dl class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-lg bg-gray-50 p-3">
                        <dt class="text-sm font-medium text-gray-500">{{ $t('common.total_cost') }}</dt>
                        <dd class="mt-1 text-xl font-semibold text-gray-900">{{ supplier_total_cost ?? 0 }}</dd>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-3">
                        <dt class="text-sm font-medium text-gray-500">{{ $t('common.paid') }}</dt>
                        <dd class="mt-1 text-xl font-semibold text-green-700">{{ supplier_paid_amount ?? 0 }}</dd>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-3">
                        <dt class="text-sm font-medium text-gray-500">{{ $t('common.remaining') }}</dt>
                        <dd class="mt-1 text-xl font-semibold text-amber-700">{{ supplier_remaining_amount ?? 0 }}</dd>
                    </div>
                </dl>
            </Card>
            <Card>
                <template #title>{{ $t('common.supplier_payments') }}</template>
                <table v-if="allPayments().length" class="data-table min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.date') }}</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.amount') }}</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.method') }}</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.container') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="p in allPayments()" :key="p.id">
                            <td class="px-4 py-2">{{ p.payment_date }}</td>
                            <td class="px-4 py-2">{{ p.amount }}</td>
                            <td class="px-4 py-2">{{ p.method }}</td>
                            <td class="px-4 py-2">
                                <Link :href="route('containers.show', p.container_id)" class="text-primary-navy hover:underline">{{ p.container_name }}</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p v-else class="text-gray-500">{{ $t('common.no_payments') }}</p>
            </Card>
            <Card>
                <template #title>{{ $t('nav.containers') }} ({{ supplier?.containers?.length ?? 0 }})</template>
                <ul class="space-y-1">
                    <li v-for="c in supplier?.containers" :key="c.id" class="flex flex-wrap items-center gap-2">
                        <Link :href="route('containers.show', c.id)" class="text-primary-navy hover:underline">{{ c.product_name }}</Link>
                        <span class="text-gray-500">— {{ $t('common.total_cost') }}: {{ c.total_cost }} — {{ $t('common.paid') }}: {{ c.supplier_payments_sum_amount ?? 0 }}</span>
                        <Link :href="route('containers.supplier-payments.index', c.id)" class="text-sm text-blue-600 hover:underline">{{ $t('common.add_payment') }}</Link>
                    </li>
                    <li v-if="!supplier?.containers?.length" class="text-gray-500">{{ $t('pages.containers.no_containers') }}</li>
                </ul>
            </Card>
        </div>
    </DashboardLayout>
</template>
