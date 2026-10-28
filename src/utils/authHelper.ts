import AsyncStorage from '@react-native-async-storage/async-storage';
import api from '../api/axiosConfig';

const TOKEN_KEY = 'sanctum_token';
const USER_KEY = 'user';

export const checkAuthStatus = async () => {
  try {
    const token = await AsyncStorage.getItem(TOKEN_KEY);
    if (!token) return null;

    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    try {
      const res = await api.get('/check-auth');
      if (res?.data?.user) {
        await AsyncStorage.setItem(USER_KEY, JSON.stringify(res.data.user));
        return res.data.user;
      }
    } catch (error: any) {
      if (
        error?.response?.status === 401 ||
        error?.response?.data?.message === 'Token invalid'
      ) {
        await AsyncStorage.multiRemove([TOKEN_KEY, USER_KEY]);
        delete api.defaults.headers.common['Authorization'];
        return null;
      }
    }

    const cached = await AsyncStorage.getItem(USER_KEY);
    if (cached) return JSON.parse(cached);

    try {
      const res2 = await api.get('/me');
      const userData = res2?.data?.data || res2?.data?.user || res2?.data;
      if (userData) {
        await AsyncStorage.setItem(USER_KEY, JSON.stringify(userData));
        return userData;
      }
    } catch {
      return null;
    }

    return null;
  } catch {
    return null;
  }
};

export const login = async (username: string, password: string) => {
  try {
    const { data } = await api.post(
      '/login',
      { login: username, username, password },
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

export const loginWithGoogle = async (googleData: { email: string; name: string; google_id: string; avatar?: string }) => {
  try {
    const { data } = await api.post('/auth/google', googleData);

    if (data?.status === false) {
      return {
        status: false,
        message: data?.message || 'Login Google gagal: Akun belum terdaftar di sistem.'
      };
    }

    const token = data?.token;
    const user = data?.user;

    if (!token || !user) {
      return {
        status: false,
        message: 'Data autentikasi tidak valid dari server.'
      };
    }

    await AsyncStorage.setItem(TOKEN_KEY, token);
    await AsyncStorage.setItem(USER_KEY, JSON.stringify(user));
    api.defaults.headers.common['Authorization'] = `Bearer ${token}`;

    return {
      status: true,
      user,
      token,
      message: 'Login Google berhasil'
    };
  } catch (error: any) {
    return {
      status: false,
      message: error?.response?.data?.message || 'Login Google gagal: Akun belum terdaftar di sistem. Akun dibuat oleh Superadmin atau Admin Instansi Anda.'
    };
  }
};

export const register = async (registerData: {
  name: string;
  username: string;
  email: string;
  password: string;
  password_confirmation: string;
  invite_code?: string;
}) => {
  try {
    const { data } = await api.post('/register', registerData);

    if (data?.status === false) {
      return {
        status: false,
        message: data?.message || 'Registrasi gagal.',
      };
    }

    const token = data?.token;
    const user = data?.user;

    if (token && user) {
      await AsyncStorage.setItem(TOKEN_KEY, token);
      await AsyncStorage.setItem(USER_KEY, JSON.stringify(user));
      api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }

    return {
      status: true,
      user,
      token,
      message: data?.message || 'Registrasi berhasil',
    };
  } catch (error: any) {
    return {
      status: false,
      message: error?.response?.data?.message || 'Pendaftaran mandiri dinonaktifkan. Akun siswa/member dibuat dan dikelola langsung oleh Admin Institusi / Instansi terkait.',
    };
  }
};

export const logout = async () => {
  try {
    await AsyncStorage.multiRemove([TOKEN_KEY, USER_KEY, 'userData', 'userToken']);
    delete api.defaults.headers.common['Authorization'];
    console.log('logout: local cleared');
  } catch {}

  try {
    await api.post('/logout');
  } catch {}
};
