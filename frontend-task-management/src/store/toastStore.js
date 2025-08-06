import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useToastStore = defineStore('toast', () => {
    const message = ref('');
    const type = ref('success'); // o 'error'
    const isVisible = ref(false);
    let timer = null;

    function showToast(msg, toastType = 'success', duration = 3000) {
        message.value = msg;
        type.value = toastType;
        isVisible.value = true;

        if (timer) clearTimeout(timer);
        timer = setTimeout(() => {
            isVisible.value = false;
        }, duration);
    }

    function hideToast() {
        isVisible.value = false;
        if (timer) clearTimeout(timer);
    }

    return {
        message,
        type,
        isVisible,
        showToast,
        hideToast
    };
});
