import {shallowRef} from 'vue';
import axios from 'axios';
import {router} from '@statamic/cms/inertia';

export function useConversationActions(initialTitle, updateTitleUrl, deleteUrl) {
    const title = shallowRef(initialTitle);
    const showRenameModal = shallowRef(false);
    const renameInput = shallowRef('');
    const renaming = shallowRef(false);
    const showDeleteModal = shallowRef(false);
    const deleting = shallowRef(false);

    function openRenameModal() {
        renameInput.value = title.value;
        showRenameModal.value = true;
    }

    async function submitRename() {
        if (renaming.value || !renameInput.value.trim()) return;

        renaming.value = true;
        try {
            const {data: response} = await axios.patch(updateTitleUrl, {
                title: renameInput.value.trim(),
            });
            title.value = response.data.title;
            showRenameModal.value = false;
            return response.data.title;
        } finally {
            renaming.value = false;
        }
    }

    function deleteConversation() {
        if (deleting.value) return;

        deleting.value = true;
        router.delete(deleteUrl, {
            onFinish: () => deleting.value = false,
        });
    }

    return {
        title,
        showRenameModal,
        renameInput,
        renaming,
        showDeleteModal,
        deleting,
        openRenameModal,
        submitRename,
        deleteConversation,
    };
}