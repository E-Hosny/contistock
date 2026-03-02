<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({ supplier: Object });
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
                <template #title>{{ $t('nav.containers') }} ({{ supplier?.containers?.length ?? 0 }})</template>
                <ul class="space-y-1">
                    <li v-for="c in supplier?.containers" :key="c.id">
                        <Link :href="route('containers.show', c.id)" class="text-primary-navy hover:underline">{{ c.product_name }}</Link>
                        — {{ $t('common.total_cost') }}: {{ c.total_cost }} — {{ $t('common.paid') }}: {{ c.supplier_payments_sum_amount ?? 0 }}
                    </li>
                    <li v-if="!supplier?.containers?.length" class="text-gray-500">{{ $t('pages.containers.no_containers') }}</li>
                </ul>
            </Card>
        </div>
    </DashboardLayout>
</template>
