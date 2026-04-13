<script setup>
import { computed, ref } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Badge from '@/Components/Badge.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const { t } = useI18n();

const props = defineProps({
    container: Object,
    pendingReceiptItems: Array,
    receivedReceiptItems: Array,
    products: Array,
    purchaseSubtotal: [Number, String],
    expensesSubtotal: [Number, String],
    canEditPurchasePlan: Boolean,
    canReceiveToWarehouse: Boolean,
    purchasesCard: Object,
});

const usedProductIds = computed(() => new Set((props.pendingReceiptItems || []).map((i) => i.product_id)));
const productsForLine = computed(() =>
    (props.products || []).filter((p) => !usedProductIds.value.has(p.id)),
);

const lineForm = useForm({
    product_id: '',
    qty_received: 1,
    buy_price: '',
    sale_price: '',
});

const showAddLineForm = ref(false);
const showAddExpenseForm = ref(false);

function closeAddLineForm() {
    showAddLineForm.value = false;
    lineForm.reset();
    lineForm.clearErrors();
    lineForm.qty_received = 1;
}

function toggleAddLineForm() {
    if (showAddLineForm.value) {
        closeAddLineForm();
    } else {
        showAddLineForm.value = true;
    }
}

function submitLine() {
    lineForm.post(route('containers.receipt-items.store', props.container.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeAddLineForm();
        },
    });
}

const expenseForm = useForm({
    description: '',
    amount: '',
    notes: '',
});

function closeAddExpenseForm() {
    showAddExpenseForm.value = false;
    expenseForm.reset();
    expenseForm.clearErrors();
}

function toggleAddExpenseForm() {
    if (showAddExpenseForm.value) {
        closeAddExpenseForm();
    } else {
        showAddExpenseForm.value = true;
    }
}

function submitExpense() {
    expenseForm.post(route('containers.expenses.store', props.container.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeAddExpenseForm();
        },
    });
}

const editingLineId = ref(null);
const editLineForm = useForm({
    qty_received: '',
    buy_price: '',
    sale_price: '',
});

function startEditLine(item) {
    editingLineId.value = item.id;
    editLineForm.qty_received = item.qty_received;
    editLineForm.buy_price = item.buy_price;
    editLineForm.sale_price = item.sale_price;
    editLineForm.clearErrors();
}

function cancelEditLine() {
    editingLineId.value = null;
    editLineForm.reset();
}

function saveEditLine(itemId) {
    editLineForm.put(route('containers.receipt-items.update', [props.container.id, itemId]), {
        preserveScroll: true,
        onSuccess: cancelEditLine,
    });
}

function deleteLine(item) {
    if (!confirm(t('common.delete') + '?')) return;
    router.delete(route('containers.receipt-items.destroy', [props.container.id, item.id]), { preserveScroll: true });
}

function lineTotal(item) {
    const q = Number(item.qty_received);
    const b = Number(item.buy_price);
    if (Number.isNaN(q) || Number.isNaN(b)) return '—';
    return (q * b).toFixed(2);
}

const editingExpenseId = ref(null);
const editExpenseForm = useForm({
    description: '',
    amount: '',
    notes: '',
});

function startEditExpense(exp) {
    editingExpenseId.value = exp.id;
    editExpenseForm.description = exp.description;
    editExpenseForm.amount = exp.amount;
    editExpenseForm.notes = exp.notes || '';
    editExpenseForm.clearErrors();
}

function cancelEditExpense() {
    editingExpenseId.value = null;
    editExpenseForm.reset();
}

function saveEditExpense(expId) {
    editExpenseForm.put(route('containers.expenses.update', [props.container.id, expId]), {
        preserveScroll: true,
        onSuccess: cancelEditExpense,
    });
}

function deleteExpense(exp) {
    if (!confirm(t('common.delete') + '?')) return;
    router.delete(route('containers.expenses.destroy', [props.container.id, exp.id]), { preserveScroll: true });
}

