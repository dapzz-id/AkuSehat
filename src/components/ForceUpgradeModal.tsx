import React, { useEffect } from 'react';
import {
  Modal,
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  BackHandler,
  Linking,
  ScrollView,
  Platform,
} from 'react-native';
import Feather from 'react-native-vector-icons/Feather';
import { Colors } from '../settings/Colors';
import { Config } from '../settings/Config';
import { moderateScale, verticalScale, scale, wp, hp } from '../utils/responsive';
import { SoftwareReleaseData } from '../api/appUpdateService';

interface ForceUpgradeModalProps {
  visible: boolean;
  updateData?: SoftwareReleaseData | null;
}

export const ForceUpgradeModal: React.FC<ForceUpgradeModalProps> = ({
  visible,
  updateData,
}) => {
  // Block Android hardware back button unconditionally when modal is visible
  useEffect(() => {
    if (!visible) return;

    const backHandler = BackHandler.addEventListener('hardwareBackPress', () => {
      // Return true to prevent default back action
      return true;
    });

    return () => backHandler.remove();
  }, [visible]);

  if (!visible) return null;

  const handleUpdatePress = async () => {
    const targetUrl =
      updateData?.download_url ||
      (Platform.OS === 'android'
        ? 'https://play.google.com/store/apps'
        : 'https://apps.apple.com');

    try {
      const supported = await Linking.canOpenURL(targetUrl);
      if (supported) {
        await Linking.openURL(targetUrl);
      } else {
        await Linking.openURL(targetUrl);
      }
    } catch (err) {
      console.warn('Cannot open update link:', err);
      // Fallback
      Linking.openURL(targetUrl).catch(() => {});
    }
  };

  const newVersion = updateData?.version || 'Terbaru';
  const currentVersion = Config.APP_VERSION;

  return (
    <Modal
      visible={visible}
      transparent={true}
      animationType="fade"
      statusBarTranslucent={true}
      onRequestClose={() => {
        // Unclosable dialog: do nothing on request close
      }}
    >
      <View style={styles.overlay}>
        <View style={styles.cardContainer}>
          {/* Header Icon */}
          <View style={styles.iconCircleOuter}>
            <View style={styles.iconCircleInner}>
              <Feather name="arrow-up-circle" size={moderateScale(36)} color="#047857" />
            </View>
          </View>

          {/* Title & Version info */}
          <View style={styles.headerTextWrapper}>
            <Text style={styles.badgeLabel}>PEMBARUAN WAJIB SISTEM</Text>
            <Text style={styles.title}>Versi Baru Tersedia!</Text>
            <Text style={styles.subtitle}>
              Versi aplikasi Anda saat ini (<Text style={styles.highlightText}>v{currentVersion}</Text>) sudah tidak didukung. Harap lakukan pembaruan ke versi <Text style={styles.highlightText}>v{newVersion}</Text> untuk terus menggunakan layanan Aku Sehat.
            </Text>
          </View>

          {/* Details / Changelog Box */}
          <View style={styles.changelogBox}>
            <View style={styles.changelogHeader}>
              <Feather name="layers" size={moderateScale(14)} color="#059669" />
              <Text style={styles.changelogHeaderTitle}>
                {updateData?.name || `Aku Sehat v${newVersion}`}
              </Text>
            </View>

            <ScrollView
              style={styles.changelogScroll}
              showsVerticalScrollIndicator={true}
              nestedScrollEnabled={true}
            >
              {updateData?.description ? (
                <Text style={styles.descriptionText}>{updateData.description}</Text>
              ) : null}

              {updateData?.changelog ? (
                <Text style={styles.changelogBodyText}>{updateData.changelog}</Text>
              ) : (
                <Text style={styles.changelogBodyText}>
                  • Peningkatan performa dan keamanan sistem{'\n'}
                  • Penyesuaian fitur terbaru untuk kenyamanan Anda
                </Text>
              )}
            </ScrollView>
          </View>

          {/* Action Button - Unclosable */}
          <TouchableOpacity
            style={styles.actionButton}
            onPress={handleUpdatePress}
            activeOpacity={0.88}
          >
            <Feather name="download-cloud" size={moderateScale(18)} color="#FFFFFF" />
            <Text style={styles.actionButtonText}>Perbarui Sekarang</Text>
          </TouchableOpacity>

          <Text style={styles.footerNote}>
            Pembaruan diperlukan sesuai instruksi administrator
          </Text>
        </View>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(15, 23, 42, 0.75)',
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
    shadowOpacity: 0.25,
    shadowRadius: 24,
    elevation: 20,
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  iconCircleOuter: {
    width: moderateScale(72),
    height: moderateScale(72),
    borderRadius: moderateScale(36),
    backgroundColor: '#ECFDF5',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: verticalScale(14),
    borderWidth: 3,
    borderColor: '#D1FAE5',
  },
  iconCircleInner: {
    width: moderateScale(56),
    height: moderateScale(56),
    borderRadius: moderateScale(28),
    backgroundColor: '#D1FAE5',
    alignItems: 'center',
    justifyContent: 'center',
  },
  headerTextWrapper: {
    alignItems: 'center',
    marginBottom: verticalScale(14),
  },
  badgeLabel: {
    fontSize: moderateScale(10),
    fontWeight: '800',
    color: '#059669',
    letterSpacing: 1.2,
    backgroundColor: '#ECFDF5',
    paddingHorizontal: scale(10),
    paddingVertical: verticalScale(3),
    borderRadius: moderateScale(12),
    marginBottom: verticalScale(8),
    borderWidth: 1,
    borderColor: '#A7F3D0',
  },
  title: {
    fontSize: moderateScale(19),
    fontWeight: '800',
    color: '#0F172A',
    textAlign: 'center',
    letterSpacing: 0.2,
    marginBottom: verticalScale(6),
  },
  subtitle: {
    fontSize: moderateScale(12.5),
    color: '#475569',
    textAlign: 'center',
    lineHeight: moderateScale(18),
    paddingHorizontal: scale(6),
  },
  highlightText: {
    fontWeight: '700',
    color: '#047857',
  },
  changelogBox: {
    width: '100%',
    backgroundColor: '#F8FAFC',
    borderRadius: moderateScale(16),
    borderWidth: 1,
    borderColor: '#E2E8F0',
    padding: scale(14),
    marginBottom: verticalScale(18),
  },
  changelogHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: scale(6),
    borderBottomWidth: 1,
    borderBottomColor: '#E2E8F0',
    paddingBottom: verticalScale(8),
    marginBottom: verticalScale(8),
  },
  changelogHeaderTitle: {
    fontSize: moderateScale(12),
    fontWeight: '700',
    color: '#1E293B',
  },
  changelogScroll: {
    maxHeight: hp(16),
  },
  descriptionText: {
    fontSize: moderateScale(11.5),
    fontWeight: '600',
    color: '#334155',
    marginBottom: verticalScale(6),
  },
  changelogBodyText: {
    fontSize: moderateScale(11),
    color: '#64748B',
    lineHeight: moderateScale(17),
    fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace',
  },
  actionButton: {
    width: '100%',
    backgroundColor: Colors.secondaryGreen || '#047857',
    borderRadius: moderateScale(14),
    paddingVertical: verticalScale(13),
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: scale(8),
    shadowColor: '#059669',
    shadowOffset: { width: 0, height: 6 },
    shadowOpacity: 0.25,
    shadowRadius: 10,
    elevation: 4,
  },
  actionButtonText: {
    fontSize: moderateScale(14),
    fontWeight: '700',
    color: '#FFFFFF',
    letterSpacing: 0.3,
  },
  footerNote: {
    fontSize: moderateScale(10.5),
    color: '#94A3B8',
    textAlign: 'center',
    marginTop: verticalScale(10),
  },
});

export default ForceUpgradeModal;
