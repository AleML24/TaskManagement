<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    isDialogVisible: { type: Boolean, required: true, default: false },
    isLoading: { type: Boolean, default: false }
});

const emit = defineEmits(['save', 'update:isDialogVisible']);
const isLoading = ref(false);

const name = ref(null);
 
const save = () => {
    if (!name.value.trim()) return;
    
    emit('save', {name: name.value})
};

const close = () => {
    emit('update:isDialogVisible', false);
};
</script>

<template>
    <dialog v-if="isDialogVisible" open class="modal-dialog-centered">
        <div class="modal-content p-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5>Nueva Palabra Clave</h5>
                <button type="button" class="btn-close" aria-label="Cerrar" @click="close"></button>
            </div>

            <form @submit.prevent="save">
                <div class="mb-3 d-flex flex-content-center align-items-center gap-2">
                    <label for="keyword-name" class="form-label">Nombre: </label>
                    <input id="keyword-name" type="text" class="form-control" v-model="name" required />
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary"
                        @click="emit('update:isDialogVisible', false)">Cancelar</button>
                    <button type="submit" class="btn btn-primary" :disabled="isLoading">
                        <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                        <div v-else>Guardar</div>
                    </button>
                </div>
            </form>
        </div>
    </dialog>
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
</style>
