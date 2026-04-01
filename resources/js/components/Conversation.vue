<script setup>
import {nextTick, onMounted, onUnmounted} from 'vue';
import {
  Button,
  Card,
  ConfirmationModal,
  Dropdown,
  DropdownItem,
  DropdownMenu,
  DropdownSeparator,
  Input,
  Modal,
  ModalClose
} from "@statamic/cms/ui";
import {marked} from 'marked';
import TypingIndicator from "./TypingIndicator.vue";
import {useMessages} from "../composables/useMessages.js";
import {useConversationActions} from "../composables/useConversationActions.js";
import {formatTime} from "../utils/formatTime.js";

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
  sidebarOpen: Boolean,
});

const emit = defineEmits(['toggle-sidebar', 'title-updated']);

const {
  messageContainer,
  allMessages,
  hasMore,
  loadingOlder,
  newMessage,
  sending,
  hasNewInput,
  waitingForReply,
  polling,
  initMessages,
  scrollToBottom,
  loadIfNotScrollable,
  sendMessage,
  onScroll,
} = useMessages(props.messagesUrl, props.initialMessages);

const {
  title,
  showRenameModal,
  renameInput,
  renaming,
  showDeleteModal,
  deleting,
  openRenameModal,
  submitRename,
  deleteConversation,
} = useConversationActions(props.conversationTitle, props.updateTitleUrl, props.deleteUrl);

async function handleRename() {
  const newTitle = await submitRename();
  if (newTitle) {
    emit('title-updated', newTitle);
  }
}

function handleSendMessage() {
  sendMessage(props.storeMessageUrl);
}

onMounted(() => {
  initMessages();
  nextTick(() => {
    scrollToBottom();
    loadIfNotScrollable();

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
  <Card class="flex flex-col flex-1 min-w-0 content-card divide-y divide-gray-200">
    <div class="flex items-center py-4 text-lg">
      <Button
          :title="sidebarOpen ? __('ai-entries-assistant::frontend.conversation.close_sidebar') : __('ai-entries-assistant::frontend.conversation.open_sidebar')"
          icon="burger-menu-no-border"
          icon-only
          size="sm"
          variant="ghost"
          @click="emit('toggle-sidebar')"
      />
      <Dropdown>
        <template #trigger>
          <button class="cursor-pointer hover:text-primary  transition-colors">
            {{ title }}
          </button>
        </template>
        <DropdownMenu>
          <DropdownItem :text="__('ai-entries-assistant::frontend.conversation.rename')" icon="pencil"
                        @click="openRenameModal"/>
          <DropdownSeparator/>
          <DropdownItem :text="__('ai-entries-assistant::frontend.conversation.delete')" icon="trash"
                        variant="destructive" @click="showDeleteModal = true"/>
        </DropdownMenu>
      </Dropdown>
    </div>
    <div class="relative flex-1">
      <div ref="messageContainer" class="absolute inset-0 overflow-y-auto px-4 py-4" @scroll="onScroll">
        <div v-if="loadingOlder" class="mb-4 text-center text-sm text-gray-400 animate-pulse">
          {{ __('ai-entries-assistant::frontend.conversation.loading_older_messages') }}
        </div>

        <div v-if="!hasMore && !loadingOlder" class="mb-4 text-center text-sm text-gray-400">
          {{ __('ai-entries-assistant::frontend.conversation.start_of_conversation') }}
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
            <div v-if="message.role === 'ai_assistant'" class="prose prose-sm"
                 v-html="marked(message.content)"></div>
            <p v-else class="whitespace-pre-wrap text-sm">{{ message.content }}</p>
            <p class="mt-1 text-xs text-gray-400">
              {{ formatTime(message.created_at) }}
            </p>
          </div>
        </div>

        <TypingIndicator v-if="waitingForReply"/>
      </div>
    </div>

    <div>
      <Input
          v-model="newMessage"
          :disabled="sending"
          :placeholder="__('ai-entries-assistant::frontend.conversation.input_placeholder')"
          @keydown.enter="handleSendMessage"
      >
        <template #append>
          <Button
              :disabled="!hasNewInput || sending"
              icon="ai-spark"
              variant="ghost"
              @click="handleSendMessage"
          />
        </template>
      </Input>
    </div>
  </Card>

  <Modal
      :open="showRenameModal"
      :title="__('ai-entries-assistant::frontend.conversation.rename_modal_title')"
      blur
      @update:open="showRenameModal = $event"
  >
    <form @submit.prevent="handleRename">
      <Input
          v-model="renameInput"
          :disabled="renaming"
          :focus="true"
          :placeholder="__('ai-entries-assistant::frontend.conversation.rename_modal_placeholder')"
      />
    </form>
    <template #footer>
      <div class="flex items-center justify-end space-x-3 pt-3 pb-1">
        <ModalClose>
          <Button :text="__('ai-entries-assistant::frontend.conversation.cancel')" variant="ghost"/>
        </ModalClose>
        <Button
            :disabled="renaming || !renameInput.trim()"
            :text="__('ai-entries-assistant::frontend.conversation.save')"
            variant="primary"
            @click="handleRename"
        />
      </div>
    </template>
  </Modal>

  <ConfirmationModal
      :body-text="__('ai-entries-assistant::frontend.conversation.delete_modal_body')"
      :busy="deleting"
      :button-text="__('ai-entries-assistant::frontend.conversation.delete')"
      :danger="true"
      :open="showDeleteModal"
      :title="__('ai-entries-assistant::frontend.conversation.delete_modal_title')"
      blur
      @confirm="deleteConversation"
      @update:open="showDeleteModal = $event"
  />
</template>