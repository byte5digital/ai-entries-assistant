<script setup>
import {computed, ref} from 'vue';
import {Header, Panel} from '@statamic/cms/ui';
import NewConversation from "../js/components/NewConversation.vue";
import ConversationSidebar from "../js/components/ConversationSidebar.vue";

const props = defineProps({
  initialConversations: Object,
  conversationsUrl: String,
  landingPageUrl: String,
  startConversationUrl: String,
});

const hasConversations = computed(() => (props.initialConversations?.data?.length ?? 0) > 0);
const sidebarRef = ref(null);
const mdBreakpoint = getComputedStyle(document.documentElement).getPropertyValue('--breakpoint-md');
const sidebarOpen = ref(window.matchMedia(`(min-width: ${mdBreakpoint})`).matches);

</script>


<template>
  <div class="h-full flex flex-col">
    <Header :title="__('ai-entries-assistant::frontend.landing_page.title')" icon="ai-spark"/>
    <Panel class="relative flex flex-row flex-1 min-h-0">
      <ConversationSidebar
          v-if="hasConversations"
          ref="sidebarRef"
          :active-conversation-id="null"
          :conversations-url="conversationsUrl"
          :initial-conversations="initialConversations"
          :landing-page-url="landingPageUrl"
          :open="sidebarOpen"
          @close="sidebarOpen = false"
      />

      <NewConversation
          :show-sidebar-toggle="hasConversations"
          :sidebar-open="sidebarOpen"
          :start-conversation-url="startConversationUrl"
          @toggle-sidebar="sidebarOpen = !sidebarOpen"
      />
    </Panel>
  </div>
</template>
