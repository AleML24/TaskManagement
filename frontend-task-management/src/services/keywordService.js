import api from './config/axios';

export const fetchKeyword = async () => {
    try {
        const response = await api.get('/keywords');
        return response.data.data;
    } catch (error) {
        console.error('Error fetching keywords:', error);
        return [];
    }
};

export const createKeyword = async (keyword) => {
    return await api.post('/keywords', { name: keyword });
};

export const editKeyword = (keywordId) => {
    return api.patch(`/keywords/${keywordId}/toggleStatus`);
};

export const deleteKeyword = (keywordId) => {
    return api.delete(`/keywords/${keywordId}`);
};