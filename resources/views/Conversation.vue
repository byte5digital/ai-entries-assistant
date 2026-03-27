<script setup>
import {ref, computed, onMounted, nextTick} from 'vue';
import axios from 'axios';
import {Head} from '@statamic/cms/inertia';
import {Button, Header} from '@statamic/cms/ui';
import ExpandableTextarea from "../js/components/ExpandableTextarea.vue";

const props = defineProps({
  conversationId: String,
  conversationTitle: String,
  initialMessages: Object,
  messagesUrl: String,
  storeMessageUrl: String,
});

const scrollContainer = ref(null);
const allMessages = ref([]);
const nextCursor = ref(null);
const hasMore = ref(false);
const loadingOlder = ref(false);

const newMessage = ref('');
const sending = ref(false);
const hasNewInput = computed(() => newMessage.value.trim().length > 0);

function initMessages() {
  const data = props.initialMessages?.data ?? [];
  allMessages.value = [...data].reverse();
  nextCursor.value = props.initialMessages?.meta?.next_cursor ?? null;
  hasMore.value = nextCursor.value !== null;
}

function scrollToBottom() {
  const el = scrollContainer.value;
  if (el) {
    el.scrollTop = el.scrollHeight;
  }
}

async function loadOlderMessages() {
  if (loadingOlder.value || !hasMore.value) return;

  loadingOlder.value = true;
  const el = scrollContainer.value;
  const prevHeight = el.scrollHeight;

  try {
    const url = `${props.messagesUrl}?cursor=${nextCursor.value}`;
    const {data: json} = await axios.get(url);
    const olderMessages = (json.data ?? []).reverse();

    allMessages.value = [...olderMessages, ...allMessages.value];
    nextCursor.value = json.meta?.next_cursor ?? null;
    hasMore.value = nextCursor.value !== null;

    await nextTick();
    el.scrollTop = el.scrollHeight - prevHeight;
  } finally {
    loadingOlder.value = false;
  }
}

async function sendMessage() {
  if (!hasNewInput.value || sending.value) return;

  sending.value = true;

  try {
    const {data: json} = await axios.post(props.storeMessageUrl, {
      content: newMessage.value,
    });

    allMessages.value.push(json.data);
    newMessage.value = '';

    await nextTick();
    scrollToBottom();
  } finally {
    sending.value = false;
  }
}

function onScroll() {
  const el = scrollContainer.value;
  if (el && el.scrollTop < 50 && hasMore.value) {
    loadOlderMessages();
  }
}

function formatTime(isoString) {
  const date = new Date(isoString);
  return date.toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'});
}

onMounted(() => {
  initMessages();
  nextTick(() => scrollToBottom());
});
</script>

<template>
  <Head :title="conversationTitle"/>

  <div class="flex flex-col" style="height: calc(100vh - 52px);">
    <Header :title="conversationTitle"/>

    <div
        ref="scrollContainer"
        class="min-h-0 flex-1 overflow-y-auto px-4 py-4"
        @scroll="onScroll"
    >
      <div v-if="loadingOlder" class="mb-4 text-center text-sm text-gray-400">
        Loading older messages...
      </div>

      <div
          v-for="message in allMessages"
          :key="message.id"
          class="mb-4 flex"
          :class="message.role === 'user' ? 'justify-end' : 'justify-start'"
      >
        <div
            class="max-w-[75%] rounded-lg px-4 py-2"
            :class="message.role === 'user'
              ? 'bg-blue-500 text-white'
              : 'bg-gray-100 text-gray-900'"
        >
          <p class="whitespace-pre-wrap text-sm">{{ message.content }}</p>
          <p
              class="mt-1 text-xs"
              :class="message.role === 'user' ? 'text-blue-200' : 'text-gray-400'"
          >
            {{ formatTime(message.created_at) }}
          </p>
        </div>
      </div>
    </div>

    <div class="border-t border-gray-200 px-4 py-3">
      <div class="flex items-end gap-2">
        <div class="flex-1">
          <ExpandableTextarea
              v-model="newMessage"
              placeholder="Type a message..."
              :disabled="sending"
              :min-rows="1"
              :max-rows="5"
              @submit="sendMessage"
          />
        </div>
        <Button
            round
            variant="primary"
            text="Send"
            :disabled="!hasNewInput || sending"
            @click="sendMessage"
        />
      </div>
    </div>
  </div>
</template>
