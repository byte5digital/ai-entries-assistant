<script setup>
import { computed } from 'vue';
import ExpandableTextarea from './ExpandableTextarea.vue';
import {Button} from "@statamic/cms/ui";

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue', 'assistant-message-submitted']);

const hasInput = computed(() => (props.modelValue ?? '').trim().length > 0);

function onInput(value) {
    emit('update:modelValue', value);
}
</script>

<template>
    <div class="w-full">
        <ExpandableTextarea
            :model-value="modelValue"
            :disabled="disabled"
            @update:model-value="onInput"
            v-bind="$attrs"
        />
        <div class="mt-2 flex justify-end">
            <Button round variant="primary" text="Send" :disabled="!hasInput || disabled" @click="emit('assistant-message-submitted')" />
        </div>
    </div>
</template>
