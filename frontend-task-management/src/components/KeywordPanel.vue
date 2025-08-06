<script setup>
import { defineEmits, defineProps, ref } from 'vue';
import KeywordForm from './dialogs/KeywordForm.vue';
import { createKeyword, deleteKeyword } from '@/services/keywordService';
import ConfirmDelete from './dialogs/ConfirmDelete.vue';
import { useToastStore } from '@/store/toastStore';
import NotificationToast from './NotificationToast.vue';

const toastStore = useToastStore();
const props = defineProps({
    keywords: {
        type: Array,
        required: true
    },
    isLoading: {
        type: Boolean,
        default: false
    },
    isDialogVisible: {
        type: Boolean,
        required: true,
        default: false
    }
});

const emit = defineEmits(['update:isDialogVisible', 'syncKeywords']);
const isFormVisible = ref(false);
const isConfirmVisible = ref(false);
const sendingKeywordRequest = ref(false);

// Guardar palabra clave
const saveKeyword = async (keyword) => {
    sendingKeywordRequest.value = true;
    try {
        await createKeyword(keyword.name);
        toastStore.showToast('Palabra clave guardada exitosamente', 'success');
        emit('syncKeywords');
        isFormVisible.value = false;
    } catch (error) {
        console.error('Error guardando palabra clave:', error);
        toastStore.showToast('Error al guardar la palabra clave', 'error');
    } finally {
        sendingKeywordRequest.value = false;
    }
}

const onEditKeyword = (keyword) => emit('openForm', keyword);
const keywordToDelete = ref(null);

const openConfirmDialog = (task) => {
    keywordToDelete.value = task;
    isConfirmVisible.value = true;
};

const closeConfirmDialog = () => {
    isConfirmVisible.value = false;
    keywordToDelete.value = null;
};

const confirmDelete = async () => {
    if (keywordToDelete.value) {
        try {
            await deleteKeyword(keywordToDelete.value);
            toastStore.showToast('Palabra clave eliminada exitosamente', 'success');
            emit('syncKeywords');
        } catch (error) {
            console.error('Error eliminando palabra clave:', error);
            const message = error.response?.data?.message || 'Error eliminando la palabra clave.';
            toastStore.showToast(message, 'error');
        }
    }
    closeConfirmDialog();
};

const onClose = () => emit('update:isDialogVisible', false);
</script>

<template>
    <dialog v-if="isDialogVisible" class="modal-dialog-centered" style="border: none;">
        <div class="modal-overlay" @click="onClose"></div>
        <aside class="side-modal" @click.stop>
            <div class="side-modal-header d-flex justify-content-between align-items-center">
                <h3 class="h4 m-0">Palabras Clave</h3>
                <button type="button" class="btn-close" aria-label="Cerrar" @click="onClose"></button>
            </div>

            <div class="side-modal-content">
                <div class="d-flex justify-content-end align-items-center mb-3">
                    <button class="btn btn-primary" @click="isFormVisible = true">
                        <i class="bi bi-plus-lg me-1"></i> Nueva Palabra Clave
                    </button>
                </div>

                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th style="width: 80px;">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="keywords.length === 0">
                            <td colspan="2" class="text-center text-muted">
                                No hay palabras clave para mostrar.
                            </td>
                        </tr>
                        <tr v-for="keyword in keywords" :key="keyword.id">
                            <td @click="onEditKeyword(keyword)" style="cursor: pointer;">{{ keyword.name }}</td>
                            <td>
                                <button class="btn btn-sm btn-danger" :disabled="isLoading"
                                    @click="openConfirmDialog(keyword.id)" aria-label="Eliminar palabra clave"
                                    title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </aside>
        <KeywordForm v-model:is-dialog-visible="isFormVisible" v-model:isLoading="sendingKeywordRequest"
            @save="saveKeyword" />
        <ConfirmDelete message="¿Está seguro que desea eliminar esta palabra clave?"
            v-model:is-dialog-visible="isConfirmVisible" @confirm="confirmDelete"></ConfirmDelete>
        <NotificationToast />

    </dialog>
</template>

<style scoped>
/* incluido el CSS que ya tenías para modal-overlay y side-modal */
.modal-overlay {
    position: fixed;
    z-index: 1040;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.side-modal {
    position: fixed;
    top: 0;
    right: 0;
    z-index: 1050;
    width: 320px;
    height: 100%;
    background-color: #fff;
    box-shadow: -4px 0 12px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    padding: 1rem;
    overflow-y: auto;
}

.side-modal-header {
    border-bottom: 1px solid #dee2e6;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

.side-modal-content {
    padding-right: 0.2rem;
    flex-grow: 1;
    overflow-y: auto;
    scrollbar-width: thin;
}

.btn-close {
    padding: 0;
    width: 1.5rem;
    height: 1.5rem;
}
</style>
