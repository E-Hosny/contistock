<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    suppliers: Object,
    filters: Object,
});
</script>

<template>
    <Head title="Suppliers" />
    <DashboardLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">{{ $t('nav.suppliers') }}</h2>
                <Link :href="route('suppliers.create')"><PrimaryButton>{{ $t('common.add') }}</PrimaryButton></Link>
            </div>
        </template>
        <div class="space-y-4">
            <Card>
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ $t('auth.name') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ $t('common.phone') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500">{{ $t('common.containers_count') }}</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">{{ $t('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="s in suppliers.data" :key="s.id">
                            <td class="px-4 py-2">
                                <Link :href="route('suppliers.show', s.id)" class="text-primary-navy hover:underline">{{ s.name }}</Link>
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-600">{{ s.phone }}</td>
                            <td class="px-4 py-2 text-sm">{{ s.containers_count }}</td>
                            <td class="px-4 py-2 text-right">
                                <Link :href="route('suppliers.edit', s.id)" class="text-sm text-blue-600 hover:underline">{{ $t('common.edit') }}</Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="suppliers.data && !suppliers.data.length" class="p-4 text-center text-gray-500">{{ $t('pages.suppliers.no_suppliers') }}</div>
            </Card>
        </div>
    </DashboardLayout>
</template>
