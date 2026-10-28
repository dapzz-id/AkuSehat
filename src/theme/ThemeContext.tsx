import React, { createContext, useContext, useEffect, useState, useCallback, useMemo } from 'react';
import { useColorScheme, Alert } from 'react-native';
import { ThemeContextValue, ThemeDefinition, ThemeId, ThemeMode, ThemeColorPalette } from './types';
import { emeraldTheme } from './emeraldTheme';
import {
  isThemeDownloaded,
  readDownloadedTheme,
  downloadThemeFromUrl,
  deleteDownloadedTheme,
  getSavedActiveThemeId,
  saveActiveThemeId,
  getSavedThemeMode,
  saveThemeMode,
  getThemeFilePath,
} from './themeStorage';
import { Config } from '../settings/Config';

const MODERN_FILE_NAME = 'theme_atlas_modern.json';

const ThemeContext = createContext<ThemeContextValue | null>(null);

export const ThemeProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const systemColorScheme = useColorScheme();

  const [themeId, setThemeIdState] = useState<ThemeId>('emerald');
  const [mode, setModeState] = useState<ThemeMode>(systemColorScheme === 'dark' ? 'dark' : 'light');
  const [modernThemeDef, setModernThemeDef] = useState<ThemeDefinition | null>(null);
  const [isDownloaded, setIsDownloaded] = useState<boolean>(false);
  const [isDownloading, setIsDownloading] = useState<boolean>(false);
  const [downloadProgress, setDownloadProgress] = useState<number>(0);

  // Check storage and initial preferences on mount
  useEffect(() => {
    const initializeTheme = async () => {
      // 1. Check if modern theme is downloaded in storage
      const downloaded = await isThemeDownloaded(MODERN_FILE_NAME);
      setIsDownloaded(downloaded);

      if (downloaded) {
        const customDef = await readDownloadedTheme(MODERN_FILE_NAME);
        if (customDef) {
          setModernThemeDef(customDef);
        }
      }

      // 2. Read saved theme selection
      const savedThemeId = await getSavedActiveThemeId();
      if (savedThemeId === 'atlas_modern' && downloaded) {
        setThemeIdState('atlas_modern');
      } else {
        setThemeIdState('emerald');
      }

      // 3. Read saved mode
      const savedMode = await getSavedThemeMode();
      if (savedMode) {
        setModeState(savedMode);
      }
    };

    initializeTheme();
  }, []);

  // Current active theme definition
  const currentThemeDef: ThemeDefinition = useMemo(() => {
    if (themeId === 'atlas_modern' && modernThemeDef) {
      return modernThemeDef;
    }
    return emeraldTheme;
  }, [themeId, modernThemeDef]);

  // Current active color palette: Emerald is strictly Light Mode; Dark Mode is exclusive to Atlas Neo Modern
  const theme: ThemeColorPalette = useMemo(() => {
    const effectiveMode = themeId === 'atlas_modern' ? mode : 'light';
    return effectiveMode === 'dark' ? currentThemeDef.dark : currentThemeDef.light;
  }, [currentThemeDef, mode, themeId]);

  const isDark = themeId === 'atlas_modern' && mode === 'dark';

  // Switch Theme
  const setTheme = useCallback(
    async (newId: ThemeId): Promise<boolean> => {
      if (newId === 'atlas_modern') {
        const downloaded = await isThemeDownloaded(MODERN_FILE_NAME);
        if (!downloaded) {
          Alert.alert(
            'Tema Belum Diunduh',
            'Tema Atlas Neo Modern harus diunduh terlebih dahulu sebelum dapat diterapkan. Silakan unduh melalui menu pengaturan tema.',
            [{ text: 'Mengerti' }]
          );
          return false;
        }

        let def = modernThemeDef;
        if (!def) {
          def = await readDownloadedTheme(MODERN_FILE_NAME);
          if (def) {
            setModernThemeDef(def);
          }
        }

        if (!def) {
          Alert.alert(
            'Tema Belum Diunduh',
            'Tema Atlas Neo Modern harus diunduh terlebih dahulu sebelum dapat diterapkan. Silakan unduh melalui menu pengaturan tema.',
            [{ text: 'Mengerti' }]
          );
          return false;
        }
      } else {
        // When switching back to Emerald Classic, always reset to Light Mode
        setModeState('light');
        await saveThemeMode('light');
      }

      setThemeIdState(newId);
      await saveActiveThemeId(newId);
      return true;
    },
    [modernThemeDef]
  );

  // Switch Mode (light / dark) - Dark Mode is exclusive to Atlas Neo Modern
  const setMode = useCallback(
    async (newMode: ThemeMode) => {
      if (themeId !== 'atlas_modern' && newMode === 'dark') {
        Alert.alert(
          'Mode Gelap Eksklusif',
          'Mode Gelap hanya tersedia untuk tema Atlas Neo Modern. Silakan terapkan tema Atlas Neo Modern terlebih dahulu.',
          [{ text: 'Mengerti' }]
        );
        return;
      }
      setModeState(newMode);
      await saveThemeMode(newMode);
    },
    [themeId]
  );

  const toggleDarkMode = useCallback(async () => {
    if (themeId !== 'atlas_modern') {
      Alert.alert(
        'Mode Gelap Eksklusif',
        'Mode Gelap hanya tersedia untuk tema Atlas Neo Modern. Silakan terapkan tema Atlas Neo Modern terlebih dahulu.',
        [{ text: 'Mengerti' }]
      );
      return;
    }
    const nextMode = mode === 'dark' ? 'light' : 'dark';
    await setMode(nextMode);
  }, [themeId, mode, setMode]);

  // Download Theme from GitHub / Custom URL
  const downloadTheme = useCallback(
    async (customUrl?: string): Promise<{ success: boolean; message: string }> => {
      setIsDownloading(true);
      setDownloadProgress(0.05);

      const targetUrl = customUrl || Config.DEFAULT_MODERN_THEME_URL;

      try {
        const result = await downloadThemeFromUrl(targetUrl, MODERN_FILE_NAME, (ratio) => {
          setDownloadProgress(ratio);
        });

        if (result.success && result.data) {
          setModernThemeDef(result.data);
          setIsDownloaded(true);
          setDownloadProgress(1.0);
          return { success: true, message: result.message };
        } else {
          return { success: false, message: result.message || 'Gagal mengunduh tema.' };
        }
      } catch (err: any) {
        return { success: false, message: err?.message || 'Gagal mengunduh tema.' };
      } finally {
        setTimeout(() => {
          setIsDownloading(false);
          setDownloadProgress(0);
        }, 500);
      }
    },
    []
  );

  // Delete Downloaded Theme
  const deleteTheme = useCallback(async (): Promise<boolean> => {
    const success = await deleteDownloadedTheme(MODERN_FILE_NAME);
    if (success) {
      setIsDownloaded(false);
      setModernThemeDef(null);
      if (themeId === 'atlas_modern') {
        setThemeIdState('emerald');
        await saveActiveThemeId('emerald');
      }
    }
    return success;
  }, [themeId]);

  const value: ThemeContextValue = {
    themeId,
    theme,
    themeDefinition: currentThemeDef,
    mode,
    isDark,
    isDownloaded,
    isDownloading,
    downloadProgress,
    setTheme,
    setMode,
    toggleDarkMode,
    downloadTheme,
    deleteTheme,
    getThemeFilePath: () => getThemeFilePath(MODERN_FILE_NAME),
  };

  return <ThemeContext.Provider value={value}>{children}</ThemeContext.Provider>;
};

export const useTheme = (): ThemeContextValue => {
  const context = useContext(ThemeContext);
  if (!context) {
    throw new Error('useTheme must be used within a ThemeProvider');
  }
  return context;
};

export default ThemeContext;
