import React from 'react';
import {
  Modal,
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ActivityIndicator,
  ScrollView,
  Platform,
} from 'react-native';
import Feather from 'react-native-vector-icons/Feather';
import { Colors } from '../settings/Colors';
import { Config } from '../settings/Config';
import { moderateScale, verticalScale, scale, wp, hp } from '../utils/responsive';
import { SoftwareReleaseData } from '../api/appUpdateService';

export type CheckUpdateStatus =
  | 'checking'
  | 'up_to_date'
  | 'update_available'
  | 'error';

interface CheckUpdateModalProps {
  visible: boolean;
  onClose: () => void;
  status: CheckUpdateStatus;
  updateData?: SoftwareReleaseData | null;
  errorMessage?: string | null;
  onUpdateNow?: () => void;
}

export const CheckUpdateModal: React.FC<CheckUpdateModalProps> = ({
  visible,
  onClose,
  status,
  updateData,
  errorMessage,
  onUpdateNow,
}) => {
  if (!visible) return null;

  return (
    <Modal
      visible={visible}
      transparent={true}
      animationType="fade"
      statusBarTranslucent={true}
      onRequestClose={status === 'checking' ? undefined : onClose}
    >
      <View style={styles.overlay}>
        <View style={styles.cardContainer}>
          {/* Status: Checking */}
          {status === 'checking' && (
            <View style={styles.contentWrapper}>
              <View style={styles.iconCircleChecking}>
                <ActivityIndicator size="large" color="#047857" />
              </View>
              <Text style={styles.title}>Memeriksa Pembaruan...</Text>
              <Text style={styles.subtitle}>
                Menghubungkan ke server untuk memverifikasi versi rilis terbaru Aku Sehat Mobile.
              </Text>
            </View>
          )}

          {/* Status: Up To Date */}
          {status === 'up_to_date' && (
            <View style={styles.contentWrapper}>
              <View style={styles.iconCircleSuccess}>
                <Feather name="check-circle" size={moderateScale(36)} color="#059669" />
              </View>
              <View style={styles.badgeSuccess}>
                <Text style={styles.badgeSuccessText}>SISTEM TERKINI</Text>
              </View>
              <Text style={styles.title}>Aplikasi Sudah Versi Terbaru!</Text>
              <Text style={styles.subtitle}>
                Anda saat ini menggunakan versi <Text style={styles.boldText}>v{Config.APP_VERSION}</Text> ({Config.APP_BUILD}). Sistem Anda sudah optimal dan tidak memerlukan pembaruan.
              </Text>

              <TouchableOpacity
                style={styles.primaryButton}
                onPress={onClose}
                activeOpacity={0.88}
              >
                <Text style={styles.primaryButtonText}>Mengerti</Text>
              </TouchableOpacity>
            </View>
          )}

          {/* Status: Update Available */}
          {status === 'update_available' && (
            <View style={styles.contentWrapper}>
              <View style={styles.iconCircleUpdate}>
                <Feather name="arrow-up-circle" size={moderateScale(36)} color="#0284C7" />
              </View>
              <View style={styles.badgeUpdate}>
                <Text style={styles.badgeUpdateText}>VERSI BARU TERSEDIA</Text>
              </View>
              <Text style={styles.title}>
                {updateData?.name || `Aku Sehat v${updateData?.version || 'Baru'}`}
              </Text>
              <Text style={styles.subtitle}>
                Versi baru <Text style={styles.boldText}>v{updateData?.version}</Text> telah tersedia. Tingkatkan aplikasi Anda untuk menikmati fitur dan stabilitas terbaru.
              </Text>

              {/* Changelog preview if any */}
              {updateData?.description || updateData?.changelog ? (
                <View style={styles.changelogBox}>
                  <ScrollView
                    style={styles.changelogScroll}
                    showsVerticalScrollIndicator={true}
                    nestedScrollEnabled={true}
                  >
                    {updateData?.description ? (
                      <Text style={styles.descText}>{updateData.description}</Text>
                    ) : null}
                    {updateData?.changelog ? (
                      <Text style={styles.changelogText}>{updateData.changelog}</Text>
                    ) : null}
                  </ScrollView>
                </View>
              ) : null}

              {/* Buttons */}
              <TouchableOpacity
                style={styles.primaryButton}
                onPress={onUpdateNow}
                activeOpacity={0.88}
              >
                <Feather name="download-cloud" size={moderateScale(16)} color="#FFFFFF" />
                <Text style={styles.primaryButtonText}>Perbarui Sekarang</Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={styles.secondaryButton}
                onPress={onClose}
                activeOpacity={0.7}
              >
                <Text style={styles.secondaryButtonText}>Nanti Saja</Text>
              </TouchableOpacity>
            </View>
          )}

          {/* Status: Error / Offline */}
          {status === 'error' && (
            <View style={styles.contentWrapper}>
              <View style={styles.iconCircleError}>
                <Feather name="alert-triangle" size={moderateScale(36)} color="#DC2626" />
              </View>
              <View style={styles.badgeError}>
                <Text style={styles.badgeErrorText}>GAGAL MEMERIKSA</Text>
              </View>
              <Text style={styles.title}>Koneksi Terkendala</Text>
              <Text style={styles.subtitle}>
                {errorMessage ||
                  'Tidak dapat memverifikasi pembaruan ke server. Harap periksa koneksi internet Anda dan coba kembali.'}
              </Text>

              <TouchableOpacity
                style={styles.primaryButton}
                onPress={onClose}
                activeOpacity={0.88}
              >
                <Text style={styles.primaryButtonText}>Tutup</Text>
              </TouchableOpacity>
            </View>
          )}
        </View>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(15, 23, 42, 0.65)',
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: wp(6),
  },
  cardContainer: {
    width: '100%',
    maxWidth: 390,
    backgroundColor: '#FFFFFF',
    borderRadius: moderateScale(24),
    paddingHorizontal: scale(20),
    paddingVertical: verticalScale(26),
    alignItems: 'center',
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 12 },
    shadowOpacity: 0.2,
    shadowRadius: 20,
    elevation: 16,
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  contentWrapper: {
    width: '100%',
    alignItems: 'center',
  },
  iconCircleChecking: {
    width: moderateScale(68),
    height: moderateScale(68),
    borderRadius: moderateScale(34),
    backgroundColor: '#ECFDF5',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: verticalScale(14),
  },
  iconCircleSuccess: {
    width: moderateScale(68),
    height: moderateScale(68),
    borderRadius: moderateScale(34),
    backgroundColor: '#ECFDF5',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: verticalScale(12),
    borderWidth: 2,
    borderColor: '#A7F3D0',
  },
  iconCircleUpdate: {
    width: moderateScale(68),
    height: moderateScale(68),
    borderRadius: moderateScale(34),
    backgroundColor: '#E0F2FE',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: verticalScale(12),
    borderWidth: 2,
    borderColor: '#BAE6FD',
  },
  iconCircleError: {
    width: moderateScale(68),
    height: moderateScale(68),
    borderRadius: moderateScale(34),
    backgroundColor: '#FEE2E2',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: verticalScale(12),
    borderWidth: 2,
    borderColor: '#FECACA',
  },
  badgeSuccess: {
    backgroundColor: '#ECFDF5',
    paddingHorizontal: scale(10),
    paddingVertical: verticalScale(3),
    borderRadius: moderateScale(10),
    marginBottom: verticalScale(8),
    borderWidth: 1,
    borderColor: '#A7F3D0',
  },
  badgeSuccessText: {
    fontSize: moderateScale(10),
    fontWeight: '800',
    color: '#059669',
    letterSpacing: 1,
  },
  badgeUpdate: {
    backgroundColor: '#E0F2FE',
    paddingHorizontal: scale(10),
    paddingVertical: verticalScale(3),
    borderRadius: moderateScale(10),
    marginBottom: verticalScale(8),
    borderWidth: 1,
    borderColor: '#BAE6FD',
  },
  badgeUpdateText: {
    fontSize: moderateScale(10),
    fontWeight: '800',
    color: '#0284C7',
    letterSpacing: 1,
  },
  badgeError: {
    backgroundColor: '#FEE2E2',
    paddingHorizontal: scale(10),
    paddingVertical: verticalScale(3),
    borderRadius: moderateScale(10),
    marginBottom: verticalScale(8),
    borderWidth: 1,
    borderColor: '#FECACA',
  },
  badgeErrorText: {
    fontSize: moderateScale(10),
    fontWeight: '800',
    color: '#DC2626',
    letterSpacing: 1,
  },
  title: {
    fontSize: moderateScale(17.5),
    fontWeight: '800',
    color: '#0F172A',
    textAlign: 'center',
    marginBottom: verticalScale(6),
  },
  subtitle: {
    fontSize: moderateScale(12),
    color: '#64748B',
    textAlign: 'center',
    lineHeight: moderateScale(18),
    paddingHorizontal: scale(4),
    marginBottom: verticalScale(18),
  },
  boldText: {
    fontWeight: '700',
    color: '#0F172A',
  },
  changelogBox: {
    width: '100%',
    backgroundColor: '#F8FAFC',
    borderRadius: moderateScale(12),
    borderWidth: 1,
    borderColor: '#E2E8F0',
    padding: scale(12),
    marginBottom: verticalScale(16),
  },
  changelogScroll: {
    maxHeight: hp(14),
  },
  descText: {
    fontSize: moderateScale(11.5),
    fontWeight: '600',
    color: '#334155',
    marginBottom: verticalScale(4),
  },
  changelogText: {
    fontSize: moderateScale(11),
    color: '#64748B',
    lineHeight: moderateScale(16),
    fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace',
  },
  primaryButton: {
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
  primaryButtonText: {
    fontSize: moderateScale(13.5),
    fontWeight: '700',
    color: '#FFFFFF',
    letterSpacing: 0.2,
  },
  secondaryButton: {
    width: '100%',
    paddingVertical: verticalScale(10),
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: verticalScale(4),
  },
  secondaryButtonText: {
    fontSize: moderateScale(12.5),
    fontWeight: '600',
    color: '#64748B',
  },
});

export default CheckUpdateModal;
