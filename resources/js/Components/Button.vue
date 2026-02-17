<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    variant: {
        type: String,
        default: 'primary', // primary | secondary | danger | ghost
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const variantClasses = {
    primary: 'bg-primary-navy text-white hover:bg-primary-navy/90 focus:ring-primary-navy',
    secondary: 'bg-background-light text-gray-700 hover:bg-gray-200 focus:ring-gray-400',
    accent: 'bg-accent-orange text-white hover:bg-accent-orange/90 focus:ring-accent-orange',
    danger: 'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',
    ghost: 'bg-transparent text-gray-700 hover:bg-gray-100 focus:ring-gray-400',
};
</script>

<template>
    <button
        :type="type"
        :disabled="disabled"
        :class="[
            'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed',
            variantClasses[variant] || variantClasses.primary
        ]"
    >
        <slot />
    </button>
</template>
