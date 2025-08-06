import api from './config/axios';

export const fetchTasks = async (title = null, is_done = null, keyword_ids = null) => {
    try {
        const params = {};
        if (title !== null && title !== '') params.title = title;
        if (is_done !== null) params.is_done = is_done;
        if (keyword_ids !== null) params.keyword_ids = keyword_ids;

        const response = await api.get('/tasks', { params });
        return response.data.data;
    } catch (error) {
        console.error('Error fetching tasks:', error);
        return [];
    }
};

export const createTask = (taskData) => {
    return api.post('/tasks', taskData);
};

export const toggleTaskStatus = (taskId) => {
    return api.patch(`/tasks/${taskId}/toggleStatus`);
};

export const updateTask = (taskData) => {
    return api.put(`/tasks/${taskData.id}`, taskData);
};

export const deleteTask = (taskId) => {
    return api.delete(`/tasks/${taskId}`);
}
