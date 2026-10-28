import React, { useEffect, useState } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { View, StyleSheet } from 'react-native';
import { checkAuthStatus } from '../utils/authHelper';
import { navigationRef } from '../navigation/rootNavigation';
import SplashScreen from '../screens/SplashScreen';

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

    return () => {
      isMounted = false;
    };
  }, []);
  return (
    <View style={styles.root}>
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
    </View>
  );
};

const AppNavigator = () => {
  return (
    <ThemeProvider>
      <AppNavigatorContent />
    </ThemeProvider>
  );
};

const styles = StyleSheet.create({
  root: {
    flex: 1,
    backgroundColor: '#FFFFFF',
  },
});

export default AppNavigator;