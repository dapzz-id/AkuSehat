import axios from "axios"
import AsyncStorage from "@react-native-async-storage/async-storage"
import { Config } from "../settings/Config"
import { Alert } from 'react-native';
import { navigationRef } from '../navigation/rootNavigation';

const api = axios.create({
  baseURL: Config.API_URL,
  timeout: Config.AXIOS_TIMEOUT,
  withCredentials: Config.AXIOS_CREDENTIALS,
})

// Default headers to guarantee Laravel recognizes API requests
api.defaults.headers.common['Accept'] = 'application/json';

api.interceptors.request.use(async (config) => {
  try {
    if (config.headers) {
      config.headers['Accept'] = 'application/json';
      if (!config.headers['Content-Type'] && !(config.data instanceof FormData)) {
        config.headers['Content-Type'] = 'application/json';
      }

      const token = await AsyncStorage.getItem('sanctum_token');
      if (token) {
        config.headers['Authorization'] = `Bearer ${token}`;
      }
    }
  } catch (e) {
    console.log('Error preparing request headers', e);
  }
  return config;
});

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    const status = error?.response?.status;
    const data = error?.response?.data;

    if (status === 401) {
      // Clear all stored credentials and authorization header
      await AsyncStorage.multiRemove(['sanctum_token', 'user', 'userData', 'userToken']);
      delete api.defaults.headers.common['Authorization'];

      const serverMessage = data?.message || 'Sesi Anda telah berakhir. Silakan login kembali.';

      if (navigationRef.isReady()) {
        navigationRef.reset({
          index: 0,
          routes: [{ name: 'Login' }],
        });
      }
      Alert.alert('Sesi Berakhir', serverMessage);
    } else if (status === 403) {
      const forbiddenMessage = data?.message || 'Akses ditolak: Peran Anda tidak memiliki wewenang untuk aksi ini.';
      Alert.alert('Akses Dibatasi', forbiddenMessage);
    } else if (error.code === 'ECONNABORTED' || !error.response) {
      console.warn('Network timeout or connection error:', error.message);
    }

    return Promise.reject(error);
  }
);

// api.interceptors.response.use(
//   (res) => res,
//   async (error) => {
//     if (error?.response?.status === 401) {
//       try {
//         await AsyncStorage.removeItem("sanctum_token")
//       } catch {}
//     }
//     return Promise.reject(error)
//   },
// )

export default api
