<script setup>
import { computed, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    eligibleContainers: Array,
    preselectedContainerId: [Number, String, null],
    plannedItems: Array,
});

const form = useForm({
    container_id: props.preselectedContainerId ? String(props.preselectedContainerId) : '',
    receipt_date: new Date().toISOString().slice(0, 10),
    notes: '',
});

watch(
    () => props.preselectedContainerId,
    (id) => {
        if (id) {
            form.container_id = String(id);
        }
    },
    { immediate: true },
);

function onContainerChange(event) {
    const id = event.target.value;
    if (!id) {
        return;
    }
    router.get(route('warehouse-receipts.create'), { container_id: id }, { preserveState: true, replace: true, only: ['plannedItems', 'preselectedContainerId', 'eligibleContainers'] });
}

const canSubmit = computed(() => !!form.container_id && (props.plannedItems?.length ?? 0) > 0);

function lineTotal(item) {
    const q = Number(item.qty_received);
    const b = Number(item.buy_price);
    if (Number.isNaN(q) || Number.isNaN(b)) return '—';
    return (q * b).toFixed(2);
}
</script>

<template>
    <Head :title="$t('pages.warehouse_receipts.new')" />
    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold text-gray-800">{{ $t('pages.warehouse_receipts.new') }}</h2>
        </template>

        <div class="mx-auto max-w-3xl space-y-6">
            <p class="text-sm text-gray-600">{{ $t('common.warehouse_receipt_intro') }}</p>

            <div v-if="!eligibleContainers?.length" class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                {{ $t('common.no_eligible_containers') }}
            </div>

            <form @submit.prevent="form.post(route('warehouse-receipts.store'))" class="space-y-6">
                <div class="rounded-lg bg-white p-4 shadow">
                    <div>
                        <InputLabel for="container_id" :value="$t('common.select_container')" />
                        <select
                            id="container_id"
                            v-model="form.container_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                            @change="onContainerChange"
                        >
                            <option value="">{{ $t('common.select') }}</option>
                            <option v-for="c in eligibleContainers" :key="c.id" :value="String(c.id)">{{ c.product_name }}</option>
                        </select>
                    </div>
                    <div class="mt-4">
                        <InputLabel for="receipt_date" :value="$t('common.receipt_date')" />
                        <TextInput id="receipt_date" v-model="form.receipt_date" type="date" class="mt-1 block w-full" required />
                    </div>
                    <div class="mt-4">
                        <InputLabel for="notes" :value="$t('common.notes')" />
                        <textarea id="notes" v-model="form.notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                    </div>
                </div>

                <div class="rounded-lg bg-white p-4 shadow">
                    <h3 class="font-semibold text-gray-900">{{ $t('common.planned_lines_title') }}</h3>
                    <p v-if="!form.container_id" class="mt-2 text-sm text-gray-500">{{ $t('common.select') }}</p>
                    <p v-else-if="!plannedItems?.length" class="mt-2 text-sm text-amber-800">{{ $t('common.no_purchase_lines') }}</p>
                    <div v-else class="mt-4 overflow-x-auto">
                        <table class="data-table min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.product') }}</th>
                                    <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.qty') }}</th>
                                    <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.buy_price') }}</th>
                                    <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.sale_price') }}</th>
                                    <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.line_total') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="item in plannedItems" :key="item.id">
                                    <td class="px-3 py-2">{{ item.product?.name }} ({{ item.product?.sku }})</td>
                                    <td class="px-3 py-2">{{ item.qty_received }}</td>
                                    <td class="px-3 py-2">{{ item.buy_price }}</td>
                                    <td class="px-3 py-2">{{ item.sale_price }}</td>
                                    <td class="px-3 py-2 font-medium">{{ lineTotal(item) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <PrimaryButton type="submit" :disabled="form.processing || !canSubmit">{{ $t('common.create_receipt') }}</PrimaryButton>
                    <Link :href="route('warehouse-receipts.index')"><SecondaryButton type="button">{{ $t('common.cancel') }}</SecondaryButton></Link>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>
