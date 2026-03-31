import {ref, onUnmounted} from 'vue';

/**
 * Listens for new messages (user and AI assistant) via Laravel Echo on a
 * private conversation channel.
 *
 * Connects once when called and stays connected for the component's lifetime.
 * The channel is automatically left when the component unmounts.
 *
 * Requires Laravel Echo to be bootstrapped on `window.Echo` in the host app.
 * If Echo is not available, logs a warning and falls back to a no-op.
 *
 * @see docs/ECHO_SETUP.md for host app configuration instructions.
 */
export function useEchoMessages(conversationId, onNewMessages) {
    const isListening = ref(false);
    const channelName = `conversation.${conversationId}`;

    if (typeof window.Echo === 'undefined') {
        console.warn(
            '[AI Entries Assistant] window.Echo is not available. ' +
            'Install Laravel Echo and a broadcast driver to use real-time updates. ' +
            'See docs/ECHO_SETUP.md for setup instructions.'
        );

        return {isListening, cleanup: () => {}};
    }

    window.Echo.private(channelName)
        .listen('.user.message.created', (event) => {
            onNewMessages([event.message]);
        })
        .listen('.assistant.message.created', (event) => {
            onNewMessages([event.message]);
        });

    isListening.value = true;

    function cleanup() {
        window.Echo.leave(channelName);
        isListening.value = false;
    }

    onUnmounted(cleanup);

    return {isListening, cleanup};
}
