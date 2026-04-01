<script setup>
import {computed, shallowRef} from 'vue';
import {Head, useForm} from '@statamic/cms/inertia';
import {Button, Card, Input} from "@statamic/cms/ui";
import Message from "./Message.vue";

const emit = defineEmits(['toggle-sidebar']);
const props = defineProps({
  sidebarOpen: {type: Boolean, default: true},
  startConversationUrl: String,
});

const newMessage = shallowRef('');
const hasNewInput = computed(() => newMessage.value.trim().length > 0);

const form = useForm({
  content: null
});

const welcomeMessage = {
  role: 'ai_assistant',
  content: __('ai-entries-assistant::frontend.new_conversation.welcome_message'),
  created_at: new Date().toISOString(),
};

function handleCreateConversation() {
  form.post(props.startConversationUrl, {
    preserveScroll: true,
  });
}
</script>

<template>
  <Head :title="__('ai-entries-assistant::frontend.conversations.welcome_message')"/>

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
      <h2>{{ __('ai-entries-assistant::frontend.new_conversation.title') }}</h2>
    </div>
    <div class="relative flex-1">
      <div class="absolute inset-0 overflow-y-auto px-4 py-4">
        <Message :message="welcomeMessage"/>
      </div>
    </div>
    <div>
      <Input
          v-model="form.content"
          :disabled="form.processing"
          :placeholder="__('ai-entries-assistant::frontend.conversation.input_placeholder')"
          @keydown.enter="handleCreateConversation"
      >
        <template #append>
          <Button
              :disabled="!hasNewInput || form.processing"
              icon="ai-spark"
              variant="ghost"
              @click="handleCreateConversation"
          />
        </template>
      </Input>
    </div>
  </Card>
</template>