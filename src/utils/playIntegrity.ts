import { NativeModules, Platform } from 'react-native';

const { PlayIntegrityModule } = NativeModules;

/**
 * Cek apakah Google Play Services tersedia di perangkat pengguna.
 */
export async function isGooglePlayServicesAvailable(): Promise<boolean> {
  if (Platform.OS !== 'android' || !PlayIntegrityModule) {
    return false;
  }
  try {
    return await PlayIntegrityModule.isPlayServicesAvailable();
  } catch {
    return false;
  }
}

/**
 * Cek apakah aplikasi diinstal secara resmi melalui Google Play Store.
 * Memeriksa package installer tanpa mensyaratkan sertifikasi device/Play Protect,
 * sehingga tetap berfungsi untuk perangkat dengan custom ROM / uncertified.
 */
export async function isInstalledFromPlayStore(): Promise<boolean> {
  if (Platform.OS !== 'android' || !PlayIntegrityModule) {
    return true; // Mode dev / emulator non-android diabaikan
  }
  try {
    return await PlayIntegrityModule.isInstalledFromPlayStore();
  } catch {
    return false;
  }
}

/**
 * Dapatkan nama installer package yang memasang aplikasi ini.
 */
export async function getInstallerPackageName(): Promise<string | null> {
  if (Platform.OS !== 'android' || !PlayIntegrityModule) {
    return null;
  }
  try {
    return await PlayIntegrityModule.getInstallerPackageName();
  } catch {
    return null;
  }
}

/**
 * Meminta token integritas dari Google Play Integrity API.
 * Token ini kemudian dikirimkan ke backend Laravel untuk diverifikasi keasliannya.
 * 
 * @param customNonce String acak dari backend (opsional, disarankan untuk mencegah replay attack)
 * @param cloudProjectNumber Nomor proyek Google Cloud (opsional jika sudah ditautkan di Play Console)
 */
export async function getPlayIntegrityToken(
  customNonce?: string,
  cloudProjectNumber?: string
): Promise<string | null> {
  if (Platform.OS !== 'android') {
    return null;
  }

  if (!PlayIntegrityModule) {
    console.warn('[PlayIntegrity] Module tidak tersedia di platform ini.');
    return null;
  }

  try {
    // Generate standard base64 nonce jika tidak disediakan
    const nonce = customNonce || generateClientNonce();
    const token = await PlayIntegrityModule.requestIntegrityToken(nonce, cloudProjectNumber || null);
    return token;
  } catch (error) {
    console.warn('[PlayIntegrity] Gagal mengambil token integritas:', error);
    return null;
  }
}

/**
 * Generate client nonce fallback berbasis timestamp dan random string (URL-safe base64)
 */
function generateClientNonce(): string {
  const randomChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_';
  let result = 'akusehat_' + Date.now().toString(36) + '_';
  for (let i = 0; i < 24; i++) {
    result += randomChars.charAt(Math.floor(Math.random() * randomChars.length));
  }
  return result;
}

