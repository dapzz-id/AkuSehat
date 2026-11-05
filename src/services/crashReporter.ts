import { Platform } from 'react-native';
import axios from 'axios';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { Config } from '../settings/Config';

export interface CrashReportPayload {
  error_name: string;
  error_message: string;
  stack_trace?: string;
  component_stack?: string | null;
  app_version: string;
  build_number: number | string;
  platform: string;
  platform_version?: string;
  device_model?: string;
  device_brand?: string;
  user_id?: string;
  user_name?: string;
  user_role?: string;
  instansi_name?: string;
  screen_name?: string;
}

class CrashReporterService {
  private lastSentHash: string = '';

  /**
   * Mengirim laporan crash / unhandled exception otomatis ke Superadmin Crash Analytics.
   */
  public async sendCrashReport(
    error: Error | any,
    componentStack?: string | null,
    screenName?: string
  ): Promise<{ success: boolean; message: string }> {
    try {
      const errorMsg = error?.message || (typeof error === 'string' ? error : 'Unknown Error');
      const errorStack = error?.stack || '';
      const errorName = error?.name || 'JavaScriptException';

      // Anti-duplicate spam check
      const currentHash = `${errorMsg}_${errorStack.slice(0, 60)}`;
      if (this.lastSentHash === currentHash) {
        return { success: true, message: 'Laporan crash sudah pernah dikirim.' };
      }
      this.lastSentHash = currentHash;

      // Ambil data user yang sedang login secara aman
      let userId: string | undefined;
      let userName: string | undefined;
      let userRole: string | undefined;
      let instansiName: string | undefined;

      try {
        const rawUser = await AsyncStorage.getItem('user_data');
        if (rawUser) {
          const u = JSON.parse(rawUser);
          userId = u.id ? String(u.id) : undefined;
          userName = u.nama || u.username;
          userRole = u.level || u.role_label;
          instansiName = u.instansi?.nama_instansi;
        }
      } catch (e) {
        // Lewati jika user_data belum ada
      }

      // Ambil informasi perangkat
      const constants = (Platform as any).constants || {};
      const deviceModel = constants.Model || constants.model || constants.Fingerprint || Platform.OS;
      const deviceBrand = constants.Brand || constants.brand || constants.Manufacturer || Platform.OS;

      const payload: CrashReportPayload = {
        error_name: errorName,
        error_message: errorMsg,
        stack_trace: errorStack,
        component_stack: componentStack,
        app_version: Config.APP_VERSION,
        build_number: 18,
        platform: Platform.OS,
        platform_version: String(Platform.Version),
        device_model: String(deviceModel),
        device_brand: String(deviceBrand),
        user_id: userId,
        user_name: userName,
        user_role: userRole,
        instansi_name: instansiName,
        screen_name: screenName,
      };

      const res = await axios.post(`${Config.API_URL}/crash-reports`, payload, {
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
        },
        timeout: 10000,
      });

      return {
        success: true,
        message: res.data?.message || 'Laporan crash berhasil diterima dan dicatat ke Crash Analytics Superadmin.',
      };
    } catch (err: any) {
      console.warn('[CrashReporter] Gagal mengirim crash report:', err?.message || err);
      return {
        success: false,
        message: 'Gagal mengirim laporan crash ke developer.',
      };
    }
  }
}

export const crashReporter = new CrashReporterService();
export default crashReporter;
