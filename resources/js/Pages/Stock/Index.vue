<script setup>
import { ref, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';

const props = defineProps({ stock: Array, containers: Array, filterContainerId: [Number, String] });

const selectedContainerId = ref(
    props.filterContainerId != null && props.filterContainerId !== '' ? String(props.filterContainerId) : '',
);

watch(
    () => props.filterContainerId,
    (v) => {
        selectedContainerId.value = v != null && v !== '' ? String(v) : '';
    },
);

function applyContainerFilter() {
    const id = selectedContainerId.value;
    router.get(
        route('stock.index'),
        id ? { container_id: id } : {},
        { preserveState: true, preserveScroll: true, replace: true },
    );
}
</script>

<template>
    <Head title="Stock" />
    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">{{ $t('nav.stock') }}</h2>
        </template>
        <div class="space-y-4">
            <div class="flex flex-wrap items-center gap-2">
                <label for="stock_container_filter" class="text-sm text-gray-600">{{ $t('common.container') }}</label>
                <select
                    id="stock_container_filter"
                    v-model="selectedContainerId"
                    class="rounded border-gray-300 shadow-sm"
                    @change="applyContainerFilter"
                >
                    <option value="">{{ $t('common.all_containers') }}</option>
                    <option v-for="c in containers" :key="c.id" :value="String(c.id)">{{ c.product_name }}</option>
                </select>
            </div>
            <Card>
                <table class="data-table min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.product') }}</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('pages.products.sku') }}</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.available_qty') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="s in stock" :key="s.product?.id">
                            <td class="px-4 py-2" :data-label="$t('common.product')">{{ s.product?.name }}</td>
                            <td class="px-4 py-2 text-sm" :data-label="$t('pages.products.sku')">{{ s.product?.sku }}</td>
                            <td class="px-4 py-2 font-medium" :data-label="$t('common.available_qty')">{{ s.available_qty }}</td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="!stock?.length" class="p-4 text-center text-gray-500">{{ $t('pages.stock.no_stock') }}</div>
            </Card>
        </div>
    </DashboardLayout>
</template>
