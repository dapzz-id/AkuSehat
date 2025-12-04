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

api.interceptors.request.use(async (config) => {
  try {
    const token = await AsyncStorage.getItem("sanctum_token")
    if (token) {
      if (config.headers) {
        config.headers['Authorization'] = `Bearer ${token}`;
        config.headers['Accept'] = "application/json";
        config.headers['Content-Type'] = "application/json";
      }
    }
  } catch (e) {
    console.log("Error getting token", e)
  }
  return config
})

api.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error?.response?.status === 401) {
      await AsyncStorage.multiRemove(['sanctum_token', 'user']);

      if (navigationRef.isReady()) {
        navigationRef.reset({
          index: 0,
          routes: [{ name: 'Login' }],
        });
      } else {
        Alert.alert('Sesi Berakhir', 'Silakan login kembali.');
      }
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
