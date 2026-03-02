<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({ product: Object });
const form = useForm({
    name: props.product?.name ?? '',
    sku: props.product?.sku ?? '',
    unit: props.product?.unit ?? 'piece',
    description: props.product?.description ?? '',
});
</script>

<template>
    <Head title="Edit Product" />
    <DashboardLayout>
        <template #header><h2 class="text-xl font-semibold text-gray-800">{{ $t('pages.products.edit') }}</h2></template>
        <form @submit.prevent="form.put(route('products.update', product.id))" class="max-w-xl space-y-4">
            <div>
                <InputLabel for="name" :value="$t('auth.name')" />
                <TextInput id="name" v-model="form.name" class="mt-1 block w-full" required />
                <InputError :message="form.errors.name" />
            </div>
            <div>
                <InputLabel for="sku" :value="$t('pages.products.sku')" />
                <TextInput id="sku" v-model="form.sku" class="mt-1 block w-full" required />
                <InputError :message="form.errors.sku" />
            </div>
            <div class="flex gap-2">
                <PrimaryButton type="submit" :disabled="form.processing">{{ $t('common.update') }}</PrimaryButton>
                <Link :href="route('products.index')"><SecondaryButton type="button">{{ $t('common.cancel') }}</SecondaryButton></Link>
            </div>
        </form>
    </DashboardLayout>
</template>
