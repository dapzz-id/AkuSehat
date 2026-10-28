"use client"
import React from "react"
import { useEffect, useRef, useState, useMemo, useCallback } from "react"
import { Animated, StatusBar, StyleSheet, Text, View, ScrollView, Pressable, TextInput, ActivityIndicator, useWindowDimensions, Dimensions, Modal, Alert, Image, Linking, Platform, AppState, RefreshControl } from "react-native"
import Icon from 'react-native-vector-icons/Feather'
import { SafeAreaView } from "react-native-safe-area-context"
import { Colors } from "../../settings/Colors"
import Svg, { Defs, LinearGradient as SvgLinearGradient, Stop, Rect } from 'react-native-svg'
import api from "../../api/axiosConfig"
import AnimatedBottomTabs, { type TabItem } from "../../components/AnimatedBottomTabs"
import HealthBarChart from "../../components/HealthBarChart"
import RunTrackerView from "./RunTrackerView"
import DailyVitalityView from "./DailyVitalityView"
import EditProfileModal from "../../components/EditProfileModal"
import ChangelogModal from "../../components/ChangelogModal"
import CheckUpdateModal, { type CheckUpdateStatus } from "../../components/CheckUpdateModal"
import ForceUpgradeModal from "../../components/ForceUpgradeModal"
import MaintenanceModal from "../../components/MaintenanceModal"
import ThemeHubModal from "../../components/ThemeHubModal"
import { useTheme } from "../../theme/ThemeContext"
import useRealtimeSystemSync from "../../hooks/useRealtimeSystemSync"
import { checkAppUpdate, type SoftwareReleaseData } from "../../api/appUpdateService"
import CounselingMemberView from "./CounselingMemberView"
import ErgonomicsMemberView from "./ErgonomicsMemberView"
import BurnoutMemberView from "./BurnoutMemberView"
import NutritionMemberView from "./NutritionMemberView"
import VaccineMemberView from "./VaccineMemberView"
import { logout } from "../../utils/authHelper"
import { useNavigation, useFocusEffect } from '@react-navigation/native';
import { checkAuthStatus } from '../../utils/authHelper';
type Summary = {
  nama: string;
  bmi?: number;
  tinggi?: number;
  berat?: number;
  sistol?: number;
  diastol?: number;
  hb?: number;
  status?: string
}
type Profile = {
  id: string | number;
  nama: string;
  email?: string;
  username: string;
  avatar?: string | null;
  nomor_induk: string;
  id_divisi?: string | number;
  divisi?: string;
  color_cover?: string;
  tertiary_color?: string;
  can_add_user?: boolean;
  jk?: "L" | "P";
  level?: string;
  active_modules?: string[];
  instansi?: {
    id: string | number;
    nama_instansi: string;
    jenis_instansi: string;
    active_modules?: string[];
  };
}
type KesehatanItem = {
  id_kesehatan: string | number;
  tgl: string;
  bb: string;
  tb: string;
  sistol: string;
  diastol: string;
  status_darah: string;
  imt: string;
  status: string;
  pesan_imt: string;
  pesan_tkd: string
}
type HbItem = {
  id_hb: string | number;
  tgl: string;
  hb: string;
  status: string;
  pesan: string
}
type HaidItem = {
  id: string | number;
  id_user: string | number;
  tanggal_mulai: string;
  tanggal_selesai: string | null;
  durasi_hari: number;
  status: string;
  catatan: string | null;
  created_at: string | null;
  updated_at: string | null
}
type PitaItem = {
  id: string | number;
  id_user: string | number;
  id_haid: string | number;
  jumlah_pita: number;
  tanggal_pinjam: string;
  tanggal_kembali: string | null;
  estimasi_selesai_haid: string | null;
  status: string;
  keterangan: string;
  created_at: string;
  updated_at: string;
  tanggal_mulai?: string;
  estimasi_selesai?: string | null
}
type MonthData<T> = {
  month: string;
  data: T[]
}
type YearData<T> = {
  year: number;
  months: MonthData<T>[]
}
type PeminjamanNotification = {
  type: 'info' | 'warning' | 'danger';
  title: string;
  message: string;
  days_remaining?: number;
  days_late?: number;
}
type PeminjamanStatus = {
  peminjaman: PitaItem;
  notification: PeminjamanNotification | null;
  estimasi_selesai: string;
  days_diff: number;
}
const { width: SCREEN_WIDTH, height: SCREEN_HEIGHT } = Dimensions.get('window');
const guidelineBaseWidth = 375, guidelineBaseHeight = 812;
const horizontalScale = (size: number) => (SCREEN_WIDTH / guidelineBaseWidth) * size;
const verticalScale = (size: number) => (SCREEN_HEIGHT / guidelineBaseHeight) * size;
const moderateScale = (size: number, factor = 0.5) => size + (horizontalScale(size) - size) * factor;
const HomeScreenMember = () => {
  const navigation = useNavigation<any>();
  const [active, setActive] = useState("ringkasan")
  const fadeMount = useRef(new Animated.Value(1)).current
  const slideMount = useRef(new Animated.Value(0)).current
  const fadeTab = useRef(new Animated.Value(1)).current
  const shimmerAnim = useRef(new Animated.Value(0)).current

  useEffect(() => {
    fadeMount.setValue(0);
    slideMount.setValue(12);
    Animated.parallel([
      Animated.timing(fadeMount, { toValue: 1, duration: 400, useNativeDriver: true }),
      Animated.timing(slideMount, { toValue: 0, duration: 400, useNativeDriver: true }),
    ]).start();
  }, [fadeMount, slideMount]);
  const hasFetchedKesehatan = useRef<string | null>(null);
  const hasFetchedHb = useRef<string | null>(null);
  const [profile, setProfile] = useState<Profile | null>(null)
  const [kesehatanSubTab, setKesehatanSubTab] = useState<'klinis' | 'hemoglobin' | 'vital'>('klinis')
  const { width } = useWindowDimensions()
  const isSmallScreen = width < 375
  const [summary, setSummary] = useState<Summary | null>(null)
  const [loadingSummary, setLoadingSummary] = useState(false)
  const [errorSummary, setErrorSummary] = useState<string | null>(null)

  // Collapsible section states for Dashboard (default: open / kebuka)
  const [isSummaryOpen, setIsSummaryOpen] = useState(true);
  const [isMenuGridOpen, setIsMenuGridOpen] = useState(true);
  const [isHaidOpen, setIsHaidOpen] = useState(true);
  const [isChartOpen, setIsChartOpen] = useState(true);

  const userMods: string[] = useMemo(() => {
    if (Array.isArray(profile?.active_modules)) {
      return profile.active_modules;
    }
    if (Array.isArray(profile?.instansi?.active_modules)) {
      return profile.instansi.active_modules;
    }
    return [];
  }, [profile]);

  const isModActive = useCallback((modKey: string): boolean => {
    if (!profile) return true; // keep visible during initial load
    return userMods.includes(modKey);
  }, [profile, userMods]);

  const tabs: TabItem[] = useMemo(() => {
    const list: TabItem[] = [
      { key: "ringkasan", label: "Ringkasan", icon: <Icon name="activity" size={20} /> },
    ];

    if (isModActive('kesehatan') || isModActive('hemoglobin') || isModActive('vitalitas')) {
      list.push({ key: "kesehatan", label: "Kesehatan", icon: <Icon name="heart" size={20} /> });
    }

    if (isModActive('olahraga')) {
      list.push({ key: "olahraga", label: "Aktivitas", icon: <Icon name="zap" size={20} /> });
    } else if (isModActive('konsultasi')) {
      list.push({ key: "konseling", label: "Konseling", icon: <Icon name="message-square" size={20} /> });
    }

    list.push({ key: "profil", label: "Profil", icon: <Icon name="user" size={20} /> });
    return list;
  }, [isModActive]);

  useEffect(() => {
    if (kesehatanSubTab === 'klinis' && !isModActive('kesehatan')) {
      if (isModActive('hemoglobin')) setKesehatanSubTab('hemoglobin');
      else if (isModActive('vitalitas')) setKesehatanSubTab('vital');
    } else if (kesehatanSubTab === 'hemoglobin' && !isModActive('hemoglobin')) {
      if (isModActive('kesehatan')) setKesehatanSubTab('klinis');
      else if (isModActive('vitalitas')) setKesehatanSubTab('vital');
    } else if (kesehatanSubTab === 'vital' && !isModActive('vitalitas')) {
      if (isModActive('kesehatan')) setKesehatanSubTab('klinis');
      else if (isModActive('hemoglobin')) setKesehatanSubTab('hemoglobin');
    }
  }, [isModActive, kesehatanSubTab]);
  const [loadingProfile, setLoadingProfile] = useState(false)
  const [kesehatanData, setKesehatanData] = useState<YearData<KesehatanItem>[]>([])
  const [loadingKesehatan, setLoadingKesehatan] = useState(false)
  const [hbData, setHbData] = useState<YearData<HbItem>[]>([])
  const [loadingHb, setLoadingHb] = useState(false)
  const [haidData, setHaidData] = useState<YearData<HaidItem>[]>([])
  const [loadingHaid, setLoadingHaid] = useState(false)
  const [pitaData, setPitaData] = useState<YearData<PitaItem>[]>([])
  const [loadingPita, setLoadingPita] = useState(false)
  const [peminjamanNotification, setPeminjamanNotification] = useState<PeminjamanStatus | null>(null);
  const [initialLoading, setInitialLoading] = useState(true);
  // State untuk modal
  const [riwayatHaidModalVisible, setRiwayatHaidModalVisible] = useState(false)
  const [riwayatPitaModalVisible, setRiwayatPitaModalVisible] = useState(false)
  const [startingHaid, setStartingHaid] = useState(false)
  const [endingHaid, setEndingHaid] = useState(false)
  const [submittingPita, setSubmittingPita] = useState(false)
  // State untuk password modal
  const [modalVisible, setModalVisible] = useState(false)
  const [currentPassword, setCurrentPassword] = useState("")
  const [newPassword, setNewPassword] = useState("")
  const [retypePassword, setRetypePassword] = useState("")
  const [passwordError, setPasswordError] = useState<string | null>(null)
  const [savingPassword, setSavingPassword] = useState(false)
  // State untuk edit profile modal
  const [editProfileModalVisible, setEditProfileModalVisible] = useState(false)
  // State untuk theme hub modal
  const [themeHubModalVisible, setThemeHubModalVisible] = useState(false)
  const { theme, themeId, isDark } = useTheme()
  // State untuk changelog modal
  const [changelogModalVisible, setChangelogModalVisible] = useState(false)
  // State untuk check update modal
  const [checkUpdateVisible, setCheckUpdateVisible] = useState(false)
  const [checkUpdateStatus, setCheckUpdateStatus] = useState<CheckUpdateStatus>('checking')
  const [checkUpdateData, setCheckUpdateData] = useState<SoftwareReleaseData | null>(null)
  const [checkUpdateError, setCheckUpdateError] = useState<string | null>(null)
  const [forceUpgradeVisible, setForceUpgradeVisible] = useState(false)
  const [forceUpgradeData, setForceUpgradeData] = useState<SoftwareReleaseData | null>(null)

  const handleCheckUpdate = async () => {
    setCheckUpdateStatus('checking')
    setCheckUpdateVisible(true)
    setCheckUpdateError(null)
    setCheckUpdateData(null)

    try {
      const res = await checkAppUpdate()

      if (!res.success && res.isOffline) {
        setCheckUpdateStatus('error')
        setCheckUpdateError('Koneksi internet terputus. Silakan hubungkan perangkat Anda ke internet.')
        return
      }

      if (!res.success) {
        setCheckUpdateStatus('error')
        setCheckUpdateError(res.message || 'Gagal memeriksa pembaruan aplikasi.')
        return
      }

      if (res.has_update && res.data) {
        if (res.is_mandatory) {
          setCheckUpdateVisible(false)
          setForceUpgradeData(res.data)
          setForceUpgradeVisible(true)
        } else {
          setCheckUpdateData(res.data)
          setCheckUpdateStatus('update_available')
        }
      } else {
        setCheckUpdateStatus('up_to_date')
      }
    } catch (err: any) {
      setCheckUpdateStatus('error')
      setCheckUpdateError(err?.message || 'Terjadi kesalahan sistem saat memeriksa pembaruan.')
    }
  }

  const handleApplyUpdate = () => {
    const targetUrl =
      checkUpdateData?.download_url ||
      (Platform.OS === 'android'
        ? 'https://play.google.com/store/apps'
        : 'https://apps.apple.com')
    Linking.openURL(targetUrl).catch(() => {})
  }

  // State untuk modal informasi kesehatan
  const [healthInfoModalVisible, setHealthInfoModalVisible] = useState(false)
  // State untuk loading logout
  const [isLoggingOut, setIsLoggingOut] = useState(false)
  // Ambil data haid yang sedang berlangsung
  const currentHaidData = useMemo(() => {
    if (!haidData.length) return null;
    for (const yearData of haidData) {
      for (const monthData of yearData.months) {
        for (const haidItem of monthData.data) {
          if (haidItem.status === 'berlangsung') {
            return haidItem;
          }
        }
      }
    }
    return null;
  }, [haidData]);
  // Ambil data peminjaman pita yang masih aktif
  const activePitaData = useMemo(() => {
    if (!pitaData.length) return null;
    for (const yearData of pitaData) {
      for (const monthData of yearData.months) {
        for (const pitaItem of monthData.data) {
          if (['menunggu', 'dipinjam', 'terlambat'].includes(pitaItem.status)) {
            return pitaItem;
          }
        }
      }
    }
    return null;
  }, [pitaData]);
  // Cek apakah bisa mulai haid (tidak ada yang sedang berlangsung)
  const canStartHaid = useMemo(() => {
    return currentHaidData === null;
  }, [currentHaidData]);
  // Cek apakah bisa mengajukan pita (ada haid berlangsung dan tidak ada pinjaman aktif)
  const canSubmitPita = useMemo(() => {
    return currentHaidData !== null && activePitaData === null;
  }, [currentHaidData, activePitaData]);
  const handleLogout = () => {
    Alert.alert(
      "Keluar dari Akun",
      "Apakah Anda yakin ingin mengakhiri sesi dan keluar dari aplikasi?",
      [
        { text: "Batal", style: "cancel" },
        {
          text: "Ya, Keluar",
          style: "destructive",
          onPress: async () => {
            try {
              setIsLoggingOut(true)
              await logout()
              navigation.reset({
                index: 0,
                routes: [{ name: "Login" }],
              })
            } catch (error) {
              console.log("Logout error:", error)
            } finally {
              setIsLoggingOut(false)
            }
          }
        }
      ]
    )
  }
  const handleChangePassword = async () => {
    if (newPassword !== retypePassword) {
      setPasswordError("Kata sandi baru tidak cocok.");
      return;
    }
    try {
      setSavingPassword(true);
      setPasswordError(null);
      await api.post("/change-password", {
        current_password: currentPassword,
        new_password: newPassword,
        new_password_confirmation: retypePassword,
      });
      setModalVisible(false);
      setCurrentPassword("");
      setNewPassword("");
      setRetypePassword("");
      Alert.alert("Kata sandi berhasil diubah.");
    } catch (e: any) {
      setPasswordError(e?.response?.data?.message || "Gagal mengubah kata sandi.");
    } finally {
      setSavingPassword(false);
    }
  };
  const fetchHaidData = async () => {
    if (!profile) return;
    try {
      setLoadingHaid(true);
      const res = await api.get(`/member/${profile.id}/riwayat-haid`);
      setHaidData(res?.data?.data || []);
    } catch (e: any) {
      setHaidData([]);
    } finally {
      setLoadingHaid(false);
    }
  };
  const fetchPitaData = async () => {
    if (!profile) return;
    try {
      setLoadingPita(true);
      const res = await api.get(`/member/${profile.id}/riwayat-pita`);
      setPitaData(res?.data?.data || []);
    } catch (e: any) {
      console.error("Error fetching pita:", e);
      setPitaData([]);
    } finally {
      setLoadingPita(false);
    }
  };
  const handleStartHaid = async () => {
    try {
      setStartingHaid(true);
      const today = new Date().toISOString().slice(0, 10);
      await api.post("/member/data-haid", {
        tanggal_mulai: today,
        catatan: "Data haid dari aplikasi",
      });
      Alert.alert("Sukses", "Jadwal haid berhasil dimulai");
      await fetchHaidData();
    } catch (e: any) {
      Alert.alert("Error", e?.response?.data?.message || "Gagal memulai jadwal haid");
    } finally {
      setStartingHaid(false);
    }
  };
  const handleEndHaid = async () => {
    if (!currentHaidData) return;
    try {
      setEndingHaid(true);
      await api.put(`/member/data-haid/${currentHaidData.id}`);
      Alert.alert("Sukses", "Jadwal haid berhasil diselesaikan");
      await fetchHaidData();
    } catch (e: any) {
      Alert.alert("Error", e?.response?.data?.message || "Gagal menyelesaikan jadwal haid");
    } finally {
      setEndingHaid(false);
    }
  };
  const handleSubmitPita = async () => {
    if (!canSubmitPita) return;
    try {
      setSubmittingPita(true);
      const today = new Date().toISOString().slice(0, 10);
      await api.post("/member/peminjaman-pita", {
        jumlah_pita: 1,
        tanggal_pinjam: today,
      });
      Alert.alert("Sukses", "Pengajuan peminjaman pita berhasil dikirim");
      await fetchPitaData();
    } catch (e: any) {
      Alert.alert("Error", e?.response?.data?.message || "Gagal mengajukan peminjaman pita");
    } finally {
      setSubmittingPita(false);
    }
  };
  const [refreshing, setRefreshing] = useState(false);

  // Reusable profile & active modules fetcher
  const fetchProfileAndModules = useCallback(async (isSilent = false) => {
    try {
      if (!isSilent) setLoadingProfile(true);
      const meRes = await api.get("/me");
      const profileData = meRes?.data?.data || meRes?.data || null;
      if (profileData) {
        setProfile(profileData);
      }
    } catch (e) {
      console.log("Error fetching profile/modules:", e);
    } finally {
      if (!isSilent) setLoadingProfile(false);
    }
  }, []);

  const fetchAll = useCallback(async () => {
    setLoadingSummary(true);
    setLoadingProfile(true);
    setErrorSummary(null);
    setInitialLoading(true);
    try {
      const [sumRes, meRes] = await Promise.all([
        api.get("/member/summary"),
        api.get("/me"),
      ]);
      setSummary(sumRes?.data?.data || sumRes?.data || null);
      const profileData = meRes?.data?.data || meRes?.data || null;
      setProfile(profileData);
      const hasHaidMod = profileData?.active_modules ? profileData.active_modules.includes('haid_pita') : true;
      if (profileData?.jk === "P" && hasHaidMod) {
        setLoadingHaid(true);
        setLoadingPita(true);
        const [haidRes, pitaRes, notifRes] = await Promise.all([
          api.get(`/member/${profileData.id}/riwayat-haid`),
          api.get(`/member/${profileData.id}/riwayat-pita`),
          api.get('/member/check-peminjaman-status')
        ]);
        setHaidData(haidRes?.data?.data || []);
        setPitaData(pitaRes?.data?.data || []);
        setPeminjamanNotification(notifRes?.data?.data || null);
      }
    } catch (e: any) {
      setErrorSummary(e?.message || "Gagal memuat data");
    } finally {
      setLoadingSummary(false);
      setLoadingProfile(false);
      setLoadingHaid(false);
      setLoadingPita(false);
      setInitialLoading(false);
    }
  }, []);

  const handleRefresh = useCallback(async () => {
    setRefreshing(true);
    try {
      await fetchAll();
    } finally {
      setRefreshing(false);
    }
  }, [fetchAll]);

  // 1. Initial Load
  useEffect(() => {
    fetchAll();
  }, [fetchAll]);

  // 2. Webhook-Style Conditional Realtime Sync (Zero Overhead / 304 Not Modified):
  // Simultaneously checks Maintenance Mode, Mandatory Update, and Active Feature Modules in 1 request.
  const handleActiveModulesChange = useCallback((newMods: string[]) => {
    setProfile(prev => {
      if (!prev) return prev;
      const prevMods = prev.active_modules || [];
      const prevStr = [...prevMods].sort().join(',');
      const newStr = [...newMods].sort().join(',');
      if (prevStr !== newStr) {
        return {
          ...prev,
          active_modules: newMods,
          instansi: prev.instansi ? { ...prev.instansi, active_modules: newMods } : prev.instansi,
        };
      }
      return prev;
    });
  }, []);

  const {
    isMaintenance,
    maintenanceMessage,
    isMandatoryUpgrade,
    forceUpgradeData: syncForceUpgradeData,
    recheckSync,
  } = useRealtimeSystemSync({
    onActiveModulesChange: handleActiveModulesChange,
    intervalMs: 4000,
  });

  // 3. useFocusEffect: Immediately refresh auth & profile when screen is focused
  useFocusEffect(
    useCallback(() => {
      const check = async () => {
        const user = await checkAuthStatus();
        if (!user) {
          navigation.navigate('Login');
          return;
        }
        // Immediate module & profile sync on screen focus
        fetchProfileAndModules(true);
      };
      check();
    }, [navigation, fetchProfileAndModules])
  );

  // 4. AppState listener: Refresh instantly when switching back from browser to app
  useEffect(() => {
    const subscription = AppState.addEventListener('change', (nextAppState) => {
      if (nextAppState === 'active') {
        fetchProfileAndModules(true);
      }
    });
    return () => subscription.remove();
  }, [fetchProfileAndModules]);

  // 5. Auto bounce-back if user is currently inside a disabled module sub-screen
  useEffect(() => {
    if (active === 'ringkasan' || active === 'profil') return;
    const modKey = active === 'konseling' ? 'konsultasi' : active;
    if (!isModActive(modKey)) {
      setActive('ringkasan');
      Alert.alert("Fitur Dinonaktifkan", "Modul ini telah dinonaktifkan oleh Administrator Institusi Anda.");
    }
  }, [active, isModActive]);

  useEffect(() => {
    if (active === "kesehatan" && profile?.id) {
      if (hasFetchedKesehatan.current === String(profile.id)) return;
      const fetchKesehatan = async () => {
        try {
          setLoadingKesehatan(true);
          const res = await api.get(`/member/${profile.id}/kesehatan`);
          setKesehatanData(res?.data?.data || []);
          hasFetchedKesehatan.current = String(profile.id);
        } catch (e: any) {
          console.error("Error fetching kesehatan:", e);
          setKesehatanData([]);
        } finally {
          setLoadingKesehatan(false);
        }
      };
      fetchKesehatan();
    }
  }, [active, profile?.id]);

  useEffect(() => {
    if ((active === "hemoglobin" || (active === "kesehatan" && kesehatanSubTab === "hemoglobin")) && profile?.id) {
      if (hasFetchedHb.current === String(profile.id)) return;
      const fetchHb = async () => {
        try {
          setLoadingHb(true);
          const res = await api.get(`/member/${profile.id}/hb`);
          setHbData(res?.data?.data || []);
          hasFetchedHb.current = String(profile.id);
        } catch (e: any) {
          console.error("Error fetching hb:", e);
          setHbData([]);
        } finally {
          setLoadingHb(false);
        }
      };
      fetchHb();
    }
  }, [active, kesehatanSubTab, profile?.id]);
  const getGreetingTime = (): string => {
    const hour = new Date().getHours();
    if (hour >= 0 && hour < 12) return "Selamat pagi 🌥️";
    else if (hour >= 12 && hour < 15) return "Selamat siang 🌞";
    else if (hour >= 15 && hour < 18) return "Selamat sore 🌤️";
    else return "Selamat malam 🌜";
  };
  const getStatusColor = (status: string) => {
    switch (status.toLowerCase()) {
      case 'menunggu': return '#f59e0b';
      case 'dipinjam': return '#8b5cf6';
      case 'terlambat': return '#ef4444';
      case 'dikembalikan': return '#10b981';
      case 'ditolak': return '#ef4444';
      default: return Colors.muted;
    }
  };
  const getStatusLabel = (status: string) => {
    switch (status.toLowerCase()) {
      case 'menunggu': return 'Menunggu Verifikasi';
      case 'dipinjam': return 'Sedang Dipinjam';
      case 'terlambat': return 'Terlambat';
      case 'dikembalikan': return 'Dikembalikan';
      case 'ditolak': return 'Ditolak';
      default: return status;
    }
  };
  const NotificationBanner = ({ notification }: { notification: PeminjamanNotification }) => {
    const bgColor = notification.type === 'danger' ? '#fee2e2' :
      notification.type === 'warning' ? '#fef3c7' : '#dbeafe';
    const iconColor = notification.type === 'danger' ? '#ef4444' :
      notification.type === 'warning' ? '#f59e0b' : '#3b82f6';
    const textColor = notification.type === 'danger' ? '#991b1b' :
      notification.type === 'warning' ? '#92400e' : '#1e40af';
    return (
      <View style={[styles.notificationBanner, { backgroundColor: bgColor }]}>
        <Icon name="alert-circle" size={20} color={iconColor} />{/* */}
        <View style={{ flex: 1, marginLeft: moderateScale(12) }}>
          <Text style={[styles.notificationTitle, { color: textColor }]}>
            {notification.title}
          </Text>{/* */}
          <Text style={[styles.notificationText, { color: textColor }]}>
            {notification.message}
          </Text>
        </View>
      </View>
    );
  };
  const pitaButtonColor = activePitaData
    ? getStatusColor(activePitaData.status)
    : canSubmitPita ? '#8b5cf6' : Colors.muted;

  // Palet Warna:
  // Primer: Putih (Colors.white / surface card)
  // Sekunder: Hijau kesehatan (Colors.primary = #059669)
  // Tersier: Warna cover divisi pengguna yang ditentukan oleh Admin Instansi
  const hasDivisi = Boolean(profile?.divisi || profile?.id_divisi);
  const tertiaryColor = profile?.color_cover || profile?.tertiary_color || Colors.defaultTertiary || '#2e7d32';

  const isAtlas = themeId === 'atlas_modern';
  const effectivePrimary = isAtlas ? (isDark ? '#6366F1' : '#4F46E5') : tertiaryColor;
  const effectiveSecondary = isAtlas ? (isDark ? '#22D3EE' : '#06B6D4') : '#10B981';
  const effectiveAccent = isAtlas ? (isDark ? '#A78BFA' : '#8B5CF6') : '#14B8A6';
  const effectiveGlow = isAtlas ? theme.glow : tertiaryColor;

  const shimmerTranslateX = shimmerAnim.interpolate({
    inputRange: [0, 1],
    outputRange: [-SCREEN_WIDTH * 0.9, SCREEN_WIDTH * 1.3],
  });
  const shimmerOpacity = shimmerAnim.interpolate({
    inputRange: [0, 0.15, 0.85, 1],
    outputRange: [0, 1, 1, 0],
  });

  const currentTabKey = useMemo(() => {
    if (['kesehatan', 'hemoglobin', 'vital'].includes(active)) return 'kesehatan';
    if (active === 'olahraga') return 'olahraga';
    if (active === 'konseling' && tabs.some(t => t.key === 'konseling')) return 'konseling';
    if (active === 'profil') return 'profil';
    return 'ringkasan';
  }, [active, tabs]);

  const bmiInfo = useMemo(() => {
    const val = summary?.bmi ?? 0;
    if (val === 0) {
      return {
        val: 0,
        status: "Belum Ada Data",
        color: theme.textSecondary,
        bg: isDark ? "rgba(148, 163, 184, 0.15)" : "#F1F5F9",
      };
    }
    if (val < 18.5) {
      return {
        val,
        status: "Kurang Berat",
        color: isDark ? "#FBBF24" : "#D97706",
        bg: isDark ? "rgba(245, 158, 11, 0.18)" : "#FFFBEB",
      };
    }
    if (val <= 24.9) {
      return {
        val,
        status: "Optimal / Normal",
        color: isDark ? "#34D399" : "#059669",
        bg: isDark ? "rgba(16, 185, 129, 0.18)" : "#ECFDF5",
      };
    }
    if (val <= 29.9) {
      return {
        val,
        status: "Kelebihan Berat",
        color: isDark ? "#FB923C" : "#EA580C",
        bg: isDark ? "rgba(249, 115, 22, 0.18)" : "#FFF7ED",
      };
    }
    return {
      val,
      status: "Obesitas",
      color: isDark ? "#F87171" : "#DC2626",
      bg: isDark ? "rgba(239, 68, 68, 0.18)" : "#FEF2F2",
    };
  }, [summary?.bmi, isDark, theme.textSecondary]);

  return (
    <SafeAreaView edges={["top", "left", "right"]} style={[styles.safe, { flex: 1, backgroundColor: theme.bg }]}>
      <StatusBar barStyle={isDark ? "light-content" : "dark-content"} backgroundColor={theme.bg} />
      <ScrollView
        contentContainerStyle={[styles.scroll, {
          flexGrow: 1,
          backgroundColor: theme.bg,
          paddingBottom: verticalScale(120),
          paddingTop: verticalScale(30),
          paddingHorizontal: isSmallScreen ? horizontalScale(12) : horizontalScale(16)
        }]}
        showsVerticalScrollIndicator={false}
        showsHorizontalScrollIndicator={false}
        bounces={true}
        keyboardShouldPersistTaps="handled"
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={handleRefresh}
            colors={[effectivePrimary, effectiveSecondary]}
            tintColor={effectivePrimary}
          />
        }
      >
        <View style={[
          styles.header, 
          { 
            shadowColor: isDark ? "#000" : (isAtlas ? effectivePrimary : (hasDivisi ? tertiaryColor : "#000")),
            shadowOpacity: isDark ? 0.35 : (isAtlas ? 0.18 : (hasDivisi ? 0.16 : 0.08)),
            shadowRadius: isAtlas ? 16 : 14,
            borderRadius: isAtlas ? moderateScale(22) : moderateScale(16),
            borderColor: isDark ? (theme.border || '#334155') : (isAtlas ? '#E0E7FF' : (hasDivisi ? tertiaryColor + '30' : theme.border)),
            borderWidth: isAtlas ? 1.5 : (hasDivisi ? 1.2 : StyleSheet.hairlineWidth),
            backgroundColor: theme.surface,
          }
        ]}>
          <View style={StyleSheet.absoluteFill} pointerEvents="none">
            <View 
              style={{
                position: 'absolute',
                top: 0,
                left: 0,
                right: 0,
                height: isAtlas ? 3.5 : 2.5,
                backgroundColor: isAtlas ? effectivePrimary : (hasDivisi ? tertiaryColor : 'transparent'),
                borderTopLeftRadius: isAtlas ? moderateScale(22) : moderateScale(16),
                borderTopRightRadius: isAtlas ? moderateScale(22) : moderateScale(16),
                opacity: 0.95,
              }} 
            />
            {isAtlas && (
              <View 
                style={{
                  position: 'absolute',
                  top: 0,
                  right: 0,
                  width: '45%',
                  height: 3.5,
                  backgroundColor: effectiveSecondary,
                  borderTopRightRadius: moderateScale(22),
                  opacity: 0.9,
                }} 
              />
            )}
            <View 
              style={{
                position: 'absolute',
                top: -moderateScale(35),
                right: -moderateScale(25),
                width: moderateScale(160),
                height: moderateScale(160),
                borderRadius: moderateScale(80),
                backgroundColor: effectivePrimary,
                opacity: isAtlas ? (isDark ? 0.08 : 0.05) : (hasDivisi ? 0.04 : 0),
              }} 
            />
          </View>
          <View style={styles.headerRow}>
            <View style={styles.greetingContainer}>
              {isAtlas && (
                <View style={{
                  flexDirection: 'row',
                  alignItems: 'center',
                  alignSelf: 'flex-start',
                  gap: horizontalScale(5),
                  paddingHorizontal: horizontalScale(8),
                  paddingVertical: verticalScale(2.5),
                  borderRadius: moderateScale(12),
                  backgroundColor: isDark ? 'rgba(99, 102, 241, 0.2)' : '#EEF2FF',
                  borderWidth: 1,
                  borderColor: isDark ? 'rgba(99, 102, 241, 0.45)' : '#C7D2FE',
                  marginBottom: verticalScale(4),
                }}>
                  <Icon name="grid" size={10} color={isDark ? '#818CF8' : '#4F46E5'} />
                  <Text style={{
                    fontSize: moderateScale(10),
                    fontWeight: '800',
                    color: isDark ? '#A5B4FC' : '#4338CA',
                    letterSpacing: 0.6,
                  }}>
                    ATLAS NEO PORTAL
                  </Text>
                </View>
              )}
              {active === "profil" ? (
                <View style={[styles.profileHeroTag, { backgroundColor: `${effectivePrimary}15` }]}>
                  <Icon name="user-check" size={11} color={effectivePrimary} />
                  <Text style={[styles.profileHeroTagText, { color: effectivePrimary }]}>
                    Profil Pengguna
                  </Text>
                </View>
              ) : (
                <Text style={[styles.greet, { color: theme.textSecondary }]}>
                  {getGreetingTime()},
                </Text>
              )}
              <Text style={[styles.name, { color: theme.text }]} numberOfLines={2} ellipsizeMode="tail">
                {profile?.nama || "Member"}
              </Text>
              <View style={{ flexDirection: 'row', alignItems: 'center', gap: horizontalScale(6), flexWrap: 'wrap', marginTop: verticalScale(3) }}>
                {profile?.divisi ? (
                  <View style={[
                    styles.divisiBadge, 
                    {
                      backgroundColor: isAtlas ? (isDark ? 'rgba(34, 211, 238, 0.15)' : 'rgba(6, 182, 212, 0.12)') : tertiaryColor + '14', 
                      borderColor: isAtlas ? (isDark ? 'rgba(34, 211, 238, 0.4)' : 'rgba(6, 182, 212, 0.35)') : tertiaryColor + '38',
                      borderWidth: 1,
                      marginTop: 0,
                    }
                  ]}>
                    <View style={[styles.divisiDot, { backgroundColor: isAtlas ? (isDark ? '#22D3EE' : '#0891B2') : tertiaryColor }]} />
                    <Text style={[styles.divisiBadgeText, { color: isAtlas ? (isDark ? '#22D3EE' : '#0891B2') : tertiaryColor }]} numberOfLines={1}>
                      {profile.divisi}
                    </Text>
                  </View>
                ) : null}
                {active === "profil" && (
                  <View style={[
                    styles.divisiBadge,
                    {
                      backgroundColor: isAtlas ? (isDark ? 'rgba(167, 139, 250, 0.18)' : '#F3E8FF') : '#0d948815',
                      borderColor: isAtlas ? (isDark ? 'rgba(167, 139, 250, 0.4)' : '#DDD6FE') : '#0d948835',
                      borderWidth: 1,
                      marginTop: 0,
                    }
                  ]}>
                    <Icon name="shield" size={10} color={isAtlas ? (isDark ? '#C084FC' : '#7C3AED') : '#0d9488'} />
                    <Text style={[styles.divisiBadgeText, { color: isAtlas ? (isDark ? '#C084FC' : '#7C3AED') : '#0d9488' }]} numberOfLines={1}>
                      {profile?.level || 'Member'}
                    </Text>
                  </View>
                )}
              </View>
            </View>
            <View style={{ flexDirection: 'row', alignItems: 'center', gap: horizontalScale(8) }}>
              <Pressable
                onPress={() => setThemeHubModalVisible(true)}
                style={({ pressed }) => [
                  {
                    width: moderateScale(42),
                    height: moderateScale(42),
                    borderRadius: moderateScale(21),
                    alignItems: 'center',
                    justifyContent: 'center',
                    backgroundColor: isAtlas ? (isDark ? 'rgba(99, 102, 241, 0.2)' : '#EEF2FF') : '#ECFDF5',
                    borderWidth: 1.5,
                    borderColor: isAtlas ? (isDark ? 'rgba(99, 102, 241, 0.5)' : '#C7D2FE') : '#A7F3D0',
                    shadowColor: effectivePrimary,
                    shadowOpacity: isAtlas ? 0.25 : 0.1,
                    shadowRadius: 4,
                  },
                  pressed && { opacity: 0.8, transform: [{ scale: 0.94 }] },
                ]}
              >
                <Icon
                  name={isAtlas ? (isDark ? 'moon' : 'sun') : 'layout'}
                  size={18}
                  color={effectivePrimary}
                />
              </Pressable>
              <Pressable 
                onPress={() => {
                  if (active === "profil") {
                    setEditProfileModalVisible(true);
                  } else {
                    setActive("profil");
                  }
                }}
                style={({ pressed }) => [
                  styles.profileBadge, 
                  { 
                    backgroundColor: isAtlas ? (isDark ? 'rgba(99, 102, 241, 0.2)' : '#EEF2FF') : tertiaryColor + '12', 
                    borderColor: isAtlas ? (isDark ? '#6366F1' : '#4F46E5') : tertiaryColor + '40',
                    borderWidth: isAtlas ? 2 : 1.5,
                    shadowColor: isAtlas ? (isDark ? '#6366F1' : '#4F46E5') : tertiaryColor,
                    shadowOpacity: isAtlas ? 0.35 : 0.15,
                    shadowRadius: isAtlas ? 8 : 6,
                    overflow: 'hidden' 
                  },
                  pressed && { opacity: 0.85, transform: [{ scale: 0.96 }] }
                ]}
              >
                {profile?.avatar ? (
                  <Image source={{ uri: profile.avatar }} style={{ width: '100%', height: '100%' }} />
                ) : (
                  <Icon name="user" size={20} color={effectivePrimary} />
                )}
                {active === "profil" && (
                  <View style={[styles.avatarEditIconBadge, { backgroundColor: effectivePrimary }]}>
                    <Icon name="camera" size={9} color={Colors.white} />
                  </View>
                )}
              </Pressable>
            </View>
          </View>
        </View>

        <Animated.View style={{ opacity: fadeTab }}>
          {active === "ringkasan" && (
            <View style={{ gap: verticalScale(12) }}>
              <Card
                style={{
                  padding: isSmallScreen ? moderateScale(18) : moderateScale(22),
                  borderTopWidth: isAtlas ? 0 : 3.5,
                  borderTopColor: effectivePrimary,
                  overflow: 'hidden',
                }}
              >
                <Pressable
                  onPress={() => setIsSummaryOpen(prev => !prev)}
                  style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: isSummaryOpen ? verticalScale(10) : 0 }}
                >
                  <View style={{ flexDirection: 'row', alignItems: 'center', gap: moderateScale(8) }}>
                    {isAtlas && (
                      <View style={{
                        width: moderateScale(4),
                        height: moderateScale(18),
                        borderRadius: moderateScale(2),
                        backgroundColor: isDark ? '#818CF8' : '#4F46E5',
                      }} />
                    )}
                    <Text style={[styles.cardTitle, isAtlas && { color: theme.text, fontSize: moderateScale(17), fontWeight: '800', letterSpacing: -0.3 }]}>
                      Ringkasan Kesehatan
                    </Text>
                  </View>
                  <View style={[styles.collapseArrowBtn, { backgroundColor: isAtlas ? (isDark ? 'rgba(99, 102, 241, 0.2)' : '#EEF2FF') : tertiaryColor + '15' }]}>
                    <Icon name={isSummaryOpen ? "chevron-up" : "chevron-down"} size={18} color={effectivePrimary} />
                  </View>
                </Pressable>
                {isSummaryOpen && (
                  <>
                    {loadingSummary ? (
                      <View style={styles.centerRow}>
                        <ActivityIndicator color={effectivePrimary} />
                        <Text style={styles.mutedText}>Memuat...</Text>
                      </View>
                    ) : errorSummary ? (
                      <Text style={[styles.mutedText, { color: "#dc2626" }]}>{errorSummary}</Text>
                    ) : isAtlas ? (
                      <View style={{ gap: verticalScale(12), marginTop: verticalScale(4) }}>
                        {/* HERO BENTO CARD: IMT / BMI VITALITY */}
                        <View
                          style={{
                            backgroundColor: isDark ? 'rgba(30, 41, 59, 0.75)' : '#F8FAFC',
                            borderRadius: moderateScale(18),
                            padding: moderateScale(16),
                            borderWidth: 1.2,
                            borderColor: isDark ? 'rgba(99, 102, 241, 0.25)' : '#E0E7FF',
                            shadowColor: isDark ? '#000000' : 'rgba(79, 70, 229, 0.1)',
                            shadowOffset: { width: 0, height: 4 },
                            shadowOpacity: isDark ? 0.3 : 0.08,
                            shadowRadius: 10,
                            elevation: 2,
                          }}
                        >
                          <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: verticalScale(10) }}>
                            <View style={{ flexDirection: 'row', alignItems: 'center', gap: horizontalScale(6) }}>
                              <View style={{
                                width: moderateScale(26),
                                height: moderateScale(26),
                                borderRadius: moderateScale(8),
                                backgroundColor: isDark ? 'rgba(99, 102, 241, 0.25)' : '#EEF2FF',
                                alignItems: 'center',
                                justifyContent: 'center',
                              }}>
                                <Icon name="activity" size={14} color={isDark ? '#818CF8' : '#4F46E5'} />
                              </View>
                              <Text style={{
                                fontSize: moderateScale(11),
                                fontWeight: '700',
                                color: theme.textSecondary,
                                letterSpacing: 0.8,
                                textTransform: 'uppercase',
                              }}>
                                Indeks Massa Tubuh
                              </Text>
                            </View>

                            <View
                              style={{
                                paddingHorizontal: horizontalScale(10),
                                paddingVertical: verticalScale(4),
                                borderRadius: moderateScale(20),
                                backgroundColor: bmiInfo.bg,
                                borderWidth: 1,
                                borderColor: `${bmiInfo.color}40`,
                              }}
                            >
                              <Text style={{ fontSize: moderateScale(11), fontWeight: '700', color: bmiInfo.color }}>
                                {bmiInfo.status}
                              </Text>
                            </View>
                          </View>

                          <View style={{ flexDirection: 'row', alignItems: 'baseline', marginBottom: verticalScale(12) }}>
                            <Text style={{
                              fontSize: moderateScale(34),
                              fontWeight: '900',
                              color: theme.text,
                              letterSpacing: -1,
                            }}>
                              {(summary?.bmi ?? 0).toFixed(1)}
                            </Text>
                            <Text style={{
                              fontSize: moderateScale(14),
                              fontWeight: '700',
                              color: isDark ? '#22D3EE' : '#0891B2',
                              marginLeft: horizontalScale(6),
                            }}>
                              kg/m²
                            </Text>
                            <View style={{ flex: 1 }} />
                            <Text style={{
                              fontSize: moderateScale(11),
                              color: theme.textSecondary,
                              fontWeight: '500',
                            }}>
                              Rentang Ideal: 18.5 - 24.9
                            </Text>
                          </View>

                          {/* 4-Segment Spectrum Bar */}
                          <View style={{ flexDirection: 'row', gap: horizontalScale(4), height: verticalScale(6), borderRadius: moderateScale(3), overflow: 'hidden' }}>
                            <View style={{ flex: 1, backgroundColor: bmiInfo.val > 0 && bmiInfo.val < 18.5 ? '#F59E0B' : (isDark ? 'rgba(245, 158, 11, 0.25)' : '#FDE68A'), borderRadius: moderateScale(3) }} />
                            <View style={{ flex: 1.5, backgroundColor: bmiInfo.val >= 18.5 && bmiInfo.val <= 24.9 ? '#10B981' : (isDark ? 'rgba(16, 185, 129, 0.25)' : '#A7F3D0'), borderRadius: moderateScale(3) }} />
                            <View style={{ flex: 1, backgroundColor: bmiInfo.val > 24.9 && bmiInfo.val <= 29.9 ? '#F97316' : (isDark ? 'rgba(249, 115, 22, 0.25)' : '#FDBA74'), borderRadius: moderateScale(3) }} />
                            <View style={{ flex: 1, backgroundColor: bmiInfo.val > 29.9 ? '#EF4444' : (isDark ? 'rgba(239, 68, 68, 0.25)' : '#FECACA'), borderRadius: moderateScale(3) }} />
                          </View>
                          <View style={{ flexDirection: 'row', justifyContent: 'space-between', marginTop: verticalScale(4) }}>
                            <Text style={{ fontSize: moderateScale(9), color: theme.textSecondary, fontWeight: '600' }}>Kurus</Text>
                            <Text style={{ fontSize: moderateScale(9), color: isDark ? '#34D399' : '#059669', fontWeight: '700' }}>Normal</Text>
                            <Text style={{ fontSize: moderateScale(9), color: theme.textSecondary, fontWeight: '600' }}>Berlebih</Text>
                            <Text style={{ fontSize: moderateScale(9), color: theme.textSecondary, fontWeight: '600' }}>Obesitas</Text>
                          </View>
                        </View>

                        {/* TRIPLE TELEMETRY TILES */}
                        <View style={{ flexDirection: 'row', gap: horizontalScale(8) }}>
                          {/* Tekanan Darah */}
                          <View
                            style={{
                              flex: 1,
                              backgroundColor: isDark ? 'rgba(30, 41, 59, 0.65)' : '#FFFFFF',
                              borderRadius: moderateScale(16),
                              padding: moderateScale(12),
                              borderWidth: 1.2,
                              borderColor: isDark ? 'rgba(244, 63, 94, 0.25)' : '#FFE4E6',
                              shadowColor: '#F43F5E',
                              shadowOpacity: isDark ? 0.2 : 0.05,
                              shadowRadius: 6,
                              elevation: 2,
                            }}
                          >
                            <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: verticalScale(8) }}>
                              <View style={{
                                width: moderateScale(28),
                                height: moderateScale(28),
                                borderRadius: moderateScale(8),
                                backgroundColor: isDark ? 'rgba(244, 63, 94, 0.18)' : '#FFE4E6',
                                alignItems: 'center',
                                justifyContent: 'center',
                              }}>
                                <Icon name="heart" size={14} color="#F43F5E" />
                              </View>
                              <Text style={{
                                fontSize: moderateScale(9),
                                fontWeight: '800',
                                color: '#F43F5E',
                                textTransform: 'uppercase',
                              }}>
                                DARAH
                              </Text>
                            </View>
                            <Text
                              style={{
                                fontSize: moderateScale(15),
                                fontWeight: '800',
                                color: theme.text,
                                letterSpacing: -0.3,
                              }}
                              numberOfLines={1}
                            >
                              {`${summary?.sistol ?? 0}/${summary?.diastol ?? 0}`}
                            </Text>
                            <Text style={{ fontSize: moderateScale(10), color: '#F43F5E', fontWeight: '700', marginTop: verticalScale(1) }}>
                              mmHg
                            </Text>
                            <Text style={{ fontSize: moderateScale(10), color: theme.textSecondary, fontWeight: '500', marginTop: verticalScale(2) }}>
                              Sistol / Diastol
                            </Text>
                          </View>

                          {/* Postur Tubuh */}
                          <View
                            style={{
                              flex: 1,
                              backgroundColor: isDark ? 'rgba(30, 41, 59, 0.65)' : '#FFFFFF',
                              borderRadius: moderateScale(16),
                              padding: moderateScale(12),
                              borderWidth: 1.2,
                              borderColor: isDark ? 'rgba(99, 102, 241, 0.25)' : '#E0E7FF',
                              shadowColor: '#6366F1',
                              shadowOpacity: isDark ? 0.2 : 0.05,
                              shadowRadius: 6,
                              elevation: 2,
                            }}
                          >
                            <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: verticalScale(8) }}>
                              <View style={{
                                width: moderateScale(28),
                                height: moderateScale(28),
                                borderRadius: moderateScale(8),
                                backgroundColor: isDark ? 'rgba(99, 102, 241, 0.18)' : '#EEF2FF',
                                alignItems: 'center',
                                justifyContent: 'center',
                              }}>
                                <Icon name="user" size={14} color={isDark ? '#818CF8' : '#4F46E5'} />
                              </View>
                              <Text style={{
                                fontSize: moderateScale(9),
                                fontWeight: '800',
                                color: isDark ? '#818CF8' : '#4F46E5',
                                textTransform: 'uppercase',
                              }}>
                                POSTUR
                              </Text>
                            </View>
                            <View style={{ flexDirection: 'row', alignItems: 'baseline', gap: horizontalScale(2) }}>
                              <Text style={{ fontSize: moderateScale(13), fontWeight: '800', color: theme.text }}>
                                {summary?.tinggi ?? 0}
                              </Text>
                              <Text style={{ fontSize: moderateScale(9), color: theme.textSecondary, fontWeight: '600' }}>cm</Text>
                              <Text style={{ fontSize: moderateScale(11), color: theme.textSecondary, opacity: 0.5 }}>•</Text>
                              <Text style={{ fontSize: moderateScale(13), fontWeight: '800', color: theme.text }}>
                                {summary?.berat ?? 0}
                              </Text>
                              <Text style={{ fontSize: moderateScale(9), color: theme.textSecondary, fontWeight: '600' }}>kg</Text>
                            </View>
                            <Text style={{ fontSize: moderateScale(10), color: isDark ? '#818CF8' : '#4F46E5', fontWeight: '700', marginTop: verticalScale(1) }}>
                              TB • BB
                            </Text>
                            <Text style={{ fontSize: moderateScale(10), color: theme.textSecondary, fontWeight: '500', marginTop: verticalScale(2) }}>
                              Tinggi & Berat
                            </Text>
                          </View>

                          {/* Hb */}
                          <View
                            style={{
                              flex: 1,
                              backgroundColor: isDark ? 'rgba(30, 41, 59, 0.65)' : '#FFFFFF',
                              borderRadius: moderateScale(16),
                              padding: moderateScale(12),
                              borderWidth: 1.2,
                              borderColor: isDark ? 'rgba(236, 72, 153, 0.25)' : '#FCE7F3',
                              shadowColor: '#EC4899',
                              shadowOpacity: isDark ? 0.2 : 0.05,
                              shadowRadius: 6,
                              elevation: 2,
                            }}
                          >
                            <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: verticalScale(8) }}>
                              <View style={{
                                width: moderateScale(28),
                                height: moderateScale(28),
                                borderRadius: moderateScale(8),
                                backgroundColor: isDark ? 'rgba(236, 72, 153, 0.18)' : '#FCE7F3',
                                alignItems: 'center',
                                justifyContent: 'center',
                              }}>
                                <Icon name="droplet" size={14} color="#EC4899" />
                              </View>
                              <Text style={{
                                fontSize: moderateScale(9),
                                fontWeight: '800',
                                color: '#EC4899',
                                textTransform: 'uppercase',
                              }}>
                                HB
                              </Text>
                            </View>
                            <Text
                              style={{
                                fontSize: moderateScale(15),
                                fontWeight: '800',
                                color: theme.text,
                                letterSpacing: -0.3,
                              }}
                              numberOfLines={1}
                            >
                              {`${summary?.hb ?? 0}`}
                            </Text>
                            <Text style={{ fontSize: moderateScale(10), color: '#EC4899', fontWeight: '700', marginTop: verticalScale(1) }}>
                              g/dL
                            </Text>
                            <Text style={{ fontSize: moderateScale(10), color: theme.textSecondary, fontWeight: '500', marginTop: verticalScale(2) }}>
                              Hemoglobin
                            </Text>
                          </View>
                        </View>
                      </View>
                    ) : (
                      <>
                        <View style={styles.metrics}>
                          <Metric label="IMT" value={(summary?.bmi ?? 0).toFixed(1)} unit="" />
                          <Metric label="Tinggi" value={`${summary?.tinggi ?? 0}`} unit="cm" />
                          <Metric label="Berat" value={`${summary?.berat ?? 0}`} unit="kg" />
                        </View>
                        <View style={styles.metrics}>
                          <Metric label="Sistol" value={`${summary?.sistol ?? 0}`} unit="mmHg" />
                          <Metric label="Diastol" value={`${summary?.diastol ?? 0}`} unit="mmHg" />
                          <Metric label="Hb" value={`${summary?.hb ?? 0}`} unit="g/dL" />
                        </View>
                      </>
                    )}
                  </>
                )}
              </Card>

              {/* MENU LAYANAN & FITUR UTAMA DASHBOARD */}
              <Card style={{ padding: isSmallScreen ? moderateScale(16) : moderateScale(20) }}>
                <Pressable
                  onPress={() => setIsMenuGridOpen(prev => !prev)}
                  style={[
                    styles.menuGridHeader,
                    {
                      flexDirection: 'row',
                      alignItems: 'center',
                      justifyContent: 'space-between',
                      marginBottom: isMenuGridOpen ? verticalScale(12) : 0,
                    }
                  ]}
                >
                  <View style={{ flex: 1, paddingRight: moderateScale(10), flexDirection: 'row', alignItems: 'center', gap: moderateScale(8) }}>
                    {isAtlas && (
                      <View style={{
                        width: moderateScale(4),
                        height: moderateScale(18),
                        borderRadius: moderateScale(2),
                        backgroundColor: isDark ? '#22D3EE' : '#0891B2',
                      }} />
                    )}
                    <View style={{ flex: 1 }}>
                      <Text style={[styles.cardTitle, isAtlas && { color: theme.text, fontSize: moderateScale(17), fontWeight: '800', letterSpacing: -0.3 }]}>
                        Layanan & Fitur
                      </Text>
                      <Text style={[styles.menuGridSubtitle, isAtlas && { color: theme.textSecondary }]}>
                        Akses cepat fitur kesehatan Anda
                      </Text>
                    </View>
                  </View>
                  <View style={[styles.collapseArrowBtn, { backgroundColor: isAtlas ? (isDark ? 'rgba(99, 102, 241, 0.2)' : '#EEF2FF') : tertiaryColor + '15' }]}>
                    <Icon name={isMenuGridOpen ? "chevron-up" : "chevron-down"} size={18} color={effectivePrimary} />
                  </View>
                </Pressable>

                {isMenuGridOpen && (
                  <View style={[styles.menuGrid, isAtlas && { rowGap: verticalScale(10) }]}>
                    {isModActive('kesehatan') && (
                      <LauncherTile
                        icon="heart"
                        iconColor="#dc2626"
                        iconBgLight="#fef2f2"
                        iconBgDark="rgba(220, 38, 38, 0.18)"
                        title="Klinis"
                        subtitle="Cek Fisik"
                        onPress={() => {
                          setKesehatanSubTab('klinis');
                          setActive('kesehatan');
                        }}
                      />
                    )}

                    {isModActive('olahraga') && (
                      <LauncherTile
                        icon="zap"
                        iconColor="#059669"
                        iconBgLight="#ecfdf5"
                        iconBgDark="rgba(5, 150, 105, 0.18)"
                        title="Olahraga"
                        subtitle="Lacak Lari"
                        onPress={() => setActive('olahraga')}
                      />
                    )}

                    {isModActive('hemoglobin') && (
                      <LauncherTile
                        icon="droplet"
                        iconColor="#e11d48"
                        iconBgLight="#fff1f2"
                        iconBgDark="rgba(225, 29, 72, 0.18)"
                        title="Hb Darah"
                        subtitle="Kadar Hb"
                        onPress={() => {
                          setKesehatanSubTab('hemoglobin');
                          setActive('kesehatan');
                        }}
                      />
                    )}

                    {isModActive('vitalitas') && (
                      <LauncherTile
                        icon="sun"
                        iconColor="#d97706"
                        iconBgLight="#fffbeb"
                        iconBgDark="rgba(217, 119, 6, 0.18)"
                        title="Vitalitas"
                        subtitle="Log Harian"
                        onPress={() => {
                          setKesehatanSubTab('vital');
                          setActive('kesehatan');
                        }}
                      />
                    )}

                    {isModActive('konsultasi') && (
                      <LauncherTile
                        icon="message-square"
                        iconColor="#2563eb"
                        iconBgLight="#eff6ff"
                        iconBgDark="rgba(37, 99, 235, 0.18)"
                        title="Konseling"
                        subtitle="Konsultasi"
                        onPress={() => setActive('konseling')}
                      />
                    )}

                    {profile?.jk === "P" && isModActive('haid_pita') && (
                      <LauncherTile
                        icon="calendar"
                        iconColor="#7c3aed"
                        iconBgLight="#f5f3ff"
                        iconBgDark="rgba(124, 58, 237, 0.18)"
                        title="Pita & Haid"
                        subtitle="Siklus & UKS"
                        onPress={() => setRiwayatHaidModalVisible(true)}
                      />
                    )}

                    {isModActive('nutrisi') && (
                      <LauncherTile
                        icon="coffee"
                        iconColor="#d97706"
                        iconBgLight="#fef3c7"
                        iconBgDark="rgba(217, 119, 6, 0.18)"
                        title="Nutrisi"
                        subtitle="Pola Makan"
                        onPress={() => setActive('nutrisi')}
                      />
                    )}

                    {isModActive('burnout') && (
                      <LauncherTile
                        icon="activity"
                        iconColor="#9333ea"
                        iconBgLight="#f3e8ff"
                        iconBgDark="rgba(147, 51, 234, 0.18)"
                        title="Burnout"
                        subtitle="Cek Stres"
                        onPress={() => setActive('burnout')}
                      />
                    )}

                    {isModActive('ergonomi') && (
                      <LauncherTile
                        icon="crosshair"
                        iconColor="#0891b2"
                        iconBgLight="#ecfeff"
                        iconBgDark="rgba(8, 145, 178, 0.18)"
                        title="Ergonomi"
                        subtitle="Postur Duduk"
                        onPress={() => setActive('ergonomi')}
                      />
                    )}

                    {isModActive('vaksinasi') && (
                      <LauncherTile
                        icon="shield"
                        iconColor="#2563eb"
                        iconBgLight="#eff6ff"
                        iconBgDark="rgba(37, 99, 235, 0.18)"
                        title="Vaksinasi"
                        subtitle="Paspor Imunisasi"
                        onPress={() => setActive('vaksinasi')}
                      />
                    )}
                  </View>
                )}
              </Card>

              {/* NOTIFIKASI BANNER */}
              {profile?.jk === "P" && peminjamanNotification?.notification && (
                <NotificationBanner notification={peminjamanNotification.notification} />
              )}

              {/* MENU HAID */}
              {profile?.jk === "P" && (
                <Card>
                  <Pressable
                    onPress={() => setIsHaidOpen(prev => !prev)}
                    style={{
                      flexDirection: 'row',
                      alignItems: 'center',
                      justifyContent: 'space-between',
                      marginBottom: isHaidOpen ? verticalScale(12) : 0,
                    }}
                  >
                    <Text style={styles.cardTitle}>Menu Haid</Text>
                    <View style={[styles.collapseArrowBtn, { backgroundColor: '#f5f3ff' }]}>
                      <Icon name={isHaidOpen ? "chevron-up" : "chevron-down"} size={18} color="#7c3aed" />
                    </View>
                  </Pressable>

                  {isHaidOpen && (
                    <>

                  {/* Status Haid Saat Ini */}
                  {currentHaidData && (
                    <View style={styles.currentHaidBanner}>
                      <Icon name="calendar" size={20} color={Colors.white} />{/* */}
                      <View style={{ flex: 1, marginLeft: moderateScale(12) }}>
                        <Text style={styles.currentHaidBannerTitle}>Sedang Haid</Text>{/* */}
                        <Text style={styles.currentHaidBannerText}>
                          Mulai: {new Date(currentHaidData.tanggal_mulai).toLocaleDateString('id-ID')}
                        </Text>
                      </View>
                    </View>
                  )}

                  {/* Action Buttons */}
                  <View style={styles.haidActionButtons}>
                    {canStartHaid ? (
                      <Pressable
                        onPress={handleStartHaid}
                        disabled={startingHaid}
                        style={({ pressed }) => [
                          styles.haidActionButton,
                          styles.haidStartButton,
                          { opacity: startingHaid ? 0.6 : pressed ? 0.9 : 1 }
                        ]}
                      >
                        {startingHaid ? (
                          <ActivityIndicator color={Colors.white} size="small" />
                        ) : (
                          <>
                            <Icon name="play-circle" size={24} color={Colors.white} />{/* */}
                            <Text style={styles.haidActionButtonText}>Mulai Haid</Text>
                          </>
                        )}
                      </Pressable>
                    ) : (
                      <Pressable
                        onPress={handleEndHaid}
                        disabled={endingHaid}
                        style={({ pressed }) => [
                          styles.haidActionButton,
                          styles.haidEndButton,
                          { opacity: endingHaid ? 0.6 : pressed ? 0.9 : 1 }
                        ]}
                      >
                        {endingHaid ? (
                          <ActivityIndicator color={Colors.white} size="small" />
                        ) : (
                          <>
                            <Icon name="stop-circle" size={24} color={Colors.white} />{/* */}
                            <Text style={styles.haidActionButtonText}>Selesai Haid</Text>
                          </>
                        )}
                      </Pressable>
                    )}{/* */}
                    <Pressable
                      onPress={handleSubmitPita}
                      disabled={!canSubmitPita || submittingPita}
                      style={({ pressed }) => [
                        styles.haidActionButton,
                        styles.haidPitaButton,
                        {
                          opacity: (!canSubmitPita || submittingPita) ? 0.6 : pressed ? 0.9 : 1,
                          backgroundColor: pitaButtonColor
                        }
                      ]}
                    >
                      {submittingPita ? (
                        <ActivityIndicator color={Colors.white} size="small" />
                      ) : (
                        <>
                          <Icon name="package" size={24} color={Colors.white} />{/* */}
                          <Text style={styles.haidActionButtonText}>
                            {activePitaData ?
                              (getStatusLabel(activePitaData.status) === 'Menunggu Verifikasi' ?
                                'Proses Verifikasi' : getStatusLabel(activePitaData.status)) :
                              'Pinjam Pita'}
                          </Text>
                        </>
                      )}
                    </Pressable>
                  </View>

                  {/* Info Text */}
                  {!canSubmitPita && !currentHaidData && (
                    <View style={styles.infoBox}>
                      <Icon name="info" size={16} color={Colors.muted} />{/* */}
                      <Text style={styles.infoText}>
                        Mulai jadwal haid terlebih dahulu untuk dapat meminjam pita
                      </Text>
                    </View>
                  )}

                  {activePitaData && (
                    <View style={[styles.infoBox, { backgroundColor: '#f0f9ff', borderColor: '#3b82f6' }]}>
                      <Icon name="alert-circle" size={16} color="#3b82f6" />{/* */}
                      <Text style={[styles.infoText, { color: '#1e40af' }]}>
                        Status peminjaman: {getStatusLabel(activePitaData.status)}
                      </Text>
                    </View>
                  )}

                  {/* Riwayat Links */}
                  <View style={styles.haidLinks}>
                    <Pressable
                      onPress={() => setRiwayatHaidModalVisible(true)}
                      style={styles.haidLink}
                    >
                      <Icon name="list" size={18} color={Colors.primary} />{/* */}
                      <Text style={styles.haidLinkText}>Lihat Riwayat Haid</Text>{/* */}
                      <Icon name="chevron-right" size={18} color={Colors.muted} />
                    </Pressable>{/* */}
                    <Pressable
                      onPress={() => setRiwayatPitaModalVisible(true)}
                      style={styles.haidLink}
                    >
                      <Icon name="package" size={18} color={Colors.primary} />{/* */}
                      <Text style={styles.haidLinkText}>Lihat Riwayat Pita</Text>{/* */}
                      <Icon name="chevron-right" size={18} color={Colors.muted} />
                    </Pressable>
                  </View>
                </>
                )}
              </Card>
            )}

              {/* Card untuk HealthBarChart */}
              <Card>
                <View style={[styles.chartHeader, { marginBottom: isChartOpen ? verticalScale(12) : 0 }]}>
                  <Pressable
                    onPress={() => setIsChartOpen(prev => !prev)}
                    style={{ flex: 1, flexDirection: 'row', alignItems: 'center' }}
                  >
                    <Text style={styles.chartTitle}>Tren Kesehatan</Text>
                  </Pressable>
                  <View style={{ flexDirection: 'row', alignItems: 'center', gap: moderateScale(6) }}>
                    <Pressable
                      onPress={() => setHealthInfoModalVisible(true)}
                      style={styles.infoIconButton}
                    >
                      <Icon name="info" size={18} color={Colors.primary} />
                    </Pressable>
                    <Pressable
                      onPress={() => setIsChartOpen(prev => !prev)}
                      style={[styles.collapseArrowBtn, { backgroundColor: isAtlas ? (isDark ? 'rgba(99, 102, 241, 0.2)' : '#EEF2FF') : tertiaryColor + '15' }]}
                    >
                      <Icon name={isChartOpen ? "chevron-up" : "chevron-down"} size={18} color={effectivePrimary} />
                    </Pressable>
                  </View>
                </View>
                {isChartOpen && <HealthBarChart />}
              </Card>
            </View>
          )}

          {active === "kesehatan" && (
            <View style={{ gap: verticalScale(12) }}>
              <View style={styles.subScreenHeader}>
                <Pressable onPress={() => setActive('ringkasan')} style={styles.backButton}>
                  <Icon name="arrow-left" size={18} color={effectivePrimary} />
                  <Text style={[styles.backButtonText, { color: effectivePrimary }]}>Ringkasan</Text>
                </Pressable>
                <Text style={styles.subScreenTitle}>Pusat Rekam Medis</Text>
              </View>

              {/* Segmented Filter Bar */}
              <View style={styles.subTabRow}>
                {isModActive('kesehatan') && (
                  <Pressable
                    onPress={() => setKesehatanSubTab('klinis')}
                    style={[styles.subTabButton, kesehatanSubTab === 'klinis' && { backgroundColor: effectivePrimary }]}
                  >
                    <Text style={[styles.subTabButtonText, kesehatanSubTab === 'klinis' && styles.subTabButtonTextActive]}>
                      Klinis
                    </Text>
                  </Pressable>
                )}
                {isModActive('hemoglobin') && (
                  <Pressable
                    onPress={() => setKesehatanSubTab('hemoglobin')}
                    style={[styles.subTabButton, kesehatanSubTab === 'hemoglobin' && { backgroundColor: effectivePrimary }]}
                  >
                    <Text style={[styles.subTabButtonText, kesehatanSubTab === 'hemoglobin' && styles.subTabButtonTextActive]}>
                      Hemoglobin
                    </Text>
                  </Pressable>
                )}
                {isModActive('vitalitas') && (
                  <Pressable
                    onPress={() => setKesehatanSubTab('vital')}
                    style={[styles.subTabButton, kesehatanSubTab === 'vital' && { backgroundColor: effectivePrimary }]}
                  >
                    <Text style={[styles.subTabButtonText, kesehatanSubTab === 'vital' && styles.subTabButtonTextActive]}>
                      Vitalitas
                    </Text>
                  </Pressable>
                )}
              </View>

              {kesehatanSubTab === 'klinis' && (
                <Card>
                  <Text style={styles.cardTitle}>Riwayat Pemeriksaan Klinis</Text>
                  {loadingKesehatan ? (
                    <View style={styles.centerRow}>
                      <ActivityIndicator color={Colors.primary} />
                      <Text style={styles.mutedText}>Memuat...</Text>
                    </View>
                  ) : kesehatanData.length === 0 ? (
                    <View style={styles.emptyState}>
                      <Icon name="heart" size={40} color={Colors.muted} />
                      <Text style={[styles.mutedText, { textAlign: 'center', marginTop: 8 }]}>Belum ada data kesehatan</Text>
                    </View>
                  ) : (
                    <ScrollView style={{ maxHeight: verticalScale(500) }} showsVerticalScrollIndicator={false} nestedScrollEnabled={true}>
                      {kesehatanData.map((yG) => (
                        <View key={yG.year} style={styles.yearGroup}>
                          <Text style={styles.yearTitle}>{yG.year}</Text>
                          {yG.months.map((mG, idx) => (
                            <View key={`${yG.year}-${idx}`} style={styles.monthGroup}>
                              <Text style={styles.monthTitle}>{mG.month.charAt(0).toUpperCase() + mG.month.slice(1)}</Text>
                              {mG.data.map((item) => (
                                <View key={item.id_kesehatan} style={styles.healthCard}>
                                  <View style={styles.healthHeader}>
                                    <Icon name="calendar" size={16} color={Colors.primary} />
                                    <Text style={styles.healthDate}>
                                      {new Date(item.tgl).toLocaleDateString('id-ID', {
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric'
                                      })}
                                    </Text>
                                  </View>
                                  <View style={styles.healthMetrics}>
                                    <View style={styles.healthMetricRow}>
                                      <Text style={styles.healthLabel}>IMT:</Text>
                                      <Text style={styles.healthValue}>{item.imt}</Text>
                                    </View>
                                    <View style={styles.healthMetricRow}>
                                      <Text style={styles.healthLabel}>BB/TB:</Text>
                                      <Text style={styles.healthValue}>{item.bb} kg / {item.tb} cm</Text>
                                    </View>
                                    <View style={styles.healthMetricRow}>
                                      <Text style={styles.healthLabel}>Tekanan Darah:</Text>
                                      <Text style={styles.healthValue}>{item.sistol}/{item.diastol} mmHg</Text>
                                    </View>
                                    <View style={styles.healthMetricRow}>
                                      <Text style={styles.healthLabel}>Status:</Text>
                                      <Text style={[styles.healthValue, { color: Colors.primary, fontWeight: '700' }]}>
                                        {item.status}
                                      </Text>
                                    </View>
                                  </View>
                                  {item.pesan_imt && (
                                    <View style={styles.healthMessage}>
                                      <Icon name="info" size={14} color={Colors.muted} />
                                      <Text style={styles.healthMessageText}>{item.pesan_imt}</Text>
                                    </View>
                                  )}
                                </View>
                              ))}
                            </View>
                          ))}
                        </View>
                      ))}
                    </ScrollView>
                  )}
                </Card>
              )}

              {kesehatanSubTab === 'hemoglobin' && (
                <>
                  <TipCard
                    style={{
                      paddingBottom: verticalScale(10),
                      borderBottomWidth: 4.5,
                      borderBottomColor: Colors.primary,
                      marginBottom: verticalScale(8)
                    }}
                    title="Tentang Hemoglobin"
                    text="Hemoglobin normal untuk remaja perempuan: 12-16 g/dL, remaja laki-laki: 13-18 g/dL"
                  />
                  <Card>
                    <Text style={styles.cardTitle}>Riwayat Hemoglobin</Text>
                    {loadingHb ? (
                      <View style={styles.centerRow}>
                        <ActivityIndicator color={Colors.primary} />
                        <Text style={styles.mutedText}>Memuat...</Text>
                      </View>
                    ) : hbData.length === 0 ? (
                      <View style={styles.emptyState}>
                        <Icon name="droplet" size={40} color={Colors.muted} />
                        <Text style={[styles.mutedText, { textAlign: 'center', marginTop: 8 }]}>
                          Belum ada data hemoglobin
                        </Text>
                      </View>
                    ) : (
                      <ScrollView style={{ maxHeight: verticalScale(500) }} showsVerticalScrollIndicator={false} nestedScrollEnabled={true}>
                        {hbData.map((yG) => (
                          <View key={yG.year} style={styles.yearGroup}>
                            <Text style={styles.yearTitle}>{yG.year}</Text>
                            {yG.months.map((mG, idx) => (
                              <View key={`${yG.year}-${idx}`} style={styles.monthGroup}>
                                <Text style={styles.monthTitle}>
                                  {mG.month.charAt(0).toUpperCase() + mG.month.slice(1)}
                                </Text>
                                {mG.data.map((item) => (
                                  <View key={item.id_hb} style={styles.hbCard}>
                                    <View style={styles.hbHeader}>
                                      <Icon name="droplet" size={18} color="#dc2626" />
                                      <View style={{ flex: 1, marginLeft: moderateScale(8) }}>
                                        <Text style={styles.hbDate}>
                                          {new Date(item.tgl).toLocaleDateString('id-ID', {
                                            day: 'numeric',
                                            month: 'long',
                                            year: 'numeric'
                                          })}
                                        </Text>
                                      </View>
                                      <View style={styles.hbBadge}>
                                        <Text style={styles.hbBadgeText}>{item.hb} g/dL</Text>
                                      </View>
                                    </View>
                                    <View style={styles.hbStatus}>
                                      <Text style={styles.hbStatusLabel}>Status:</Text>
                                      <Text style={[
                                        styles.hbStatusValue,
                                        { color: item.status === 'Normal' ? '#16a34a' : '#dc2626' }
                                      ]}>
                                        {item.status}
                                      </Text>
                                    </View>
                                    {item.pesan && (
                                      <View style={styles.hbMessage}>
                                        <Icon name="info" size={14} color={Colors.muted} />
                                        <Text style={styles.hbMessageText}>{item.pesan}</Text>
                                      </View>
                                    )}
                                  </View>
                                ))}
                              </View>
                            ))}
                          </View>
                        ))}
                      </ScrollView>
                    )}
                  </Card>
                </>
              )}

              {kesehatanSubTab === 'vital' && (
                <DailyVitalityView />
              )}
            </View>
          )}

          {active === "olahraga" && isModActive('olahraga') && (
            <View style={{ gap: verticalScale(12) }}>
              <View style={styles.subScreenHeader}>
                <Pressable onPress={() => setActive('ringkasan')} style={styles.backButton}>
                  <Icon name="arrow-left" size={18} color={effectivePrimary} />
                  <Text style={[styles.backButtonText, { color: effectivePrimary }]}>Ringkasan</Text>
                </Pressable>
                <Text style={styles.subScreenTitle}>Olahraga & Kebugaran</Text>
              </View>
              <RunTrackerView />
            </View>
          )}

          {active === "konseling" && isModActive('konsultasi') && (
            <View style={{ gap: verticalScale(12) }}>
              <View style={styles.subScreenHeader}>
                <Pressable onPress={() => setActive('ringkasan')} style={styles.backButton}>
                  <Icon name="arrow-left" size={18} color={effectivePrimary} />
                  <Text style={[styles.backButtonText, { color: effectivePrimary }]}>Ringkasan</Text>
                </Pressable>
                <Text style={styles.subScreenTitle}>Konseling Kesehatan</Text>
              </View>
              <CounselingMemberView themeColor={effectivePrimary} profile={profile} />
            </View>
          )}

          {active === "ergonomi" && isModActive('ergonomi') && (
            <View style={{ gap: verticalScale(12) }}>
              <View style={styles.subScreenHeader}>
                <Pressable onPress={() => setActive('ringkasan')} style={styles.backButton}>
                  <Icon name="arrow-left" size={18} color={effectivePrimary} />
                  <Text style={[styles.backButtonText, { color: effectivePrimary }]}>Ringkasan</Text>
                </Pressable>
                <Text style={styles.subScreenTitle}>Ergonomi & Peregangan Postur</Text>
              </View>
              <ErgonomicsMemberView themeColor="#0891b2" />
            </View>
          )}

          {active === "burnout" && isModActive('burnout') && (
            <View style={{ gap: verticalScale(12) }}>
              <View style={styles.subScreenHeader}>
                <Pressable onPress={() => setActive('ringkasan')} style={styles.backButton}>
                  <Icon name="arrow-left" size={18} color={effectivePrimary} />
                  <Text style={[styles.backButtonText, { color: effectivePrimary }]}>Ringkasan</Text>
                </Pressable>
                <Text style={styles.subScreenTitle}>Skrining Burnout & Stres</Text>
              </View>
              <BurnoutMemberView themeColor="#9333ea" onNavigateToCounseling={() => setActive('konseling')} />
            </View>
          )}

          {active === "nutrisi" && isModActive('nutrisi') && (
            <View style={{ gap: verticalScale(12) }}>
              <View style={styles.subScreenHeader}>
                <Pressable onPress={() => setActive('ringkasan')} style={styles.backButton}>
                  <Icon name="arrow-left" size={18} color={effectivePrimary} />
                  <Text style={[styles.backButtonText, { color: effectivePrimary }]}>Ringkasan</Text>
                </Pressable>
                <Text style={styles.subScreenTitle}>Nutrisi & Pola Makan</Text>
              </View>
              <NutritionMemberView themeColor="#d97706" />
            </View>
          )}

          {active === "vaksinasi" && isModActive('vaksinasi') && (
            <View style={{ gap: verticalScale(12) }}>
              <View style={styles.subScreenHeader}>
                <Pressable onPress={() => setActive('ringkasan')} style={styles.backButton}>
                  <Icon name="arrow-left" size={18} color={effectivePrimary} />
                  <Text style={[styles.backButtonText, { color: effectivePrimary }]}>Ringkasan</Text>
                </Pressable>
                <Text style={styles.subScreenTitle}>Paspor Vaksinasi Digital</Text>
              </View>
              <VaccineMemberView themeColor="#2563eb" userName={profile?.nama || 'Member'} />
            </View>
          )}

          {active === "profil" && (
            <View style={{ gap: verticalScale(14) }}>
              {/* Quick Stat Highlights */}
              {profile && (
                <View style={styles.profileStatGrid}>
                  {/* Card 1: Peran */}
                  <View style={[styles.profileStatCard, { backgroundColor: theme.surface, borderColor: isDark ? theme.border : '#E2E8F0' }]}>
                    <View style={[styles.profileStatIconBox, { backgroundColor: '#f0fdf4' }]}>
                      <Icon name="shield" size={15} color="#0d9488" />
                    </View>
                    <Text style={[styles.profileStatLabel, { color: theme.textSecondary }]}>PERAN</Text>
                    <Text style={[styles.profileStatValue, { color: theme.text }]} numberOfLines={1}>
                      {profile.level || 'Member'}
                    </Text>
                  </View>

                  {/* Card 2: Divisi */}
                  <View style={[styles.profileStatCard, { backgroundColor: theme.surface, borderColor: isDark ? theme.border : '#E2E8F0' }]}>
                    <View style={[styles.profileStatIconBox, { backgroundColor: `${effectivePrimary}15` }]}>
                      <Icon name="layers" size={15} color={effectivePrimary} />
                    </View>
                    <Text style={[styles.profileStatLabel, { color: theme.textSecondary }]}>DIVISI</Text>
                    <Text style={[styles.profileStatValue, { color: effectivePrimary }]} numberOfLines={1}>
                      {profile.divisi || '-'}
                    </Text>
                  </View>

                  {/* Card 3: Nomor Induk */}
                  <View style={[styles.profileStatCard, { backgroundColor: theme.surface, borderColor: isDark ? theme.border : '#E2E8F0' }]}>
                    <View style={[styles.profileStatIconBox, { backgroundColor: '#eff6ff' }]}>
                      <Icon name="credit-card" size={15} color="#2563eb" />
                    </View>
                    <Text style={[styles.profileStatLabel, { color: theme.textSecondary }]}>NO. INDUK</Text>
                    <Text style={[styles.profileStatValue, { color: theme.text }]} numberOfLines={1}>
                      {profile.nomor_induk || '-'}
                    </Text>
                  </View>
                </View>
              )}

              {/* Card 1: Informasi Pribadi */}
              <Card>
                <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: verticalScale(8) }}>
                  <View style={{ flex: 1, marginRight: horizontalScale(8) }}>
                    <Text style={[styles.cardTitle, { color: theme.text }]}>Informasi Pribadi</Text>
                    <Text style={{ fontSize: moderateScale(11), color: theme.textSecondary, marginTop: verticalScale(1) }}>
                      Data identitas dan keanggotaan institusi
                    </Text>
                  </View>
                  <Pressable
                    onPress={() => setEditProfileModalVisible(true)}
                    style={({ pressed }) => [
                      {
                        flexDirection: 'row',
                        alignItems: 'center',
                        gap: horizontalScale(4),
                        paddingHorizontal: horizontalScale(10),
                        paddingVertical: verticalScale(6),
                        borderRadius: moderateScale(8),
                        backgroundColor: `${effectivePrimary}14`,
                        borderWidth: 1,
                        borderColor: `${effectivePrimary}32`,
                      },
                      pressed && { opacity: 0.8, transform: [{ scale: 0.98 }] },
                    ]}
                  >
                    <Icon name="edit-3" size={12} color={effectivePrimary} />
                    <Text style={{ fontSize: moderateScale(11.5), fontWeight: '700', color: effectivePrimary }}>Edit Profil</Text>
                  </Pressable>
                </View>

                {loadingProfile ? (
                  <View style={styles.centerRow}>
                    <ActivityIndicator color={Colors.primary} />
                    <Text style={styles.mutedText}>Memuat data profil...</Text>
                  </View>
                ) : profile ? (
                  <View style={{ marginTop: verticalScale(4) }}>
                    <ProfileRow
                      icon="user"
                      iconBg="#ecfdf5"
                      iconColor="#059669"
                      label="Nama Lengkap"
                      value={profile.nama}
                    />
                    <ProfileRow
                      icon="at-sign"
                      iconBg="#f5f3ff"
                      iconColor="#7c3aed"
                      label="Username"
                      value={profile.username}
                    />
                    <ProfileRow
                      icon="mail"
                      iconBg="#fffbeb"
                      iconColor="#d97706"
                      label="Alamat Email"
                      value={profile.email}
                    />
                    <ProfileRow
                      icon="user-check"
                      iconBg="#fff1f2"
                      iconColor="#e11d48"
                      label="Jenis Kelamin"
                      value={profile.jk === 'P' ? 'Perempuan' : profile.jk === 'L' ? 'Laki-laki' : null}
                    />
                    <ProfileRow
                      icon="briefcase"
                      iconBg="#eff6ff"
                      iconColor="#2563eb"
                      label="Institusi"
                      value={profile.instansi?.nama_instansi || 'SMK Telekomunikasi Telesandi Bekasi'}
                      numberOfLines={2}
                      isLast={true}
                    />
                  </View>
                ) : (
                  <View style={styles.emptyState}>
                    <Icon name="user-x" size={40} color={Colors.muted} />
                    <Text style={[styles.mutedText, { textAlign: 'center', marginTop: 8 }]}>
                      Profil tidak tersedia
                    </Text>
                  </View>
                )}
              </Card>

              {/* Card 2: Pengaturan & Keamanan */}
              <Card>
                <View style={{ marginBottom: verticalScale(6) }}>
                  <Text style={[styles.cardTitle, { color: theme.text }]}>Akun & Keamanan</Text>
                  <Text style={{ fontSize: moderateScale(11), color: theme.textSecondary, marginTop: verticalScale(1) }}>
                    Kelola kata sandi dan sesi akun Anda
                  </Text>
                </View>

                <View style={{ marginTop: verticalScale(2) }}>
                  <AccountActionItem
                    icon="user-check"
                    iconBg="#e0e7ff"
                    iconColor="#4338ca"
                    title="Ubah Profil"
                    subtitle="Perbarui nama dan foto tampilan"
                    onPress={() => setEditProfileModalVisible(true)}
                  />
                  <AccountActionItem
                    icon="lock"
                    iconBg="#fef3c7"
                    iconColor="#b45309"
                    title="Ganti Kata Sandi"
                    subtitle="Amankan akun dengan sandi baru"
                    onPress={() => setModalVisible(true)}
                  />
                  <AccountActionItem
                    icon="refresh-cw"
                    iconBg="#ecfdf5"
                    iconColor="#059669"
                    title="Periksa Pembaruan"
                    subtitle="Cek ketersediaan versi terbaru aplikasi"
                    onPress={handleCheckUpdate}
                  />
                  <AccountActionItem
                    icon="file-text"
                    iconBg="#e0f2fe"
                    iconColor="#0284c7"
                    title="Catatan Pembaruan"
                    subtitle="Riwayat versi dan pembaruan sistem"
                    onPress={() => setChangelogModalVisible(true)}
                  />
                  <AccountActionItem
                    icon="layout"
                    iconBg={themeId === 'atlas_modern' ? '#e0e7ff' : '#ecfdf5'}
                    iconColor={themeId === 'atlas_modern' ? '#4f46e5' : '#059669'}
                    title="Tampilan & Tema Aplikasi"
                    subtitle={
                      themeId === 'atlas_modern'
                        ? `Atlas Neo Modern • Mode ${isDark ? 'Gelap' : 'Terang'}`
                        : `Emerald Classic • Mode Terang (Bawaan)`
                    }
                    onPress={() => setThemeHubModalVisible(true)}
                  />
                  <AccountActionItem
                    icon="log-out"
                    iconBg="#fee2e2"
                    iconColor="#dc2626"
                    title="Keluar dari Akun"
                    subtitle="Akhiri sesi login di perangkat ini"
                    onPress={handleLogout}
                    isDanger={true}
                    isLast={true}
                  />
                </View>
              </Card>

              {/* Card 3: Footer Versi Aplikasi */}
              <View style={styles.profileFooter}>
                <Text style={styles.profileFooterVersion}>Versi 1.0.0 • Build Stabil</Text>
              </View>
            </View>
          )}
        </Animated.View>
      </ScrollView>

      {/* Modal Informasi Kesehatan */}
      <Modal
        animationType="slide"
        transparent={true}
        visible={healthInfoModalVisible}
        onRequestClose={() => setHealthInfoModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={[
            styles.modalContentLarge,
            {
              maxHeight: SCREEN_HEIGHT * 0.85,
              width: horizontalScale(340)
            }
          ]}>
            <View style={styles.modalHeader}>
              <Text style={[styles.cardTitle, { color: Colors.primary }]}>
                Ketentuan Nilai Kesehatan
              </Text>{/* */}
              <Pressable
                onPress={() => setHealthInfoModalVisible(false)}
                style={styles.modalCloseButton}
              >
                <Icon name="x" size={24} color={Colors.text} />
              </Pressable>
            </View>
            <ScrollView
              showsVerticalScrollIndicator={false}
              style={styles.modalScrollView}
            >
              <View style={styles.modalSection}>
                <View style={styles.sectionTitleRow}>
                  <Icon name="bar-chart-2" size={20} color={Colors.primary} />{/* */}
                  <Text style={styles.modalSectionTitle}>Penjelasan Nilai Chart</Text>
                </View>
                <Text style={styles.modalSectionText}>
                  Nilai pada chart merupakan rata-rata dari data kesehatan yang tersedia per bulan atau per tahun.
                  Rata-rata ini dihitung menggunakan fungsi AVG pada skor individu dari setiap entri data kesehatan.
                </Text>
              </View>

              <View style={styles.modalSection}>
                <View style={styles.sectionTitleRow}>
                  <Icon name="percent" size={20} color={Colors.primary} />{/* */}
                  <Text style={styles.modalSectionTitle}>Ketentuan Nilai Persentase (Score)</Text>
                </View>

                <View style={styles.detailTable}>
                  <View style={styles.detailTableRow}>
                    <Text style={styles.detailTableHeader}>Kondisi</Text>{/* */}
                    <Text style={styles.detailTableHeader}>Skor</Text>{/* */}
                    <Text style={styles.detailTableHeader}>Kategori</Text>
                  </View>

                  <View style={styles.detailTableRow}>
                    <Text style={styles.detailTableCell}>Semua normal</Text>{/* */}
                    <Text style={[styles.detailTableCell, { fontWeight: '700', color: '#10b981' }]}>
                      100
                    </Text>{/* */}
                    <Text style={styles.detailTableCell}>Sempurna</Text>
                  </View>

                  <View style={styles.detailTableRow}>
                    <Text style={styles.detailTableCell}>Tekanan darah normal + tidak berisiko</Text>{/* */}
                    <Text style={[styles.detailTableCell, { fontWeight: '700', color: '#3b82f6' }]}>
                      85
                    </Text>{/* */}
                    <Text style={styles.detailTableCell}>Baik</Text>
                  </View>

                  <View style={styles.detailTableRow}>
                    <Text style={styles.detailTableCell}>Masalah berat badan atau tekanan darah</Text>{/* */}
                    <Text style={[styles.detailTableCell, { fontWeight: '700', color: '#f59e0b' }]}>
                      60
                    </Text>{/* */}
                    <Text style={styles.detailTableCell}>Perlu Perhatian</Text>
                  </View>

                  <View style={styles.detailTableRow}>
                    <Text style={styles.detailTableCell}>Perilaku berisiko atau gangguan reproduksi</Text>{/* */}
                    <Text style={[styles.detailTableCell, { fontWeight: '700', color: '#ef4444' }]}>
                      35
                    </Text>{/* */}
                    <Text style={styles.detailTableCell}>Buruk</Text>
                  </View>

                  <View style={styles.detailTableRow}>
                    <Text style={styles.detailTableCell}>Obesitas parah atau kombinasi masalah</Text>{/* */}
                    <Text style={[styles.detailTableCell, { fontWeight: '700', color: '#991b1b' }]}>
                      20
                    </Text>{/* */}
                    <Text style={styles.detailTableCell}>Sangat Buruk</Text>
                  </View>
                </View>

                <View style={styles.noteBox}>
                  <Icon name="info" size={16} color={Colors.muted} />{/* */}
                  <Text style={styles.noteText}>
                    Catatan: Logika dihitung secara berurutan, sehingga kondisi atas lebih prioritas.
                    Jika ada overlap (misal gangguan reproduksi), skor 35 akan mendahului 20.
                  </Text>
                </View>
              </View>
            </ScrollView>
          </View>
        </View>
      </Modal>

      {/* Modal Riwayat Haid */}
      <Modal
        animationType="slide"
        transparent={true}
        visible={riwayatHaidModalVisible}
        onRequestClose={() => setRiwayatHaidModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={[
            styles.modalContent,
            {
              maxHeight: SCREEN_HEIGHT * 0.8,
              width: horizontalScale(340)
            }
          ]}>
            <View style={styles.modalHeader}>
              <Text style={styles.cardTitle}>Riwayat Haid</Text>{/* */}
              <Pressable onPress={() => setRiwayatHaidModalVisible(false)}>
                <Icon name="x" size={24} color={Colors.text} />
              </Pressable>
            </View>

            {loadingHaid ? (
              <View style={styles.centerRow}>
                <ActivityIndicator color={Colors.primary} />{/* */}
                <Text style={styles.mutedText}>Memuat...</Text>
              </View>
            ) : haidData.length === 0 ? (
              <View style={styles.emptyState}>
                <Icon name="calendar" size={40} color={Colors.muted} />{/* */}
                <Text style={[styles.mutedText, { textAlign: 'center', marginTop: 8 }]}>
                  Belum ada data riwayat haid
                </Text>
              </View>
            ) : (
              <ScrollView style={{ maxHeight: verticalScale(500) }} showsVerticalScrollIndicator={false} nestedScrollEnabled={true}>
                {haidData.map((yG) => (
                  <View key={yG.year} style={styles.yearGroup}>
                    <Text style={styles.yearTitle}>{yG.year}</Text>
                    {yG.months.map((mG, idx) => (
                      <View key={`${yG.year}-${idx}`} style={styles.monthGroup}>
                        <Text style={styles.monthTitle}>
                          {mG.month.charAt(0).toUpperCase() + mG.month.slice(1)}
                        </Text>
                        {mG.data.map((item) => (
                          <View key={item.id} style={styles.haidCard}>
                            <View style={styles.haidHeader}>
                              <Icon name="calendar" size={18} color={Colors.primary} />{/* */}
                              <Text style={styles.haidDate}>
                                {new Date(item.tanggal_mulai).toLocaleDateString('id-ID', {
                                  day: 'numeric',
                                  month: 'long',
                                  year: 'numeric'
                                })}
                                {item.tanggal_selesai && ` - ${new Date(item.tanggal_selesai).toLocaleDateString('id-ID', {
                                  day: 'numeric',
                                  month: 'long',
                                  year: 'numeric'
                                })}`}
                              </Text>
                            </View>
                            <View style={styles.haidMetrics}>
                              <View style={styles.haidMetricRow}>
                                <Text style={styles.haidLabel}>Durasi:</Text>{/* */}
                                <Text style={styles.haidValue}>{item.durasi_hari} hari</Text>
                              </View>{/* */}
                              <View style={styles.haidMetricRow}>
                                <Text style={styles.haidLabel}>Status:</Text>{/* */}
                                <Text style={[
                                  styles.haidValue,
                                  {
                                    color: item.status === 'berlangsung' ? '#16a34a' : Colors.text,
                                    fontWeight: '700'
                                  }
                                ]}>
                                  {item.status}
                                </Text>
                              </View>
                              {item.catatan && (
                                <View style={styles.haidMetricRow}>
                                  <Text style={styles.haidLabel}>Catatan:</Text>{/* */}
                                  <Text style={styles.haidValue}>{item.catatan}</Text>
                                </View>
                              )}
                            </View>
                          </View>
                        ))}
                      </View>
                    ))}
                  </View>
                ))}
              </ScrollView>
            )}

            <Pressable
              onPress={() => setRiwayatHaidModalVisible(false)}
              style={[styles.ghostBtn, { alignSelf: 'center', marginTop: verticalScale(12) }]}
            >
              <Text style={styles.ghostBtnText}>Tutup</Text>
            </Pressable>
          </View>
        </View>
      </Modal>

      {/* Modal Riwayat Pita */}
      <Modal
        animationType="slide"
        transparent={true}
        visible={riwayatPitaModalVisible}
        onRequestClose={() => setRiwayatPitaModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={[
            styles.modalContent,
            {
              maxHeight: SCREEN_HEIGHT * 0.8,
              width: horizontalScale(340)
            }
          ]}>
            <View style={styles.modalHeader}>
              <Text style={styles.cardTitle}>Riwayat Peminjaman Pita</Text>{/* */}
              <Pressable onPress={() => setRiwayatPitaModalVisible(false)}>
                <Icon name="x" size={24} color={Colors.text} />
              </Pressable>
            </View>

            {loadingPita ? (
              <View style={styles.centerRow}>
                <ActivityIndicator color={Colors.primary} />{/* */}
                <Text style={styles.mutedText}>Memuat...</Text>
              </View>
            ) : pitaData.length === 0 ? (
              <View style={styles.emptyState}>
                <Icon name="package" size={40} color={Colors.muted} />{/* */}
                <Text style={[styles.mutedText, { textAlign: 'center', marginTop: 8 }]}>
                  Belum ada data peminjaman pita
                </Text>
              </View>
            ) : (
              <ScrollView style={{ maxHeight: verticalScale(500) }} showsVerticalScrollIndicator={false} nestedScrollEnabled={true}>
                {pitaData.map((yG) => (
                  <View key={yG.year} style={styles.yearGroup}>
                    <Text style={styles.yearTitle}>{yG.year}</Text>
                    {yG.months.map((mG, idx) => (
                      <View key={`${yG.year}-${idx}`} style={styles.monthGroup}>
                        <Text style={styles.monthTitle}>
                          {mG.month.charAt(0).toUpperCase() + mG.month.slice(1)}
                        </Text>
                        {mG.data.map((item) => (
                          <View key={item.id} style={styles.pitaCard}>
                            <View style={styles.pitaHeader}>
                              <Icon name="package" size={18} color="#8b5cf6" />{/* */}
                              <View style={{ flex: 1, marginLeft: moderateScale(8) }}>
                                <Text style={styles.pitaDate}>
                                  {new Date(item.tanggal_pinjam).toLocaleDateString('id-ID', {
                                    day: 'numeric',
                                    month: 'long',
                                    year: 'numeric'
                                  })}
                                </Text>
                              </View>{/* */}
                              <View style={[
                                styles.pitaStatusBadge,
                                { backgroundColor: getStatusColor(item.status) }
                              ]}>
                                <Text style={styles.pitaStatusText}>
                                  {getStatusLabel(item.status)}
                                </Text>
                              </View>
                            </View>
                            <View style={styles.pitaMetrics}>
                              <View style={styles.pitaMetricRow}>
                                <Text style={styles.pitaLabel}>Jumlah:</Text>{/* */}
                                <Text style={styles.pitaValue}>{item.jumlah_pita} pita</Text>
                              </View>
                              {item.tanggal_kembali && (
                                <View style={styles.pitaMetricRow}>
                                  <Text style={styles.pitaLabel}>Dikembalikan:</Text>{/* */}
                                  <Text style={styles.pitaValue}>
                                    {new Date(item.tanggal_kembali).toLocaleDateString('id-ID', {
                                      day: 'numeric',
                                      month: 'long',
                                      year: 'numeric'
                                    })}
                                  </Text>
                                </View>
                              )}
                              {item.estimasi_selesai_haid && (
                                <View style={styles.pitaMetricRow}>
                                  <Text style={styles.pitaLabel}>Estimasi Selesai:</Text>{/* */}
                                  <Text style={styles.pitaValue}>
                                    {new Date(item.estimasi_selesai_haid).toLocaleDateString('id-ID', {
                                      day: 'numeric',
                                      month: 'long',
                                      year: 'numeric'
                                    })}
                                  </Text>
                                </View>
                              )}
                              {item.keterangan && (
                                <View style={styles.pitaMetricRow}>
                                  <Text style={styles.pitaLabel}>Keterangan:</Text>{/* */}
                                  <Text style={styles.pitaValue}>{item.keterangan}</Text>
                                </View>
                              )}
                            </View>
                          </View>
                        ))}
                      </View>
                    ))}
                  </View>
                ))}
              </ScrollView>
            )}

            <Pressable
              onPress={() => setRiwayatPitaModalVisible(false)}
              style={[styles.ghostBtn, { alignSelf: 'center', marginTop: verticalScale(12) }]}
            >
              <Text style={styles.ghostBtnText}>Tutup</Text>
            </Pressable>
          </View>
        </View>
      </Modal>

      {/* Modal Ganti Password */}
      <Modal
        animationType="slide"
        transparent={true}
        visible={modalVisible}
        onRequestClose={() => setModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
            <Text style={styles.cardTitle}>Ganti Password</Text>

            <Text style={styles.label}>Kata Sandi Saat Ini</Text>{/* */}
            <TextInput
              value={currentPassword}
              onChangeText={setCurrentPassword}
              placeholder="Masukkan kata sandi saat ini"
              placeholderTextColor={Colors.muted}
              style={styles.input}
              secureTextEntry
            />

            <Text style={styles.label}>Kata Sandi Baru</Text>{/* */}
            <TextInput
              value={newPassword}
              onChangeText={setNewPassword}
              placeholder="Masukkan kata sandi baru"
              placeholderTextColor={Colors.muted}
              style={styles.input}
              secureTextEntry
            />

            <Text style={styles.label}>Ketik Ulang Kata Sandi Baru</Text>{/* */}
            <TextInput
              value={retypePassword}
              onChangeText={setRetypePassword}
              placeholder="Ketik ulang kata sandi baru"
              placeholderTextColor={Colors.muted}
              style={styles.input}
              secureTextEntry
            />

            {passwordError && (
              <Text style={[styles.mutedText, { color: "#dc2626", marginTop: verticalScale(8) }]}>
                {passwordError}
              </Text>
            )}

            <View style={styles.modalActions}>
              <Pressable
                onPress={() => setModalVisible(false)}
                style={[styles.modalButton, { backgroundColor: Colors.muted }]}
              >
                <Text style={styles.modalButtonText}>Batal</Text>
              </Pressable>{/* */}
              <Pressable
                onPress={handleChangePassword}
                disabled={savingPassword || !currentPassword || !newPassword || !retypePassword}
                style={({ pressed }) => [
                  styles.modalButton,
                  {
                    backgroundColor: Colors.primary,
                    opacity: savingPassword || !currentPassword || !newPassword || !retypePassword ? 0.6 : pressed ? 0.9 : 1
                  }
                ]}
              >
                {savingPassword ? (
                  <ActivityIndicator color={Colors.white} />
                ) : (
                  <Text style={styles.modalButtonText}>Simpan</Text>
                )}
              </Pressable>
            </View>
          </View>
        </View>
      </Modal>

      {/* Modal Edit Profil */}
      <EditProfileModal
        visible={editProfileModalVisible}
        onClose={() => setEditProfileModalVisible(false)}
        currentProfile={profile}
        onProfileUpdated={(updated) => {
          setProfile((prev: any) => ({
            ...prev,
            nama: updated.nama,
            email: updated.email,
            avatar: updated.avatar,
          }));
        }}
        themeColor={effectivePrimary}
      />

      {/* Modal Catatan Pembaruan (Changelog) */}
      <ChangelogModal
        visible={changelogModalVisible}
        onClose={() => setChangelogModalVisible(false)}
      />

      {/* Modal Periksa Pembaruan */}
      <CheckUpdateModal
        visible={checkUpdateVisible}
        onClose={() => setCheckUpdateVisible(false)}
        status={checkUpdateStatus}
        updateData={checkUpdateData}
        errorMessage={checkUpdateError}
        onUpdateNow={handleApplyUpdate}
      />

      {/* Modal Force Upgrade jika Wajib */}
      <ForceUpgradeModal
        visible={forceUpgradeVisible || isMandatoryUpgrade}
        updateData={syncForceUpgradeData || forceUpgradeData}
      />

      {/* Modal Maintenance Mode - Unclosable */}
      <MaintenanceModal
        visible={isMaintenance}
        message={maintenanceMessage}
        onRefreshStatus={recheckSync}
      />

      {/* Modal Tampilan & Tema (On-Demand Download Theme Hub) */}
      <ThemeHubModal
        visible={themeHubModalVisible}
        onClose={() => setThemeHubModalVisible(false)}
      />
      {/* Loading Popup */}
      <Modal
        visible={initialLoading}
        animationType="fade"
        transparent={true}
      >
        <View style={styles.loadingOverlay}>
          <View style={styles.loadingContainer}>
            <ActivityIndicator size="large" color={Colors.primary} />{/* */}
            <Text style={styles.loadingText}>Memuat data...</Text>
          </View>
        </View>
      </Modal>
      {/* Logout Loading Popup */}
      <Modal
        visible={isLoggingOut}
        animationType="fade"
        transparent={true}
      >
        <View style={styles.loadingOverlay}>
          <View style={styles.loadingContainer}>
            <ActivityIndicator size="large" color={Colors.primary} />{/* */}
            <Text style={styles.loadingText}>Sedang keluar...</Text>
          </View>
        </View>
      </Modal>
      <AnimatedBottomTabs items={tabs} activeKey={currentTabKey} onChange={setActive} themeColor={isAtlas ? theme.tabBarActive : tertiaryColor} />
    </SafeAreaView>
  )
}
// Komponen helper
const Card = ({ children, style }: { children: React.ReactNode; style?: any }) => {
  const { theme, themeId, isDark } = useTheme();
  const isAtlas = themeId === 'atlas_modern';
  return (
    <View
      style={[
        styles.card,
        isAtlas ? {
          backgroundColor: theme.surface,
          borderRadius: moderateScale(22),
          borderWidth: 1.2,
          borderColor: isDark ? '#2A3854' : '#E0E7FF',
          shadowColor: isDark ? '#000000' : 'rgba(79, 70, 229, 0.22)',
          shadowOffset: { width: 0, height: 6 },
          shadowOpacity: isDark ? 0.35 : 0.12,
          shadowRadius: 14,
          elevation: 4,
        } : {
          backgroundColor: theme.surface,
          borderColor: isDark ? (theme.border || '#334155') : theme.border,
          shadowColor: isDark ? '#000000' : '#0F172A',
          shadowOpacity: isDark ? 0.3 : 0.08,
        },
        style,
      ]}
    >
      {children}
    </View>
  );
};

const LauncherTile = ({
  icon,
  iconColor,
  iconBgLight,
  iconBgDark,
  title,
  subtitle,
  onPress,
}: {
  icon: string;
  iconColor: string;
  iconBgLight: string;
  iconBgDark: string;
  title: string;
  subtitle: string;
  onPress: () => void;
}) => {
  const { theme, themeId, isDark } = useTheme();
  const isAtlas = themeId === 'atlas_modern';
  return (
    <Pressable
      onPress={onPress}
      style={({ pressed }) => [
        isAtlas
          ? {
              width: '31%',
              backgroundColor: isDark ? 'rgba(30, 41, 59, 0.7)' : '#FFFFFF',
              borderRadius: moderateScale(16),
              paddingVertical: verticalScale(14),
              paddingHorizontal: horizontalScale(6),
              alignItems: 'center',
              borderWidth: 1.2,
              borderColor: isDark ? 'rgba(99, 102, 241, 0.22)' : '#E0E7FF',
              shadowColor: isDark ? '#000000' : 'rgba(79, 70, 229, 0.1)',
              shadowOffset: { width: 0, height: 4 },
              shadowOpacity: isDark ? 0.3 : 0.08,
              shadowRadius: 8,
              elevation: 2,
            }
          : styles.menuGridItem,
        pressed && (isAtlas ? { opacity: 0.8, transform: [{ scale: 0.95 }] } : styles.menuGridItemPressed),
      ]}
    >
      <View
        style={[
          isAtlas
            ? {
                width: moderateScale(46),
                height: moderateScale(46),
                borderRadius: moderateScale(14),
                alignItems: 'center',
                justifyContent: 'center',
                backgroundColor: isDark ? iconBgDark : iconBgLight,
                marginBottom: verticalScale(6),
              }
            : [styles.menuGridIconContainer, { backgroundColor: iconBgLight }],
        ]}
      >
        <Icon name={icon as any} size={22} color={iconColor} />
      </View>
      <Text
        style={[
          isAtlas
            ? {
                fontSize: moderateScale(12),
                fontWeight: '700',
                color: theme.text,
                textAlign: 'center',
              }
            : styles.menuGridItemTitle,
        ]}
        numberOfLines={1}
      >
        {title}
      </Text>
      <Text
        style={[
          isAtlas
            ? {
                fontSize: moderateScale(10),
                color: theme.textSecondary,
                textAlign: 'center',
                marginTop: verticalScale(1),
              }
            : styles.menuGridItemSub,
        ]}
        numberOfLines={1}
      >
        {subtitle}
      </Text>
    </Pressable>
  );
};

const Metric = ({ label, value, unit }: { label: string; value: string; unit: string }) => {
  const { theme, themeId, isDark } = useTheme();
  const isAtlas = themeId === 'atlas_modern';

  if (isAtlas) {
    return (
      <View
        style={{
          flex: 1,
          alignItems: 'center',
          justifyContent: 'center',
          backgroundColor: isDark ? 'rgba(30, 41, 59, 0.55)' : '#F1F5F999',
          borderRadius: moderateScale(14),
          borderWidth: 1,
          borderColor: isDark ? 'rgba(99, 102, 241, 0.25)' : '#E0E7FF',
          paddingVertical: verticalScale(10),
          paddingHorizontal: horizontalScale(4),
          marginHorizontal: horizontalScale(2),
        }}
      >
        <Text
          style={{
            color: isDark ? '#818CF8' : '#4F46E5',
            fontSize: moderateScale(20),
            fontWeight: '800',
            letterSpacing: -0.5,
          }}
        >
          {value}
        </Text>
        <Text
          style={{
            color: isDark ? '#22D3EE' : '#0891B2',
            fontSize: moderateScale(10),
            fontWeight: '700',
            textTransform: 'uppercase',
            letterSpacing: 0.5,
            marginTop: verticalScale(1),
            marginBottom: verticalScale(2),
          }}
        >
          {unit || ' '}
        </Text>
        <Text
          style={{
            color: theme.textSecondary,
            fontSize: moderateScale(11),
            fontWeight: '600',
          }}
          numberOfLines={1}
        >
          {label}
        </Text>
      </View>
    );
  }

  return (
    <View style={styles.metric}>
      <Text style={[styles.metricValue, { color: theme.primary }]}>{value}</Text>
      <Text style={[styles.metricUnit, { color: theme.textSecondary }]}>{unit}</Text>
      <Text style={[styles.metricLabel, { color: theme.text }]}>{label}</Text>
    </View>
  );
};

interface ProfileRowProps {
  icon: string;
  iconBg: string;
  iconColor: string;
  label: string;
  value?: string | null;
  isBadge?: boolean;
  badgeColor?: string;
  isLast?: boolean;
  numberOfLines?: number;
}

const ProfileRow = ({
  icon,
  iconBg,
  iconColor,
  label,
  value,
  isBadge = false,
  badgeColor,
  isLast = false,
  numberOfLines = 1,
}: ProfileRowProps) => {
  const { theme } = useTheme();
  const displayVal = value && value.trim() !== '' && value !== '-' ? value : null;

  return (
    <View style={[styles.profileRowContainer, !isLast && [styles.profileRowDivider, { borderBottomColor: theme.divider || theme.border }]]}>
      <View style={styles.profileRowLeft}>
        <View style={[styles.profileRowIconBox, { backgroundColor: iconBg }]}>
          <Icon name={icon as any} size={16} color={iconColor} />
        </View>
        <Text style={[styles.profileRowLabel, { color: theme.textSecondary }]} numberOfLines={1}>
          {label}
        </Text>
      </View>
      <View style={[styles.profileRowRight, numberOfLines > 1 && { maxWidth: '58%' }]}>
        {isBadge && displayVal ? (
          <View
            style={[
              styles.profileRowBadge,
              badgeColor ? { backgroundColor: badgeColor + '15', borderColor: badgeColor + '35' } : null,
            ]}
          >
            <Text
              style={[
                styles.profileRowBadgeText,
                badgeColor ? { color: badgeColor } : null,
              ]}
              numberOfLines={1}
            >
              {displayVal}
            </Text>
          </View>
        ) : (
          <Text
            style={[
              displayVal ? [styles.profileRowValue, { color: theme.text }] : [styles.profileRowValueEmpty, { color: theme.muted }],
            ]}
            numberOfLines={numberOfLines}
            ellipsizeMode="tail"
          >
            {displayVal || 'Belum diisi'}
          </Text>
        )}
      </View>
    </View>
  );
};

interface AccountActionItemProps {
  icon: string;
  iconBg: string;
  iconColor: string;
  title: string;
  subtitle: string;
  onPress: () => void;
  isDanger?: boolean;
  isLast?: boolean;
}

const AccountActionItem = ({
  icon,
  iconBg,
  iconColor,
  title,
  subtitle,
  onPress,
  isDanger = false,
  isLast = false,
}: AccountActionItemProps) => {
  const { theme, isDark } = useTheme();
  return (
    <Pressable
      onPress={onPress}
      style={({ pressed }) => [
        styles.actionItem,
        !isLast && [styles.actionItemDivider, { borderBottomColor: theme.divider || theme.border }],
        pressed && [styles.actionItemPressed, { backgroundColor: isDark ? '#33415550' : '#F8FAFC' }],
      ]}
    >
      <View style={[styles.actionIconBox, { backgroundColor: iconBg }]}>
        <Icon name={icon as any} size={18} color={iconColor} />
      </View>
      <View style={styles.actionTextBox}>
        <Text style={[styles.actionTitle, { color: isDanger ? '#dc2626' : theme.text }]} numberOfLines={1}>
          {title}
        </Text>
        <Text style={[styles.actionSubtitle, { color: isDanger ? '#ef4444' : theme.textSecondary }]} numberOfLines={1}>
          {subtitle}
        </Text>
      </View>
      <Icon
        name="chevron-right"
        size={18}
        color={isDanger ? '#f87171' : theme.muted}
        style={{ opacity: 0.6 }}
      />
    </Pressable>
  );
};
const TipCard = ({ title, text, style }: { title: string; text: string; style?: any }) => (
  <View style={[styles.tipCard, style]}>
    <Icon name="info" size={20} color={Colors.white} />{/* */}
    <View style={{ flex: 1, marginLeft: 12 }}>
      <Text style={styles.tipTitle}>{title}</Text>{/* */}
      <Text style={styles.tipText}>{text}</Text>
    </View>
  </View>
)
const styles = StyleSheet.create({
  safe: { flex: 1, backgroundColor: Colors.bg },
  scroll: { gap: verticalScale(12) },
  header: {
    backgroundColor: Colors.white,
    borderRadius: moderateScale(16),
    padding: moderateScale(16),
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: Colors.border,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.08,
    shadowRadius: 12,
    elevation: 4,
    overflow: 'hidden',
    position: 'relative',
  },
  headerRow: {
    flexDirection: "row",
    alignItems: "flex-start",
    justifyContent: "space-between"
  },
  greetingContainer: {
    flex: 1,
    marginRight: moderateScale(12),
    flexShrink: 1
  },
  greet: {
    color: Colors.muted,
    fontSize: moderateScale(14),
    marginBottom: verticalScale(4)
  },
  name: {
    color: Colors.text,
    fontSize: moderateScale(16),
    fontWeight: "700",
    flexWrap: 'wrap',
    flexShrink: 1
  },
  profileBadge: {
    backgroundColor: Colors.primary,
    width: moderateScale(40),
    height: moderateScale(40),
    borderRadius: moderateScale(20),
    alignItems: 'center',
    justifyContent: 'center',
    flexShrink: 0,
    marginTop: verticalScale(2)
  },
  divisiBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'flex-start',
    paddingHorizontal: horizontalScale(8),
    paddingVertical: verticalScale(3),
    borderRadius: moderateScale(6),
    borderWidth: 1,
    marginTop: verticalScale(6),
    gap: horizontalScale(6),
  },
  divisiDot: {
    width: moderateScale(6),
    height: moderateScale(6),
    borderRadius: moderateScale(3),
  },
  divisiBadgeText: {
    fontSize: moderateScale(11),
    fontWeight: '700',
  },
  divisiPillSmall: {
    paddingHorizontal: horizontalScale(8),
    paddingVertical: verticalScale(2),
    borderRadius: moderateScale(6),
  },
  divisiPillSmallText: {
    fontSize: moderateScale(11),
    fontWeight: '700',
  },
  card: {
    backgroundColor: Colors.white,
    borderRadius: moderateScale(16),
    padding: moderateScale(16),
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: Colors.border,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 8,
    elevation: 3,
    gap: verticalScale(12)
  },
  cardTitle: {
    color: Colors.text,
    fontSize: moderateScale(18),
    fontWeight: "700",
    marginBottom: verticalScale(4)
  },
  chartHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: verticalScale(12),
  },
  chartTitle: {
    fontSize: moderateScale(18),
    fontWeight: '700',
    color: Colors.text,
  },
  infoIconButton: {
    padding: moderateScale(6),
    backgroundColor: Colors.bg,
    borderRadius: moderateScale(20),
    borderWidth: 1,
    borderColor: Colors.border,
  },
  profileHeroTag: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'flex-start',
    gap: horizontalScale(4),
    paddingHorizontal: horizontalScale(8),
    paddingVertical: verticalScale(2.5),
    borderRadius: moderateScale(6),
    marginBottom: verticalScale(2),
  },
  profileHeroTagText: {
    fontSize: moderateScale(11),
    fontWeight: '700',
    letterSpacing: 0.2,
  },
  avatarEditIconBadge: {
    position: 'absolute',
    bottom: 0,
    right: 0,
    width: moderateScale(18),
    height: moderateScale(18),
    borderRadius: moderateScale(9),
    alignItems: 'center',
    justifyContent: 'center',
    borderWidth: 1.5,
    borderColor: Colors.white,
  },
  profileStatGrid: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    gap: horizontalScale(8),
  },
  profileStatCard: {
    flex: 1,
    backgroundColor: Colors.white,
    borderRadius: moderateScale(14),
    paddingVertical: verticalScale(10),
    paddingHorizontal: horizontalScale(6),
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#E2E8F0',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.04,
    shadowRadius: 4,
    elevation: 2,
  },
  profileStatIconBox: {
    width: moderateScale(30),
    height: moderateScale(30),
    borderRadius: moderateScale(8),
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: verticalScale(4),
  },
  profileStatLabel: {
    fontSize: moderateScale(9.5),
    fontWeight: '700',
    color: '#64748B',
    letterSpacing: 0.4,
    marginBottom: verticalScale(2),
  },
  profileStatValue: {
    fontSize: moderateScale(12.5),
    fontWeight: '700',
    color: Colors.text,
    textAlign: 'center',
  },
  // Profile Row Styles
  profileRowContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: verticalScale(11),
    gap: horizontalScale(10),
  },
  profileRowDivider: {
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: '#F1F5F9',
  },
  profileRowLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: horizontalScale(10),
    flex: 1,
    marginRight: horizontalScale(8),
  },
  profileRowIconBox: {
    width: moderateScale(34),
    height: moderateScale(34),
    borderRadius: moderateScale(10),
    alignItems: 'center',
    justifyContent: 'center',
    flexShrink: 0,
  },
  profileRowLabel: {
    color: '#475569',
    fontSize: moderateScale(13),
    fontWeight: '600',
    flexShrink: 1,
  },
  profileRowRight: {
    alignItems: 'flex-end',
    justifyContent: 'center',
    flexShrink: 1,
    maxWidth: '52%',
  },
  profileRowValue: {
    color: Colors.text,
    fontSize: moderateScale(13.5),
    fontWeight: '700',
    textAlign: 'right',
  },
  profileRowValueEmpty: {
    color: '#94A3B8',
    fontWeight: '400',
    fontStyle: 'italic',
    fontSize: moderateScale(12),
  },
  profileRowBadge: {
    paddingHorizontal: horizontalScale(9),
    paddingVertical: verticalScale(3),
    borderRadius: moderateScale(6),
    borderWidth: 1,
  },
  profileRowBadgeText: {
    fontSize: moderateScale(11.5),
    fontWeight: '700',
  },

  // Account Action Items
  actionItem: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: verticalScale(12),
    paddingHorizontal: horizontalScale(8),
    borderRadius: moderateScale(12),
    gap: horizontalScale(12),
  },
  actionItemDivider: {
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: '#F1F5F9',
  },
  actionItemPressed: {
    backgroundColor: '#F8FAFC',
    opacity: 0.85,
  },
  actionIconBox: {
    width: moderateScale(38),
    height: moderateScale(38),
    borderRadius: moderateScale(11),
    alignItems: 'center',
    justifyContent: 'center',
    flexShrink: 0,
  },
  actionTextBox: {
    flex: 1,
    justifyContent: 'center',
    marginRight: horizontalScale(6),
  },
  actionTitle: {
    color: Colors.text,
    fontSize: moderateScale(13.5),
    fontWeight: '700',
    marginBottom: verticalScale(1),
  },
  actionSubtitle: {
    color: Colors.muted,
    fontSize: moderateScale(11),
    fontWeight: '500',
  },

  // Profile Footer
  profileFooter: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: verticalScale(20),
    gap: verticalScale(4),
  },
  profileFooterBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: horizontalScale(6),
  },
  profileFooterDot: {
    width: moderateScale(6),
    height: moderateScale(6),
    borderRadius: moderateScale(3),
  },
  profileFooterApp: {
    fontSize: moderateScale(12),
    fontWeight: '700',
    color: Colors.text,
    letterSpacing: 0.3,
  },
  profileFooterOrg: {
    fontSize: moderateScale(11),
    color: Colors.muted,
    fontWeight: '500',
  },
  profileFooterVersion: {
    fontSize: moderateScale(10),
    color: '#94A3B8',
    marginTop: verticalScale(2),
  },
  metrics: {
    flexDirection: "row",
    justifyContent: "space-between",
    gap: horizontalScale(2)
  },
  metric: { alignItems: "center", flex: 1 },
  metricValue: {
    color: Colors.primary,
    fontSize: moderateScale(20),
    fontWeight: "800",
    marginBottom: verticalScale(2)
  },
  metricUnit: {
    color: Colors.muted,
    marginBottom: verticalScale(4),
    fontSize: moderateScale(12)
  },
  metricLabel: {
    color: Colors.text,
    opacity: 0.8,
    fontSize: moderateScale(12)
  },
  label: {
    color: Colors.muted,
    marginBottom: verticalScale(6),
    marginTop: verticalScale(2),
    fontSize: moderateScale(14)
  },
  input: {
    backgroundColor: Colors.white,
    borderWidth: 1,
    borderColor: Colors.border,
    borderRadius: moderateScale(12),
    paddingHorizontal: horizontalScale(12),
    paddingVertical: verticalScale(12),
    color: Colors.text,
    fontSize: moderateScale(16)
  },
  primaryBtn: {
    backgroundColor: Colors.primary,
    paddingVertical: verticalScale(14),
    borderRadius: moderateScale(12),
    alignItems: "center",
    marginTop: verticalScale(16),
    shadowColor: Colors.primary,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 4
  },
  primaryBtnText: {
    color: Colors.white,
    fontWeight: "700",
    fontSize: moderateScale(16)
  },
  ghostBtn: {
    borderWidth: 1,
    borderColor: Colors.border,
    paddingHorizontal: horizontalScale(12),
    paddingVertical: verticalScale(8),
    borderRadius: moderateScale(10),
    backgroundColor: Colors.white,
  },
  ghostBtnText: {
    color: Colors.accent,
    fontWeight: "700"
  },
  centerRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: horizontalScale(8)
  },
  emptyState: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: verticalScale(40)
  },
  mutedText: {
    color: Colors.muted,
    fontSize: moderateScale(14)
  },
  tipCard: {
    backgroundColor: Colors.text,
    borderRadius: moderateScale(16),
    padding: moderateScale(16),
    flexDirection: "row",
    alignItems: "flex-start"
  },
  tipTitle: {
    color: Colors.white,
    fontWeight: "800",
    marginBottom: verticalScale(2),
    fontSize: moderateScale(16)
  },
  tipText: {
    color: Colors.white,
    opacity: 0.9,
    fontSize: moderateScale(14),
    lineHeight: moderateScale(20)
  },
  yearGroup: { marginBottom: verticalScale(20) },
  yearTitle: {
    color: Colors.text,
    fontSize: moderateScale(18),
    fontWeight: '800',
    marginBottom: verticalScale(12),
    paddingBottom: verticalScale(8),
    borderBottomWidth: 2,
    borderBottomColor: Colors.primary
  },
  monthGroup: { marginBottom: verticalScale(16) },
  monthTitle: {
    color: Colors.primary,
    fontSize: moderateScale(16),
    fontWeight: '700',
    marginBottom: verticalScale(8)
  },
  healthCard: {
    backgroundColor: Colors.bg,
    borderRadius: moderateScale(12),
    padding: moderateScale(12),
    marginBottom: verticalScale(8),
    borderWidth: 1,
    borderColor: Colors.border
  },
  healthHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: verticalScale(8),
    gap: horizontalScale(6)
  },
  healthDate: {
    color: Colors.text,
    fontSize: moderateScale(13),
    fontWeight: '600'
  },
  healthMetrics: { gap: verticalScale(6) },
  healthMetricRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center'
  },
  healthLabel: {
    color: Colors.muted,
    fontSize: moderateScale(13)
  },
  healthValue: {
    color: Colors.text,
    fontSize: moderateScale(13),
    fontWeight: '600'
  },
  healthMessage: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginTop: verticalScale(8),
    paddingTop: verticalScale(8),
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: Colors.border,
    gap: horizontalScale(6)
  },
  healthMessageText: {
    flex: 1,
    color: Colors.muted,
    fontSize: moderateScale(12),
    lineHeight: moderateScale(16)
  },
  hbCard: {
    backgroundColor: Colors.bg,
    borderRadius: moderateScale(12),
    padding: moderateScale(12),
    marginBottom: verticalScale(8),
    borderWidth: 1,
    borderColor: Colors.border
  },
  hbHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: verticalScale(8)
  },
  hbDate: {
    color: Colors.text,
    fontSize: moderateScale(13),
    fontWeight: '600'
  },
  hbBadge: {
    backgroundColor: Colors.primary,
    paddingHorizontal: moderateScale(10),
    paddingVertical: verticalScale(4),
    borderRadius: moderateScale(8)
  },
  hbBadgeText: {
    color: Colors.white,
    fontSize: moderateScale(13),
    fontWeight: '700'
  },
  hbStatus: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: horizontalScale(8),
    marginBottom: verticalScale(4)
  },
  hbStatusLabel: {
    color: Colors.muted,
    fontSize: moderateScale(13)
  },
  hbStatusValue: {
    fontSize: moderateScale(14),
    fontWeight: '700'
  },
  hbMessage: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginTop: verticalScale(8),
    paddingTop: verticalScale(8),
    borderTopWidth: StyleSheet.hairlineWidth,
    borderTopColor: Colors.border,
    gap: horizontalScale(6)
  },
  hbMessageText: {
    flex: 1,
    color: Colors.muted,
    fontSize: moderateScale(12),
    lineHeight: moderateScale(16)
  },
  haidCard: {
    backgroundColor: Colors.bg,
    borderRadius: moderateScale(12),
    padding: moderateScale(12),
    marginBottom: verticalScale(8),
    borderWidth: 1,
    borderColor: Colors.border
  },
  haidHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: verticalScale(8),
    gap: horizontalScale(6)
  },
  haidDate: {
    color: Colors.text,
    fontSize: moderateScale(13),
    fontWeight: '600',
    flex: 1
  },
  haidMetrics: { gap: verticalScale(6) },
  haidMetricRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center'
  },
  haidLabel: {
    color: Colors.muted,
    fontSize: moderateScale(13)
  },
  haidValue: {
    color: Colors.text,
    fontSize: moderateScale(13),
    fontWeight: '600',
    flex: 1,
    textAlign: 'right'
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'center',
    alignItems: 'center'
  },
  modalContent: {
    backgroundColor: Colors.white,
    borderRadius: moderateScale(16),
    padding: moderateScale(20),
    width: horizontalScale(300),
    gap: verticalScale(12)
  },
  modalContentLarge: {
    backgroundColor: Colors.white,
    borderRadius: moderateScale(20),
    padding: moderateScale(20),
    width: horizontalScale(340),
    maxHeight: SCREEN_HEIGHT * 0.85,
  },
  modalScrollView: {
    maxHeight: SCREEN_HEIGHT * 0.65,
  },
  modalActions: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginTop: verticalScale(16)
  },
  modalButton: {
    flex: 1,
    paddingVertical: verticalScale(12),
    borderRadius: moderateScale(12),
    alignItems: 'center',
    marginHorizontal: horizontalScale(4)
  },
  modalButtonText: {
    color: Colors.white,
    fontWeight: '700',
    fontSize: moderateScale(16)
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: verticalScale(16),
  },
  modalCloseButton: {
    padding: moderateScale(4),
  },
  currentHaidBanner: {
    backgroundColor: Colors.primary,
    borderRadius: moderateScale(12),
    padding: moderateScale(16),
    flexDirection: 'row',
    alignItems: 'center',
  },
  currentHaidBannerTitle: {
    color: Colors.white,
    fontSize: moderateScale(16),
    fontWeight: '700',
    marginBottom: verticalScale(4),
  },
  currentHaidBannerText: {
    color: Colors.white,
    fontSize: moderateScale(13),
    opacity: 0.9,
  },
  haidActionButtons: {
    flexDirection: 'row',
    gap: horizontalScale(8),
  },
  haidActionButton: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: verticalScale(14),
    borderRadius: moderateScale(12),
    gap: horizontalScale(8),
  },
  haidStartButton: {
    backgroundColor: '#10b981',
  },
  haidEndButton: {
    backgroundColor: '#ef4444',
  },
  haidPitaButton: {
    backgroundColor: '#8b5cf6',
  },
  haidActionButtonText: {
    color: Colors.white,
    fontSize: moderateScale(14),
    fontWeight: '700',
  },
  infoBox: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    backgroundColor: Colors.bg,
    padding: moderateScale(12),
    borderRadius: moderateScale(8),
    gap: horizontalScale(8),
    borderWidth: 1,
    borderColor: Colors.border,
  },
  infoText: {
    flex: 1,
    color: Colors.muted,
    fontSize: moderateScale(13),
    lineHeight: moderateScale(18),
  },
  haidLinks: {
    gap: verticalScale(8),
  },
  haidLink: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: verticalScale(12),
    paddingHorizontal: moderateScale(8),
    borderRadius: moderateScale(8),
    backgroundColor: Colors.bg,
  },
  haidLinkText: {
    flex: 1,
    color: Colors.text,
    fontSize: moderateScale(14),
    fontWeight: '500',
    marginLeft: moderateScale(12),
  },
  notificationBanner: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    padding: moderateScale(16),
    borderRadius: moderateScale(12),
    borderWidth: 1,
    borderColor: 'transparent',
  },
  notificationTitle: {
    fontSize: moderateScale(14),
    fontWeight: '700',
    marginBottom: verticalScale(4),
  },
  notificationText: {
    fontSize: moderateScale(13),
    lineHeight: moderateScale(18),
  },
  pitaCard: {
    backgroundColor: Colors.bg,
    borderRadius: moderateScale(12),
    padding: moderateScale(12),
    marginBottom: verticalScale(8),
    borderWidth: 1,
    borderColor: Colors.border
  },
  pitaHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: verticalScale(8)
  },
  pitaDate: {
    color: Colors.text,
    fontSize: moderateScale(13),
    fontWeight: '600'
  },
  pitaStatusBadge: {
    paddingHorizontal: moderateScale(10),
    paddingVertical: verticalScale(4),
    borderRadius: moderateScale(8)
  },
  pitaStatusText: {
    color: Colors.white,
    fontSize: moderateScale(11),
    fontWeight: '700'
  },
  pitaMetrics: { gap: verticalScale(6) },
  pitaMetricRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center'
  },
  pitaLabel: {
    color: Colors.muted,
    fontSize: moderateScale(13)
  },
  pitaValue: {
    color: Colors.text,
    fontSize: moderateScale(13),
    fontWeight: '600',
    flex: 1,
    textAlign: 'right'
  },
  modalSection: {
    marginBottom: verticalScale(4),
    paddingBottom: verticalScale(16),
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: Colors.border,
  },
  modalSectionTitle: {
    fontSize: moderateScale(16),
    fontWeight: '700',
    color: Colors.text,
    marginLeft: moderateScale(8),
    flex: 1,
  },
  sectionTitleRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: verticalScale(12),
  },
  modalSectionText: {
    fontSize: moderateScale(14),
    color: Colors.text,
    lineHeight: moderateScale(20),
    marginBottom: verticalScale(8),
  },
  noteBox: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    backgroundColor: '#fef3c7',
    padding: moderateScale(12),
    borderRadius: moderateScale(8),
    borderWidth: 1,
    borderColor: '#f59e0b',
    marginTop: verticalScale(16),
    gap: horizontalScale(8),
  },
  noteText: {
    flex: 1,
    color: '#92400e',
    fontSize: moderateScale(13),
    lineHeight: moderateScale(18),
  },
  detailTable: {
    borderWidth: 1,
    borderColor: Colors.border,
    borderRadius: moderateScale(8),
    overflow: 'hidden',
    backgroundColor: Colors.bg,
  },
  detailTableRow: {
    flexDirection: 'row',
    paddingVertical: verticalScale(10),
    paddingHorizontal: horizontalScale(12),
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: Colors.border,
  },
  detailTableHeader: {
    fontSize: moderateScale(12),
    fontWeight: '700',
    color: Colors.text,
    flex: 1,
    textAlign: 'center',
  },
  detailTableCell: {
    fontSize: moderateScale(12),
    color: Colors.text,
    flex: 1,
    textAlign: 'center',
  },
  loadingOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center',
    alignItems: 'center'
  },
  loadingContainer: {
    backgroundColor: Colors.white,
    padding: moderateScale(20),
    borderRadius: moderateScale(16),
    alignItems: 'center',
  },
  loadingText: {
    marginTop: verticalScale(12),
    color: Colors.text,
    fontSize: moderateScale(16),
    fontWeight: '600',
  },
  menuGridHeader: {
    marginBottom: verticalScale(12),
  },
  menuGridSubtitle: {
    fontSize: moderateScale(12),
    color: Colors.muted,
    marginTop: verticalScale(2),
  },
  menuGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    justifyContent: 'flex-start',
    gap: moderateScale(10),
  },
  menuGridItem: {
    width: '31%',
    backgroundColor: '#F8FAFC',
    borderRadius: moderateScale(12),
    paddingVertical: verticalScale(12),
    paddingHorizontal: horizontalScale(6),
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  menuGridItemPressed: {
    opacity: 0.8,
    transform: [{ scale: 0.97 }],
  },
  menuGridIconContainer: {
    width: moderateScale(44),
    height: moderateScale(44),
    borderRadius: moderateScale(12),
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: verticalScale(6),
  },
  menuGridItemTitle: {
    fontSize: moderateScale(12),
    fontWeight: '700',
    color: Colors.text,
    textAlign: 'center',
  },
  menuGridItemSub: {
    fontSize: moderateScale(10),
    color: Colors.muted,
    textAlign: 'center',
    marginTop: verticalScale(1),
  },
  subTabRow: {
    flexDirection: 'row',
    backgroundColor: '#F1F5F9',
    borderRadius: moderateScale(10),
    padding: moderateScale(4),
    gap: moderateScale(4),
  },
  subTabButton: {
    flex: 1,
    paddingVertical: verticalScale(8),
    borderRadius: moderateScale(8),
    alignItems: 'center',
    justifyContent: 'center',
  },
  subTabButtonText: {
    fontSize: moderateScale(12),
    fontWeight: '600',
    color: Colors.muted,
  },
  subTabButtonTextActive: {
    color: Colors.white,
    fontWeight: '700',
  },
  subScreenHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: verticalScale(6),
    paddingHorizontal: horizontalScale(4),
  },
  backButton: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: verticalScale(6),
    paddingHorizontal: horizontalScale(10),
    borderRadius: moderateScale(8),
    backgroundColor: Colors.white,
    borderWidth: 1,
    borderColor: '#E2E8F0',
    gap: horizontalScale(4),
  },
  backButtonText: {
    fontSize: moderateScale(12),
    fontWeight: '600',
  },
  subScreenTitle: {
    fontSize: moderateScale(15),
    fontWeight: '700',
    color: Colors.text,
  },
  collapseArrowBtn: {
    width: moderateScale(28),
    height: moderateScale(28),
    borderRadius: moderateScale(14),
    alignItems: 'center',
    justifyContent: 'center',
  },
})
export default HomeScreenMember