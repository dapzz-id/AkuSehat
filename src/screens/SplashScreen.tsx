import React, { useEffect, useRef, useState, useCallback } from 'react';
import {
  View,
  Text,
  Image,
  StyleSheet,
  Animated,
  StatusBar,
  Easing,
  Platform,
  TouchableOpacity,
  ActivityIndicator,
} from 'react-native';
import { SafeAreaView } from 'react-native-safe-area-context';
import Feather from 'react-native-vector-icons/Feather';
import { Colors } from '../settings/Colors';
import {
  moderateScale,
  verticalScale,
  scale,
  wp,
  hp,
  isSmallDevice,
} from '../utils/responsive';
import { checkAppUpdate, SoftwareReleaseData } from '../api/appUpdateService';
import ForceUpgradeModal from '../components/ForceUpgradeModal';
import MaintenanceModal from '../components/MaintenanceModal';

// In Jest / test environments, useNativeDriver is disabled to avoid native handle warnings
declare const process: any;
const useNativeDriver = typeof process !== 'undefined' && process?.env?.NODE_ENV !== 'test';

interface SplashScreenProps {
  onFinish?: () => void;
  isReady?: boolean;
}

export const SplashScreen: React.FC<SplashScreenProps> = ({
  onFinish,
  isReady = false,
}) => {
  // Verification & Update State
  const [isVerifying, setIsVerifying] = useState(true);
  const [isOffline, setIsOffline] = useState(false);
  const [isVerified, setIsVerified] = useState(false);
  const [forceUpdateData, setForceUpdateData] = useState<SoftwareReleaseData | null>(null);
  const [showForceUpgrade, setShowForceUpgrade] = useState(false);
  const [isMaintenance, setIsMaintenance] = useState(false);
  const [maintenanceMessage, setMaintenanceMessage] = useState<string | null>(null);

  // Animation Values
  const logoScale = useRef(new Animated.Value(0.75)).current;
  const logoOpacity = useRef(new Animated.Value(0)).current;
  const pulseScale = useRef(new Animated.Value(1)).current;
  const pulseOpacity = useRef(new Animated.Value(0.35)).current;

  const contentTranslateY = useRef(new Animated.Value(24)).current;
  const contentOpacity = useRef(new Animated.Value(0)).current;

  const footerOpacity = useRef(new Animated.Value(0)).current;
  const footerTranslateY = useRef(new Animated.Value(16)).current;

  const screenOpacity = useRef(new Animated.Value(1)).current;
  const screenScale = useRef(new Animated.Value(1)).current;

  // Pulse loop reference
  const pulseAnimRef = useRef<Animated.CompositeAnimation | null>(null);

  // Check network connectivity and software upgrade requirement
  const runVerification = useCallback(async () => {
    setIsVerifying(true);
    setIsOffline(false);

    try {
      const res = await checkAppUpdate();

      if (!res.success && res.isOffline) {
        // No internet connection -> block splash
        setIsOffline(true);
        setIsVerifying(false);
        return;
      }

      if (res.is_maintenance) {
        // Superadmin activated mobile maintenance mode -> block with unclosable dialog
        setIsMaintenance(true);
        setMaintenanceMessage(res.message || null);
        setIsVerifying(false);
        return;
      }

      setIsMaintenance(false);

      if (res.has_update && res.is_mandatory && res.data) {
        // Superadmin forces mandatory update -> block with unclosable dialog
        setForceUpdateData(res.data);
        setShowForceUpgrade(true);
        setIsVerifying(false);
        return;
      }

      // Internet verified and no mandatory upgrade or maintenance blocking
      setIsVerified(true);
      setIsVerifying(false);
    } catch (err) {
      setIsOffline(true);
      setIsVerifying(false);
    }
  }, []);

  useEffect(() => {
    runVerification();
  }, [runVerification]);

  useEffect(() => {
    if (process.env.NODE_ENV === 'test') {
      return;
    }

    // 1. Entrance sequence
    Animated.parallel([
      // Logo Entrance (Smooth Spring + Fade)
      Animated.timing(logoOpacity, {
        toValue: 1,
        duration: 650,
        useNativeDriver,
      }),
      Animated.spring(logoScale, {
        toValue: 1,
        friction: 6,
        tension: 45,
        useNativeDriver,
      }),

      // Content Slide & Fade
      Animated.timing(contentOpacity, {
        toValue: 1,
        duration: 700,
        delay: 200,
        easing: Easing.out(Easing.cubic),
        useNativeDriver,
      }),
      Animated.timing(contentTranslateY, {
        toValue: 0,
        duration: 700,
        delay: 200,
        easing: Easing.out(Easing.cubic),
        useNativeDriver,
      }),

      // Footer Entrance
      Animated.timing(footerOpacity, {
        toValue: 1,
        duration: 650,
        delay: 450,
        useNativeDriver,
      }),
      Animated.timing(footerTranslateY, {
        toValue: 0,
        duration: 650,
        delay: 450,
        easing: Easing.out(Easing.cubic),
        useNativeDriver,
      }),
    ]).start(() => {
      // Start ambient pulse animation after entrance
      pulseAnimRef.current = Animated.loop(
        Animated.sequence([
          Animated.parallel([
            Animated.timing(pulseScale, {
              toValue: 1.14,
              duration: 1200,
              easing: Easing.inOut(Easing.ease),
              useNativeDriver,
            }),
            Animated.timing(pulseOpacity, {
              toValue: 0.1,
              duration: 1200,
              useNativeDriver,
            }),
          ]),
          Animated.parallel([
            Animated.timing(pulseScale, {
              toValue: 1,
              duration: 1200,
              easing: Easing.inOut(Easing.ease),
              useNativeDriver,
            }),
            Animated.timing(pulseOpacity, {
              toValue: 0.35,
              duration: 1200,
              useNativeDriver,
            }),
          ]),
        ])
      );
      pulseAnimRef.current.start();
    });

    return () => {
      if (pulseAnimRef.current) {
        pulseAnimRef.current.stop();
      }
    };
  }, []);

  // Handle transition when both auth check and system/network verification are ready
  useEffect(() => {
    if (!isReady || !isVerified || isOffline || showForceUpgrade) return;

    if (process.env.NODE_ENV === 'test') {
      if (onFinish) {
        onFinish();
      }
      return;
    }

    // Minimum display duration before smooth exit transition
    const exitTimer = setTimeout(() => {
      Animated.parallel([
        Animated.timing(screenOpacity, {
          toValue: 0,
          duration: 450,
          easing: Easing.inOut(Easing.ease),
          useNativeDriver,
        }),
        Animated.timing(screenScale, {
          toValue: 1.05,
          duration: 450,
          easing: Easing.out(Easing.ease),
          useNativeDriver,
        }),
      ]).start(() => {
        if (onFinish) {
          onFinish();
        }
      });
    }, 1500);

    return () => clearTimeout(exitTimer);
  }, [isReady, isVerified, isOffline, showForceUpgrade, onFinish]);

  return (
    <Animated.View
      style={[
        styles.container,
        {
          opacity: screenOpacity,
          transform: [{ scale: screenScale }],
        },
      ]}
    >
      <StatusBar barStyle="dark-content" backgroundColor="#FFFFFF" translucent={false} />

      <SafeAreaView style={styles.safeArea}>
        {/* Subtle Decorative Background Circles */}
        <View style={styles.decorativeCircleTop} pointerEvents="none" />
        <View style={styles.decorativeCircleBottom} pointerEvents="none" />

        {/* Center Section: Aku Sehat Brand & Logo */}
        <View style={styles.centerSection}>
          <View style={styles.logoWrapper}>
            {/* Pulsing Aura */}
            <Animated.View
              style={[
                styles.pulseRing,
                {
                  opacity: pulseOpacity,
                  transform: [{ scale: pulseScale }],
                },
              ]}
            />

            {/* Main Aku Sehat Logo */}
            <Animated.View
              style={[
                styles.logoCard,
                {
                  opacity: logoOpacity,
                  transform: [{ scale: logoScale }],
                },
              ]}
            >
              <Image
                source={require('../assets/aku_sehat_icon.png')}
                style={styles.logoAkuSehat}
                resizeMode="contain"
              />
            </Animated.View>
          </View>

          {/* Title & Tagline */}
          <Animated.View
            style={[
              styles.textContainer,
              {
                opacity: contentOpacity,
                transform: [{ translateY: contentTranslateY }],
              },
            ]}
          >
            <Text style={styles.brandTitle}>Aku Sehat</Text>
            <View style={styles.taglineBadge}>
              <View style={styles.taglineDot} />
              <Text style={styles.brandTagline}>Sistem Manajemen Kesehatan Terpadu</Text>
            </View>
          </Animated.View>
        </View>

        {/* Footer Section: Neura Cakrawira Solusi Logo & Branding */}
        <Animated.View
          style={[
            styles.footerSection,
            {
              opacity: footerOpacity,
              transform: [{ translateY: footerTranslateY }],
            },
          ]}
        >
          <Text style={styles.footerLabel}>DIPERSEMBAHKAN OLEH</Text>

          <View style={styles.ncsLogoContainer}>
            <Image
              source={require('../assets/ncs_logo.webp')}
              style={styles.ncsLogo}
              resizeMode="contain"
            />
          </View>

          <Text style={styles.ncsCompanyText}>PT Neura Cakrawira Solusi</Text>
        </Animated.View>

        {/* Blocking Offline Dialog when no internet connection */}
        {isOffline && (
          <View style={styles.offlineBackdrop}>
            <View style={styles.offlineCard}>
              <View style={styles.offlineIconRing}>
                <Feather name="wifi-off" size={moderateScale(30)} color="#DC2626" />
              </View>

              <Text style={styles.offlineTitle}>Koneksi Internet Terputus</Text>
              <Text style={styles.offlineMessage}>
                Aku Sehat memerlukan koneksi internet aktif untuk verifikasi sistem dan sinkronisasi rekam medis Anda. Silakan periksa koneksi Wi-Fi atau data seluler.
              </Text>

              <TouchableOpacity
                style={styles.offlineRetryBtn}
                onPress={runVerification}
                disabled={isVerifying}
                activeOpacity={0.85}
              >
                {isVerifying ? (
                  <ActivityIndicator size="small" color="#FFFFFF" />
                ) : (
                  <>
                    <Feather name="refresh-cw" size={moderateScale(15)} color="#FFFFFF" />
                    <Text style={styles.offlineRetryText}>Coba Hubungkan Lagi</Text>
                  </>
                )}
              </TouchableOpacity>
            </View>
          </View>
        )}

        {/* Blocking Unclosable Force Upgrade Modal */}
        <ForceUpgradeModal
          visible={showForceUpgrade}
          updateData={forceUpdateData}
        />

        {/* Blocking Unclosable Maintenance Modal */}
        <MaintenanceModal
          visible={isMaintenance}
          message={maintenanceMessage}
          onRefreshStatus={runVerification}
        />
      </SafeAreaView>
    </Animated.View>
  );
};

