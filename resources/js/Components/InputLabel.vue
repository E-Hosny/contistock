<script setup>
defineProps({
    value: {
        type: String,
    },
    labelClass: {
        type: [String, Array, Object],
        default: '',
    },
});
</script>

<template>
    <label class="block text-sm font-medium text-gray-700" :class="labelClass">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
