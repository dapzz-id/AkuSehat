import { Platform, PermissionsAndroid, Alert } from 'react-native';
import {
  getMessaging,
  getToken,
  deleteToken,
  requestPermission,
  onMessage,
  onNotificationOpenedApp,
  getInitialNotification,
  onTokenRefresh,
  AuthorizationStatus,
  RemoteMessage,
} from '@react-native-firebase/messaging';
import api from '../api/axiosConfig';

export interface NotificationPayload {
  title?: string;
  body?: string;
  data?: Record<string, any>;
}

class FcmService {
  private isConfigured = false;
  private currentToken: string | null = null;

  /**
   * Helper aman untuk mengakses instance Firebase Messaging tanpa melempar exception fatal
   * jika Firebase belum terinisialisasi secara native (misal belum ada google-services.json).
   */
  private getSafeMessaging(): any {
    try {
      return getMessaging();
    } catch (e: any) {
      console.warn('[FCM] Firebase Messaging belum siap atau tidak tersedia:', e?.message);
      return null;
    }
  }

  /**
   * Minta izin notifikasi pengguna (Wajib untuk Android 13+ / API 33 & iOS)
   */
  async requestUserPermission(): Promise<boolean> {
    try {
      if (Platform.OS === 'android') {
        if (Platform.Version >= 33) {
          const granted = await PermissionsAndroid.request(
            PermissionsAndroid.PERMISSIONS.POST_NOTIFICATIONS,
            {
              title: 'Izin Notifikasi Aku Sehat',
              message:
                'Izinkan Aku Sehat mengirimkan pemberitahuan penting terkait jadwal konseling, rekam medis, dan pengumuman kesehatan institusi.',
              buttonPositive: 'Izinkan',
              buttonNegative: 'Nanti',
            }
          );
          return granted === PermissionsAndroid.RESULTS.GRANTED;
        }
        return true; // Android 12 ke bawah otomatis diizinkan saat instal
      }

      // iOS Notification Permission
      const messagingInstance = this.getSafeMessaging();
      if (!messagingInstance) {
        return false;
      }
      const authStatus = await requestPermission(messagingInstance);
      return (
        authStatus === AuthorizationStatus.AUTHORIZED ||
        authStatus === AuthorizationStatus.PROVISIONAL
      );
    } catch (error) {
      console.warn('[FCM] Gagal meminta izin notifikasi:', error);
      return false;
    }
  }

  /**
   * Ambil FCM Token perangkat dan sinkronkan ke backend Laravel
   */
  async registerDeviceToken(forceRefresh = false): Promise<string | null> {
    try {
      // 1. Cek izin terlebih dahulu
      const hasPermission = await this.requestUserPermission();
      if (!hasPermission) {
        console.log('[FCM] Izin notifikasi belum diberikan oleh pengguna.');
        return null;
      }

      const messagingInstance = this.getSafeMessaging();
      if (!messagingInstance) {
        return null;
      }

      // 2. Ambil token dari Firebase
      if (forceRefresh) {
        await deleteToken(messagingInstance);
      }
      const token = await getToken(messagingInstance);

      if (!token) {
        return null;
      }

      this.currentToken = token;

      // 3. Kirim ke API Laravel (/me/fcm-token)
      await api.post('/me/fcm-token', {
        fcm_token: token,
        platform: Platform.OS,
        device_model: `${Platform.OS} (SDK ${Platform.Version})`,
      });

      console.log('[FCM] Device Token berhasil disinkronkan ke server.');
      return token;
    } catch (error: any) {
      console.warn('[FCM] Registrasi device token dilewati/gagal:', error?.message);
      return null;
    }
  }

  /**
   * Hapus FCM Token dari backend saat pengguna Logout
   */
  async unregisterDeviceToken(): Promise<void> {
    try {
      if (this.currentToken) {
        await api.delete('/me/fcm-token', {
          data: { fcm_token: this.currentToken },
        });
      } else {
        await api.delete('/me/fcm-token');
      }
      this.currentToken = null;
    } catch (error) {
      console.warn('[FCM] Gagal menghapus token perangkat saat logout:', error);
    }
  }

  /**
   * Setup Listener Notifikasi (Foreground, Background Click, Cold-Start App Closed Launch)
   */
  setupNotificationListeners(
    onNotificationOpened?: (data: Record<string, any>) => void
  ): () => void {
    if (this.isConfigured) {
      return () => {};
    }

    try {
      const messagingInstance = this.getSafeMessaging();
      if (!messagingInstance) {
        return () => {};
      }
      this.isConfigured = true;

      // 1. Notifikasi saat aplikasi berjalan di FOREGROUND (Sedang dibuka)
      const unsubscribeForeground = onMessage(
        messagingInstance,
        async (remoteMessage: RemoteMessage) => {
          console.log('[FCM Foreground Message]:', remoteMessage);

          const title = remoteMessage.notification?.title || remoteMessage.data?.title || 'Aku Sehat';
          const body = remoteMessage.notification?.body || remoteMessage.data?.body || '';

          if (title || body) {
            Alert.alert(String(title), String(body), [{ text: 'OK' }]);
          }
        }
      );

      // 2. Pengguna mengklik notifikasi saat aplikasi berada di BACKGROUND
      const unsubscribeNotificationOpened = onNotificationOpenedApp(
        messagingInstance,
        (remoteMessage: RemoteMessage) => {
          console.log('[FCM Notification Opened from Background]:', remoteMessage);
          if (remoteMessage.data && onNotificationOpened) {
            onNotificationOpened(remoteMessage.data);
          }
        }
      );

      // 3. Pengguna mengklik notifikasi saat aplikasi DITUTUP TOTAL (Cold Start / Killed State)
      getInitialNotification(messagingInstance)
        .then((remoteMessage: RemoteMessage | null) => {
          if (remoteMessage && remoteMessage.data && onNotificationOpened) {
            const notificationData = remoteMessage.data;
            console.log('[FCM Initial Notification from Cold Start]:', remoteMessage);
            setTimeout(() => {
              onNotificationOpened(notificationData);
            }, 800);
          }
        })
        .catch((err: any) => console.warn('[FCM] getInitialNotification error:', err));

      // 4. Token Refresh Listener
      const unsubscribeTokenRefresh = onTokenRefresh(
        messagingInstance,
        async (newToken: string) => {
          console.log('[FCM] Token diperbarui oleh Firebase:', newToken);
          this.currentToken = newToken;
          try {
            await api.post('/me/fcm-token', {
              fcm_token: newToken,
              platform: Platform.OS,
            });
          } catch (e) {
            // Silent fail
          }
        }
      );

      return () => {
        try {
          unsubscribeForeground?.();
          unsubscribeNotificationOpened?.();
          unsubscribeTokenRefresh?.();
        } catch (e) {
          // Silent cleanup
        }
        this.isConfigured = false;
      };
    } catch (err: any) {
      console.warn('[FCM] Gagal menginisialisasi listener notifikasi:', err?.message);
      return () => {};
    }
  }
}

export const fcmService = new FcmService();
export default fcmService;
