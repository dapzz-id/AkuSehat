import React, { useEffect, useState } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { View, StyleSheet, StatusBar } from 'react-native';
import { checkAuthStatus } from '../utils/authHelper';
import { navigationRef } from '../navigation/rootNavigation';
import { fcmService } from '../services/fcmService';
import SplashScreen from '../screens/SplashScreen';
import { DebugModal, FloatingDebugButton } from '../components/DebugModal';
import { GlobalErrorBoundary } from '../components/GlobalErrorBoundary';
import debugLogger from '../services/debugLogger';

// Screens untuk Member
import HomeScreenMember from '../screens/member/MainScreen';

// Screens untuk Health Consultant
import HomeScreenHealthConsultant from '../screens/health-consultant/MainScreen';

// Screens untuk Health Monitor
import HomeScreenHealthMonitor from '../screens/health-monitor/MainScreen';

// Auth Screens
import LoginScreen from '../screens/auth/LoginScreen';
import RegisterScreen from '../screens/auth/RegisterScreen';
import { ThemeProvider } from '../theme/ThemeContext';

const Stack = createNativeStackNavigator();

const AppNavigatorContent = () => {
  const [isAuthChecked, setIsAuthChecked] = useState(false);
  const [initialRoute, setInitialRoute] = useState('Login');
  const [showSplash, setShowSplash] = useState(true);
  const [isDebugVisible, setIsDebugVisible] = useState(false);

  useEffect(() => {
    // Dengarkan auto-open trigger ketika terjadi error 500 atau unhandled exception
    const unsubscribeAutoOpen = debugLogger.onAutoOpenTrigger(() => {
      setIsDebugVisible(true);
    });

    return () => {
      unsubscribeAutoOpen();
    };
  }, []);

  useEffect(() => {
    let isMounted = true;

    const checkLogin = async () => {
      try {
        const user = await checkAuthStatus();
        if (user && user.level && isMounted) {
          const level = user.level.toLowerCase();

          if (level === 'member') {
            setInitialRoute('HomeMember');
          } else if (level === 'health consultant') {
            setInitialRoute('HomeHealthConsultant');
          } else if (level === 'health monitor') {
            setInitialRoute('HomeHealthMonitor');
          } else {
            setInitialRoute('Login');
          }

          // Inisialisasi token FCM dan listener saat user terotentikasi
          fcmService.registerDeviceToken();
        } else if (isMounted) {
          setInitialRoute('Login');
        }
      } catch (error) {
        console.log('Error checking auth:', error);
        if (isMounted) {
          setInitialRoute('Login');
        }
      } finally {
        if (isMounted) {
          setIsAuthChecked(true);
        }
      }
    };

    checkLogin();

    let unsubscribeFcm = () => {};
    try {
      unsubscribeFcm = fcmService.setupNotificationListeners((data) => {
        console.log('[AppNavigator] Notifikasi FCM diklik:', data);
      });
    } catch (e) {
      console.warn('[AppNavigator] Error setting up FCM:', e);
    }

    return () => {
      isMounted = false;
      try {
        unsubscribeFcm();
      } catch (e) {
        // Safe cleanup
      }
    };
  }, []);
  return (
    <View style={styles.root}>
      <StatusBar barStyle="dark-content" />
      {!showSplash && isAuthChecked && (
        <NavigationContainer ref={navigationRef}>
          <Stack.Navigator
            screenOptions={{ headerShown: false }}
            initialRouteName={initialRoute}
          >
            {/* Auth */}
            <Stack.Screen name="Login" component={LoginScreen} />
            <Stack.Screen name="Register" component={RegisterScreen} />

            {/* Member */}
            <Stack.Screen name="HomeMember" component={HomeScreenMember} />

            {/* Health Consultant */}
            <Stack.Screen name="HomeHealthConsultant" component={HomeScreenHealthConsultant} />

            {/* Health Monitor */}
            <Stack.Screen name="HomeHealthMonitor" component={HomeScreenHealthMonitor} />
          </Stack.Navigator>
        </NavigationContainer>
      )}

      {showSplash && (
        <SplashScreen
          isReady={isAuthChecked}
          onFinish={() => setShowSplash(false)}
        />
      )}

      {/* Floating Debug Button (Dapat diakses kapan saja untuk memeriksa status jaringan dan error) */}
      <FloatingDebugButton onPress={() => setIsDebugVisible(true)} />

      {/* Debug Activity Modal (Muncul otomatis saat terjadi status 500 atau unhandled exception) */}
      <DebugModal visible={isDebugVisible} onClose={() => setIsDebugVisible(false)} />
    </View>
  );
};

const AppNavigator = () => {
  return (
    <GlobalErrorBoundary>
      <ThemeProvider>
        <AppNavigatorContent />
      </ThemeProvider>
    </GlobalErrorBoundary>
  );
};

const styles = StyleSheet.create({
  root: {
    flex: 1,
    backgroundColor: '#FFFFFF',
  },
});

export default AppNavigator;