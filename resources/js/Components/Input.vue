<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    type: {
        type: String,
        default: 'text',
    },
    id: String,
    placeholder: String,
    required: Boolean,
    disabled: Boolean,
    error: String,
});

const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <div class="w-full">
        <input
            :id="id"
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :required="required"
            :disabled="disabled"
            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-navy focus:ring-primary-navy"
            @input="emit('update:modelValue', $event.target.value)"
        />
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>
