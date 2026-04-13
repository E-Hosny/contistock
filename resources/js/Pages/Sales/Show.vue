<script setup>
import { computed, ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Badge from '@/Components/Badge.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const { t } = useI18n();

const props = defineProps({ sale: Object });

const confirming = ref(false);
const cancelling = ref(false);

const showAddPaymentForm = ref(false);

const canRecordPayments = computed(() => ['draft', 'confirmed'].includes(props.sale?.status));

const paymentRows = computed(() => {
    const sale = props.sale;
    const list = sale?.customer_payments ? [...sale.customer_payments] : [];
    const total = Number(sale?.total ?? 0);
    const asc = [...list].sort((a, b) => {
        const d = String(a.payment_date).localeCompare(String(b.payment_date));
        if (d !== 0) {
            return d;
        }
        return Number(a.id) - Number(b.id);
    });
    let cumulative = 0;
    const remainingAfter = {};
    for (const p of asc) {
        cumulative += Number(p.amount);
        remainingAfter[p.id] = Math.round(Math.max(0, total - cumulative) * 100) / 100;
    }
    return [...list]
        .sort((a, b) => {
            const d = String(b.payment_date).localeCompare(String(a.payment_date));
            if (d !== 0) {
                return d;
            }
            return Number(b.id) - Number(a.id);
        })
        .map((p) => ({ ...p, remaining_after: remainingAfter[p.id] }));
});

const paymentForm = useForm({
    sale_id: props.sale?.id != null ? String(props.sale.id) : '',
    amount: '',
    payment_date: new Date().toISOString().slice(0, 10),
    method: 'cash',
});

function resetPaymentForm() {
    paymentForm.reset();
    paymentForm.clearErrors();
    paymentForm.sale_id = props.sale?.id != null ? String(props.sale.id) : '';
    paymentForm.payment_date = new Date().toISOString().slice(0, 10);
    paymentForm.method = 'cash';
}

function toggleAddPaymentForm() {
    if (showAddPaymentForm.value) {
        showAddPaymentForm.value = false;
        resetPaymentForm();
    } else {
        resetPaymentForm();
        showAddPaymentForm.value = true;
    }
}

watch(canRecordPayments, (ok) => {
    if (!ok) {
        showAddPaymentForm.value = false;
        resetPaymentForm();
    }
});

function submitPayment() {
    paymentForm.post(route('customer-payments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            showAddPaymentForm.value = false;
            resetPaymentForm();
        },
    });
}

function deletePayment(paymentId) {
    if (!confirm(t('common.delete') + '?')) return;
    router.delete(route('customer-payments.destroy', paymentId), { preserveScroll: true });
}

function confirmSale() {
    if (!props.sale?.id) return;
    confirming.value = true;
    router.post(route('sales.confirm', props.sale.id), {}, {
        preserveScroll: true,
        onFinish: () => { confirming.value = false; },
    });
}

function cancelSale() {
    if (!props.sale?.id) return;
    cancelling.value = true;
    router.post(route('sales.cancel', props.sale.id), {}, {
        preserveScroll: true,
        onFinish: () => { cancelling.value = false; },
    });
}
</script>

