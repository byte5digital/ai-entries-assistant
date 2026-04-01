import {computed, nextTick, ref, shallowRef} from 'vue';
import axios from 'axios';
import {usePolling} from './usePolling.js';

export function useMessages(messagesUrl, initialMessages) {
    const messagesContainer = shallowRef(null);
    const allMessages = ref([]);
    const nextCursor = shallowRef(null);
    const hasMore = shallowRef(false);
    const loadingOlder = shallowRef(false);

    const newMessage = shallowRef('');
    const sending = shallowRef(false);
    const hasNewInput = computed(() => newMessage.value.trim().length > 0);

    const waitingForReply = shallowRef(false);
    const lastMessageId = computed(() => {
        const messages = allMessages.value;
        return messages.length > 0 ? messages[messages.length - 1].id : null;
    });

    function handleNewMessages(messages) {
        if (messages.length === 0) return;

        const existingIds = new Set(allMessages.value.map(m => m.id));
        const newMsgs = messages.filter(m => !existingIds.has(m.id));

        if (newMsgs.length === 0) return;

        allMessages.value.push(...newMsgs);
        waitingForReply.value = false;
        polling.stop();

        nextTick(() => scrollToBottom());
    }

    const polling = usePolling(messagesUrl, lastMessageId, handleNewMessages);

    function initMessages() {
        const data = initialMessages?.data ?? [];
        allMessages.value = [...data].reverse();
        nextCursor.value = initialMessages?.meta?.next_cursor ?? null;
        hasMore.value = nextCursor.value !== null;
    }

    function scrollToBottom() {
        const el = messagesContainer.value;
        if (el) {
            el.scrollTop = el.scrollHeight;
        }
    }

    async function loadIfNotScrollable() {
        await nextTick();
        const el = messagesContainer.value;
        if (el && hasMore.value && el.scrollHeight <= el.clientHeight) {
            loadOlderMessages();
        }
    }

    async function loadOlderMessages() {
        if (loadingOlder.value || !hasMore.value) return;

        loadingOlder.value = true;
        const el = messagesContainer.value;
        const prevHeight = el.scrollHeight;

        try {
            const url = `${messagesUrl}?cursor=${nextCursor.value}`;
            const {data: json} = await axios.get(url);
            const olderMessages = (json.data ?? []).reverse();

            allMessages.value = [...olderMessages, ...allMessages.value];
            nextCursor.value = json.meta?.next_cursor ?? null;
            hasMore.value = nextCursor.value !== null;

            await nextTick();
            el.scrollTop = el.scrollHeight - prevHeight;
        } finally {
            loadingOlder.value = false;
            loadIfNotScrollable();
        }
    }

    async function sendMessage(storeMessageUrl) {
        if (!hasNewInput.value || sending.value) return;

        sending.value = true;

        try {
            const {data: json} = await axios.post(storeMessageUrl, {
                content: newMessage.value,
            });

            allMessages.value.push(json.data);
            newMessage.value = '';
            waitingForReply.value = true;

            await nextTick();
            scrollToBottom();

            polling.start();
        } finally {
            sending.value = false;
        }
    }

    function onScroll() {
        const el = messagesContainer.value;
        if (el && el.scrollTop < 50 && hasMore.value) {
            loadOlderMessages();
        }
    }

    return {
        messagesContainer,
        allMessages,
        hasMore,
        loadingOlder,
        newMessage,
        sending,
        hasNewInput,
        waitingForReply,
        polling,
        initMessages,
        scrollToBottom,
        loadIfNotScrollable,
        sendMessage,
        onScroll,
    };
}