import { NativeModules, Platform } from 'react-native';

const { GoogleSignInModule } = NativeModules;

export const GOOGLE_WEB_CLIENT_ID = '941122548461-gb8bo6ktddvrjlt2vgungh288cff4r2s.apps.googleusercontent.com';

export interface GoogleAuthResult {
  idToken: string;
  email: string;
  name: string;
  id: string;
  photoUrl?: string;
}

/**
 * Memunculkan dialog Google Account Picker resmi Android
 * dan mengambil Google ID Token untuk dikirim ke backend Laravel.
 */
export async function promptGoogleSignIn(clientId: string = GOOGLE_WEB_CLIENT_ID): Promise<GoogleAuthResult> {
  if (Platform.OS !== 'android') {
    throw new Error('Google Sign-In hanya didukung pada platform Android saat ini.');
  }

  if (!GoogleSignInModule) {
    throw new Error('Module GoogleSignInModule belum terpasang. Harap jalankan rebuild aplikasi.');
  }

  return await GoogleSignInModule.signIn(clientId);
}

/**
 * Sign out sesi Google lokal jika diperlukan
 */
export async function promptGoogleSignOut(): Promise<boolean> {
  if (Platform.OS !== 'android' || !GoogleSignInModule) {
    return true;
  }
  try {
    return await GoogleSignInModule.signOut();
  } catch {
    return false;
  }
}

