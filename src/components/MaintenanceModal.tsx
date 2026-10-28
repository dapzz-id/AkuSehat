import React, { useEffect, useState, useRef } from 'react';
import {
  Modal,
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  BackHandler,
  ActivityIndicator,
  Animated,
  Easing,
  Platform,
} from 'react-native';
import Feather from 'react-native-vector-icons/Feather';
import { moderateScale, verticalScale, scale, wp, hp } from '../utils/responsive';

interface MaintenanceModalProps {
  visible: boolean;
  message?: string | null;
  onRefreshStatus?: () => Promise<void> | void;
}

export const MaintenanceModal: React.FC<MaintenanceModalProps> = ({
  visible,
  message,
  onRefreshStatus,
}) => {
  const [checking, setChecking] = useState(false);
  const pulseAnim = useRef(new Animated.Value(1)).current;
  const rotateAnim = useRef(new Animated.Value(0)).current;

  // Unconditionally block Android hardware back button
  useEffect(() => {
    if (!visible) return;

    const backHandler = BackHandler.addEventListener('hardwareBackPress', () => {
      return true; // Prevents back navigation
    });

    return () => backHandler.remove();
  }, [visible]);

  // Pulse & subtle rotation animation for maintenance icon
  useEffect(() => {
    if (!visible) return;

    const pulse = Animated.loop(
      Animated.sequence([
        Animated.timing(pulseAnim, {
          toValue: 1.12,
          duration: 1400,
          easing: Easing.inOut(Easing.ease),
          useNativeDriver: true,
        }),
        Animated.timing(pulseAnim, {
          toValue: 1,
          duration: 1400,
          easing: Easing.inOut(Easing.ease),
          useNativeDriver: true,
        }),
      ])
    );

    pulse.start();

    return () => pulse.stop();
  }, [visible, pulseAnim]);

  if (!visible) return null;

  const handleManualCheck = async () => {
    if (checking || !onRefreshStatus) return;
    setChecking(true);
    try {
      await onRefreshStatus();
    } finally {
      setTimeout(() => {
        setChecking(false);
      }, 600);
    }
  };

  return (
    <Modal
      visible={visible}
      transparent={true}
      animationType="fade"
      statusBarTranslucent={true}
      onRequestClose={() => {
        // Strictly unclosable
      }}
    >
      <View style={styles.overlay}>
        <View style={styles.cardContainer}>
          {/* Animated Glowing Icon Circle */}
          <Animated.View style={[styles.glowRing, { transform: [{ scale: pulseAnim }] }]}>
            <View style={styles.iconCircleOuter}>
              <View style={styles.iconCircleInner}>
                <Feather name="tool" size={moderateScale(34)} color="#D97706" />
              </View>
            </View>
          </Animated.View>

          {/* Header Badge */}
          <View style={styles.badgeWrapper}>
            <View style={styles.badgeDot} />
            <Text style={styles.badgeText}>MODE PEMELIHARAAN SISTEM</Text>
          </View>

          {/* Title & Description */}
          <Text style={styles.title}>Sistem Sedang Dirawat</Text>
          <Text style={styles.subtitle}>
            {message ||
              'Aplikasi Aku Sehat saat ini sedang dalam proses pemeliharaan berkala untuk peningkatan performa, stabilitas, dan keamanan layanan.'}
          </Text>

          {/* Info Card */}
          <View style={styles.infoCard}>
            <View style={styles.infoRow}>
              <Feather name="shield" size={moderateScale(15)} color="#D97706" />
              <Text style={styles.infoLabel}>Status Layanan:</Text>
              <Text style={styles.infoValueActive}>Sedang Berlangsung</Text>
            </View>
            <View style={styles.infoDivider} />
            <View style={styles.infoRow}>
              <Feather name="clock" size={moderateScale(15)} color="#64748B" />
              <Text style={styles.infoLabel}>Pemeriksaan:</Text>
              <Text style={styles.infoValue}>Sinkronisasi Realtime Otomatis</Text>
            </View>
          </View>

          {/* Re-check Action Button */}
          <TouchableOpacity
            style={[styles.actionButton, checking && styles.actionButtonDisabled]}
            onPress={handleManualCheck}
            activeOpacity={0.85}
            disabled={checking}
          >
            {checking ? (
              <ActivityIndicator size="small" color="#FFFFFF" />
            ) : (
              <>
                <Feather name="refresh-cw" size={moderateScale(16)} color="#FFFFFF" />
                <Text style={styles.actionButtonText}>Periksa Status Sistem</Text>
              </>
            )}
          </TouchableOpacity>

          <Text style={styles.footerNote}>
            Halaman ini akan otomatis tertutup begitu pemeliharaan selesai oleh Administrator.
          </Text>
        </View>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(15, 23, 42, 0.82)',
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: wp(5),
  },
  cardContainer: {
    width: '100%',
    maxWidth: 400,
    backgroundColor: '#FFFFFF',
    borderRadius: moderateScale(24),
    paddingHorizontal: scale(20),
    paddingTop: verticalScale(28),
    paddingBottom: verticalScale(20),
    alignItems: 'center',
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 16 },
    shadowOpacity: 0.28,
    shadowRadius: 24,
    elevation: 20,
    borderWidth: 1,
    borderColor: '#FEF3C7',
  },
  glowRing: {
    marginBottom: verticalScale(14),
    alignItems: 'center',
    justifyContent: 'center',
  },
  iconCircleOuter: {
    width: moderateScale(76),
    height: moderateScale(76),
    borderRadius: moderateScale(38),
    backgroundColor: '#FFFBEB',
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 3,
    borderColor: '#FDE68A',
  },
  iconCircleInner: {
    width: moderateScale(58),
    height: moderateScale(58),
    borderRadius: moderateScale(29),
    backgroundColor: '#FEF3C7',
    alignItems: 'center',
    justifyContent: 'center',
  },
  badgeWrapper: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FFFBEB',
    borderWidth: 1,
    borderColor: '#FDE68A',
    paddingHorizontal: scale(10),
    paddingVertical: verticalScale(4),
    borderRadius: moderateScale(14),
    marginBottom: verticalScale(10),
    gap: scale(6),
  },
  badgeDot: {
    width: moderateScale(7),
    height: moderateScale(7),
    borderRadius: moderateScale(3.5),
    backgroundColor: '#D97706',
  },
  badgeText: {
    fontSize: moderateScale(10),
    fontWeight: '800',
    color: '#B45309',
    letterSpacing: 1.1,
  },
  title: {
    fontSize: moderateScale(20),
    fontWeight: '800',
    color: '#0F172A',
    textAlign: 'center',
    marginBottom: verticalScale(6),
  },
  subtitle: {
    fontSize: moderateScale(12.5),
    color: '#475569',
    textAlign: 'center',
    lineHeight: moderateScale(18),
    paddingHorizontal: scale(6),
    marginBottom: verticalScale(16),
  },
  infoCard: {
    width: '100%',
    backgroundColor: '#F8FAFC',
    borderRadius: moderateScale(16),
    borderWidth: 1,
    borderColor: '#E2E8F0',
    padding: scale(14),
    marginBottom: verticalScale(18),
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: scale(8),
  },
  infoLabel: {
    fontSize: moderateScale(12),
    color: '#64748B',
    fontWeight: '600',
  },
  infoValueActive: {
    fontSize: moderateScale(12),
    color: '#D97706',
    fontWeight: '700',
    marginLeft: 'auto',
  },
  infoValue: {
    fontSize: moderateScale(11.5),
    color: '#334155',
    fontWeight: '600',
    marginLeft: 'auto',
  },
  infoDivider: {
    height: 1,
    backgroundColor: '#E2E8F0',
    marginVertical: verticalScale(10),
  },
  actionButton: {
    width: '100%',
    backgroundColor: '#D97706',
    borderRadius: moderateScale(14),
    paddingVertical: verticalScale(13),
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: scale(8),
    shadowColor: '#D97706',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.25,
    shadowRadius: 10,
    elevation: 4,
  },
  actionButtonDisabled: {
    opacity: 0.7,
  },
  actionButtonText: {
    fontSize: moderateScale(13.5),
    fontWeight: '700',
    color: '#FFFFFF',
    letterSpacing: 0.3,
  },
  footerNote: {
    fontSize: moderateScale(10.5),
    color: '#94A3B8',
    textAlign: 'center',
    marginTop: verticalScale(10),
    lineHeight: moderateScale(15),
  },
});

export default MaintenanceModal;
