import React, { useEffect, useState } from 'react';
import {
  Modal,
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ScrollView,
  ActivityIndicator,
  Linking,
  RefreshControl,
  Platform,
} from 'react-native';
import Feather from 'react-native-vector-icons/Feather';
import { SafeAreaView } from 'react-native-safe-area-context';
import { Colors } from '../settings/Colors';
import { Config } from '../settings/Config';
import { moderateScale, verticalScale, scale, wp, hp } from '../utils/responsive';
import { fetchAppChangelogs, SoftwareReleaseData } from '../api/appUpdateService';

interface ChangelogModalProps {
  visible: boolean;
  onClose: () => void;
}

// Helper to compare version numbers (v1, v2). Returns > 0 if v1 > v2, < 0 if v1 < v2, 0 if equal
const compareVersions = (v1: string, v2: string): number => {
  const clean1 = (v1 || '').replace(/^[^0-9]*/, '');
  const clean2 = (v2 || '').replace(/^[^0-9]*/, '');
  const p1 = clean1.split('.').map((n) => parseInt(n, 10) || 0);
  const p2 = clean2.split('.').map((n) => parseInt(n, 10) || 0);
  const maxLen = Math.max(p1.length, p2.length);

  for (let i = 0; i < maxLen; i++) {
    const num1 = p1[i] ?? 0;
    const num2 = p2[i] ?? 0;
    if (num1 > num2) return 1;
    if (num1 < num2) return -1;
  }
  return 0;
};

