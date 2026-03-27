<script setup>
import { Head } from '@statamic/cms/inertia';
import { Header } from '@statamic/cms/ui';
</script>

<template>
    <Head :title="__('ai-entries-assistant::frontend.conversations.title')" />

    <div class="flex h-full flex-col">
        <Header :title="__('ai-entries-assistant::frontend.conversations.title')" icon="ai-chat-spark" />
    </div>
</template>
