<script setup>
import { computed, onMounted, ref, watch } from 'vue';
import { createTask, deleteTask, fetchTasks, toggleTaskStatus as toggleTaskStatusService, updateTask } from '../services/taskService';
import { fetchKeyword } from '@/services/keywordService';
import TaskForm from './dialogs/TaskForm.vue';
import ConfirmDelete from './dialogs/ConfirmDelete.vue';
import KeywordForm from './KeywordPanel.vue';
import KeywordPanel from './KeywordPanel.vue';
import { useToastStore } from '@/store/toastStore';
import NotificationToast from './NotificationToast.vue';

const toastStore = useToastStore();

const isLoading = ref(false)
const tasks = ref([]);
const keywords = ref([]);
const filterTitle = ref(null);
const filterStatus = ref(null);
const filterKeywords = ref([]);
const computedKeywords = computed(() => {
    if (filterKeywords.value.length > 0) {
        return filterKeywords.value.map(keyword => keyword.name).join(', ');
    }
    return null
})
const isFormVisible = ref(false);
const taskToEdit = ref(null);

const getTasks = async () => {
    isLoading.value = true;
    try {
        const response = await fetchTasks(filterTitle.value, filterStatus.value, filterKeywords.value.map(keyword => keyword.id));
        tasks.value = response;
    } catch (error) {
        console.error('Error fetching tasks with filters:', error);
        tasks.value = [];
    } finally {
        isLoading.value = false;
    }
};

const toggleTaskStatus = async (taskId) => {
    try {
        isLoading.value = true
        await toggleTaskStatusService(taskId);
        toastStore.showToast('Estado de tarea actualizado correctamente', 'success');

        const taskToUpdate = tasks.value.find(task => task.id === taskId);
        if (taskToUpdate) {
            taskToUpdate.is_done = !taskToUpdate.is_done;
        }

    } catch (error) {
        console.error('Error toggling task status:', error);
        toastStore.showToast('Error al actualizar el estado de la tarea', 'error');
    } finally {
        isLoading.value = false
    }

};

const saveTask = async (taskData) => {
    try {
        if (taskData.id) {
            // Actualizar una tarea existente
            await updateTask(taskData);
            toastStore.showToast('Tarea actualizada correctamente', 'success');
        } else {
            // Crear una tarea
            await createTask(taskData);
            toastStore.showToast('Tarea creada correctamente', 'success');
        }
        await getTasks(); // Recargar la lista de tareas
        closeDialog();
    } catch (error) {
        console.error('Error saving task:', error);
        toastStore.showToast('Error al guardar la tarea', 'error');
    } finally {
        isLoading.value = false;
    }
};

const openDialog = (task = null) => {
    taskToEdit.value = task;
    isFormVisible.value = true;
};

const closeDialog = () => {
    isFormVisible.value = false;
    taskToEdit.value = null;
};

const isConfirmVisible = ref(false);
const taskToDelete = ref(null);

const openConfirmDialog = (taskId) => {
    taskToDelete.value = taskId;
    isConfirmVisible.value = true;
};

const closeConfirmDialog = () => {
    isConfirmVisible.value = false;
    taskToDelete.value = null;
};

const confirmDelete = async () => {
    if (taskToDelete.value) {
        isLoading.value = true;
        try {
            await deleteTask(taskToDelete.value);
            toastStore.showToast('Tarea eliminada correctamente', 'success');
            await getTasks(); // Recargar tareas para reflejar el cambio
        } catch (error) {
            console.error('Error eliminando tarea:', error);
            toastStore.showToast('Error al eliminar la tarea', 'error');
        } finally {
            isLoading.value = false;
            closeConfirmDialog();
        }
    }
};

onMounted(async () => {
    isLoading.value = true;
    try {
        const keywordsResponse = await fetchKeyword();
        keywords.value = keywordsResponse;

        await getTasks();
    } catch (error) {
        console.error('Error during initial fetch:', error);
        toastStore.showToast('Error al cargar los datos', 'error');
    } finally {
        isLoading.value = false;
    }
});

watch([filterStatus, () => keywords.value?.map(k => k.isSelected),
], () => {
    filterKeywords.value = keywords.value.filter(keyword => keyword.isSelected);
    getTasks();
}, { deep: true });

watch(filterTitle, (newValue, oldValue) => {
    if (
        oldValue !== null &&
        (typeof newValue === 'string' || null) &&
        newValue.trim() === ''
    ) {
        getTasks();
    }

});

const isKeywordFormVisible = ref(false);

const syncKeywords = async () => {
    keywords.value = await fetchKeyword()
};

</script>

