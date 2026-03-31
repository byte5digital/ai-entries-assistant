import {ref, onUnmounted} from 'vue';

/**
 * Listens for new messages via Laravel Echo on a private conversation channel.
 *
 * Unlike polling, Echo connects once on mount and stays connected for the
 * lifetime of the component. Messages are pushed from the server in real-time.
 *
 * Scaffold only — requires:
 *   1. Install @laravel/echo-vue and a broadcast driver (Reverb, Pusher, Soketi)
 *   2. Configure Echo via configureEcho() in your app bootstrap
 *   3. Make AiAssistantMessageAddedToConversation implement ShouldBroadcast
 *   4. Add broadcastOn() returning: new PrivateChannel('conversation.'.$this->conversation->id)
 *   5. Add channel authorization in routes/channels.php
 */
export function useEchoMessages(conversationId, onNewMessages) {
    const isListening = ref(false);

    // TODO: Replace with actual Echo implementation:
    //
    // import {useEcho} from '@laravel/echo-vue';
    //
    // const {leaveChannel} = useEcho(
    //     `conversation.${conversationId}`,
    //     'AiAssistantMessageAddedToConversation',
    //     (event) => {
    //         onNewMessages([event.message]);
    //     },
    // );
    //
    // isListening.value = true;
    //
    // When using useEcho from @laravel/echo-vue, the channel is automatically
    // left when the component unmounts — no manual cleanup needed.

    console.warn(
        '[AI Entries Assistant] Echo strategy is not yet implemented. ' +
        'Install @laravel/echo-vue and a broadcast driver, then update ' +
        'resources/js/composables/useEcho.js. Falling back to no-op.'
    );

    function cleanup() {
        // TODO: call leaveChannel() if manual cleanup is needed
        isListening.value = false;
    }

    onUnmounted(cleanup);

    return {isListening, cleanup};
}
