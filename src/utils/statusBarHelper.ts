import { NativeModules, Platform } from 'react-native';

/**
 * Mengatur warna background status bar dan navigation bar di Android secara dinamis
 * sesuai tema aplikasi saat ini.
 *
 * @param backgroundColor Kode warna HEX background (misal '#F8FAFC' atau '#0F172A')
 * @param isDark Apakah tema saat ini dalam mode gelap (true = ikon putih, false = ikon hitam)
 */
export const setAppStatusBarTheme = (backgroundColor: string, isDark: boolean) => {
  if (Platform.OS === 'android' && NativeModules.StatusBarColorModule?.setColor) {
    try {
      NativeModules.StatusBarColorModule.setColor(backgroundColor, isDark);
    } catch (e) {
      // Safe fallback
    }
  }
};

export default setAppStatusBarTheme;