const receiveUrl = computed(
    () => `${route('warehouse-receipts.create')}?container_id=${props.container.id}`,
);

const pageTitle = computed(() =>
    props.container?.product_name
        ? `${props.container.product_name} — ${t('pages.containers.card_purchases')}`
        : t('pages.containers.card_purchases'),
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
                        <h2 class="text-xl font-semibold text-gray-800">{{ $t('pages.containers.card_purchases') }} — {{ container?.product_name }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ $t('pages.containers.card_purchases_hint') }}</p>
                    </div>
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
            <Card class="border-primary-navy/20 bg-gradient-to-br from-primary-navy/5 to-white">
                <template #title>{{ $t('common.financial_summary') }}</template>
                <dl class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div class="rounded-lg bg-white p-3 shadow-sm">
                        <dt class="text-xs font-medium text-gray-500">{{ $t('common.total') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ purchasesCard?.total }}</dd>
                    </div>
                    <div class="rounded-lg bg-white p-3 shadow-sm">
                        <dt class="text-xs font-medium text-gray-500">{{ $t('common.paid') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-green-700">{{ purchasesCard?.paid }}</dd>
                    </div>
                    <div class="rounded-lg bg-white p-3 shadow-sm">
                        <dt class="text-xs font-medium text-gray-500">{{ $t('common.remaining') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-amber-700">{{ purchasesCard?.remaining }}</dd>
                    </div>
                </dl>
            </Card>

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

            <div class="grid gap-6 lg:grid-cols-2">
                <Card>
                    <div class="-mx-4 -mt-4 mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <h3 class="text-sm font-semibold text-gray-800">{{ $t('common.purchase_lines') }}</h3>
                        <SecondaryButton
                            v-if="canEditPurchasePlan && productsForLine.length"
                            type="button"
                            @click="toggleAddLineForm"
                        >
                            {{ showAddLineForm ? $t('common.cancel') : $t('common.add_purchase_line') }}
                        </SecondaryButton>
                    </div>

                    <form
                        v-if="showAddLineForm && canEditPurchasePlan && productsForLine.length"
                        class="mb-4 space-y-3 rounded-lg border border-gray-200 bg-gray-50/80 p-4"
                        @submit.prevent="submitLine"
                    >
                        <div>
                            <InputLabel for="product_id" :value="$t('common.product')" />
                            <select
                                id="product_id"
                                v-model="lineForm.product_id"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                required
                            >
                                <option value="">{{ $t('common.select') }}</option>
                                <option v-for="p in productsForLine" :key="p.id" :value="p.id">{{ p.name }} ({{ p.sku }})</option>
                            </select>
                            <InputError class="mt-1" :message="lineForm.errors.product_id" />
                        </div>
                        <div class="grid gap-3 sm:grid-cols-3">
                            <div>
                                <InputLabel for="qty_received" :value="$t('common.qty')" />
                                <TextInput id="qty_received" v-model="lineForm.qty_received" type="number" step="0.01" min="0.01" class="mt-1 block w-full" required />
                                <InputError class="mt-1" :message="lineForm.errors.qty_received" />
                            </div>
                            <div>
                                <InputLabel for="buy_price" :value="$t('common.buy_price')" />
                                <TextInput id="buy_price" v-model="lineForm.buy_price" type="number" step="0.01" min="0" class="mt-1 block w-full" required />
                                <InputError class="mt-1" :message="lineForm.errors.buy_price" />
                            </div>
                            <div>
                                <InputLabel for="sale_price" :value="$t('common.sale_price')" />
                                <TextInput id="sale_price" v-model="lineForm.sale_price" type="number" step="0.01" min="0" class="mt-1 block w-full" required />
                                <InputError class="mt-1" :message="lineForm.errors.sale_price" />
                            </div>
                        </div>
                        <PrimaryButton type="submit" :disabled="lineForm.processing">{{ $t('common.add') }}</PrimaryButton>
                    </form>

                    <div v-if="pendingReceiptItems?.length" class="overflow-x-auto">
                        <table class="data-table min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.product') }}</th>
                                    <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.qty') }}</th>
                                    <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.buy_price') }}</th>
                                    <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.sale_price') }}</th>
                                    <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.line_total') }}</th>
                                    <th v-if="canEditPurchasePlan" class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="item in pendingReceiptItems" :key="item.id">
                                    <template v-if="editingLineId === item.id">
                                        <td class="px-3 py-2" :data-label="$t('common.product')">{{ item.product?.name }}</td>
                                        <td class="px-3 py-2" :data-label="$t('common.qty')">
                                            <TextInput v-model="editLineForm.qty_received" type="number" step="0.01" min="0.01" class="w-24" />
                                        </td>
                                        <td class="px-3 py-2" :data-label="$t('common.buy_price')">
                                            <TextInput v-model="editLineForm.buy_price" type="number" step="0.01" min="0" class="w-24" />
                                        </td>
                                        <td class="px-3 py-2" :data-label="$t('common.sale_price')">
                                            <TextInput v-model="editLineForm.sale_price" type="number" step="0.01" min="0" class="w-24" />
                                        </td>
                                        <td class="px-3 py-2 text-gray-600">—</td>
                                        <td class="px-3 py-2" :data-label="$t('common.actions')">
                                            <div class="flex flex-wrap gap-1">
                                                <button type="button" class="text-sm text-primary-navy hover:underline" @click="saveEditLine(item.id)">
                                                    {{ $t('common.save_line') }}
                                                </button>
                                                <button type="button" class="text-sm text-gray-600 hover:underline" @click="cancelEditLine">{{ $t('common.cancel') }}</button>
                                            </div>
                                            <InputError class="mt-1" :message="editLineForm.errors.qty_received" />
                                            <InputError class="mt-1" :message="editLineForm.errors.buy_price" />
                                            <InputError class="mt-1" :message="editLineForm.errors.sale_price" />
                                        </td>
                                    </template>
                                    <template v-else>
                                        <td class="px-3 py-2" :data-label="$t('common.product')">{{ item.product?.name }} ({{ item.product?.sku }})</td>
                                        <td class="px-3 py-2" :data-label="$t('common.qty')">{{ item.qty_received }}</td>
                                        <td class="px-3 py-2" :data-label="$t('common.buy_price')">{{ item.buy_price }}</td>
                                        <td class="px-3 py-2" :data-label="$t('common.sale_price')">{{ item.sale_price }}</td>
                                        <td class="px-3 py-2 font-medium" :data-label="$t('common.line_total')">{{ lineTotal(item) }}</td>
                                        <td v-if="canEditPurchasePlan" class="px-3 py-2" :data-label="$t('common.actions')">
                                            <button type="button" class="me-2 text-sm text-primary-navy hover:underline" @click="startEditLine(item)">{{ $t('common.edit') }}</button>
                                            <button type="button" class="text-sm text-red-600 hover:underline" @click="deleteLine(item)">{{ $t('common.remove') }}</button>
                                        </td>
                                    </template>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else class="text-sm text-gray-500">{{ $t('common.no_purchase_lines') }}</p>

                    <p v-if="canEditPurchasePlan && !productsForLine.length" class="mt-4 text-sm text-gray-500">
                        {{ $t('common.no_products') }}
                    </p>
                    <p v-else-if="!canEditPurchasePlan" class="mt-4 text-sm text-gray-500">{{ $t('statusOptions.received_to_warehouse') }}</p>
                </Card>

                <Card>
                    <div class="-mx-4 -mt-4 mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 bg-gray-50 px-4 py-3">
                        <h3 class="text-sm font-semibold text-gray-800">{{ $t('common.extra_expenses') }}</h3>
                        <SecondaryButton v-if="canEditPurchasePlan" type="button" @click="toggleAddExpenseForm">
                            {{ showAddExpenseForm ? $t('common.cancel') : $t('common.add_expense') }}
                        </SecondaryButton>
                    </div>

                    <form
                        v-if="showAddExpenseForm && canEditPurchasePlan"
                        class="mb-4 space-y-3 rounded-lg border border-gray-200 bg-gray-50/80 p-4"
                        @submit.prevent="submitExpense"
                    >
                        <div>
                            <InputLabel for="exp_desc" :value="$t('common.description')" />
                            <TextInput id="exp_desc" v-model="expenseForm.description" class="mt-1 block w-full" required />
                            <InputError class="mt-1" :message="expenseForm.errors.description" />
                        </div>
                        <div>
                            <InputLabel for="exp_amount" :value="$t('common.amount')" />
                            <TextInput id="exp_amount" v-model="expenseForm.amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" required />
                            <InputError class="mt-1" :message="expenseForm.errors.amount" />
                        </div>
                        <div>
                            <InputLabel for="exp_notes" :value="$t('common.notes')" />
                            <textarea id="exp_notes" v-model="expenseForm.notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" />
                            <InputError class="mt-1" :message="expenseForm.errors.notes" />
                        </div>
                        <PrimaryButton type="submit" :disabled="expenseForm.processing">{{ $t('common.add') }}</PrimaryButton>
                    </form>

                    <div v-if="container?.expenses?.length" class="overflow-x-auto">
                        <table class="data-table min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.description') }}</th>
                                    <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.amount') }}</th>
                                    <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.notes') }}</th>
                                    <th v-if="canEditPurchasePlan" class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="exp in container.expenses" :key="exp.id">
                                    <template v-if="editingExpenseId === exp.id">
                                        <td class="px-3 py-2" :data-label="$t('common.description')">
                                            <TextInput v-model="editExpenseForm.description" class="w-full max-w-xs" />
                                        </td>
                                        <td class="px-3 py-2" :data-label="$t('common.amount')">
                                            <TextInput v-model="editExpenseForm.amount" type="number" step="0.01" min="0.01" class="w-28" />
                                        </td>
                                        <td class="px-3 py-2" :data-label="$t('common.notes')">
                                            <textarea v-model="editExpenseForm.notes" rows="2" class="w-full max-w-xs rounded-md border-gray-300 text-sm" />
                                        </td>
                                        <td class="px-3 py-2" :data-label="$t('common.actions')">
                                            <button type="button" class="me-2 text-sm text-primary-navy hover:underline" @click="saveEditExpense(exp.id)">{{ $t('common.save') }}</button>
                                            <button type="button" class="text-sm text-gray-600 hover:underline" @click="cancelEditExpense">{{ $t('common.cancel') }}</button>
                                            <InputError class="mt-1" :message="editExpenseForm.errors.description" />
                                            <InputError class="mt-1" :message="editExpenseForm.errors.amount" />
                                        </td>
                                    </template>
                                    <template v-else>
                                        <td class="px-3 py-2 font-medium" :data-label="$t('common.description')">{{ exp.description }}</td>
                                        <td class="px-3 py-2" :data-label="$t('common.amount')">{{ exp.amount }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600" :data-label="$t('common.notes')">{{ exp.notes || '—' }}</td>
                                        <td v-if="canEditPurchasePlan" class="px-3 py-2" :data-label="$t('common.actions')">
                                            <button type="button" class="me-2 text-sm text-primary-navy hover:underline" @click="startEditExpense(exp)">{{ $t('common.edit') }}</button>
                                            <button type="button" class="text-sm text-red-600 hover:underline" @click="deleteExpense(exp)">{{ $t('common.remove') }}</button>
                                        </td>
                                    </template>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <p v-else class="text-sm text-gray-500">{{ $t('common.none') }}</p>

                    <p v-if="!canEditPurchasePlan" class="mt-4 text-sm text-gray-500">{{ $t('statusOptions.received_to_warehouse') }}</p>
                </Card>
            </div>

            <Card>
                <template #title>{{ $t('common.cost_breakdown') }}</template>
                <dl class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="rounded-lg bg-gray-50 p-4">
                        <dt class="text-sm font-medium text-gray-500">{{ $t('common.products_purchases_total') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ purchaseSubtotal }}</dd>
                    </div>
                    <div class="rounded-lg bg-gray-50 p-4">
                        <dt class="text-sm font-medium text-gray-500">{{ $t('common.expenses_total') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ expensesSubtotal }}</dd>
                    </div>
                    <div class="rounded-lg bg-primary-navy/5 p-4 sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-600">{{ $t('common.computed_total_cost') }}</dt>
                        <dd class="mt-1 text-2xl font-bold text-primary-navy">{{ container?.total_cost }}</dd>
                    </div>
                </dl>
            </Card>

            <Card v-if="receivedReceiptItems?.length">
                <template #title>{{ $t('common.received_lines') }}</template>
                <div class="overflow-x-auto">
                    <table class="data-table min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.receipt_date') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.product') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.qty') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.buy_price') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.sale_price') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="item in receivedReceiptItems" :key="'r-' + item.id">
                                <td class="px-3 py-2" :data-label="$t('common.receipt_date')">
                                    <Link
                                        v-if="item.warehouse_receipt_id"
                                        :href="route('warehouse-receipts.show', item.warehouse_receipt_id)"
                                        class="text-primary-navy hover:underline"
                                    >
                                        {{ item.warehouse_receipt?.receipt_date }}
                                    </Link>
                                </td>
                                <td class="px-3 py-2" :data-label="$t('common.product')">{{ item.product?.name }}</td>
                                <td class="px-3 py-2" :data-label="$t('common.qty')">{{ item.qty_received }}</td>
                                <td class="px-3 py-2" :data-label="$t('common.buy_price')">{{ item.buy_price }}</td>
                                <td class="px-3 py-2" :data-label="$t('common.sale_price')">{{ item.sale_price }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </Card>

            <Card>
                <template #title>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span>{{ $t('common.supplier_payments') }}</span>
                        <Link :href="route('containers.supplier-payments.index', container.id)" class="rounded bg-primary-navy px-3 py-1.5 text-sm text-white hover:bg-primary-navy/90">{{ $t('common.add_payment') }}</Link>
                    </div>
                </template>
                <table v-if="container?.supplier_payments?.length" class="data-table min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.date') }}</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.amount') }}</th>
                            <th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.method') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="p in container?.supplier_payments" :key="p.id">
                            <td class="px-4 py-2" :data-label="$t('common.date')">{{ p.payment_date }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.amount')">{{ p.amount }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.method')">{{ p.method }}</td>
                        </tr>
                    </tbody>
                </table>
                <div v-else class="rounded-lg border border-dashed border-gray-300 bg-gray-50/50 p-6 text-center text-gray-500">
                    <p class="mb-3">{{ $t('common.no_payments') }}</p>
                    <Link :href="route('containers.supplier-payments.index', container.id)" class="inline-flex rounded bg-primary-navy px-4 py-2 text-sm text-white hover:bg-primary-navy/90">{{ $t('common.add_payment') }}</Link>
                </div>
            </Card>

            <Card>
                <template #title>{{ $t('common.warehouse_receipts') }}</template>
                <ul class="space-y-1">
                    <li v-for="r in container?.warehouse_receipts" :key="r.id">
                        <Link :href="route('warehouse-receipts.show', r.id)" class="text-primary-navy hover:underline">{{ r.receipt_date }}</Link>
                    </li>
                    <li v-if="!container?.warehouse_receipts?.length" class="text-gray-500">{{ $t('common.none') }}</li>
                </ul>
            </Card>

            <div class="flex justify-center pb-4">
                <Link :href="route('containers.show', container.id)">
                    <SecondaryButton type="button">{{ $t('common.back_to_container') }}</SecondaryButton>
                </Link>
            </div>
        </div>
    </DashboardLayout>
</template>
