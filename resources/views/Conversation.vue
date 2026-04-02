<script setup>
import {ref, useTemplateRef} from 'vue';
import {Header, Panel} from '@statamic/cms/ui';
import ConversationComponent from "../js/components/Conversation.vue";
import ConversationSidebar from "../js/components/ConversationSidebar.vue";

const props = defineProps({
  conversationId: String,
  conversationTitle: String,
  initialMessages: Object,
  initialConversations: Object,
  landingPageUrl: String,
  conversationsIndexUrl: String,
  conversationMessagesIndexUrl: String,
  conversationMessagesStoreUrl: String,
  conversationTitleUpdateUrl: String,
  conversationDestroyUrl: String,
});

const mdBreakpoint = getComputedStyle(document.documentElement).getPropertyValue('--breakpoint-md');
const sidebarOpen = ref(window.matchMedia(`(min-width: ${mdBreakpoint})`).matches);
const sidebarRef = useTemplateRef('sidebarRef');

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
          :conversations-index-url="conversationsIndexUrl"
          :initial-conversations="initialConversations"
          :landing-page-url="landingPageUrl"
          :open="sidebarOpen"
          @close="sidebarOpen = false"
      />

      <ConversationComponent
          :conversation-id="conversationId"
          :conversation-title="conversationTitle"
          :conversation-destroy-url="conversationDestroyUrl"
          :initial-messages="initialMessages"
          :conversation-messages-index-url="conversationMessagesIndexUrl"
          :conversation-messages-store-url="conversationMessagesStoreUrl"
          :sidebar-open="sidebarOpen"
          :conversation-title-update-url="conversationTitleUpdateUrl"
          @toggle-sidebar="toggleSidebar"
          @title-updated="onTitleUpdated"
      />
    </Panel>
  </div>
</template>
