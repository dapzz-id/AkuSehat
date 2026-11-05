/**
 * @format
 */

import 'react-native-reanimated';
import { AppRegistry, LogBox } from 'react-native';
import { getMessaging, setBackgroundMessageHandler } from '@react-native-firebase/messaging';
import App from './App';
import { name as appName } from './app.json';

// Register background message handler for when app is completely closed (killed state)
try {
  const messaging = getMessaging();
  if (messaging) {
    setBackgroundMessageHandler(messaging, async remoteMessage => {
      console.log('[FCM] Background message received when app is closed:', remoteMessage);
    });
  }
} catch (e) {
  // Graceful fallback if firebase app is not initialized
}

LogBox.ignoreAllLogs(true);
AppRegistry.registerComponent(appName, () => App);
