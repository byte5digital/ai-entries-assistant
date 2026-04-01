<script setup>
import {computed, nextTick, onMounted, onUnmounted, ref} from 'vue';
import axios from 'axios';
import {router} from '@statamic/cms/inertia';
import {
  Button,
  ConfirmationModal,
  Dropdown,
  DropdownItem,
  DropdownMenu,
  DropdownSeparator,
  Header,
  Input,
  Modal,
  ModalClose
} from '@statamic/cms/ui';
import {usePolling} from "../js/composables/usePolling.js";
import TypingIndicator from "../js/components/TypingIndicator.vue";
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
  deleteUrl: String,
});

const title = ref(props.conversationTitle);
const showRenameModal = ref(false);
const renameInput = ref('');
const renaming = ref(false);
const showDeleteModal = ref(false);
const deleting = ref(false);

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

function deleteConversation() {
  if (deleting.value) return;

  deleting.value = true;
  router.delete(props.deleteUrl, {
    onFinish: () => deleting.value = false,
  });
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
  <div class="relative">
    <Header>
      <template #title>
        <Dropdown>
          <template #trigger>
            <button class="cursor-pointer hover:text-primary transition-colors">{{ title }}</button>
          </template>
          <DropdownMenu>
            <DropdownItem icon="pencil" text="Rename" @click="openRenameModal"/>
            <DropdownSeparator/>
            <DropdownItem icon="trash" text="Delete" variant="destructive" @click="showDeleteModal = true"/>
          </DropdownMenu>
        </Dropdown>
      </template>
    </Header>

    <div
        ref="scrollContainer"
        class="overflow-y-auto px-4 py-4 pb-24"
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
              ? 'bg-primary text-gray-100'
              : 'bg-gray-100 text-gray-900'"
            class="max-w-[75%] rounded-lg px-4 py-2"
        >
          <div v-if="message.role === 'ai_assistant'" class="prose prose-sm" v-html="marked(message.content)"></div>
          <p v-else class="whitespace-pre-wrap text-sm">{{ message.content }}</p>
          <p class="mt-1 text-xs text-gray-400">
            {{ formatTime(message.created_at) }}
          </p>
        </div>
      </div>

      <TypingIndicator v-if="waitingForReply"/>
    </div>

    <div class="sticky bottom-0 bg-white ">
      <Input
          v-model="newMessage"
          :disabled="sending"
          :placeholder="__('ai-entries-assistant::frontend.conversation.input_placeholder')"
          @keydown.enter="sendMessage"
      >
        <template #append>
          <Button
              :disabled="!hasNewInput || sending"
              icon="ai-spark"
              variant="ghost"
              @click="sendMessage"
          />
        </template>
      </Input>
    </div>
  </div>

  <Modal
      :open="showRenameModal"
      blur
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
      <div class="flex items-center justify-end space-x-3 pt-3 pb-1">
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

  <ConfirmationModal
      :busy="deleting"
      :danger="true"
      :open="showDeleteModal"
      blur
      body-text="Are you sure you want to delete this conversation? This action cannot be undone."
      button-text="Delete"
      title="Delete conversation"
      @confirm="deleteConversation"
      @update:open="showDeleteModal = $event"
  />
</template>
