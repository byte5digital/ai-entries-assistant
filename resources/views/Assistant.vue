<script setup>
import { ref } from 'vue';
import { Head } from '@statamic/cms/inertia';
import { Header } from '@statamic/cms/ui';
import UserMessage from './components/UserMessage.vue';
import AgentMessage from './components/AgentMessage.vue';

const conversations = ref([
    { id: '1', title: 'How do blog entries work?', updated_at: '2 hours ago' },
    { id: '2', title: 'Product catalog questions', updated_at: '1 day ago' },
    { id: '3', title: 'Site navigation help', updated_at: '3 days ago' },
]);

const activeConversationId = ref('1');

const messages = ref([
    { id: '1', role: 'user', content: 'How do blog entries work in this system?' },
    { id: '2', role: 'assistant', content: 'Blog entries are managed through Statamic\'s collection system. Each entry has a title, slug, content fields, and optional metadata like categories and tags.\n\nYou can create entries from the Control Panel, and they\'re stored as markdown files by default. The system supports:\n\n- **Rich text editing** with a visual editor\n- **Custom fields** via blueprints\n- **Taxonomies** for categorization\n- **Revisions** for version history' },
    { id: '3', role: 'user', content: 'Can I query entries programmatically?' },
    { id: '4', role: 'assistant', content: 'Yes! Statamic provides a fluent Entry API. Here\'s a quick example:\n\n```php\nEntry::query()\n    ->where(\'collection\', \'blog\')\n    ->where(\'status\', \'published\')\n    ->orderBy(\'date\', \'desc\')\n    ->limit(10)\n    ->get();\n```\n\nYou can also use Antlers or Blade templates to loop through entries directly in your views.' },
]);

const inputMessage = ref('');
const isLoading = ref(false);

function selectConversation(id) {
    activeConversationId.value = id;
}

function startNewConversation() {
    activeConversationId.value = null;
    messages.value = [];
}

function sendMessage() {
    if (!inputMessage.value.trim() || isLoading.value) return;
    inputMessage.value = '';
}
</script>

<template>
    <Head :title="__('ai-entries-assistant::frontend.assistant.title')" />
    <div class="max-w-page mx-auto">
      <Header :title="__('ai-entries-assistant::frontend.assistant.title')" icon="ai-spark"/>
    </div>
</template>