const styles = StyleSheet.create({
  container: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: '#FFFFFF',
    zIndex: 9999,
  },
  safeArea: {
    flex: 1,
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingVertical: hp(3),
    paddingHorizontal: wp(6),
  },

  // Subtle background glowing gradients
  decorativeCircleTop: {
    position: 'absolute',
    top: -hp(12),
    right: -wp(18),
    width: moderateScale(260),
    height: moderateScale(260),
    borderRadius: moderateScale(130),
    backgroundColor: '#ECFDF5', // Soft emerald tint
    opacity: 0.7,
  },
  decorativeCircleBottom: {
    position: 'absolute',
    bottom: -hp(10),
    left: -wp(16),
    width: moderateScale(240),
    height: moderateScale(240),
    borderRadius: moderateScale(120),
    backgroundColor: '#F0FDF4',
    opacity: 0.6,
  },

  // Center Section
  centerSection: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    width: '100%',
  },
  logoWrapper: {
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: verticalScale(20),
  },
  pulseRing: {
    position: 'absolute',
    width: moderateScale(140),
    height: moderateScale(140),
    borderRadius: moderateScale(70),
    backgroundColor: '#10B981',
  },
  logoCard: {
    width: moderateScale(112),
    height: moderateScale(112),
    borderRadius: moderateScale(28),
    backgroundColor: '#FFFFFF',
    alignItems: 'center',
    justifyContent: 'center',
    shadowColor: '#059669',
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.16,
    shadowRadius: 18,
    elevation: 8,
    borderWidth: 1.5,
    borderColor: '#E6F4EA',
  },
  logoAkuSehat: {
    width: moderateScale(86),
    height: moderateScale(86),
  },

  // Text Styling
  textContainer: {
    alignItems: 'center',
  },
  brandTitle: {
    fontSize: moderateScale(isSmallDevice ? 26 : 30),
    fontWeight: '800',
    color: '#0F172A',
    letterSpacing: 0.4,
    marginBottom: verticalScale(6),
  },
  taglineBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ECFDF5',
    paddingHorizontal: scale(14),
    paddingVertical: verticalScale(5),
    borderRadius: moderateScale(20),
    borderWidth: 1,
    borderColor: '#D1FAE5',
  },
  taglineDot: {
    width: moderateScale(6),
    height: moderateScale(6),
    borderRadius: moderateScale(3),
    backgroundColor: Colors.secondaryGreen,
    marginRight: scale(8),
  },
  brandTagline: {
    fontSize: moderateScale(isSmallDevice ? 11.5 : 12.5),
    color: '#047857',
    fontWeight: '600',
    letterSpacing: 0.2,
  },

  // Footer Section
  footerSection: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingBottom: verticalScale(8),
    width: '100%',
  },
  footerLabel: {
    fontSize: moderateScale(9.5),
    fontWeight: '700',
    color: '#94A3B8',
    letterSpacing: 1.6,
    marginBottom: verticalScale(8),
  },
  ncsLogoContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    marginVertical: verticalScale(4),
  },
  ncsLogo: {
    width: moderateScale(140),
    height: moderateScale(40),
  },
  ncsCompanyText: {
    fontSize: moderateScale(11.5),
    fontWeight: '600',
    color: '#475569',
    letterSpacing: 0.3,
    marginTop: verticalScale(3),
  },

  // Offline Overlay
  offlineBackdrop: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: 'rgba(15, 23, 42, 0.7)',
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: wp(6),
    zIndex: 10000,
  },
  offlineCard: {
    width: '100%',
    maxWidth: 380,
    backgroundColor: '#FFFFFF',
    borderRadius: moderateScale(22),
    paddingHorizontal: scale(20),
    paddingVertical: verticalScale(26),
    alignItems: 'center',
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 12 },
    shadowOpacity: 0.2,
    shadowRadius: 20,
    elevation: 16,
  },
  offlineIconRing: {
    width: moderateScale(66),
    height: moderateScale(66),
    borderRadius: moderateScale(33),
    backgroundColor: '#FEE2E2',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: verticalScale(14),
    borderWidth: 2,
    borderColor: '#FECACA',
  },
  offlineTitle: {
    fontSize: moderateScale(17),
    fontWeight: '800',
    color: '#0F172A',
    textAlign: 'center',
    marginBottom: verticalScale(6),
  },
  offlineMessage: {
    fontSize: moderateScale(12),
    color: '#64748B',
    textAlign: 'center',
    lineHeight: moderateScale(18),
    marginBottom: verticalScale(20),
    paddingHorizontal: scale(6),
  },
  offlineRetryBtn: {
    width: '100%',
    backgroundColor: Colors.secondaryGreen || '#047857',
    borderRadius: moderateScale(12),
    paddingVertical: verticalScale(12),
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: scale(8),
    shadowColor: '#059669',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 8,
    elevation: 3,
  },
  offlineRetryText: {
    fontSize: moderateScale(13.5),
    fontWeight: '700',
    color: '#FFFFFF',
  },
});

export default SplashScreen;
