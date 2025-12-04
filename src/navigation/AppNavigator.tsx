import React, { useEffect, useState } from 'react';
import { NavigationContainer } from '@react-navigation/native';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { ActivityIndicator, View } from 'react-native';
import { checkAuthStatus } from '../utils/authHelper';
import { navigationRef } from '../navigation/rootNavigation';  // Import the ref (adjust path if needed)

// Screens untuk Member
import HomeScreenMember from '../screens/member/MainScreen';

// Screens untuk Health Consultant
import HomeScreenHealthConsultant from '../screens/health-consultant/MainScreen';

// Screens untuk Health Monitor
import HomeScreenHealthMonitor from '../screens/health-monitor/MainScreen';

// Auth Screens
import LoginScreen from '../screens/auth/LoginScreen';

const Stack = createNativeStackNavigator();

const AppNavigator = () => {
  const [isLoading, setIsLoading] = useState(true);
  const [initialRoute, setInitialRoute] = useState('Login');

  useEffect(() => {
    const checkLogin = async () => {
      try {
        const user = await checkAuthStatus();
        if (user && user.level) {
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
        } else {
          setInitialRoute('Login');
        }
      } catch (error) {
        console.log('Error checking auth:', error);
        setInitialRoute('Login');
      } finally {
        setIsLoading(false);
      }
    };

    checkLogin();
  }, []);

  if (isLoading) {
    return (
      <View style={{ flex: 1, justifyContent: 'center', alignItems: 'center' }}>
        <ActivityIndicator size="large" color="#0000ff" />
      </View>
    );
  }

  return (
    <NavigationContainer ref={navigationRef}>  {/* Add ref here to the existing container */}
      <Stack.Navigator
        screenOptions={{ headerShown: false }}
        initialRouteName={initialRoute}
      >
        {/* Auth */}
        <Stack.Screen name="Login" component={LoginScreen} />

        {/* Member */}
        <Stack.Screen name="HomeMember" component={HomeScreenMember} />

        {/* Health Consultant */}
        <Stack.Screen name="HomeHealthConsultant" component={HomeScreenHealthConsultant} />

        {/* Health Monitor */}
        <Stack.Screen name="HomeHealthMonitor" component={HomeScreenHealthMonitor} />
      </Stack.Navigator>
    </NavigationContainer>
  );
};

export default AppNavigator;