import AsyncStorage from '@react-native-async-storage/async-storage';
import api from '../api/axiosConfig';

const TOKEN_KEY = 'sanctum_token';
const USER_KEY = 'user';

export const checkAuthStatus = async () => {
  try {
    const token = await AsyncStorage.getItem(TOKEN_KEY);
    if (!token) return null;

    // === FIX PENTING: SET TOKEN KE AXIOS ===
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    try {
      await api.get('/check-auth');
    } catch (error: any) {
      if (
        error?.response?.status === 401 ||
        error?.response?.data?.message === 'Token invalid'
      ) {
        await AsyncStorage.multiRemove([TOKEN_KEY, USER_KEY]);
        return null;
      }
      return null;
    }

    const cached = await AsyncStorage.getItem(USER_KEY);
    if (cached) return JSON.parse(cached);

    try {
      const res = await api.get('/user');
      await AsyncStorage.setItem(USER_KEY, JSON.stringify(res.data.data));
      return res.data.data;
    } catch {
      const res2 = await api.get('/me');
      await AsyncStorage.setItem(USER_KEY, JSON.stringify(res2.data.data));
      return res2.data.data;
    }
  } catch {
    return null;
  }
};

export const login = async (username: string, password: string) => {
  try {
    const { data } = await api.post(
      '/login',
      { username, password },
      {
        headers: {
          'Content-Type': 'application/json',
        },
      },
    );

    if (data?.status === false) {
      return {
        status: false,
        message: data?.message || 'Login gagal'
      };
    }

    const token = data?.token;
    const user = data?.user;

    if (!token) {
      return {
        status: false,
        message: 'Token tidak diterima dari server'
      };
    }

    await AsyncStorage.setItem(TOKEN_KEY, token);
    await AsyncStorage.setItem(USER_KEY, JSON.stringify(user));
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    return {
      status: true,
      user,
      token,
      message: 'Login berhasil'
    };
  } catch (error: any) {
    return {
      status: false,
      message: error?.response?.data?.message || 'Terjadi kesalahan jaringan'
    };
  }
};

export const logout = async () => {
  try {
    await AsyncStorage.multiRemove([TOKEN_KEY, USER_KEY]);
    console.log('logout: local cleared');
  } catch {}

  try {
    await api.post('/logout');
  } catch {}
};
