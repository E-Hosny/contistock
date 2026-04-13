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
    salesCard: Object,
    containerSales: Array,
    customerPayments: Array,
    payableSales: Array,
});

const pageTitle = computed(() =>
    props.container?.product_name
        ? `${props.container.product_name} — ${t('pages.containers.card_sales')}`
        : t('pages.containers.card_sales'),
);

const showSalesAllocationNote = computed(() =>
    (props.containerSales || []).some((s) => Number(s.lines_subtotal) < Number(s.sale_total) - 0.005),
);

const newSaleUrl = computed(() => `${route('sales.create')}?container_id=${props.container.id}`);

const showAddPaymentForm = ref(false);

const paymentForm = useForm({
    sale_id: '',
    amount: '',
    payment_date: new Date().toISOString().slice(0, 10),
    method: 'cash',
});

function defaultSaleId() {
    const first = props.payableSales?.[0];
    return first ? String(first.id) : '';
}

function resetPaymentForm() {
    paymentForm.reset();
    paymentForm.clearErrors();
    paymentForm.sale_id = defaultSaleId();
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

function goToSaleShow(saleId) {
    router.visit(route('sales.show', saleId));
}
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
                        <h2 class="text-xl font-semibold text-gray-800">{{ $t('pages.containers.card_sales') }} — {{ container?.product_name }}</h2>
                        <p class="mt-1 text-sm text-gray-500">{{ $t('pages.containers.card_sales_hint') }}</p>
                    </div>
                </div>
                <Link :href="newSaleUrl">
                    <PrimaryButton type="button">{{ $t('common.new_sale') }}</PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="space-y-6">
            <Card class="border-emerald-600/20 bg-gradient-to-br from-emerald-50/80 to-white">
                <template #title>{{ $t('common.financial_summary') }}</template>
                <dl class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div class="rounded-lg bg-white p-3 shadow-sm">
                        <dt class="text-xs font-medium text-gray-500">{{ $t('common.total') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ salesCard?.total }}</dd>
                    </div>
                    <div class="rounded-lg bg-white p-3 shadow-sm">
                        <dt class="text-xs font-medium text-gray-500">{{ $t('common.paid') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-green-700">{{ salesCard?.paid }}</dd>
                    </div>
                    <div class="rounded-lg bg-white p-3 shadow-sm">
                        <dt class="text-xs font-medium text-gray-500">{{ $t('common.remaining') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-amber-700">{{ salesCard?.remaining }}</dd>
                    </div>
                </dl>
            </Card>

            <p class="text-sm text-gray-600">{{ $t('pages.containers.sales_detail_intro') }}</p>
            <p v-if="showSalesAllocationNote" class="text-xs text-gray-500">{{ $t('common.sales_allocation_note') }}</p>

            <Card>
                <table v-if="containerSales?.length" class="data-table min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.date') }}</th>
                            <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.customer') }}</th>
                            <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('pages.containers.lines_for_container') }}</th>
                            <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.paid') }}</th>
                            <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.remaining') }}</th>
                            <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.status') }}</th>
                            <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr
                            v-for="s in containerSales"
                            :key="s.id"
                            class="cursor-pointer transition-colors hover:bg-gray-50/90 md:hover:bg-transparent"
                            @click="goToSaleShow(s.id)"
                        >
                            <td class="px-3 py-2" :data-label="$t('common.date')">{{ s.sale_date }}</td>
                            <td class="px-3 py-2" :data-label="$t('common.customer')">{{ s.customer_name }}</td>
                            <td class="px-3 py-2 font-medium" :data-label="$t('pages.containers.lines_for_container')">{{ s.lines_subtotal }}</td>
                            <td class="px-3 py-2 text-green-700" :data-label="$t('common.paid')">{{ s.paid_portion }}</td>
                            <td class="px-3 py-2 text-amber-700" :data-label="$t('common.remaining')">{{ s.remaining_portion }}</td>
                            <td class="px-3 py-2" :data-label="$t('common.status')">
                                <Badge>{{ $t('statusOptions.' + (s.status || 'draft')) }}</Badge>
                            </td>
                            <td class="px-3 py-2" :data-label="$t('common.actions')" @click.stop>
                                <div class="flex flex-wrap gap-2">
                                    <Link :href="route('sales.show', s.id)" class="text-sm text-primary-navy hover:underline">{{ $t('common.view') }}</Link>
                                    <Link v-if="s.status === 'draft'" :href="route('sales.edit', s.id)" class="text-sm text-gray-700 hover:underline">{{ $t('common.edit') }}</Link>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-else class="p-8 text-center text-gray-500">
                    <p>{{ $t('pages.sales.no_sales') }}</p>
                    <Link :href="newSaleUrl" class="mt-4 inline-block text-primary-navy hover:underline">{{ $t('common.new_sale') }}</Link>
                </div>
            </Card>

            <Card>
                <div class="-mx-4 -mt-4 mb-4 flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 bg-gray-50 px-4 py-3">
                    <h3 class="text-sm font-semibold text-gray-800">{{ $t('common.payment_history') }}</h3>
                    <SecondaryButton v-if="payableSales?.length" type="button" @click="toggleAddPaymentForm">
                        {{ showAddPaymentForm ? $t('common.cancel') : $t('common.add_payment') }}
                    </SecondaryButton>
                </div>

                <form
                    v-if="showAddPaymentForm && payableSales?.length"
                    class="mb-4 space-y-3 rounded-lg border border-gray-200 bg-gray-50/80 p-4"
                    @submit.prevent="submitPayment"
                >
                    <div>
                        <InputLabel for="pay_sale_id" :value="$t('nav.sales')" />
                        <select
                            id="pay_sale_id"
                            v-model="paymentForm.sale_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            required
                        >
                            <option v-for="ps in payableSales" :key="ps.id" :value="String(ps.id)">
                                #{{ ps.id }} — {{ ps.customer_name }} — {{ ps.sale_date }} ({{ $t('common.remaining') }}: {{ ps.remaining_amount }})
                            </option>
                        </select>
                        <InputError class="mt-1" :message="paymentForm.errors.sale_id" />
                    </div>
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

                <div v-if="customerPayments?.length" class="overflow-x-auto">
                    <table class="data-table min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.date') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.customer') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('nav.sales') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.amount') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.remaining_after_payment') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.method') }}</th>
                                <th class="px-3 py-2 text-start text-xs font-medium text-gray-500">{{ $t('common.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="p in customerPayments" :key="p.id">
                                <td class="px-3 py-2" :data-label="$t('common.date')">{{ p.payment_date }}</td>
                                <td class="px-3 py-2" :data-label="$t('common.customer')">{{ p.customer_name }}</td>
                                <td class="px-3 py-2" :data-label="$t('nav.sales')">
                                    <Link :href="route('sales.show', p.sale_id)" class="text-primary-navy hover:underline"> #{{ p.sale_id }} — {{ p.sale_date }} </Link>
                                </td>
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

            <div class="flex justify-center pb-4">
                <Link :href="route('containers.show', container.id)">
                    <SecondaryButton type="button">{{ $t('common.back_to_container') }}</SecondaryButton>
                </Link>
            </div>
        </div>
    </DashboardLayout>
</template>
