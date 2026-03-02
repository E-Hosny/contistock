<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({ containers: Object, filters: Object });
</script>

<template>
    <Head title="Containers" />
    <DashboardLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">{{ $t('nav.containers') }}</h2>
                <Link :href="route('containers.create')"><PrimaryButton>{{ $t('common.add') }}</PrimaryButton></Link>
            </div>
        </template>
        <Card>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ $t('common.product') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ $t('common.supplier') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ $t('common.total_cost') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ $t('common.paid') }}</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ $t('common.status') }}</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">{{ $t('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="c in containers.data" :key="c.id">
                        <td class="px-4 py-2">
                            <Link :href="route('containers.show', c.id)" class="text-primary-navy hover:underline">{{ c.product_name }}</Link>
                        </td>
                        <td class="px-4 py-2 text-sm">{{ c.supplier?.name }}</td>
                        <td class="px-4 py-2 text-sm">{{ c.total_cost }}</td>
                        <td class="px-4 py-2 text-sm">{{ c.paid_amount }}</td>
                        <td class="px-4 py-2"><Badge>{{ c.status }}</Badge></td>
                        <td class="px-4 py-2 text-right">
                            <Link :href="route('containers.edit', c.id)" class="text-sm text-blue-600 hover:underline">{{ $t('common.edit') }}</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div v-if="containers.data && !containers.data.length" class="p-4 text-center text-gray-500">{{ $t('pages.containers.no_containers') }}</div>
        </Card>
    </DashboardLayout>
</template>
