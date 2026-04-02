<script setup>
import {ref, shallowRef} from 'vue';
import axios from 'axios';
import {router} from '@statamic/cms/inertia';
import {Button} from '@statamic/cms/ui';

const props = defineProps({
  initialConversations: Object,
  conversationsIndexUrl: String,
  landingPageUrl: String,
  activeConversationId: String,
  open: Boolean,
});

const emit = defineEmits(['close']);

const allConversations = ref([]);
const conversationsNextCursor = shallowRef(null);
const conversationsHasMore = shallowRef(false);
const loadingMoreConversations = shallowRef(false);

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
    const url = `${props.conversationsIndexUrl}?cursor=${conversationsNextCursor.value}`;
    const {data: json} = await axios.get(url);
    const moreConversations = json.data ?? [];

    allConversations.value.push(...moreConversations);
    conversationsNextCursor.value = json.meta?.next_cursor ?? null;
    conversationsHasMore.value = conversationsNextCursor.value !== null;
  } finally {
    loadingMoreConversations.value = false;
  }
}


function truncate(text, maxLength = 30) {
  return text.length > maxLength ? text.slice(0, maxLength) + '…' : text;
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
      :class="open ? 'max-md:w-full md:w-72' : 'w-0'"
      class="max-md:absolute max-md:inset-y-0 max-md:left-0 max-md:z-10 shrink-0 overflow-hidden transition-all duration-200 ease-in-out"
  >
    <div class="flex flex-col h-full w-full md:w-72 px-3 pt-5">
      <div class="text-right md:hidden mb-5">
        <Button
            icon="x"
            icon-only
            size="sm"
            variant="ghost"
            @click="emit('close')"
        />
      </div>
      <Button
          :text="__('ai-entries-assistant::frontend.sidebar.new_conversation')"
          class="w-full mb-5 shrink-0"
          icon="add-circle"
          variant="primary"
          @click="navigateToConversation(landingPageUrl)"
      />
      <div class="relative flex-1">
      <div class="absolute inset-0 overflow-y-auto">
        <h2 class="px-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
          {{ __('ai-entries-assistant::frontend.sidebar.conversations') }}
        </h2>
        <Button
            v-for="conversation in allConversations"
            :key="conversation.id"
            :text="conversation.short_title || __('ai-entries-assistant::frontend.conversation.untitled')"
            :variant="conversation.id === activeConversationId ? 'filled' : 'ghost'"
            class="w-full mb-0.5 truncate justify-start"
            @click="navigateToConversation(conversation.url)"
        />

        <div v-if="loadingMoreConversations" class="py-2 text-center text-xs text-gray-400 animate-pulse">
          {{ __('ai-entries-assistant::frontend.conversation.loading') }}
        </div>

        <Button
            v-if="conversationsHasMore && !loadingMoreConversations"
            :text="__('ai-entries-assistant::frontend.sidebar.load_more')"
            class="w-full mt-1"
            size="sm"
            variant="ghost"
            @click="loadMoreConversations"
        />
      </div>
      </div>
    </div>
  </aside>
</template>