<template>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h2">Lista de Tareas</h1>

        </div>

        <div class="accordion mb-4" id="accordionFilters">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <strong>Filtros</strong>
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne"
                    data-bs-parent="#accordionFilters">
                    <div class="accordion-body">
                        <div class="row g-3 d-flex justify-content-around">
                            <div class="col-md-4">
                                <label for="filterTitle" class="form-label">Título</label>
                                <div class="input-group mb-3">
                                    <input :disabled="isLoading" type="text" class="form-control" id="filterTitle"
                                        placeholder="Buscar título" v-model="filterTitle" @keypress.enter="getTasks">
                                    <button :disabled="isLoading" class="btn btn-secondary" type="button"
                                        id="button-addon1" @click="getTasks">
                                        <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        <i v-else class="bi bi-search"></i>
                                    </button>

                                </div>
                            </div>
                            <div class="col-md-2">
                                <label for="filterStatus" class="form-label">Estado</label>
                                <select class="form-select" :disabled="isLoading" id="filterStatus"
                                    v-model="filterStatus">
                                    <option :value="null">Todos</option>
                                    <option :value="0">Pendiente</option>
                                    <option :value="1">Completada</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="multiSelect" class="form-label">Palabras Clave</label> <br>

                                <div class="input-group mb-3">
                                    <input :disabled="isLoading" type="text" class="form-control" id="filterTitle"
                                        placeholder="Seleccionar palabras clave" readonly v-model="computedKeywords">
                                    <button :disabled="isLoading" class="btn btn-secondary dropdown-toggle"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        Ver
                                        <span class="d-none d-sm-inline"> Palabras Clave</span>

                                        <span v-if="isLoading" class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                    </button>

                                    <ul class="dropdown-menu">
                                        <li v-for="option in keywords" :key="option.id">
                                            <div class="form-check" @click.stop>
                                                <input class="form-check-input" style="margin-left: -1.2em;"
                                                    type="checkbox" :value="option.isSelected" :id="'check' + option.id"
                                                    v-model="option.isSelected">
                                                <label class="form-check-label" :for="'check' + option.id">
                                                    {{ option.name }}
                                                </label>
                                            </div>
                                        </li>
                                    </ul>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <hr>
        <div class="row justify-content-end align-items-center gap-3 mb-3">
            <div class="col-12 col-sm-auto">
                <button class="btn btn-outline-secondary w-100" type="button" @click="isKeywordFormVisible = true">
                    <i class="bi bi-gear"></i>
                    Gestionar Palabras Clave
                </button>
            </div>
            <div class="col-12 col-sm-auto">
                <button type="button" class="btn btn-primary w-100" @click="openDialog(null)">
                    <i class="bi bi-plus-lg me-2"></i>
                    Nueva Tarea
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th scope="col" style="width: 45%;">Título</th>
                        <th scope="col" style="width: 15%;">Estado</th>
                        <th scope="col" style="width: 30%;">Palabras Clave</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-if="isLoading">
                        <td colspan="4" class="text-center py-3 bg-light">
                            Cargando datos <span class="spinner-border spinner-border-sm" role="status"
                                aria-hidden="true"></span>
                        </td>
                    </tr>
                    <tr v-if="!isLoading && tasks && tasks.length == 0">
                        <td colspan="4" class="text-center py-3 bg-light">
                            No hay datos que mostrar
                        </td>
                    </tr>
                    <tr v-if="!isLoading && tasks && tasks.length > 0" v-for="task in tasks" :key="task.id">
                        <td>{{ task.title }}</td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input" :class="task.is_done ? 'bg-success' : ''"
                                    :disabled="isLoading" type="checkbox" role="switch" :id="'switchCheck' + task.id"
                                    :checked="task.is_done" @change="toggleTaskStatus(task.id)">
                                <label class="form-check-label" :for="'switchCheck' + task.id">
                                    {{ task.is_done ? 'Completada' : 'Pendiente' }}
                                </label>
                            </div>
                        </td>
                        <td>
                            <span v-for="keyword in task.keywords" :key="keyword.id" class="badge bg-secondary me-1">
                                {{ keyword.name }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-secondary" @click="openDialog(task)">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" @click="openConfirmDialog(task.id)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <ConfirmDelete v-model:is-dialog-visible="isConfirmVisible" title="Eliminar tarea" message="¿Seguro que deseas eliminar esta tarea?"
        @cancel="closeConfirmDialog" @confirm="confirmDelete" />

    <KeywordPanel v-model:is-dialog-visible="isKeywordFormVisible" :keywords="keywords" @syncKeywords="syncKeywords" />
    <task-form v-model:is-dialog-visible="isFormVisible" :task="taskToEdit" @save="saveTask"
        v-model:is-loading="isLoading" :keywords="keywords" />
    <NotificationToast />
</template>

<style scoped>
.btn-outline-secondary:hover,
.btn-outline-secondary:focus,
.btn-outline-secondary:active {
    background-color: transparent !important;
    font-weight: 600;
    color: #6c757d;
    box-shadow: none;
}
</style>