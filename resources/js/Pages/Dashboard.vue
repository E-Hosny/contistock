<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Badge from '@/Components/Badge.vue';

defineProps({
    containers: { type: Array, default: () => [] },
});
</script>

<template>
    <Head :title="$t('nav.dashboard')" />

    <DashboardLayout>
        <template #header>
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    {{ $t('nav.containers') }}
                </h2>
                <Link :href="route('containers.create')">
                    <PrimaryButton type="button">{{ $t('pages.containers.new') }}</PrimaryButton>
                </Link>
            </div>
        </template>

        <div v-if="containers?.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <Link
                v-for="c in containers"
                :key="c.id"
                :href="route('containers.show', c.id)"
                class="block rounded-2xl border-2 border-gray-200 bg-white p-5 text-start shadow-sm transition hover:border-primary-navy hover:shadow-md focus:outline-none focus:ring-2 focus:ring-primary-navy/40"
            >
                <div class="flex items-start justify-between gap-2">
                    <h3 class="text-lg font-bold text-gray-900">{{ c.product_name }}</h3>
                    <span class="shrink-0 text-primary-navy">→</span>
                </div>
                <p v-if="c.supplier?.name" class="mt-2 text-sm text-gray-600">
                    <span class="text-gray-500">{{ $t('common.supplier') }}:</span>
                    <span class="ms-1 font-medium text-gray-800">{{ c.supplier.name }}</span>
                </p>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <Badge>{{ $t('statusOptions.' + (c.status || 'draft')) }}</Badge>
                </div>
                <dl class="mt-4 grid grid-cols-2 gap-3 text-sm">
                    <div class="rounded-lg bg-gray-50 p-2 ring-1 ring-gray-100">
                        <dt class="text-xs font-medium text-gray-500">{{ $t('common.total_cost') }}</dt>
                        <dd class="mt-0.5 font-semibold text-gray-900">{{ c.total_cost }}</dd>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-2 ring-1 ring-gray-100">
                        <dt class="text-xs font-medium text-gray-500">{{ $t('common.paid') }}</dt>
                        <dd class="mt-0.5 font-semibold text-green-700">{{ c.paid_amount }}</dd>
                    </div>
                </dl>
            </Link>
        </div>

        <div
            v-else
            class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50/60 px-6 py-16 text-center"
        >
            <p class="text-gray-600">{{ $t('pages.containers.no_containers') }}</p>
            <Link :href="route('containers.create')" class="mt-6">
                <PrimaryButton type="button">{{ $t('pages.containers.new') }}</PrimaryButton>
            </Link>
        </div>
    </DashboardLayout>
</template>
