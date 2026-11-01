import axios from "axios"
import AsyncStorage from "@react-native-async-storage/async-storage"
import { Config } from "../settings/Config"
import { Alert } from 'react-native';
import { navigationRef } from '../navigation/rootNavigation';
import { createS2sHeaders } from './s2sCrypto';

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

      const isPublicAuthRequest =
        config.url?.includes('/login') ||
        config.url?.includes('/auth/google') ||
        config.url?.includes('/register');

      if (!isPublicAuthRequest) {
        const token = await AsyncStorage.getItem('sanctum_token');
        if (token) {
          config.headers['Authorization'] = `Bearer ${token}`;
        }
      }

      // Attach S2S Cryptographic Request Signature (HMAC-SHA256 + Anti-Replay Nonce)
      if (!(config.data instanceof FormData)) {
        const method = (config.method || 'GET').toUpperCase();
        const basePath = (config.baseURL || '').replace(/^https?:\/\/[^/]+/, '').replace(/\/+$/, '');
        const reqPath = (config.url || '').startsWith('http')
          ? (config.url || '').replace(/^https?:\/\/[^/]+/, '')
          : `${basePath}/${(config.url || '').replace(/^\/+/, '')}`;
        const rawBody =
          config.data === undefined || config.data === null
            ? ''
            : typeof config.data === 'string'
              ? config.data
              : JSON.stringify(config.data);

        const s2sHeaders = createS2sHeaders(method, reqPath, rawBody);
        if (s2sHeaders) {
          config.headers['X-Timestamp'] = s2sHeaders['X-Timestamp'];
          config.headers['X-Nonce'] = s2sHeaders['X-Nonce'];
          config.headers['X-Signature'] = s2sHeaders['X-Signature'];
        }
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
    const requestUrl = error?.config?.url || '';
    const isAuthEndpoint =
      requestUrl.includes('/login') ||
      requestUrl.includes('/auth/google') ||
      requestUrl.includes('/register') ||
      requestUrl.includes('/check-auth');

    if (status === 401 && !isAuthEndpoint) {
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
    } else if (status === 403 && !isAuthEndpoint) {
      const forbiddenMessage = data?.message || 'Akses ditolak: Peran Anda tidak memiliki wewenang untuk aksi ini.';
      Alert.alert('Akses Dibatasi', forbiddenMessage);
    } else if (error.code === 'ECONNABORTED' || !error.response) {
      console.warn('Network timeout or connection error:', error.message);
    }

    return Promise.reject(error);
  }
);

export default api
