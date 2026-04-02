<script setup>
import {computed} from 'vue';
import {marked} from "marked";
import {formatTime} from "../utils/formatTime";

marked.setOptions({
  breaks: true,
  gfm: true,
});

const props = defineProps({
  message: {
    type: Object,
    required: true,
  },
});

const renderedContent = computed(() => marked(props.message.content));
</script>

<template>
  <div
      :class="message.role === 'user'
                    ? 'bg-primary text-gray-100 justify-self-end'
                    : 'bg-gray-100 text-gray-900  justify-self-start'"
      class="sm:max-w-[90%] md:max-w-[80%] rounded-lg px-4 py-2"
  >
    <div v-if="message.role === 'ai_assistant'" class="prose prose-sm"
         v-html="renderedContent"></div>
    <p v-else class="whitespace-pre-wrap text-sm">{{ message.content }}</p>
    <p class="mt-1 text-xs text-gray-400">
      {{ formatTime(message.created_at) }}
    </p>
  </div>
</template>