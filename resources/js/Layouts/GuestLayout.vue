<script setup>
import { watch, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { toast } from 'vue3-toastify';
import { setLocale } from '@/i18n';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const page = usePage();
const locale = computed(() => page.props.locale || 'ar');
watch(locale, (val) => setLocale(val || 'ar'), { immediate: true });
watch(() => page.props.flash, (flash) => {
    if (flash?.success) toast.success(flash.success);
    if (flash?.error) toast.error(flash.error);
}, { deep: true });
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center bg-background-light pt-6 sm:justify-center sm:pt-0"
    >
        <div class="absolute end-4 top-4">
            <LanguageSwitcher />
        </div>
        <div>
            <Link :href="route('login')" class="flex items-center gap-2">
                <img
                    v-if="$page.props.logoUrl"
                    :src="$page.props.logoUrl"
                    alt="ContiStock"
                    class="h-[5.5rem] w-auto"
                />
                <ApplicationLogo
                    v-else
                    class="h-[5.5rem] w-[5.5rem] fill-current text-primary-navy"
                />
            </Link>
        </div>

        <div
            class="relative mt-6 w-full overflow-hidden rounded-xl border border-primary-navy/10 bg-white px-6 py-8 shadow-lg shadow-primary-navy/5 sm:max-w-md"
        >
            <div class="absolute left-0 right-0 top-0 h-1 rounded-t-xl bg-gradient-to-r from-primary-navy to-accent-orange" aria-hidden="true" />
            <slot />
        </div>
    </div>
</template>
