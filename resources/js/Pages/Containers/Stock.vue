<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const { t } = useI18n();

const props = defineProps({
    container: Object,
    stock: Array,
});

const pageTitle = computed(() =>
    props.container?.product_name
        ? `${props.container.product_name} — ${t('nav.stock')}`
        : t('nav.stock'),
);
</script>

<template>
    <Head :title="pageTitle" />
    <DashboardLayout>
        <template #header>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex flex-col gap-2">
                    <Link :href="route('containers.show', container.id)" class="text-sm text-primary-navy hover:underline">
                        ← {{ $t('common.back_to_container') }}
                    </Link>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">{{ $t('nav.stock') }} — {{ container?.product_name }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ $t('pages.containers.container_stock_intro') }}</p>
                    </div>
                </div>
                <Link :href="route('stock.index')">
                    <SecondaryButton type="button">{{ $t('pages.containers.open_global_stock') }}</SecondaryButton>
                </Link>
            </div>
        </template>

        <div class="space-y-4">
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

            <div class="flex justify-center pb-4">
                <Link :href="route('containers.show', container.id)">
                    <SecondaryButton type="button">{{ $t('common.back_to_container') }}</SecondaryButton>
                </Link>
            </div>
        </div>
    </DashboardLayout>
</template>
