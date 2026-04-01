<script setup>
import {ref} from 'vue';
import axios from 'axios';
import {router} from '@statamic/cms/inertia';
import {Button} from '@statamic/cms/ui';

const props = defineProps({
  initialConversations: Object,
  conversationsUrl: String,
  landingPageUrl: String,
  activeConversationId: String,
  open: Boolean,
});

const sidebarScrollContainer = ref(null);
const allConversations = ref([]);
const conversationsNextCursor = ref(null);
const conversationsHasMore = ref(false);
const loadingMoreConversations = ref(false);

function initConversations() {
  const data = props.initialConversations?.data ?? [];
  allConversations.value = data;
  conversationsNextCursor.value = props.initialConversations?.meta?.next_cursor ?? null;
  conversationsHasMore.value = conversationsNextCursor.value !== null;
}

async function loadMoreConversations() {
  if (loadingMoreConversations.value || !conversationsHasMore.value) return;

  loadingMoreConversations.value = true;

  try {
    const url = `${props.conversationsUrl}?cursor=${conversationsNextCursor.value}`;
    const {data: json} = await axios.get(url);
    const moreConversations = json.data ?? [];

    allConversations.value.push(...moreConversations);
    conversationsNextCursor.value = json.meta?.next_cursor ?? null;
    conversationsHasMore.value = conversationsNextCursor.value !== null;
  } finally {
    loadingMoreConversations.value = false;
  }
}

function onSidebarScroll() {
  const el = sidebarScrollContainer.value;
  if (!el) return;

  const distanceFromBottom = el.scrollHeight - el.scrollTop - el.clientHeight;
  if (distanceFromBottom < 50 && conversationsHasMore.value) {
    loadMoreConversations();
  }
}

function navigateToConversation(url) {
  router.visit(url);
}

function updateConversationTitle(conversationId, newTitle) {
  const entry = allConversations.value.find(c => c.id === conversationId);
  if (entry) {
    entry.title = newTitle;
  }
}

defineExpose({updateConversationTitle});

initConversations();
</script>

<template>
  <aside
      ref="sidebarScrollContainer"
      :class="open ? 'w-72' : 'w-0'"
      class="shrink-0 overflow-y-auto overflow-x-hidden transition-all duration-300 ease-in-out"
      @scroll="onSidebarScroll"
  >
    <div class="w-72 p-3">
      <Button
          :text="__('ai-entries-assistant::frontend.sidebar.new_conversation')"
          class="w-full mb-2"
          icon="add-circle"
          size="sm"
          @click="navigateToConversation(landingPageUrl)"
      />

      <h2 class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
        {{ __('ai-entries-assistant::frontend.sidebar.conversations') }}
      </h2>

      <button
          v-for="conversation in allConversations"
          :key="conversation.id"
          :class="conversation.id === activeConversationId
            ? 'bg-white shadow-sm text-gray-900'
            : 'text-gray-600 hover:bg-gray-200'"
          class="w-full text-left rounded-md px-2 py-2 mb-0.5 text-sm truncate transition-colors cursor-pointer"
          @click="navigateToConversation(conversation.url)"
      >
        {{ conversation.title || __('ai-entries-assistant::frontend.conversation.untitled') }}
      </button>

      <div v-if="loadingMoreConversations" class="py-2 text-center text-xs text-gray-400">
        {{ __('ai-entries-assistant::frontend.conversation.loading') }}
      </div>
    </div>
  </aside>
</template>
