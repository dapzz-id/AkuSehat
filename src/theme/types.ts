export type ThemeMode = 'light' | 'dark';

export type ThemeId = 'emerald' | 'atlas_modern';

export interface ThemeColorPalette {
  primary: string;
  primaryLight: string;
  primaryDark: string;
  secondary: string;
  accent: string;
  bg: string;
  bgElevated: string;
  surface: string;
  surfaceSubtle: string;
  card: string;
  cardBorder: string;
  text: string;
  textSecondary: string;
  muted: string;
  border: string;
  divider: string;
  success: string;
  warning: string;
  error: string;
  info: string;
  glow: string;
  gradientStart: string;
  gradientEnd: string;
  tabBarBg: string;
  tabBarBorder: string;
  tabBarActive: string;
  tabBarInactive: string;
  badgeBg: string;
  badgeBorder: string;
  badgeText: string;
  statusBar: 'light-content' | 'dark-content';
}

export interface ThemeDefinition {
  id: ThemeId;
  name: string;
  tagline: string;
  version: string;
  description: string;
  author: string;
  updated_at: string;
  download_url?: string;
  file_name: string;
  is_downloadable: boolean;
  light: ThemeColorPalette;
  dark: ThemeColorPalette;
  animations: {
    enableGlow: boolean;
    cardSpringDamping: number;
    pulseDurationMs: number;
  };
}

export interface ThemeContextValue {
  themeId: ThemeId;
  theme: ThemeColorPalette;
  themeDefinition: ThemeDefinition;
  mode: ThemeMode;
  isDark: boolean;
  isDownloaded: boolean;
  isDownloading: boolean;
  downloadProgress: number;
  setTheme: (id: ThemeId) => Promise<boolean>;
  setMode: (mode: ThemeMode) => Promise<void>;
  toggleDarkMode: () => Promise<void>;
  downloadTheme: (customUrl?: string) => Promise<{ success: boolean; message: string }>;
  deleteTheme: () => Promise<boolean>;
  getThemeFilePath: () => string;
}
