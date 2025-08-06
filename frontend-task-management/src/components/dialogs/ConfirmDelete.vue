<script setup>
import { ref, watch } from 'vue';
const props = defineProps({
    title: { type: String, default: 'Confirmación' },
    message: { type: String, default: '¿Estás seguro que deseas eliminar?' },
    isDialogVisible: { type: Boolean, required: true, default: false }
});
const emit = defineEmits(['cancel', 'confirm', 'update:isDialogVisible']);
const isLoading = ref(false);

const onConfirmClick = () => {
    isLoading.value = true;
    emit('confirm');
};

const onCancelClick = () => {
  emit('update:isDialogVisible', false);
};

watch(() => props.isDialogVisible, (newValue) => {
    if (newValue) {
        isLoading.value = false;
    }
});
</script>

<template>
    <dialog v-if="isDialogVisible" class="modal-dialog-centered">
        <div class="modal-content p-3">
            <h5 class="mb-3">{{ title }}</h5>
            <p>{{ message }}</p>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-secondary" @click="onCancelClick">Cancelar</button>
                <button type="button" class="btn btn-danger" @click="onConfirmClick">
                    <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status"
                        aria-hidden="true"></span>
                    <div v-else>Eliminar</div>
                </button>
            </div>
        </div>
    </dialog>
    <div v-if="isDialogVisible" class="modal-backdrop fade show"></div>
</template>


<style scoped>
dialog.modal-dialog-centered {
    all: unset;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 11000;
    max-width: 400px;
    width: 90%;
    border-radius: 0.5rem;
    background-color: white;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    display: block;
    box-sizing: border-box;
}

.modal-backdrop {
    position: fixed;
    z-index: 10000;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.5);
}
</style>
