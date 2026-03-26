<script setup>
import { Head } from '@statamic/cms/inertia';
import { Header } from '@statamic/cms/ui';

const __ = window.__;
</script>

<template>
    <Head :title="__('ai-entries-chatbot::frontend.chatbot.title')" />
    <div class="max-w-page mx-auto">
        <Header :title="__('ai-entries-chatbot::frontend.chatbot.title')" icon="ai-spark" />
    </div>
</template>