<template>
    <Head title="Sale" />
    <DashboardLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Sale #{{ sale?.id }}</h2>
                <div class="flex flex-wrap gap-2">
                    <Link v-if="sale?.status === 'draft'" :href="route('sales.edit', sale.id)"><PrimaryButton>{{ $t('common.edit') }}</PrimaryButton></Link>
                    <PrimaryButton v-if="sale?.status === 'draft'" type="button" :disabled="confirming" @click="confirmSale">{{ $t('common.confirm') }}</PrimaryButton>
                    <DangerButton v-if="sale?.status === 'draft' || sale?.status === 'confirmed'" type="button" :disabled="cancelling" @click="cancelSale">{{ $t('common.cancel_sale') }}</DangerButton>
                </div>
            </div>
        </template>
        <div class="space-y-4">
            <Card>
                <dl class="grid gap-2 sm:grid-cols-2">
                    <div><dt class="text-sm text-gray-500">{{ $t('common.customer') }}</dt><dd>{{ sale?.customer?.name }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.date') }}</dt><dd>{{ sale?.sale_date }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.status') }}</dt><dd><Badge>{{ $t('statusOptions.' + (sale?.status || 'draft')) }}</Badge></dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.subtotal') }}</dt><dd>{{ sale?.subtotal }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.discount') }}</dt><dd>{{ sale?.discount }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.total') }}</dt><dd>{{ sale?.total }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.paid') }}</dt><dd>{{ sale?.paid_amount }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.remaining') }}</dt><dd>{{ sale?.remaining_amount }}</dd></div>
                </dl>
            </Card>
            <Card>
                <template #title>{{ $t('common.items') }}</template>
                <table class="data-table min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.product') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.qty') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.unit_price') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.total') }}</th><th class="px-4 py-2 text-xs font-medium text-gray-500">{{ $t('common.sold_profit') }}</th></tr></thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr v-for="item in sale?.sale_items" :key="item.id">
                            <td class="px-4 py-2" :data-label="$t('common.product')">{{ item.product?.name }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.qty')">{{ item.qty }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.unit_price')">{{ item.unit_price }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.total')">{{ item.line_total }}</td>
                            <td class="px-4 py-2" :data-label="$t('common.sold_profit')">{{ item.profit_line }}</td>
                        </tr>
                    </tbody>
                </table>
            </Card>

            <Card>
                <div class="-mx-4 -mt-4 mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="text-sm font-semibold text-gray-800">{{ $t('common.payment_history') }}</h3>
                    <SecondaryButton v-if="canRecordPayments" type="button" @click="toggleAddPaymentForm">
                        {{ showAddPaymentForm ? $t('common.cancel') : $t('common.add_payment') }}
                    </SecondaryButton>
                </div>

                <form
                    v-if="showAddPaymentForm && canRecordPayments"
                    class="mb-4 space-y-3 rounded-lg border border-gray-200 bg-gray-50/80 p-4"
                    @submit.prevent="submitPayment"
                >
                    <div>
                        <InputLabel for="pay_amount" :value="$t('common.amount')" />
                        <TextInput id="pay_amount" v-model="paymentForm.amount" type="number" step="0.01" min="0.01" class="mt-1 block w-full" required />
                        <InputError class="mt-1" :message="paymentForm.errors.amount" />
                    </div>
                    <div>
                        <InputLabel for="pay_payment_date" :value="$t('common.date')" />
                        <TextInput id="pay_payment_date" v-model="paymentForm.payment_date" type="date" class="mt-1 block w-full" required />
                        <InputError class="mt-1" :message="paymentForm.errors.payment_date" />
                    </div>
                    <div>
                        <InputLabel for="pay_method" :value="$t('common.method')" />
                        <select id="pay_method" v-model="paymentForm.method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="cash">{{ $t('common.cash') }}</option>
                            <option value="bank">{{ $t('common.bank') }}</option>
                        </select>
                        <InputError class="mt-1" :message="paymentForm.errors.method" />
                    </div>
                    <PrimaryButton type="submit" :disabled="paymentForm.processing">{{ $t('common.record_payment') }}</PrimaryButton>
                </form>

                <div v-if="paymentRows.length" class="overflow-x-auto">
                    <table class="data-table min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.date') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.amount') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.remaining_after_payment') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.method') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="p in paymentRows" :key="p.id">
                                <td class="px-3 py-2" :data-label="$t('common.date')">{{ p.payment_date }}</td>
                                <td class="px-3 py-2 font-medium" :data-label="$t('common.amount')">{{ p.amount }}</td>
                                <td class="px-3 py-2 text-amber-800" :data-label="$t('common.remaining_after_payment')">{{ p.remaining_after }}</td>
                                <td class="px-3 py-2" :data-label="$t('common.method')">{{ p.method }}</td>
                                <td class="px-3 py-2" :data-label="$t('common.actions')" @click.stop>
                                    <button type="button" class="text-sm text-red-600 hover:underline" @click="deletePayment(p.id)">{{ $t('common.delete') }}</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="text-sm text-gray-500">{{ $t('common.no_payments') }}</p>
            </Card>
        </div>
    </DashboardLayout>
</template>
