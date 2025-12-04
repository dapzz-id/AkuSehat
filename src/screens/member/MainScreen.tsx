"use client"
import React from "react"
import { useEffect, useRef, useState, useMemo } from "react"
import { Animated, StatusBar, StyleSheet, Text, View, ScrollView, Pressable, TextInput, ActivityIndicator, useWindowDimensions, Dimensions, Modal, Alert } from "react-native"
import Icon from 'react-native-vector-icons/Feather'
import { SafeAreaView } from "react-native-safe-area-context"
import { Colors } from "../../settings/Colors"
import api from "../../api/axiosConfig"
import AnimatedBottomTabs, { type TabItem } from "../../components/AnimatedBottomTabs"
import HealthBarChart from "../../components/HealthBarChart"
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
  id: number;
  nama: string;
  username: string;
  nomor_induk: string;
  divisi?: string;
  jk?: "L" | "P";
  level?: string
}
type KesehatanItem = {
  id_kesehatan: number;
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
  id_hb: number;
  tgl: string;
  hb: string;
  status: string;
  pesan: string
}
type HaidItem = {
  id: number;
  id_user: number;
  tanggal_mulai: string;
  tanggal_selesai: string | null;
  durasi_hari: number;
  status: string;
  catatan: string | null;
  created_at: string | null;
  updated_at: string | null
}
type PitaItem = {
  id: number;
  id_user: number;
  id_haid: number;
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
  const navigation = useNavigation();
  const [active, setActive] = useState("ringkasan")
  const fadeMount = useRef(new Animated.Value(0)).current
  const slideMount = useRef(new Animated.Value(16)).current
  const fadeTab = useRef(new Animated.Value(1)).current
  const [tabs, setTabs] = useState<TabItem[]>([
    { key: "ringkasan", icon: <Icon name="activity" size={22} color="#444" /> },
    { key: "profil", icon: <Icon name="user" size={22} color="#444" /> }
  ])
  const { width } = useWindowDimensions()
  const isSmallScreen = width < 375
  const [summary, setSummary] = useState<Summary | null>(null)
  const [loadingSummary, setLoadingSummary] = useState(false)
  const [errorSummary, setErrorSummary] = useState<string | null>(null)
  const [profile, setProfile] = useState<Profile | null>(null)
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
  const handleLogout = async () => {
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
  useFocusEffect(
    React.useCallback(() => {
      const check = async () => {
        const user = await checkAuthStatus();
        if (!user) {
          navigation.navigate('Login');
        }
      };
      check();
    }, [navigation])
  );
  useEffect(() => {
    Animated.parallel([
      Animated.timing(fadeMount, { toValue: 1, duration: 500, useNativeDriver: true }),
      Animated.timing(slideMount, { toValue: 0, duration: 500, useNativeDriver: true }),
    ]).start();
  }, [fadeMount, slideMount]);
  useEffect(() => {
    Animated.sequence([
      Animated.timing(fadeTab, { toValue: 0.6, duration: 120, useNativeDriver: true }),
      Animated.timing(fadeTab, { toValue: 1, duration: 160, useNativeDriver: true }),
    ]).start();
  }, [active, fadeTab]);
  useEffect(() => {
    const fetchAll = async () => {
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
        setTabs([
          { key: "ringkasan", icon: <Icon name="activity" size={22} color="#444" /> },
          { key: "kesehatan", icon: <Icon name="heart" size={22} color="#444" /> },
          { key: "hemoglobin", icon: <Icon name="droplet" size={22} color="#444" /> },
          { key: "profil", icon: <Icon name="user" size={22} color="#444" /> },
        ]);
        if (profileData?.jk === "P") {
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
    };
    fetchAll();
  }, []);
  useEffect(() => {
    if (active === "kesehatan" && profile) {
      const fetchKesehatan = async () => {
        try {
          setLoadingKesehatan(true);
          const res = await api.get(`/member/${profile.id}/kesehatan`);
          setKesehatanData(res?.data?.data || []);
        } catch (e: any) {
          console.error("Error fetching kesehatan:", e);
          setKesehatanData([]);
        } finally {
          setLoadingKesehatan(false);
        }
      };
      fetchKesehatan();
    }
  }, [active, profile]);
  useEffect(() => {
    if (active === "hemoglobin" && profile) {
      const fetchHb = async () => {
        try {
          setLoadingHb(true);
          const res = await api.get(`/member/${profile.id}/hb`);
          setHbData(res?.data?.data || []);
        } catch (e: any) {
          console.error("Error fetching hb:", e);
          setHbData([]);
        } finally {
          setLoadingHb(false);
        }
      };
      fetchHb();
    }
  }, [active, profile]);
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
  return (
    <SafeAreaView style={[styles.safe, { flex: 1 }]}>
      <StatusBar barStyle="dark-content" backgroundColor={Colors.bg} />
      <ScrollView
        contentContainerStyle={[styles.scroll, {
          flexGrow: 1,
          paddingBottom: verticalScale(120),
          paddingTop: verticalScale(30),
          paddingHorizontal: isSmallScreen ? horizontalScale(12) : horizontalScale(16)
        }]}
        showsVerticalScrollIndicator={false}
        showsHorizontalScrollIndicator={false}
        overScrollMode="never"
        bounces={false}
        keyboardShouldPersistTaps="handled"
      >
        <Animated.View style={[styles.header, { opacity: fadeMount, transform: [{ translateY: slideMount }] }]}>
          <View style={styles.headerRow}>
            <View style={styles.greetingContainer}>
              <Text style={styles.greet}>{getGreetingTime()},</Text>{/* */}
              <Text style={styles.name} numberOfLines={2} ellipsizeMode="tail">{profile?.nama || "Member"}</Text>
            </View>{/* */}
            <View style={styles.profileBadge}>
              <Icon name="user" size={20} color={Colors.white} />
            </View>
          </View>
        </Animated.View>

        <Animated.View style={{ opacity: fadeTab }}>
          {active === "ringkasan" && (
            <View style={{ gap: verticalScale(12) }}>
              <Card style={{ padding: isSmallScreen ? moderateScale(20) : moderateScale(24) }}>
                <Text style={styles.cardTitle}>Ringkasan Kesehatan</Text>
                {loadingSummary ? (
                  <View style={styles.centerRow}>
                    <ActivityIndicator color={Colors.primary} />{/* */}
                    <Text style={styles.mutedText}>Memuat...</Text>
                  </View>
                ) : errorSummary ? (
                  <Text style={[styles.mutedText, { color: "#dc2626" }]}>{errorSummary}</Text>
                ) : (
                  <>
                    <View style={styles.metrics}>
                      <Metric label="IMT" value={(summary?.bmi ?? 0).toFixed(1)} unit="" />{/* */}
                      <Metric label="Tinggi" value={`${summary?.tinggi ?? 0}`} unit="cm" />{/* */}
                      <Metric label="Berat" value={`${summary?.berat ?? 0}`} unit="kg" />
                    </View>{/* */}
                    <View style={styles.metrics}>
                      <Metric label="Sistol" value={`${summary?.sistol ?? 0}`} unit="mmHg" />{/* */}
                      <Metric label="Diastol" value={`${summary?.diastol ?? 0}`} unit="mmHg" />{/* */}
                      <Metric label="Hb" value={`${summary?.hb ?? 0}`} unit="g/dL" />
                    </View>
                  </>
                )}
              </Card>

              {/* NOTIFIKASI BANNER */}
              {profile?.jk === "P" && peminjamanNotification?.notification && (
                <NotificationBanner notification={peminjamanNotification.notification} />
              )}

              {/* MENU HAID */}
              {profile?.jk === "P" && (
                <Card>
                  <Text style={styles.cardTitle}>Menu Haid</Text>

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
                </Card>
              )}

              {/* Card untuk HealthBarChart */}
              <Card>
                <View style={styles.chartHeader}>
                  <Text style={styles.chartTitle}>Tren Kesehatan</Text>{/* */}
                  <Pressable
                    onPress={() => setHealthInfoModalVisible(true)}
                    style={styles.infoIconButton}
                  >
                    <Icon name="info" size={18} color={Colors.primary} />
                  </Pressable>
                </View>
                <HealthBarChart />
              </Card>
            </View>
          )}

          {active === "kesehatan" && (
            <View style={{ gap: verticalScale(12) }}>
              <Card>
                <Text style={styles.cardTitle}>Riwayat Kesehatan</Text>
                {loadingKesehatan ? (
                  <View style={styles.centerRow}>
                    <ActivityIndicator color={Colors.primary} />{/* */}
                    <Text style={styles.mutedText}>Memuat...</Text>
                  </View>
                ) : kesehatanData.length === 0 ? (
                  <View style={styles.emptyState}>
                    <Icon name="heart" size={40} color={Colors.muted} />{/* */}
                    <Text style={[styles.mutedText, { textAlign: 'center', marginTop: 8 }]}>Belum ada data kesehatan</Text>
                  </View>
                ) : (
                  <ScrollView style={{ maxHeight: verticalScale(500) }} showsVerticalScrollIndicator={false}>
                    {kesehatanData.map((yG) => (
                      <View key={yG.year} style={styles.yearGroup}>
                        <Text style={styles.yearTitle}>{yG.year}</Text>
                        {yG.months.map((mG, idx) => (
                          <View key={`${yG.year}-${idx}`} style={styles.monthGroup}>
                            <Text style={styles.monthTitle}>{mG.month.charAt(0).toUpperCase() + mG.month.slice(1)}</Text>
                            {mG.data.map((item) => (
                              <View key={item.id_kesehatan} style={styles.healthCard}>
                                <View style={styles.healthHeader}>
                                  <Icon name="calendar" size={16} color={Colors.primary} />{/* */}
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
                                    <Text style={styles.healthLabel}>IMT:</Text>{/* */}
                                    <Text style={styles.healthValue}>{item.imt}</Text>
                                  </View>{/* */}
                                  <View style={styles.healthMetricRow}>
                                    <Text style={styles.healthLabel}>BB/TB:</Text>{/* */}
                                    <Text style={styles.healthValue}>{item.bb} kg / {item.tb} cm</Text>
                                  </View>{/* */}
                                  <View style={styles.healthMetricRow}>
                                    <Text style={styles.healthLabel}>Tekanan Darah:</Text>{/* */}
                                    <Text style={styles.healthValue}>{item.sistol}/{item.diastol} mmHg</Text>
                                  </View>{/* */}
                                  <View style={styles.healthMetricRow}>
                                    <Text style={styles.healthLabel}>Status:</Text>{/* */}
                                    <Text style={[styles.healthValue, { color: Colors.primary, fontWeight: '700' }]}>
                                      {item.status}
                                    </Text>
                                  </View>
                                </View>
                                {item.pesan_imt && (
                                  <View style={styles.healthMessage}>
                                    <Icon name="info" size={14} color={Colors.muted} />{/* */}
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
            </View>
          )}

          {active === "hemoglobin" && (
            <View style={{ gap: verticalScale(12) }}>
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
                    <ActivityIndicator color={Colors.primary} />{/* */}
                    <Text style={styles.mutedText}>Memuat...</Text>
                  </View>
                ) : hbData.length === 0 ? (
                  <View style={styles.emptyState}>
                    <Icon name="droplet" size={40} color={Colors.muted} />{/* */}
                    <Text style={[styles.mutedText, { textAlign: 'center', marginTop: 8 }]}>
                      Belum ada data hemoglobin
                    </Text>
                  </View>
                ) : (
                  <ScrollView style={{ maxHeight: verticalScale(500) }} showsVerticalScrollIndicator={false}>
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
                                  <Icon name="droplet" size={18} color="#dc2626" />{/* */}
                                  <View style={{ flex: 1, marginLeft: moderateScale(8) }}>
                                    <Text style={styles.hbDate}>
                                      {new Date(item.tgl).toLocaleDateString('id-ID', {
                                        day: 'numeric',
                                        month: 'long',
                                        year: 'numeric'
                                      })}
                                    </Text>
                                  </View>{/* */}
                                  <View style={styles.hbBadge}>
                                    <Text style={styles.hbBadgeText}>{item.hb} g/dL</Text>
                                  </View>
                                </View>
                                <View style={styles.hbStatus}>
                                  <Text style={styles.hbStatusLabel}>Status:</Text>{/* */}
                                  <Text style={[
                                    styles.hbStatusValue,
                                    { color: item.status === 'Normal' ? '#16a34a' : '#dc2626' }
                                  ]}>
                                    {item.status}
                                  </Text>
                                </View>
                                {item.pesan && (
                                  <View style={styles.hbMessage}>
                                    <Icon name="info" size={14} color={Colors.muted} />{/* */}
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
            </View>
          )}

          {active === "profil" && (
            <View style={{ gap: verticalScale(12) }}>
              <Card style={styles.profileHeaderCard}>
                <View style={styles.profileAvatar}>
                  <Icon name="user" size={40} color={Colors.white} />
                </View>{/* */}
                <View style={styles.profileInfo}>
                  <Text style={styles.profileName}>{profile?.nama || "Member"}</Text>{/* */}
                  <Text style={styles.profileRole}>Member</Text>
                </View>
              </Card>

              <Card>
                <Text style={styles.cardTitle}>Informasi Pribadi</Text>
                {loadingProfile ? (
                  <View style={styles.centerRow}>
                    <ActivityIndicator color={Colors.primary} />{/* */}
                    <Text style={styles.mutedText}>Memuat...</Text>
                  </View>
                ) : profile ? (
                  <View style={styles.profileDetails}>
                    <ProfileField icon="user" label="Nama" value={profile.nama} />{/* */}
                    <ProfileField icon="at-sign" label="Username" value={profile.username} />{/* */}
                    <ProfileField icon="key" label="Nomor Induk" value={profile.nomor_induk || "-"} />{/* */}
                    <ProfileField icon="book" label="Divisi" value={profile.divisi || "-"} />{/* */}
                    <ProfileField
                      icon="user"
                      label="Jenis Kelamin"
                      value={profile.jk === 'P' ? 'Perempuan' : profile.jk === 'L' ? 'Laki-laki' : '-'}
                    />{/* */}
                    <ProfileField icon="shield" label="Peran" value={profile.level || "Member"} />
                  </View>
                ) : (
                  <View style={styles.emptyState}>
                    <Icon name="user-x" size={40} color={Colors.muted} />{/* */}
                    <Text style={[styles.mutedText, { textAlign: 'center', marginTop: 8 }]}>
                      Profil tidak tersedia
                    </Text>
                  </View>
                )}
              </Card>

              <Card>
                <Text style={styles.cardTitle}>Akun</Text>
                <View style={styles.accountActions}>
                  <Pressable onPress={() => setModalVisible(true)} style={styles.accountButton}>
                    <Icon name="lock" size={20} color={Colors.primary} />{/* */}
                    <Text style={styles.accountButtonText}>Ganti Password</Text>{/* */}
                    <Icon name="chevron-right" size={18} color={Colors.muted} />
                  </Pressable>{/* */}
                  <Pressable
                    onPress={handleLogout}
                    disabled={isLoggingOut}
                    style={[
                      styles.accountButton,
                      styles.logoutButton,
                      { opacity: isLoggingOut ? 0.6 : 1 }
                    ]}
                  >
                    <Icon name="log-out" size={20} color="#dc2626" />{/* */}
                    <Text style={[styles.accountButtonText, { color: '#dc2626' }]}>Keluar</Text>
                  </Pressable>
                </View>
              </Card>
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
              <ScrollView style={{ maxHeight: verticalScale(500) }} showsVerticalScrollIndicator={false}>
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
              <ScrollView style={{ maxHeight: verticalScale(500) }} showsVerticalScrollIndicator={false}>
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
      <AnimatedBottomTabs items={tabs} activeKey={active} onChange={setActive} />
    </SafeAreaView>
  )
}
// Komponen helper
const Card = ({ children, style }: { children: React.ReactNode; style?: any }) => (
  <View style={[styles.card, style]}>{children}</View>
)
const Metric = ({ label, value, unit }: { label: string; value: string; unit: string }) => (
  <View style={styles.metric}>
    <Text style={styles.metricValue}>{value}</Text>{/* */}
    <Text style={styles.metricUnit}>{unit}</Text>{/* */}
    <Text style={styles.metricLabel}>{label}</Text>
  </View>
)
const ProfileField = ({ icon, label, value }: { icon: string; label: string; value: string }) => (
  <View style={styles.profileField}>
    <View style={styles.fieldLeft}>
      <Icon name={icon as any} size={18} color={Colors.primary} style={styles.fieldIcon} />{/* */}
      <Text style={styles.fieldLabel}>{label}</Text>
    </View>{/* */}
    <Text style={styles.fieldValue}>{value}</Text>
  </View>
)
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
    shadowOpacity: 0.1,
    shadowRadius: 12,
    elevation: 5
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
  profileHeaderCard: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: moderateScale(20)
  },
  profileAvatar: {
    backgroundColor: Colors.primary,
    width: moderateScale(60),
    height: moderateScale(60),
    borderRadius: moderateScale(30),
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: moderateScale(16)
  },
  profileInfo: { flex: 1 },
  profileName: {
    color: Colors.text,
    fontSize: moderateScale(20),
    fontWeight: '700',
    marginBottom: verticalScale(4)
  },
  profileRole: {
    color: Colors.muted,
    fontSize: moderateScale(14)
  },
  profileDetails: { gap: verticalScale(16) },
  profileField: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: verticalScale(8)
  },
  fieldLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1
  },
  fieldIcon: { marginRight: moderateScale(12) },
  fieldLabel: {
    color: Colors.muted,
    fontSize: moderateScale(14),
    flex: 1
  },
  fieldValue: {
    color: Colors.text,
    fontSize: moderateScale(14),
    fontWeight: '600'
  },
  accountActions: { gap: verticalScale(8) },
  accountButton: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingVertical: verticalScale(12),
    paddingHorizontal: moderateScale(8),
    borderRadius: moderateScale(8),
    backgroundColor: Colors.bg
  },
  accountButtonText: {
    color: Colors.text,
    fontSize: moderateScale(14),
    fontWeight: '500',
    flex: 1,
    marginLeft: moderateScale(12)
  },
  logoutButton: {
    backgroundColor: 'rgba(220, 38, 38, 0.1)',
    marginTop: verticalScale(8)
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
})
export default HomeScreenMember