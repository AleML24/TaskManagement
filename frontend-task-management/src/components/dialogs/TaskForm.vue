<script setup scoped>
import { ref, watch, defineEmits, onMounted, computed } from 'vue';
const props = defineProps({
    task: {
        type: Object,
        default: null
    },
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
        default: false
    }
});

const emit = defineEmits(['save', 'update:isDialogVisible', 'update:isLoading']);
const allKeywords = ref(props.keywords.map(k => ({ ...k, isSelected: false })));
const computedKeywords = computed(() => {
    form.value.keyword_ids = allKeywords.value
        .filter(k => k.isSelected)
        .map(k => k.id);
    return allKeywords.value
        .filter(k => k.isSelected)
        .map(k => k.name)
        .join(', ');
});
const isLoading = ref(false);

const form = ref({
    id: null,
    title: '',
    is_done: false,
    keyword_ids: []
});

watch(
    () => [props.keywords, props.task],
    ([newKeywords, newTask]) => {
        if (newKeywords && newKeywords.length > 0) {
            const selectedIds = newTask ? newTask.keywords.map(k => k.id) : [];
            allKeywords.value = newKeywords.map(k => ({
                ...k,
                isSelected: selectedIds.includes(k.id)
            }));
        } else {
            allKeywords.value = [];
        }

        if (newTask) {
            form.value.id = newTask.id;
            form.value.title = newTask.title;
            form.value.is_done = newTask.is_done;
            form.value.keyword_ids = newTask.keywords.map(k => k.id);
        } else {
            form.value.id = null;
            form.value.title = '';
            form.value.is_done = false;
            form.value.keyword_ids = [];
        }
    },
    { immediate: true }
);



const saveTask = () => {
    emit('update:isLoading', true);  // avisar al padre que inicia carga
    emit('save', form.value);        // padre debe manejar la promesa y actualizar isLoading
};

const closeDialog = () => {
    emit('update:isDialogVisible', false);
    emit('update:isLoading', false); // limpia carga al cerrar
};

</script>

<template>
    <dialog v-if="isDialogVisible" open class="modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-between align-items-center mb-3">
                <h5 class="modal-title">{{ form.id ? 'Editar Tarea' : 'Nueva Tarea' }}</h5>
                <button type="button" class="btn-close" @click="closeDialog" aria-label="Close"></button>
            </div>
            <form @submit.prevent="saveTask">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="task-title" class="form-label">Título:</label>
                        <textarea class="form-control" id="task-title" v-model="form.title" required
                            rows="2"></textarea>
                    </div>

                    <div class="d-flex gap-3 align-content-center ">
                        <label for="task-title" class="form-check-label">Estado:</label>
                        <div class="mb-3 form-check form-switch d-flex gap-2 align-items-center ">
                            <input class="form-check-input" type="checkbox" id="task-status" v-model="form.is_done">
                            <label class="form-check-label" for="task-status">
                                {{ form.is_done ? 'Completada' : 'Pendiente' }}
                            </label>
                        </div>
                    </div>
                    <div>
                        <label for="filterKeywords" class="form-label">Palabras Clave:</label><br>

                        <div class="input-group mb-3">
                            <textarea :disabled="isLoading" type="text" class="form-control" id="filterKeywords"
                                placeholder="Seleccionar palabras clave" rows="2" :value="computedKeywords" readonly />

                            <button :disabled="isLoading" class="btn btn-secondary dropdown-toggle" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Ver
                                <span class="d-none d-sm-inline"> Palabras Clave</span>

                                <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status"
                                    aria-hidden="true"></span>
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end p-3"
                                style="min-width: 220px; max-height: 200px; overflow-y: auto;">
                                <li v-for="option in allKeywords" :key="option.id">
                                    <div class="form-check" @click.stop>
                                        <input class="form-check-input" type="checkbox" :id="'check-form' + option.id"
                                            v-model="option.isSelected" style="margin-left: -1.2em;">
                                        <label readonly class="form-check-label" :for="'check-form' + option.id">
                                            {{ option.name }}
                                        </label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" @click="closeDialog">
                        <span v-if="props.isLoading" class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span><div v-else>Cancelar</div></button>
                    <button type="submit" class="btn btn-primary">
                        <span v-if="props.isLoading" class="spinner-border spinner-border-sm" role="status"
                            aria-hidden="true"></span>
                        <div v-else>Guardar</div>
                    </button>
                </div>
            </form>
        </div>
    </dialog>
    <div v-if="isDialogVisible" class="modal-backdrop fade show"></div>
</template>

<style scoped>
dialog.modal-dialog-centered {
    all: unset;
    position: fixed;
    z-index: 10000;
    width: 90%;
    max-width: 350px;
    border: 1px solid white;
    border-radius: 0.5rem;
    background-color: white;
    padding: 0;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
    display: block;
    box-sizing: border-box;
}



/* Opcional: media queries para controlar el ancho */
@media(min-width: 576px) {

    /* sm */
    dialog.modal-dialog-centered {
        max-width: 400px;
    }
}

@media(min-width: 768px) {

    /* md */
    dialog.modal-dialog-centered {
        max-width: 500px;
    }
}

@media(min-width: 992px) {

    /* lg */
    dialog.modal-dialog-centered {
        max-width: 500px;
    }
}


dialog::backdrop {
    background: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fff;
    padding: 1rem;

}
</style>