<script setup>
import {ref} from 'vue';
import {Header, Panel} from '@statamic/cms/ui';
import ConversationComponent from "../js/components/Conversation.vue";
import ConversationSidebar from "../js/components/ConversationSidebar.vue";

const props = defineProps({
  conversationId: String,
  conversationTitle: String,
  initialMessages: Object,
  initialConversations: Object,
  landingPageUrl: String,
  conversationsUrl: String,
  messagesUrl: String,
  storeMessageUrl: String,
  updateTitleUrl: String,
  deleteUrl: String,
});

const sidebarOpen = ref(true);
const sidebarRef = ref(null);

function toggleSidebar() {
  sidebarOpen.value = !sidebarOpen.value;
}

function onTitleUpdated(newTitle) {
  sidebarRef.value?.updateConversationTitle(props.conversationId, newTitle);
}
</script>

<template>
  <div class="h-full flex flex-col">
    <Header :title="__('ai-entries-assistant::frontend.landing_page.title')" icon="ai-spark"/>
    <Panel class="relative flex flex-row flex-1 min-h-0">
      <ConversationSidebar
          ref="sidebarRef"
          :active-conversation-id="conversationId"
          :conversations-url="conversationsUrl"
          :initial-conversations="initialConversations"
          :landing-page-url="landingPageUrl"
          :open="sidebarOpen"
          @close="sidebarOpen = false"
      />

      <ConversationComponent
          :conversation-id="conversationId"
          :conversation-title="conversationTitle"
          :delete-url="deleteUrl"
          :initial-messages="initialMessages"
          :messages-url="messagesUrl"
          :sidebar-open="sidebarOpen"
          :store-message-url="storeMessageUrl"
          :update-title-url="updateTitleUrl"
          @toggle-sidebar="toggleSidebar"
          @title-updated="onTitleUpdated"
      />
    </Panel>
  </div>
</template>
