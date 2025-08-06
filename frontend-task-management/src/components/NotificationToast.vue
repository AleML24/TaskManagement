<script setup>
import { storeToRefs } from 'pinia';
import { useToastStore } from '@/store/toastStore';

const toastStore = useToastStore();
const { message, type, isVisible } = storeToRefs(toastStore);
</script>

<template>
    <transition name="fade">
        <div v-if="isVisible" :class="['tooltip-toast', type]">
            <span class="icon">
                <template v-if="type === 'success'"><i class="bi bi-check-circle"></i></template>
                <template v-else-if="type === 'error'"><i class="bi bi-exclamation-circle"></i></template>
            </span>
            <span class="message">{{ message }}</span>
        </div>
    </transition>
</template>

<style scoped>
.tooltip-toast {
    position: fixed;
    bottom: 20px;
    right: 20px;
    min-width: 250px;
    max-width: 350px;
    background-color: #333;
    color: white;
    padding: 12px 20px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    font-weight: 600;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    z-index: 11000;
    user-select: none;
}

.tooltip-toast .icon {
    margin-right: 12px;
    font-size: 18px;
}

.tooltip-toast.success {
    background-color: #28a745;
}

.tooltip-toast.error {
    background-color: #dc3545;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
