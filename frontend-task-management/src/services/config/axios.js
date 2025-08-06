import { useToastStore } from '@/store/toastStore';
import axios from 'axios';

const baseURL = import.meta.env.VITE_API_BASE_URL + "/api";

const api = axios.create({
  baseURL: baseURL,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  }
});


api.interceptors.response.use(
  response => response,
  error => {
    const toastStore = useToastStore();

    const message = error.response?.data?.message || 'Error desconocido';
    toastStore.showToast(message, 'error');

    return Promise.reject(error);
  }
);

export default api;