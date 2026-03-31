import {ref} from 'vue';
import axios from 'axios';

const POLL_INTERVAL_MS = 3000;

/**
 * Polls the messages endpoint for new messages since the last known message.
 *
 * Designed to be started after sending a message and stopped once the AI
 * reply arrives. This is an active fetching strategy — use Echo for
 * passive real-time listening.
 */
export function usePolling(messagesUrl, lastMessageId, onNewMessages) {
    const isPolling = ref(false);
    let intervalId = null;

    async function poll() {
        try {
            const params = {};
            if (lastMessageId.value) {
                params.since = lastMessageId.value;
            }

            const {data: json} = await axios.get(messagesUrl, {params});
            const messages = json.data ?? [];

            if (messages.length > 0) {
                onNewMessages(messages);
            }
        } catch (error) {
            console.error('Polling failed:', error);
        }
    }

    function start() {
        if (intervalId) return;

        isPolling.value = true;
        poll();
        intervalId = setInterval(poll, POLL_INTERVAL_MS);
    }

    function stop() {
        if (intervalId) {
            clearInterval(intervalId);
            intervalId = null;
        }
        isPolling.value = false;
    }

    return {start, stop, isPolling};
}
