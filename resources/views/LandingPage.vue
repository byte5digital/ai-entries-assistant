<script setup>
import {computed} from 'vue';
import {Head, Link, useForm} from '@statamic/cms/inertia';
import {Button, Header} from '@statamic/cms/ui';
import ExpandableTextarea from "../js/components/ExpandableTextarea.vue";

const props = defineProps({
  lastConversationUrl: String,
  startConversationUrl: String,
});

const form = useForm({
  content: '',
  conversation_id: null,
});

const hasInput = computed(() => form.content.trim().length > 0);

function onMessageSubmitted() {
  form.post(props.startConversationUrl, {
    preserveScroll: true,
  });
}
</script>

<template>
  <Head :title="__('ai-entries-assistant::frontend.landing_page.title')"/>

  <div class="flex h-full flex-col items-center justify-center">
    <Header :title="__('ai-entries-assistant::frontend.landing_page.title')" icon="ai-spark"/>

    <p class="mb-6 text-center text-sm text-gray-500">
      {{ __('ai-entries-assistant::frontend.landing_page.subheading') }}
      <template v-if="lastConversationUrl">
        <br>
        <Link :href="lastConversationUrl"
              class="text-blue-600 hover:text-blue-800">
          {{ __('ai-entries-assistant::frontend.landing_page.continue_conversation') }} &rarr;
        </Link>
      </template>
    </p>

    <div class="w-full">
      <ExpandableTextarea
          v-model="form.content"
          :disabled="form.processing"
          placeholder="Type a message..."
      />
      <div class="mt-2 flex justify-end">
        <Button :disabled="!hasInput || form.processing" round text="Send" variant="primary"
                @click="onMessageSubmitted"/>
      </div>
    </div>
  </div>
</template>
