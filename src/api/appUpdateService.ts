import { Platform } from 'react-native';
import api from './axiosConfig';
import { Config } from '../settings/Config';

export interface SoftwareReleaseData {
  id: string | number;
  name: string;
  version: string;
  min_version?: string | null;
  platform: string;
  download_url?: string | null;
  developer?: string;
  description?: string;
  changelog?: string | null;
  release_date?: string | null;
  release_date_formatted?: string | null;
  is_mandatory?: boolean;
}

export interface UpdateCheckResponse {
  success: boolean;
  isOffline?: boolean;
  has_update: boolean;
  is_mandatory: boolean;
  is_maintenance?: boolean;
  message?: string;
  data?: SoftwareReleaseData | null;
}

export interface ChangelogResponse {
  success: boolean;
  data: SoftwareReleaseData[];
  error?: string;
}

/**
 * Check if a newer version of the mobile app is available
 */
export const checkAppUpdate = async (
  currentVersion: string = Config.APP_VERSION,
  platform: string = Platform.OS === 'ios' ? 'ios' : 'android'
): Promise<UpdateCheckResponse> => {
  try {
    const response = await api.get('/app-update/check', {
      params: {
        platform,
        current_version: currentVersion,
      },
      timeout: 8000,
    });

    if (response.data && response.data.success !== undefined) {
      return {
        success: true,
        isOffline: false,
        has_update: !!response.data.has_update,
        is_mandatory: !!response.data.is_mandatory,
        is_maintenance: !!response.data.is_maintenance,
        message: response.data.message,
        data: response.data.data,
      };
    }

    return {
      success: true,
      isOffline: false,
      has_update: false,
      is_mandatory: false,
      data: null,
    };
  } catch (error: any) {
    const isNetworkError =
      !error.response ||
      error.code === 'ECONNABORTED' ||
      error.code === 'ERR_NETWORK' ||
      error.message?.toLowerCase().includes('network');

    return {
      success: false,
      isOffline: isNetworkError,
      has_update: false,
      is_mandatory: false,
      message: error?.message || 'Gagal memverifikasi versi aplikasi',
      data: null,
    };
  }
};

/**
 * Fetch changelog / patch notes history for the mobile platform
 */
export const fetchAppChangelogs = async (
  platform: string = Platform.OS === 'ios' ? 'ios' : 'android'
): Promise<ChangelogResponse> => {
  try {
    const response = await api.get('/app-update/history', {
      params: { platform },
      timeout: 10000,
    });

    if (response.data && response.data.success && Array.isArray(response.data.data)) {
      return {
        success: true,
        data: response.data.data,
      };
    }

    return {
      success: true,
      data: [],
    };
  } catch (error: any) {
    return {
      success: false,
      data: [],
      error: error?.message || 'Gagal memuat catatan pembaruan',
    };
  }
};
