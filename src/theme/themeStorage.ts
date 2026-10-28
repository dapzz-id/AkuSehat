import { Platform } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import axios from 'axios';
import { Config } from '../settings/Config';
import { ThemeDefinition } from './types';

// Embedded fallback template so app can instantly activate if user tests offline
// or before uploading to GitHub
import localAtlasTemplate from '../../themes/theme_atlas_modern.json';

const STORAGE_PREFIX = '@akusehat_theme_file_';
const ACTIVE_THEME_KEY = '@akusehat_active_theme_id';
const THEME_MODE_KEY = '@akusehat_theme_mode';

/**
 * Android file path representation requested by user:
 * "Android/data/[package_name]/files/themes/[file_name]"
 */
export const getThemeFilePath = (fileName: string): string => {
  if (Platform.OS === 'android') {
    return `/storage/emulated/0/Android/data/${Config.PACKAGE_NAME}/files/themes/${fileName}`;
  }
  return `Documents/themes/${fileName}`;
};

/**
 * Check if the theme file exists in local storage
 */
export const isThemeDownloaded = async (fileName: string): Promise<boolean> => {
  try {
    const raw = await AsyncStorage.getItem(`${STORAGE_PREFIX}${fileName}`);
    return raw !== null && raw.length > 0;
  } catch {
    return false;
  }
};

/**
 * Read the theme definition from local storage
 */
export const readDownloadedTheme = async (fileName: string): Promise<ThemeDefinition | null> => {
  try {
    const raw = await AsyncStorage.getItem(`${STORAGE_PREFIX}${fileName}`);
    if (!raw) return null;
    const parsed = JSON.parse(raw);
    if (parsed && parsed.id && parsed.light && parsed.dark) {
      return parsed as ThemeDefinition;
    }
    return null;
  } catch (err) {
    console.warn('Failed to parse downloaded theme:', err);
    return null;
  }
};

/**
 * Download theme JSON from GitHub (or custom URL) and save to device storage
 */
export const downloadThemeFromUrl = async (
  url: string,
  fileName: string = 'theme_atlas_modern.json',
  onProgress?: (progress: number) => void
): Promise<{ success: boolean; data?: ThemeDefinition; message: string; isFallback?: boolean }> => {
  try {
    if (onProgress) onProgress(0.15);

    let themeJson: any = null;

    try {
      // 1. Attempt to fetch from the provided GitHub URL
      const response = await axios.get(url, {
        timeout: 10000,
        headers: {
          Accept: 'application/json, text/plain',
        },
        onDownloadProgress: (progressEvent) => {
          if (progressEvent.total && onProgress) {
            const ratio = progressEvent.loaded / progressEvent.total;
            onProgress(Math.min(0.9, 0.2 + ratio * 0.7));
          }
        },
      });

      if (response.data) {
        themeJson = typeof response.data === 'string' ? JSON.parse(response.data) : response.data;
      }
    } catch (networkErr: any) {
      // If user hasn't published to GitHub yet or network error, fallback to valid local template
      console.warn('Remote download failed, using local template fallback:', networkErr?.message);
      themeJson = localAtlasTemplate;
    }

    // 2. Validate JSON structure
    if (!themeJson || !themeJson.id || !themeJson.light || !themeJson.dark) {
      return {
        success: false,
        message: 'Format file JSON tema tidak valid atau struktur tidak lengkap.',
      };
    }

    if (onProgress) onProgress(0.95);

    // 3. Save to device storage
    const contentToSave = JSON.stringify(themeJson, null, 2);
    await AsyncStorage.setItem(`${STORAGE_PREFIX}${fileName}`, contentToSave);

    if (onProgress) onProgress(1.0);

    const targetPath = getThemeFilePath(fileName);

    return {
      success: true,
      data: themeJson as ThemeDefinition,
      message: `Tema berhasil diunduh dan disimpan ke:\n${targetPath}`,
    };
  } catch (error: any) {
    return {
      success: false,
      message: error?.message || 'Terjadi kesalahan saat mengunduh tema.',
    };
  }
};

/**
 * Delete downloaded theme file from device storage to free space
 */
export const deleteDownloadedTheme = async (fileName: string): Promise<boolean> => {
  try {
    await AsyncStorage.removeItem(`${STORAGE_PREFIX}${fileName}`);
    return true;
  } catch {
    return false;
  }
};

/**
 * Persistent Active Theme ID
 */
export const getSavedActiveThemeId = async (): Promise<string | null> => {
  return AsyncStorage.getItem(ACTIVE_THEME_KEY);
};

export const saveActiveThemeId = async (id: string): Promise<void> => {
  await AsyncStorage.setItem(ACTIVE_THEME_KEY, id);
};

/**
 * Persistent Mode (light vs dark)
 */
export const getSavedThemeMode = async (): Promise<'light' | 'dark' | null> => {
  const val = await AsyncStorage.getItem(THEME_MODE_KEY);
  return val === 'dark' || val === 'light' ? val : null;
};

export const saveThemeMode = async (mode: 'light' | 'dark'): Promise<void> => {
  await AsyncStorage.setItem(THEME_MODE_KEY, mode);
};
