<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import LanguageSwitcher from '@/Components/LanguageSwitcher.vue';

const page = usePage();
const locale = computed(() => page.props.locale || 'en');
const isRtl = computed(() => locale.value === 'ar');

const sidebarOpen = ref(false);

const navItems = [
    { nameKey: 'nav.dashboard', route: 'dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0-5v5m0-5h-5' },
    { nameKey: 'nav.settings', route: 'settings', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
];
</script>

<template>
    <div class="min-h-screen bg-background-light" :class="{ 'font-arabic': isRtl }" :dir="isRtl ? 'rtl' : 'ltr'">
        <!-- Mobile sidebar backdrop -->
        <div
            v-show="sidebarOpen"
            class="fixed inset-0 z-40 bg-gray-900/50 lg:hidden"
            @click="sidebarOpen = false"
        />

        <!-- Sidebar -->
        <aside
            class="fixed top-0 z-50 h-full w-64 shrink-0 border-e border-gray-200 bg-primary-navy text-white transition-transform lg:translate-x-0"
            :class="[isRtl ? 'right-0 border-s' : 'left-0', sidebarOpen ? 'translate-x-0' : (isRtl ? 'translate-x-full' : '-translate-x-full')]"
        >
            <div class="flex h-16 items-center justify-between px-4">
                <Link :href="route('dashboard')" class="flex items-center gap-2">
                    <ApplicationLogo
                        class="block h-9 w-auto fill-current text-white"
                    />
                    <span class="text-lg font-semibold">{{ $t('app.name') }}</span>
                </Link>
                <button
                    type="button"
                    class="rounded p-2 text-white hover:bg-white/10 lg:hidden"
                    @click="sidebarOpen = false"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="isRtl ? 'M9 5l7 7-7 7' : 'M15 19l-7-7 7-7'" />
                    </svg>
                </button>
            </div>
            <nav class="mt-4 space-y-1 px-3">
                <Link
                    v-for="item in navItems"
                    :key="item.route"
                    :href="route(item.route)"
                    :class="[
                        'flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-colors',
                        route().current(item.route)
                            ? 'bg-accent-orange text-white'
                            : 'text-white/90 hover:bg-white/10'
                    ]"
                >
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                    </svg>
                    {{ $t(item.nameKey) }}
                </Link>
            </nav>
        </aside>

        <!-- Main content area -->
        <div class="lg:ps-64" :class="{ 'lg:pe-64 lg:ps-0': isRtl }">
            <!-- Topbar -->
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-gray-200 bg-white px-4 shadow-sm">
                <button
                    type="button"
                    class="rounded p-2 text-gray-600 hover:bg-gray-100 lg:hidden"
                    @click="sidebarOpen = true"
                >
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="flex flex-1 items-center justify-end gap-2">
                    <LanguageSwitcher />
                    <slot name="language-switcher" />

                    <div class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 focus:outline-none"
                                    >
                                        {{ $page.props.auth.user.name }}
                                        <svg class="ms-2 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </span>
                            </template>
                            <template #content>
                                <DropdownLink :href="route('profile.edit')">
                                    {{ $t('nav.profile') }}
                                </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button">
                                    {{ $t('nav.logout') }}
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <!-- Page header -->
            <header v-if="$slots.header" class="bg-white px-4 py-4 shadow-sm">
                <div class="mx-auto max-w-7xl">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page content -->
            <main class="p-4">
                <slot />
            </main>
        </div>
    </div>
</template>
