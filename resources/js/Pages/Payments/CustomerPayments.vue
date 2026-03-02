<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({ sale: Object });

const form = useForm({
    sale_id: props.sale?.id,
    amount: '',
    payment_date: new Date().toISOString().slice(0, 10),
    method: 'cash',
    reference: '',
    notes: '',
});
</script>

<template>
    <Head title="Customer payments" />
    <DashboardLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800">Payments — Sale #{{ sale?.id }}</h2>
                <Link :href="route('sales.show', sale?.id)">{{ $t('common.back_to_sale') }}</Link>
            </div>
        </template>
        <div class="space-y-4">
            <Card>
                <dl class="grid gap-2 sm:grid-cols-2">
                    <div><dt class="text-sm text-gray-500">{{ $t('common.total') }}</dt><dd>{{ sale?.total }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.paid') }}</dt><dd>{{ sale?.paid_amount }}</dd></div>
                    <div><dt class="text-sm text-gray-500">{{ $t('common.remaining') }}</dt><dd>{{ sale?.remaining_amount }}</dd></div>
                </dl>
            </Card>
            <Card>
                <template #title>{{ $t('common.add_payment') }}</template>
                <form @submit.prevent="form.post(route('customer-payments.store'))" class="max-w-md space-y-4">
                    <div>
                        <InputLabel for="amount" :value="$t('common.amount')" />
                        <TextInput id="amount" v-model="form.amount" type="number" step="0.01" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <InputLabel for="payment_date" :value="$t('common.date')" />
                        <TextInput id="payment_date" v-model="form.payment_date" type="date" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <InputLabel for="method" :value="$t('common.method')" />
                        <select id="method" v-model="form.method" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="cash">{{ $t('common.cash') }}</option>
                            <option value="bank">{{ $t('common.bank') }}</option>
                        </select>
                    </div>
                    <PrimaryButton type="submit" :disabled="form.processing">{{ $t('common.record_payment') }}</PrimaryButton>
                </form>
            </Card>
            <Card>
                <template #title>{{ $t('common.payment_history') }}</template>
                <table class="min-w-full">
                    <thead><tr><th class="text-left text-xs text-gray-500">{{ $t('common.date') }}</th><th class="text-left text-xs text-gray-500">{{ $t('common.amount') }}</th><th class="text-left text-xs text-gray-500">{{ $t('common.method') }}</th><th></th></tr></thead>
                    <tbody>
                        <tr v-for="p in sale?.customer_payments" :key="p.id" class="border-t">
                            <td class="py-1">{{ p.payment_date }}</td>
                            <td class="py-1">{{ p.amount }}</td>
                            <td class="py-1">{{ p.method }}</td>
                            <td class="py-1">
                                <form @submit.prevent="router.delete(route('customer-payments.destroy', p.id))" class="inline">
                                    <button type="submit" class="text-sm text-red-600">{{ $t('common.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </Card>
        </div>
    </DashboardLayout>
</template>
