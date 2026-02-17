<script setup>
import { useI18n } from 'vue-i18n';
import { router } from '@inertiajs/vue3';
import { setLocale } from '@/i18n';

const { locale } = useI18n();

function switchLocale(newLocale) {
    if (newLocale === locale.value) return;
    setLocale(newLocale);
    router.post(route('locale.update'), { locale: newLocale }, { preserveState: true });
}
</script>

<template>
    <div class="flex items-center gap-1 rounded-lg border border-gray-200 bg-gray-50 p-1">
        <button
            type="button"
            :class="[
                'rounded-md px-3 py-1.5 text-sm font-medium transition-colors',
                locale === 'en' ? 'bg-primary-navy text-white' : 'text-gray-600 hover:bg-gray-200'
            ]"
            @click="switchLocale('en')"
        >
            EN
        </button>
        <button
            type="button"
            :class="[
                'rounded-md px-3 py-1.5 text-sm font-medium transition-colors',
                locale === 'ar' ? 'bg-primary-navy text-white' : 'text-gray-600 hover:bg-gray-200'
            ]"
            @click="switchLocale('ar')"
        >
            عربي
        </button>
    </div>
</template>
