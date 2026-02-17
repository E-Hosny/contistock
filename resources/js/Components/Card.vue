<script setup>
defineOptions({
    inheritAttrs: false,
});
</script>

<template>
    <div
        class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm"
        v-bind="$attrs"
    >
        <div v-if="$slots.title" class="border-b border-gray-200 bg-gray-50 px-4 py-3">
            <h3 class="text-sm font-semibold text-gray-800">
                <slot name="title" />
            </h3>
        </div>
        <div class="p-4">
            <slot />
        </div>
    </div>
</template>