export const ChangelogModal: React.FC<ChangelogModalProps> = ({
  visible,
  onClose,
}) => {
  const [loading, setLoading] = useState(false);
  const [refreshing, setRefreshing] = useState(false);
  const [releases, setReleases] = useState<SoftwareReleaseData[]>([]);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  const loadChangelogs = async (isPull = false) => {
    if (isPull) {
      setRefreshing(true);
    } else {
      setLoading(true);
    }
    setErrorMessage(null);

    try {
      const result = await fetchAppChangelogs('android');
      if (result.success) {
        // Filter: only show historical versions up to and including current app version
        const filtered = result.data.filter((item) => {
          return compareVersions(item.version, Config.APP_VERSION) <= 0;
        });
        setReleases(filtered);
      } else {
        setErrorMessage(result.error || 'Gagal memuat catatan pembaruan.');
      }
    } catch (err: any) {
      setErrorMessage(err?.message || 'Terjadi kesalahan jaringan.');
    } finally {
      setLoading(false);
      setRefreshing(false);
    }
  };

  useEffect(() => {
    if (visible) {
      loadChangelogs();
    }
  }, [visible]);

  const handleOpenLink = (url?: string | null) => {
    if (!url) return;
    Linking.openURL(url).catch((err) =>
      console.warn('Cannot open link:', err)
    );
  };

  return (
    <Modal
      visible={visible}
      animationType="slide"
      transparent={true}
      onRequestClose={onClose}
    >
      <View style={styles.backdrop}>
        <SafeAreaView style={styles.sheetContainer} edges={['bottom', 'top']}>
          {/* Header */}
          <View style={styles.header}>
            <View style={styles.headerTitleRow}>
              <View style={styles.iconCircle}>
                <Feather name="file-text" size={moderateScale(18)} color="#047857" />
              </View>
              <View>
                <Text style={styles.headerTitle}>Catatan Pembaruan</Text>
                <Text style={styles.headerSubtitle}>
                  Riwayat versi & rilis Aku Sehat Mobile
                </Text>
              </View>
            </View>
            <TouchableOpacity
              style={styles.closeButton}
              onPress={onClose}
              activeOpacity={0.7}
              hitSlop={{ top: 12, bottom: 12, left: 12, right: 12 }}
            >
              <Feather name="x" size={moderateScale(19)} color="#64748B" />
            </TouchableOpacity>
          </View>

          {/* Current Version Pill Banner */}
          <View style={styles.currentVersionBanner}>
            <View style={styles.currentVersionLeft}>
              <View style={styles.statusDot} />
              <Text style={styles.currentVersionText}>
                Versi Terpasang: <Text style={styles.versionBold}>v{Config.APP_VERSION}</Text> ({Config.APP_BUILD})
              </Text>
            </View>
            <View style={styles.verifiedBadge}>
              <Feather name="check-circle" size={moderateScale(12)} color="#059669" />
              <Text style={styles.verifiedBadgeText}>Aktif</Text>
            </View>
          </View>

          {/* Body Content */}
          <ScrollView
            style={styles.contentScroll}
            contentContainerStyle={styles.scrollContent}
            showsVerticalScrollIndicator={false}
            refreshControl={
              <RefreshControl
                refreshing={refreshing}
                onRefresh={() => loadChangelogs(true)}
                colors={['#047857']}
                tintColor="#047857"
              />
            }
          >
            {loading ? (
              <View style={styles.centerContainer}>
                <ActivityIndicator size="large" color="#047857" />
                <Text style={styles.loadingText}>Memuat catatan pembaruan...</Text>
              </View>
            ) : errorMessage ? (
              <View style={styles.centerContainer}>
                <View style={styles.errorIconCircle}>
                  <Feather name="alert-circle" size={moderateScale(28)} color="#DC2626" />
                </View>
                <Text style={styles.errorTitle}>Gagal Mengambil Data</Text>
                <Text style={styles.errorDesc}>{errorMessage}</Text>
                <TouchableOpacity
                  style={styles.retryButton}
                  onPress={() => loadChangelogs()}
                  activeOpacity={0.8}
                >
                  <Feather name="refresh-cw" size={moderateScale(14)} color="#FFFFFF" />
                  <Text style={styles.retryButtonText}>Coba Lagi</Text>
                </TouchableOpacity>
              </View>
            ) : releases.length === 0 ? (
              <View style={styles.emptyContainer}>
                <View style={styles.emptyIconCircle}>
                  <Feather name="inbox" size={moderateScale(32)} color="#94A3B8" />
                </View>
                <Text style={styles.emptyTitle}>Belum Ada Catatan Rilis</Text>
                <Text style={styles.emptyDesc}>
                  Belum ada log rilis pembaruan baru dari administrator sistem untuk versi mobile.
                </Text>
              </View>
            ) : (
              <View style={styles.timelineContainer}>
                {releases.map((item, index) => {
                  const isCurrent = item.version === Config.APP_VERSION;
                  const isLast = index === releases.length - 1;

                  return (
                    <View key={item.id || index} style={styles.timelineItem}>
                      {/* Timeline Left Line & Node */}
                      <View style={styles.timelineCol}>
                        <View
                          style={[
                            styles.timelineNode,
                            isCurrent && styles.timelineNodeCurrent,
                          ]}
                        >
                          <View
                            style={[
                              styles.timelineInnerDot,
                              isCurrent && styles.timelineInnerDotCurrent,
                            ]}
                          />
                        </View>
                        {!isLast && <View style={styles.timelineLine} />}
                      </View>

                      {/* Timeline Right Card */}
                      <View style={styles.releaseCard}>
                        {/* Top Badges */}
                        <View style={styles.releaseCardHeader}>
                          <View style={styles.versionBadgeRow}>
                            <View
                              style={[
                                styles.versionChip,
                                isCurrent && styles.versionChipCurrent,
                              ]}
                            >
                              <Text
                                style={[
                                  styles.versionChipText,
                                  isCurrent && styles.versionChipTextCurrent,
                                ]}
                              >
                                v{item.version}
                              </Text>
                            </View>

                            {item.is_mandatory ? (
                              <View style={styles.mandatoryChip}>
                                <Text style={styles.mandatoryChipText}>Wajib</Text>
                              </View>
                            ) : null}

                            {isCurrent ? (
                              <View style={styles.installedChip}>
                                <Text style={styles.installedChipText}>Terpasang</Text>
                              </View>
                            ) : null}
                          </View>

                          <Text style={styles.releaseDate}>
                            {item.release_date_formatted || item.release_date || 'Terbaru'}
                          </Text>
                        </View>

                        {/* Title & Description */}
                        <Text style={styles.releaseName}>{item.name}</Text>
                        {item.description ? (
                          <Text style={styles.releaseDesc}>{item.description}</Text>
                        ) : null}

                        {/* Detailed Changelog */}
                        {item.changelog ? (
                          <View style={styles.changelogSection}>
                            <Text style={styles.changelogSectionTitle}>Catatan Perubahan:</Text>
                            <Text style={styles.changelogContentText}>
                              {item.changelog}
                            </Text>
                          </View>
                        ) : null}

                        {/* Download link button */}
                        {item.download_url ? (
                          <TouchableOpacity
                            style={styles.downloadLinkRow}
                            onPress={() => handleOpenLink(item.download_url)}
                            activeOpacity={0.7}
                          >
                            <Feather name="external-link" size={moderateScale(13)} color="#047857" />
                            <Text style={styles.downloadLinkText}>
                              Tautan Rilis / Download
                            </Text>
                          </TouchableOpacity>
                        ) : null}
                      </View>
                    </View>
                  );
                })}
              </View>
            )}
          </ScrollView>
        </SafeAreaView>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  backdrop: {
    flex: 1,
    backgroundColor: 'rgba(15, 23, 42, 0.6)',
    justifyContent: 'flex-end',
  },
  sheetContainer: {
    backgroundColor: '#FFFFFF',
    borderTopLeftRadius: moderateScale(28),
    borderTopRightRadius: moderateScale(28),
    height: hp(85),
    paddingTop: verticalScale(16),
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: -8 },
    shadowOpacity: 0.15,
    shadowRadius: 16,
    elevation: 20,
  },
  header: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: scale(20),
    paddingBottom: verticalScale(14),
    borderBottomWidth: 1,
    borderBottomColor: '#F1F5F9',
  },
  headerTitleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: scale(12),
  },
  iconCircle: {
    width: moderateScale(40),
    height: moderateScale(40),
    borderRadius: moderateScale(20),
    backgroundColor: '#ECFDF5',
    alignItems: 'center',
    justifyContent: 'center',
  },
  headerTitle: {
    fontSize: moderateScale(16),
    fontWeight: '800',
    color: '#0F172A',
  },
  headerSubtitle: {
    fontSize: moderateScale(11),
    color: '#64748B',
    marginTop: verticalScale(1),
  },
  closeButton: {
    width: moderateScale(34),
    height: moderateScale(34),
    borderRadius: moderateScale(17),
    backgroundColor: '#F1F5F9',
    alignItems: 'center',
    justifyContent: 'center',
  },
  currentVersionBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#F8FAFC',
    marginHorizontal: scale(16),
    marginTop: verticalScale(12),
    marginBottom: verticalScale(8),
    paddingHorizontal: scale(14),
    paddingVertical: verticalScale(9),
    borderRadius: moderateScale(12),
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  currentVersionLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: scale(8),
  },
  statusDot: {
    width: moderateScale(7),
    height: moderateScale(7),
    borderRadius: moderateScale(4),
    backgroundColor: '#10B981',
  },
  currentVersionText: {
    fontSize: moderateScale(11.5),
    color: '#475569',
  },
  versionBold: {
    fontWeight: '700',
    color: '#0F172A',
  },
  verifiedBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: scale(4),
    backgroundColor: '#ECFDF5',
    paddingHorizontal: scale(8),
    paddingVertical: verticalScale(2),
    borderRadius: moderateScale(8),
  },
  verifiedBadgeText: {
    fontSize: moderateScale(10.5),
    fontWeight: '700',
    color: '#059669',
  },
  contentScroll: {
    flex: 1,
  },
  scrollContent: {
    paddingHorizontal: scale(16),
    paddingTop: verticalScale(12),
    paddingBottom: verticalScale(30),
  },
  centerContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: verticalScale(60),
    paddingHorizontal: scale(20),
  },
  loadingText: {
    fontSize: moderateScale(12),
    color: '#64748B',
    marginTop: verticalScale(12),
  },
  errorIconCircle: {
    width: moderateScale(56),
    height: moderateScale(56),
    borderRadius: moderateScale(28),
    backgroundColor: '#FEE2E2',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: verticalScale(12),
  },
  errorTitle: {
    fontSize: moderateScale(15),
    fontWeight: '700',
    color: '#0F172A',
    marginBottom: verticalScale(4),
  },
  errorDesc: {
    fontSize: moderateScale(11.5),
    color: '#64748B',
    textAlign: 'center',
    lineHeight: moderateScale(17),
    marginBottom: verticalScale(16),
  },
  retryButton: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: scale(6),
    backgroundColor: '#047857',
    paddingHorizontal: scale(16),
    paddingVertical: verticalScale(9),
    borderRadius: moderateScale(10),
  },
  retryButtonText: {
    fontSize: moderateScale(12),
    fontWeight: '700',
    color: '#FFFFFF',
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: verticalScale(60),
    paddingHorizontal: scale(20),
  },
  emptyIconCircle: {
    width: moderateScale(60),
    height: moderateScale(60),
    borderRadius: moderateScale(30),
    backgroundColor: '#F1F5F9',
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: verticalScale(12),
  },
  emptyTitle: {
    fontSize: moderateScale(15),
    fontWeight: '700',
    color: '#334155',
    marginBottom: verticalScale(4),
  },
  emptyDesc: {
    fontSize: moderateScale(11.5),
    color: '#94A3B8',
    textAlign: 'center',
    lineHeight: moderateScale(17),
  },
  timelineContainer: {
    marginTop: verticalScale(4),
  },
  timelineItem: {
    flexDirection: 'row',
    marginBottom: verticalScale(16),
  },
  timelineCol: {
    alignItems: 'center',
    width: scale(24),
    marginRight: scale(10),
  },
  timelineNode: {
    width: moderateScale(16),
    height: moderateScale(16),
    borderRadius: moderateScale(8),
    backgroundColor: '#E2E8F0',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: verticalScale(4),
  },
  timelineNodeCurrent: {
    backgroundColor: '#D1FAE5',
    borderWidth: 2,
    borderColor: '#059669',
  },
  timelineInnerDot: {
    width: moderateScale(6),
    height: moderateScale(6),
    borderRadius: moderateScale(3),
    backgroundColor: '#94A3B8',
  },
  timelineInnerDotCurrent: {
    backgroundColor: '#047857',
  },
  timelineLine: {
    flex: 1,
    width: 2,
    backgroundColor: '#E2E8F0',
    marginVertical: verticalScale(4),
  },
  releaseCard: {
    flex: 1,
    backgroundColor: '#FFFFFF',
    borderRadius: moderateScale(16),
    borderWidth: 1,
    borderColor: '#E2E8F0',
    padding: scale(14),
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.04,
    shadowRadius: 6,
    elevation: 2,
  },
  releaseCardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: verticalScale(6),
  },
  versionBadgeRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: scale(6),
  },
  versionChip: {
    backgroundColor: '#F1F5F9',
    paddingHorizontal: scale(8),
    paddingVertical: verticalScale(3),
    borderRadius: moderateScale(8),
  },
  versionChipCurrent: {
    backgroundColor: '#ECFDF5',
    borderWidth: 1,
    borderColor: '#A7F3D0',
  },
  versionChipText: {
    fontSize: moderateScale(11),
    fontWeight: '800',
    color: '#334155',
    fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace',
  },
  versionChipTextCurrent: {
    color: '#047857',
  },
  mandatoryChip: {
    backgroundColor: '#FEF3C7',
    paddingHorizontal: scale(6),
    paddingVertical: verticalScale(2),
    borderRadius: moderateScale(6),
  },
  mandatoryChipText: {
    fontSize: moderateScale(9.5),
    fontWeight: '800',
    color: '#B45309',
  },
  installedChip: {
    backgroundColor: '#EEF2FF',
    paddingHorizontal: scale(6),
    paddingVertical: verticalScale(2),
    borderRadius: moderateScale(6),
  },
  installedChipText: {
    fontSize: moderateScale(9.5),
    fontWeight: '700',
    color: '#4338CA',
  },
  releaseDate: {
    fontSize: moderateScale(10.5),
    color: '#94A3B8',
  },
  releaseName: {
    fontSize: moderateScale(13.5),
    fontWeight: '700',
    color: '#0F172A',
    marginBottom: verticalScale(4),
  },
  releaseDesc: {
    fontSize: moderateScale(11.5),
    color: '#475569',
    lineHeight: moderateScale(17),
    marginBottom: verticalScale(8),
  },
  changelogSection: {
    backgroundColor: '#F8FAFC',
    borderRadius: moderateScale(10),
    padding: scale(10),
    marginTop: verticalScale(4),
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  changelogSectionTitle: {
    fontSize: moderateScale(10.5),
    fontWeight: '700',
    color: '#334155',
    marginBottom: verticalScale(4),
  },
  changelogContentText: {
    fontSize: moderateScale(11),
    color: '#64748B',
    lineHeight: moderateScale(17),
    fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace',
  },
  downloadLinkRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: scale(6),
    marginTop: verticalScale(10),
    paddingTop: verticalScale(8),
    borderTopWidth: 1,
    borderTopColor: '#F1F5F9',
  },
  downloadLinkText: {
    fontSize: moderateScale(11),
    fontWeight: '700',
    color: '#047857',
  },
});

export default ChangelogModal;
