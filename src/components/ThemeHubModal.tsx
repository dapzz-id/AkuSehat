import React, { useState } from 'react';
import {
  Modal,
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ScrollView,
  ActivityIndicator,
  TextInput,
  Alert,
  Platform,
} from 'react-native';
import Feather from 'react-native-vector-icons/Feather';
import { useTheme } from '../theme/ThemeContext';
import { moderateScale, verticalScale, scale, wp, hp } from '../utils/responsive';
import { Config } from '../settings/Config';

interface ThemeHubModalProps {
  visible: boolean;
  onClose: () => void;
}

export const ThemeHubModal: React.FC<ThemeHubModalProps> = ({ visible, onClose }) => {
  const {
    themeId,
    theme,
    mode,
    isDark,
    isDownloaded,
    isDownloading,
    downloadProgress,
    setTheme,
    setMode,
    downloadTheme,
    deleteTheme,
    getThemeFilePath,
  } = useTheme();

  const [showUrlInput, setShowUrlInput] = useState(false);
  const [customUrl, setCustomUrl] = useState(Config.DEFAULT_MODERN_THEME_URL);
  const [actionLoading, setActionLoading] = useState(false);

  if (!visible) return null;

  const handleDownloadPress = async () => {
    setActionLoading(true);
    try {
      const res = await downloadTheme(customUrl.trim());
      if (res.success) {
        Alert.alert('Sukses Mengunduh', res.message, [
          {
            text: 'Terapkan Sekarang',
            onPress: () => setTheme('atlas_modern'),
          },
          { text: 'Nanti Saja', style: 'cancel' },
        ]);
      } else {
        Alert.alert('Gagal Mengunduh', res.message);
      }
    } finally {
      setActionLoading(false);
    }
  };

  const handleDeletePress = () => {
    Alert.alert(
      'Hapus File Tema',
      'Apakah Anda yakin ingin menghapus file tema Atlas Neo dari penyimpanan perangkat? Aplikasi akan kembali menggunakan tema Emerald Classic.',
      [
        { text: 'Batal', style: 'cancel' },
        {
          text: 'Hapus',
          style: 'destructive',
          onPress: async () => {
            const ok = await deleteTheme();
            if (ok) {
              Alert.alert('Dihapus', 'File tema berhasil dihapus dari perangkat.');
            }
          },
        },
      ]
    );
  };

  const filePath = getThemeFilePath();

  return (
    <Modal
      visible={visible}
      transparent={true}
      animationType="slide"
      onRequestClose={onClose}
      statusBarTranslucent={true}
    >
      <View style={styles.overlay}>
        <View style={[styles.modalCard, { backgroundColor: theme.surface }]}>
          {/* Header */}
          <View style={[styles.headerRow, { borderBottomColor: theme.border }]}>
            <View style={{ flex: 1 }}>
              <View style={styles.titleRow}>
                <Feather name="layout" size={moderateScale(19)} color={theme.primary} />
                <Text style={[styles.title, { color: theme.text }]}>Tampilan & Tema</Text>
              </View>
              <Text style={[styles.subtitle, { color: theme.muted }]}>
                Pilih palet antarmuka dan sesuaikan pencahayaan
              </Text>
            </View>
            <TouchableOpacity
              onPress={onClose}
              style={[styles.closeBtn, { backgroundColor: theme.surfaceSubtle }]}
              hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}
            >
              <Feather name="x" size={moderateScale(18)} color={theme.textSecondary} />
            </TouchableOpacity>
          </View>

          <ScrollView
            showsVerticalScrollIndicator={false}
            contentContainerStyle={styles.scrollContent}
          >
            {/* Section 1: Mode Pencahayaan (Light vs Dark) */}
            <View style={styles.section}>
              <Text style={[styles.sectionTitle, { color: theme.text }]}>
                Mode Pencahayaan (Light / Dark)
              </Text>
              <View style={[styles.modeToggleContainer, { backgroundColor: theme.surfaceSubtle }]}>
                <TouchableOpacity
                  style={[
                    styles.modeOption,
                    mode === 'light' && [
                      styles.modeOptionActive,
                      { backgroundColor: theme.card, shadowColor: theme.glow },
                    ],
                  ]}
                  onPress={() => setMode('light')}
                  activeOpacity={0.8}
                >
                  <Feather
                    name="sun"
                    size={moderateScale(16)}
                    color={mode === 'light' ? theme.primary : theme.muted}
                  />
                  <Text
                    style={[
                      styles.modeOptionText,
                      { color: mode === 'light' ? theme.primary : theme.muted },
                      mode === 'light' && styles.modeOptionTextActive,
                    ]}
                  >
                    Terang (Light)
                  </Text>
                </TouchableOpacity>

                <TouchableOpacity
                  style={[
                    styles.modeOption,
                    mode === 'dark' && [
                      styles.modeOptionActive,
                      { backgroundColor: theme.card, shadowColor: theme.glow },
                    ],
                  ]}
                  onPress={() => setMode('dark')}
                  activeOpacity={0.8}
                >
                  <Feather
                    name="moon"
                    size={moderateScale(16)}
                    color={mode === 'dark' ? theme.primary : theme.muted}
                  />
                  <Text
                    style={[
                      styles.modeOptionText,
                      { color: mode === 'dark' ? theme.primary : theme.muted },
                      mode === 'dark' && styles.modeOptionTextActive,
                    ]}
                  >
                    Gelap (Dark)
                  </Text>
                </TouchableOpacity>
              </View>
            </View>

            {/* Section 2: Katalog Tema */}
            <View style={styles.section}>
              <Text style={[styles.sectionTitle, { color: theme.text }]}>Pilihan Tema</Text>

              {/* Theme 1: Emerald Classic (Bawaan) */}
              <TouchableOpacity
                style={[
                  styles.themeCard,
                  { backgroundColor: theme.card, borderColor: themeId === 'emerald' ? '#059669' : theme.cardBorder },
                  themeId === 'emerald' && styles.themeCardActive,
                ]}
                onPress={() => setTheme('emerald')}
                activeOpacity={0.88}
              >
                <View style={styles.themeCardHeader}>
                  <View style={styles.themeBadgeBawaan}>
                    <Text style={styles.themeBadgeBawaanText}>BAWAAN SISTEM</Text>
                  </View>
                  {themeId === 'emerald' ? (
                    <View style={styles.activeCheckBadge}>
                      <Feather name="check-circle" size={moderateScale(15)} color="#059669" />
                      <Text style={styles.activeCheckText}>Aktif</Text>
                    </View>
                  ) : null}
                </View>

                <Text style={[styles.themeName, { color: theme.text }]}>Emerald Classic</Text>
                <Text style={[styles.themeTagline, { color: theme.muted }]}>
                  Nuansa hijau emerald khas kesehatan, bersih, elegan, dan ringan tanpa download.
                </Text>

                {/* Color Swatch Preview */}
                <View style={styles.swatchRow}>
                  <View style={[styles.swatchCircle, { backgroundColor: '#059669' }]} />
                  <View style={[styles.swatchCircle, { backgroundColor: '#10B981' }]} />
                  <View style={[styles.swatchCircle, { backgroundColor: '#14B8A6' }]} />
                  <View style={[styles.swatchCircle, { backgroundColor: '#F8FAFC', borderWidth: 1, borderColor: '#CBD5E1' }]} />
                  <Text style={[styles.swatchLabel, { color: theme.muted }]}>4 Palet Utama</Text>
                </View>
              </TouchableOpacity>

              {/* Theme 2: Atlas Neo Modern (Download On-Demand) */}
              <View
                style={[
                  styles.themeCard,
                  { backgroundColor: theme.card, borderColor: themeId === 'atlas_modern' ? '#4F46E5' : theme.cardBorder },
                  themeId === 'atlas_modern' && styles.themeCardActiveModern,
                ]}
              >
                <View style={styles.themeCardHeader}>
                  <View style={styles.themeBadgeModern}>
                    <Text style={styles.themeBadgeModernText}>NEO MODERN • GITHUB</Text>
                  </View>
                  {themeId === 'atlas_modern' ? (
                    <View style={[styles.activeCheckBadge, { backgroundColor: '#EEF2FF', borderColor: '#C7D2FE' }]}>
                      <Feather name="check-circle" size={moderateScale(15)} color="#4F46E5" />
                      <Text style={[styles.activeCheckText, { color: '#4F46E5' }]}>Aktif</Text>
                    </View>
                  ) : isDownloaded ? (
                    <View style={[styles.activeCheckBadge, { backgroundColor: '#F0FDF4', borderColor: '#BBF7D0' }]}>
                      <Feather name="download" size={moderateScale(13)} color="#16A34A" />
                      <Text style={[styles.activeCheckText, { color: '#16A34A' }]}>Terunduh</Text>
                    </View>
                  ) : (
                    <View style={[styles.activeCheckBadge, { backgroundColor: '#FFFBEB', borderColor: '#FDE68A' }]}>
                      <Feather name="cloud-rain" size={moderateScale(13)} color="#D97706" />
                      <Text style={[styles.activeCheckText, { color: '#D97706' }]}>Perlu Diunduh</Text>
                    </View>
                  )}
                </View>

                <Text style={[styles.themeName, { color: theme.text }]}>Atlas Neo Modern</Text>
                <Text style={[styles.themeTagline, { color: theme.muted }]}>
                  Desain cyber-health modern dengan gradien elektrik (Indigo & Cyan), efek aura glow halus, sudut kartu rounded, dan optimasi dark mode.
                </Text>

                {/* Color Swatch Preview */}
                <View style={styles.swatchRow}>
                  <View style={[styles.swatchCircle, { backgroundColor: '#4F46E5' }]} />
                  <View style={[styles.swatchCircle, { backgroundColor: '#06B6D4' }]} />
                  <View style={[styles.swatchCircle, { backgroundColor: '#8B5CF6' }]} />
                  <View style={[styles.swatchCircle, { backgroundColor: '#0B0F19' }]} />
                  <Text style={[styles.swatchLabel, { color: theme.muted }]}>Cyber Palette (~14 KB)</Text>
                </View>

                {/* Storage Target Note */}
                <View style={[styles.storageBox, { backgroundColor: theme.surfaceSubtle }]}>
                  <Feather name="hard-drive" size={moderateScale(13)} color={theme.muted} />
                  <Text style={[styles.storageBoxText, { color: theme.muted }]} numberOfLines={2}>
                    Lokasi: {filePath}
                  </Text>
                </View>

                {/* Actions: Download / Apply / Delete */}
                {isDownloading ? (
                  <View style={styles.downloadingContainer}>
                    <ActivityIndicator size="small" color="#4F46E5" />
                    <View style={{ flex: 1 }}>
                      <Text style={[styles.downloadingText, { color: theme.text }]}>
                        Mengunduh file tema dari GitHub...
                      </Text>
                      <View style={styles.progressBarBg}>
                        <View
                          style={[
                            styles.progressBarFill,
                            { width: `${Math.round(downloadProgress * 100)}%` },
                          ]}
                        />
                      </View>
                    </View>
                  </View>
                ) : isDownloaded ? (
                  <View style={styles.downloadedActionsRow}>
                    <TouchableOpacity
                      style={[
                        styles.applyThemeBtn,
                        themeId === 'atlas_modern' && styles.applyThemeBtnDisabled,
                      ]}
                      onPress={() => setTheme('atlas_modern')}
                      disabled={themeId === 'atlas_modern'}
                      activeOpacity={0.85}
                    >
                      <Feather name="check" size={moderateScale(15)} color="#FFFFFF" />
                      <Text style={styles.applyThemeBtnText}>
                        {themeId === 'atlas_modern' ? 'Sedang Digunakan' : 'Terapkan Tema'}
                      </Text>
                    </TouchableOpacity>

                    <TouchableOpacity
                      style={styles.deleteThemeBtn}
                      onPress={handleDeletePress}
                      activeOpacity={0.8}
                    >
                      <Feather name="trash-2" size={moderateScale(15)} color="#DC2626" />
                    </TouchableOpacity>
                  </View>
                ) : (
                  <View style={{ gap: verticalScale(8) }}>
                    <TouchableOpacity
                      style={styles.downloadActionBtn}
                      onPress={handleDownloadPress}
                      disabled={actionLoading}
                      activeOpacity={0.88}
                    >
                      {actionLoading ? (
                        <ActivityIndicator size="small" color="#FFFFFF" />
                      ) : (
                        <>
                          <Feather name="download-cloud" size={moderateScale(16)} color="#FFFFFF" />
                          <Text style={styles.downloadActionBtnText}>Unduh Tema dari GitHub (~14 KB)</Text>
                        </>
                      )}
                    </TouchableOpacity>

                    {/* Toggle custom URL input */}
                    <TouchableOpacity
                      onPress={() => setShowUrlInput(!showUrlInput)}
                      style={styles.urlToggleRow}
                    >
                      <Text style={[styles.urlToggleText, { color: theme.primary }]}>
                        {showUrlInput ? 'Sembunyikan URL Kustom' : 'Ubah URL Repository GitHub'}
                      </Text>
                      <Feather
                        name={showUrlInput ? 'chevron-up' : 'chevron-down'}
                        size={moderateScale(14)}
                        color={theme.primary}
                      />
                    </TouchableOpacity>

                    {showUrlInput && (
                      <View style={[styles.urlInputContainer, { borderColor: theme.border, backgroundColor: theme.surfaceSubtle }]}>
                        <TextInput
                          value={customUrl}
                          onChangeText={setCustomUrl}
                          placeholder="https://raw.githubusercontent.com/..."
                          placeholderTextColor={theme.muted}
                          style={[styles.urlTextInput, { color: theme.text }]}
                          autoCapitalize="none"
                          autoCorrect={false}
                        />
                      </View>
                    )}
                  </View>
                )}
              </View>
            </View>
          </ScrollView>
        </View>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(15, 23, 42, 0.72)',
    justifyContent: 'flex-end',
  },
  modalCard: {
    width: '100%',
    maxHeight: hp(88),
    borderTopLeftRadius: moderateScale(26),
    borderTopRightRadius: moderateScale(26),
    paddingTop: verticalScale(20),
    paddingBottom: verticalScale(Platform.OS === 'ios' ? 36 : 24),
    shadowColor: '#000000',
    shadowOffset: { width: 0, height: -8 },
    shadowOpacity: 0.2,
    shadowRadius: 16,
    elevation: 24,
  },
  headerRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: scale(20),
    paddingBottom: verticalScale(14),
    borderBottomWidth: 1,
  },
  titleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: scale(8),
    marginBottom: verticalScale(4),
  },
  title: {
    fontSize: moderateScale(17.5),
    fontWeight: '800',
  },
  subtitle: {
    fontSize: moderateScale(11.5),
  },
  closeBtn: {
    width: moderateScale(34),
    height: moderateScale(34),
    borderRadius: moderateScale(17),
    alignItems: 'center',
    justifyContent: 'center',
    marginLeft: scale(8),
  },
  scrollContent: {
    paddingHorizontal: scale(20),
    paddingTop: verticalScale(16),
    paddingBottom: verticalScale(20),
  },
  section: {
    marginBottom: verticalScale(20),
  },
  sectionTitle: {
    fontSize: moderateScale(13),
    fontWeight: '700',
    letterSpacing: 0.2,
    marginBottom: verticalScale(10),
  },
  modeToggleContainer: {
    flexDirection: 'row',
    borderRadius: moderateScale(14),
    padding: scale(4),
  },
  modeOption: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: scale(8),
    paddingVertical: verticalScale(10),
    borderRadius: moderateScale(10),
  },
  modeOptionActive: {
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.12,
    shadowRadius: 4,
    elevation: 2,
  },
  modeOptionText: {
    fontSize: moderateScale(12.5),
    fontWeight: '600',
  },
  modeOptionTextActive: {
    fontWeight: '800',
  },
  themeCard: {
    borderRadius: moderateScale(18),
    borderWidth: 1.5,
    padding: scale(16),
    marginBottom: verticalScale(14),
  },
  themeCardActive: {
    borderWidth: 2,
    borderColor: '#059669',
  },
  themeCardActiveModern: {
    borderWidth: 2,
    borderColor: '#4F46E5',
  },
  themeCardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: verticalScale(8),
  },
  themeBadgeBawaan: {
    backgroundColor: '#ECFDF5',
    paddingHorizontal: scale(8),
    paddingVertical: verticalScale(3),
    borderRadius: moderateScale(8),
    borderWidth: 1,
    borderColor: '#A7F3D0',
  },
  themeBadgeBawaanText: {
    fontSize: moderateScale(9.5),
    fontWeight: '800',
    color: '#059669',
    letterSpacing: 0.8,
  },
  themeBadgeModern: {
    backgroundColor: '#EEF2FF',
    paddingHorizontal: scale(8),
    paddingVertical: verticalScale(3),
    borderRadius: moderateScale(8),
    borderWidth: 1,
    borderColor: '#C7D2FE',
  },
  themeBadgeModernText: {
    fontSize: moderateScale(9.5),
    fontWeight: '800',
    color: '#4F46E5',
    letterSpacing: 0.8,
  },
  activeCheckBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: scale(4),
    backgroundColor: '#ECFDF5',
    paddingHorizontal: scale(8),
    paddingVertical: verticalScale(2),
    borderRadius: moderateScale(10),
    borderWidth: 1,
    borderColor: '#A7F3D0',
  },
  activeCheckText: {
    fontSize: moderateScale(11),
    fontWeight: '700',
    color: '#059669',
  },
  themeName: {
    fontSize: moderateScale(15.5),
    fontWeight: '800',
    marginBottom: verticalScale(4),
  },
  themeTagline: {
    fontSize: moderateScale(11.5),
    lineHeight: moderateScale(16),
    marginBottom: verticalScale(12),
  },
  swatchRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: scale(6),
    marginBottom: verticalScale(10),
  },
  swatchCircle: {
    width: moderateScale(16),
    height: moderateScale(16),
    borderRadius: moderateScale(8),
  },
  swatchLabel: {
    fontSize: moderateScale(10.5),
    fontWeight: '600',
    marginLeft: scale(4),
  },
  storageBox: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: scale(6),
    borderRadius: moderateScale(10),
    paddingHorizontal: scale(10),
    paddingVertical: verticalScale(6),
    marginBottom: verticalScale(12),
  },
  storageBoxText: {
    fontSize: moderateScale(10),
    flex: 1,
    fontFamily: Platform.OS === 'ios' ? 'Menlo' : 'monospace',
  },
  downloadActionBtn: {
    backgroundColor: '#4F46E5',
    borderRadius: moderateScale(12),
    paddingVertical: verticalScale(11),
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: scale(8),
    shadowColor: '#4F46E5',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.25,
    shadowRadius: 8,
    elevation: 3,
  },
  downloadActionBtnText: {
    fontSize: moderateScale(12.5),
    fontWeight: '700',
    color: '#FFFFFF',
  },
  downloadedActionsRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: scale(10),
  },
  applyThemeBtn: {
    flex: 1,
    backgroundColor: '#4F46E5',
    borderRadius: moderateScale(12),
    paddingVertical: verticalScale(11),
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: scale(8),
  },
  applyThemeBtnDisabled: {
    backgroundColor: '#94A3B8',
  },
  applyThemeBtnText: {
    fontSize: moderateScale(12.5),
    fontWeight: '700',
    color: '#FFFFFF',
  },
  deleteThemeBtn: {
    width: moderateScale(42),
    height: moderateScale(42),
    borderRadius: moderateScale(12),
    backgroundColor: '#FEE2E2',
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1,
    borderColor: '#FECACA',
  },
  downloadingContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: scale(12),
    paddingVertical: verticalScale(6),
  },
  downloadingText: {
    fontSize: moderateScale(11.5),
    fontWeight: '600',
    marginBottom: verticalScale(4),
  },
  progressBarBg: {
    height: verticalScale(6),
    backgroundColor: '#E2E8F0',
    borderRadius: moderateScale(3),
    overflow: 'hidden',
  },
  progressBarFill: {
    height: '100%',
    backgroundColor: '#4F46E5',
  },
  urlToggleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: scale(4),
    paddingTop: verticalScale(4),
  },
  urlToggleText: {
    fontSize: moderateScale(11),
    fontWeight: '600',
  },
  urlInputContainer: {
    borderWidth: 1,
    borderRadius: moderateScale(10),
    paddingHorizontal: scale(10),
    paddingVertical: verticalScale(6),
  },
  urlTextInput: {
    fontSize: moderateScale(11),
    padding: 0,
  },
});

export default ThemeHubModal;
