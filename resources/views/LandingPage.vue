<script setup>
import {ref} from 'vue';
import {Header, Panel} from '@statamic/cms/ui';
import NewConversation from "../js/components/NewConversation.vue";
import ConversationSidebar from "../js/components/ConversationSidebar.vue";

const props = defineProps({
  initialConversations: Object,
  conversationsUrl: String,
  startConversationUrl: String,
});
const sidebarRef = ref(null);
const sidebarOpen = ref(true);

</script>


<template>
  <div class="h-full flex flex-col">
    <Header :title="__('ai-entries-assistant::frontend.landing_page.title')" icon="ai-spark"/>
    <Panel class="flex flex-row flex-1 min-h-0">
      <ConversationSidebar
          ref="sidebarRef"
          :active-conversation-id="null"
          :conversations-url="conversationsUrl"
          :initial-conversations="initialConversations"
          :open="sidebarOpen"
      />

      <NewConversation
          :sidebar-open="sidebarOpen"
          :start-conversation-url="startConversationUrl"
          @toggle-sidebar="sidebarOpen = !sidebarOpen"
      />
    </Panel>
  </div>
</template>
