<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';
import Card from '@/Components/Card.vue';
</script>

<template>
    <Head :title="$t('settings.title')" />

    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ $t('settings.title') }}
            </h2>
        </template>

        <div class="mx-auto max-w-3xl space-y-6">
            <Card>
                <template #title>{{ $t('settings.language') }}</template>
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">{{ $t('settings.language') }}</p>
                    <LanguageSwitcher />
                </div>
            </Card>

            <Card>
                <template #title>{{ $t('settings.profile_shortcut') }}</template>
                <Link
                    :href="route('profile.edit')"
                    class="inline-flex items-center rounded-lg bg-accent-orange px-4 py-2 text-sm font-medium text-white transition hover:bg-accent-orange/90"
                >
                    {{ $t('nav.profile') }}
                </Link>
            </Card>
        </div>
    </DashboardLayout>
</template>
