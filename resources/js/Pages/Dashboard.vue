<script setup>
import { Head } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
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

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Card>
                    <template #title>Placeholder 1</template>
                    <p class="text-sm text-gray-500">Content placeholder.</p>
                    <Badge variant="accent" class="mt-2">New</Badge>
                </Card>
                <Card>
                    <template #title>Placeholder 2</template>
                    <p class="text-sm text-gray-500">Content placeholder.</p>
                </Card>
                <Card>
                    <template #title>Placeholder 3</template>
                    <p class="text-sm text-gray-500">Content placeholder.</p>
                    <Badge variant="success" class="mt-2">Active</Badge>
                </Card>
            </div>
        </div>
    </DashboardLayout>
</template>
