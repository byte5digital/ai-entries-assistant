<script setup>
import { ref, computed, watch, nextTick } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: '',
    },
    minRows: {
        type: Number,
        default: 4,
    },
    maxRows: {
        type: Number,
        default: 10,
    },
    limit: {
        type: Number,
        default: 1000,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'submit']);

const textarea = ref(null);

const charCount = computed(() => (props.modelValue ?? '').length);

const percentage = computed(() => {
    if (!props.limit) return 0;
    return charCount.value / props.limit;
});

const counterColor = computed(() => {
    if (!props.limit) return 'text-gray-400';
    if (percentage.value >= 1) return 'text-red-500 font-semibold';
    if (percentage.value >= 0.95) return 'text-orange-600';
    if (percentage.value >= 0.9) return 'text-orange-400';
    return 'text-gray-400';
});

function resize() {
    const el = textarea.value;
    if (!el) return;

    const style = window.getComputedStyle(el);
    const lineHeight = parseFloat(style.lineHeight);
    const paddingTop = parseFloat(style.paddingTop);
    const paddingBottom = parseFloat(style.paddingBottom);
    const borderTop = parseFloat(style.borderTopWidth);
    const borderBottom = parseFloat(style.borderBottomWidth);

    const minHeight = lineHeight * props.minRows + paddingTop + paddingBottom + borderTop + borderBottom;
    const maxHeight = lineHeight * props.maxRows + paddingTop + paddingBottom + borderTop + borderBottom;

    el.style.height = 'auto';
    const scrollHeight = el.scrollHeight;
    el.style.height = Math.min(Math.max(scrollHeight, minHeight), maxHeight) + 'px';
}

function onInput(event) {
    let value = event.target.value;
    if (props.limit && value.length > props.limit) {
        value = value.slice(0, props.limit);
        event.target.value = value;
    }
    emit('update:modelValue', value);
    resize();
}

watch(() => props.modelValue, () => {
    nextTick(resize);
});

defineExpose({ resize });
</script>

<template>
    <div class="relative w-full">
        <textarea
            ref="textarea"
            :value="modelValue"
            :placeholder="placeholder"
            :rows="minRows"
            :maxlength="limit"
            :disabled="disabled"
            class="w-full resize-none overflow-y-auto rounded-md border border-gray-300 px-3 py-2 pb-6 text-sm leading-normal focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:cursor-not-allowed disabled:opacity-50"
            @input="onInput"
            @keydown.enter.exact.prevent="emit('submit')"
        />
        <span
            v-if="limit && percentage >= 0.8"
            class="absolute bottom-2 right-3 pr-1 text-xs"
            :class="counterColor"
        >
            {{ charCount }} / {{ limit }}
        </span>
    </div>
</template>
