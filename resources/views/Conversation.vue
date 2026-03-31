<script setup>
import {computed, nextTick, onMounted, onUnmounted, ref} from 'vue';
import axios from 'axios';
import {Head} from '@statamic/cms/inertia';
import {Button, Dropdown, DropdownItem, DropdownMenu, Header, Input, Modal, ModalClose} from '@statamic/cms/ui';
import ExpandableTextarea from "../js/components/ExpandableTextarea.vue";
import {usePolling} from "../js/composables/usePolling.js";
import {marked} from 'marked';

marked.setOptions({
  breaks: true,
  gfm: true,
});

const props = defineProps({
  conversationId: String,
  conversationTitle: String,
  initialMessages: Object,
  messagesUrl: String,
  storeMessageUrl: String,
  updateTitleUrl: String,
});

const title = ref(props.conversationTitle);
const showRenameModal = ref(false);
const renameInput = ref('');
const renaming = ref(false);

const scrollContainer = ref(null);
const allMessages = ref([]);
const nextCursor = ref(null);
const hasMore = ref(false);
const loadingOlder = ref(false);

const newMessage = ref('');
const sending = ref(false);
const hasNewInput = computed(() => newMessage.value.trim().length > 0);

const waitingForReply = ref(false);
const lastMessageId = computed(() => {
  const messages = allMessages.value;
  return messages.length > 0 ? messages[messages.length - 1].id : null;
});

function handleNewMessages(messages) {
  if (messages.length === 0) return;

  const existingIds = new Set(allMessages.value.map(m => m.id));
  const newMessages = messages.filter(m => !existingIds.has(m.id));

  if (newMessages.length === 0) return;

  allMessages.value.push(...newMessages);
  waitingForReply.value = false;
  polling.stop();

  nextTick(() => scrollToBottom());
}

const polling = usePolling(props.messagesUrl, lastMessageId, handleNewMessages);

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
    waitingForReply.value = true;

    await nextTick();
    scrollToBottom();

    polling.start();
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

function openRenameModal() {
  renameInput.value = title.value;
  showRenameModal.value = true;
}

async function submitRename() {
  if (renaming.value || !renameInput.value.trim()) return;

  renaming.value = true;
  try {
    const {data} = await axios.patch(props.updateTitleUrl, {
      title: renameInput.value.trim(),
    });
    title.value = data.title;
    showRenameModal.value = false;
  } finally {
    renaming.value = false;
  }
}

onMounted(() => {
  initMessages();
  nextTick(() => {
    scrollToBottom();

    // If last message is from the user, the AI reply is still pending
    const last = allMessages.value[allMessages.value.length - 1];
    if (last && last.role === 'user') {
      waitingForReply.value = true;

      polling.start();
    }
  });
});

onUnmounted(() => {
  polling.stop();
});
</script>

<template>
  <Head :title="title"/>

  <div class="flex flex-col" style="height: calc(100vh - 52px);">
    <Header>
      <template #title>
        <Dropdown>
          <template #trigger>
            <button class="cursor-pointer hover:text-blue-500 transition-colors">{{ title }}</button>
          </template>
          <DropdownMenu>
            <DropdownItem icon="pencil" text="Rename" @click="openRenameModal"/>
          </DropdownMenu>
        </Dropdown>
      </template>
    </Header>

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
          :class="message.role === 'user' ? 'justify-end' : 'justify-start'"
          class="mb-4 flex"
      >
        <div
            :class="message.role === 'user'
              ? 'bg-blue-500 text-white'
              : 'bg-gray-100 text-gray-900'"
            class="max-w-[75%] rounded-lg px-4 py-2"
        >
          <div v-if="message.role === 'ai_assistant'" class="prose prose-sm" v-html="marked(message.content)"></div>
          <p v-else class="whitespace-pre-wrap text-sm">{{ message.content }}</p>
          <p
              :class="message.role === 'user' ? 'text-blue-200' : 'text-gray-400'"
              class="mt-1 text-xs"
          >
            {{ formatTime(message.created_at) }}
          </p>
        </div>
      </div>

      <div v-if="waitingForReply" class="mb-4 flex justify-start">
        <div class="rounded-lg bg-gray-100 px-4 py-3">
          <div class="flex items-center gap-1">
            <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-400"
                  style="animation-delay: 0ms;"></span>
            <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-400"
                  style="animation-delay: 150ms;"></span>
            <span class="inline-block h-2 w-2 animate-bounce rounded-full bg-gray-400"
                  style="animation-delay: 300ms;"></span>
          </div>
        </div>
      </div>
    </div>

    <div class="border-t border-gray-200 px-4 py-3">
      <div class="flex items-end gap-2">
        <div class="flex-1">
          <ExpandableTextarea
              v-model="newMessage"
              :disabled="sending"
              :max-rows="5"
              :min-rows="1"
              placeholder="Type a message..."
              @submit="sendMessage"
          />
        </div>
        <Button
            :disabled="!hasNewInput || sending"
            round
            text="Send"
            variant="primary"
            @click="sendMessage"
        />
      </div>
    </div>
  </div>

  <Modal
      :open="showRenameModal"
      title="Rename conversation"
      @update:open="showRenameModal = $event"
  >
    <form @submit.prevent="submitRename">
      <Input
          v-model="renameInput"
          :disabled="renaming"
          :focus="true"
          placeholder="Conversation title"
      />
    </form>
    <template #footer>
      <div class="flex items-center justify-end gap-2">
        <ModalClose>
          <Button text="Cancel" variant="ghost"/>
        </ModalClose>
        <Button
            :disabled="renaming || !renameInput.trim()"
            text="Save"
            variant="primary"
            @click="submitRename"
        />
      </div>
    </template>
  </Modal>
</template>
