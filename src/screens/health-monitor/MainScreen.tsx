import React, { useEffect, useRef, useState, useMemo } from "react"
import {
  Animated,
  StatusBar,
  StyleSheet,
  Text,
  View,
  ScrollView,
  Pressable,
  TextInput,
  ActivityIndicator,
  useWindowDimensions,
  Dimensions,
  Modal,
  Alert,
  Image,
  Linking,
  Platform,
} from "react-native"
import Icon from 'react-native-vector-icons/Feather'
import { SafeAreaView } from "react-native-safe-area-context"
import { Colors } from "../../settings/Colors"
import Svg, { Defs, LinearGradient as SvgLinearGradient, Stop, Rect } from 'react-native-svg'
import { logout, checkAuthStatus } from "../../utils/authHelper"
import api from "../../api/axiosConfig"
import AnimatedBottomTabs, { type TabItem } from "../../components/AnimatedBottomTabs"
import { useNavigation, useFocusEffect } from '@react-navigation/native';
import EditProfileModal from "../../components/EditProfileModal";
import ChangelogModal from "../../components/ChangelogModal";
import CheckUpdateModal, { type CheckUpdateStatus } from "../../components/CheckUpdateModal";
import ForceUpgradeModal from "../../components/ForceUpgradeModal";
import MaintenanceModal from "../../components/MaintenanceModal";
import useRealtimeSystemSync from "../../hooks/useRealtimeSystemSync";
import { checkAppUpdate, type SoftwareReleaseData } from "../../api/appUpdateService";
type DashboardData = {
  total_member: number
  pinjaman_aktif: number
  warning_count: number
  menunggu_verifikasi: number
}
type Peminjaman = {
  id: string | number
  user: {
    id: string | number
    nama: string
    nomor_induk: string
    divisi?: string
  }
  tanggal_pinjam: string
  jumlah_pita: number
  estimasi_selesai_haid: string
  status: 'menunggu' | 'dipinjam' | 'dikembalikan' | 'terlambat'
  tanggal_kembali?: string
  keterangan?: string
  created_at: string
  verified: boolean
}
type Profile = {
  id: string | number
  nama: string
  username: string
  nomor_induk?: string
  id_divisi?: string | number
  divisi?: string
  color_cover?: string
  tertiary_color?: string
  email?: string
  avatar?: string | null
  level?: string
  jk?: "L" | "P"
  instansi?: {
    id: string | number
    nama_instansi?: string
  }
}
type Member = {
  id: string | number
  nama: string
  nomor_induk: string
  divisi_name?: string
  jk: string
  dataHaid?: {
    tanggal_selesai: string
    tanggal_mulai: string
    durasi_hari: number
  }
}
const { width: SCREEN_WIDTH, height: SCREEN_HEIGHT } = Dimensions.get('window')
const guidelineBaseWidth = 375
const guidelineBaseHeight = 812
const horizontalScale = (size: number) => (SCREEN_WIDTH / guidelineBaseWidth) * size
const verticalScale = (size: number) => (SCREEN_HEIGHT / guidelineBaseHeight) * size
const moderateScale = (size: number, factor = 0.5) => size + (horizontalScale(size) - size) * factor
const HomeScreenHealthMonitor = () => {
  const [active, setActive] = useState("dashboard")
  const fadeMount = useRef(new Animated.Value(0)).current
  const slideMount = useRef(new Animated.Value(16)).current
  const fadeTab = useRef(new Animated.Value(1)).current
  const shimmerAnim = useRef(new Animated.Value(0)).current
  const navigation = useNavigation<any>();
  // State untuk ganti password
  const [passwordModalVisible, setPasswordModalVisible] = useState(false)
  const [currentPassword, setCurrentPassword] = useState("")
  const [newPassword, setNewPassword] = useState("")
  const [retypePassword, setRetypePassword] = useState("")
  const [passwordError, setPasswordError] = useState<string | null>(null)
  const [savingPassword, setSavingPassword] = useState(false)
  const handleChangePassword = async () => {
    if (newPassword !== retypePassword) {
      setPasswordError("Kata sandi baru tidak cocok.")
      return
    }
    try {
      setSavingPassword(true)
      setPasswordError(null)
      await api.post("/change-password", {
        current_password: currentPassword,
        new_password: newPassword,
        new_password_confirmation: retypePassword,
      })
      setPasswordModalVisible(false)
      setCurrentPassword("")
      setNewPassword("")
      setRetypePassword("")
      Alert.alert("Notifikasi", "Kata sandi berhasil diubah.")
    } catch (e: any) {
      setPasswordError(e?.response?.data?.message || "Gagal mengubah kata sandi.")
    } finally {
      setSavingPassword(false)
    }
  }
  const { width } = useWindowDimensions()
  const isSmallScreen = width < 375
  // States
  const [peminjaman, setPeminjaman] = useState<Peminjaman[]>([])
  const [loadingPeminjaman, setLoadingPeminjaman] = useState(false)
  const [filterStatus, setFilterStatus] = useState<string>("all")
  const [searchQuery, setSearchQuery] = useState("")
  const [showNonActive, setShowNonActive] = useState(false)
  const [profile, setProfile] = useState<Profile | null>(null)
  const [editProfileModalVisible, setEditProfileModalVisible] = useState(false)
  const [changelogModalVisible, setChangelogModalVisible] = useState(false)
  // State untuk check update modal
  const [checkUpdateVisible, setCheckUpdateVisible] = useState(false)
  const [checkUpdateStatus, setCheckUpdateStatus] = useState<CheckUpdateStatus>('checking')
  const [checkUpdateData, setCheckUpdateData] = useState<SoftwareReleaseData | null>(null)
  const [checkUpdateError, setCheckUpdateError] = useState<string | null>(null)
  const [forceUpgradeVisible, setForceUpgradeVisible] = useState(false)
  const [forceUpgradeData, setForceUpgradeData] = useState<SoftwareReleaseData | null>(null)

  // Webhook-Style Conditional Realtime Sync (Zero Overhead / 304 Not Modified):
  // Monitors Maintenance Mode and Mandatory Update in realtime
  const {
    isMaintenance,
    maintenanceMessage,
    isMandatoryUpgrade,
    forceUpgradeData: syncForceUpgradeData,
    recheckSync,
  } = useRealtimeSystemSync({
    intervalMs: 5000,
  });

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

  const [loadingProfile, setLoadingProfile] = useState(false)
  const [warningList, setWarningList] = useState<Peminjaman[]>([])
  const [loadingWarning, setLoadingWarning] = useState(false)
  const tabs: TabItem[] = useMemo(() => [
    { key: "dashboard", label: "Dasbor", icon: <Icon name="home" size={20} /> },
    { key: "peminjaman", label: "Pita UKS", icon: <Icon name="clipboard" size={20} /> },
    {
      key: "warning",
      label: "Peringatan",
      icon: <Icon name="alert-triangle" size={20} />,
      badgeCount: warningList.length > 0 ? warningList.length : undefined,
    },
    { key: "profil", label: "Profil", icon: <Icon name="user" size={20} /> },
  ], [warningList.length]);
  const [modalVisible, setModalVisible] = useState(false)
  const [modalType, setModalType] = useState<'return' | 'delete' | 'verify'>('return')
  const [selectedItem, setSelectedItem] = useState<Peminjaman | null>(null)
  const [actionLoading, setActionLoading] = useState(false)
  // State untuk modal tolak
  const [rejectModalVisible, setRejectModalVisible] = useState(false)
  const [rejectReason, setRejectReason] = useState("")
  const [rejectLoading, setRejectLoading] = useState(false)
  // Form states
  const [showFormModal, setShowFormModal] = useState(false)
  const [formMode, setFormMode] = useState<'create' | 'edit'>('create')
  const [memberList, setMemberList] = useState<Member[]>([])
  const [loadingMember, setLoadingMember] = useState(false)
  const [selectedMember, setSelectedMember] = useState<Member | null>(null)
  const [formData, setFormData] = useState({
    keterangan: '',
  })
  const [savingForm, setSavingForm] = useState(false)
  // Dashboard data
  const [dashboardData, setDashboardData] = useState<DashboardData>({
    total_member: 0,
    pinjaman_aktif: 0,
    warning_count: 0,
    menunggu_verifikasi: 0
  })
  // State untuk loading popup
  const [initialLoading, setInitialLoading] = useState(true)
  // State untuk logout loading
  const [isLoggingOut, setIsLoggingOut] = useState(false)
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
    ]).start()
  }, [fadeMount, slideMount])
  useEffect(() => {
    Animated.sequence([
      Animated.timing(fadeTab, { toValue: 0.6, duration: 120, useNativeDriver: true }),
      Animated.timing(fadeTab, { toValue: 1, duration: 160, useNativeDriver: true }),
    ]).start()
  }, [active, fadeTab])
  useEffect(() => {
    fetchAllData()
  }, [])
  const fetchAllData = async () => {
    try {
      setInitialLoading(true)
      setLoadingPeminjaman(true);
      setLoadingProfile(true);
      setLoadingWarning(true);
      const [peminjamanRes, meRes, warningRes] = await Promise.all([
        api.get("/health-monitor"),
        api.get("/me"),
        api.get("/health-monitor/warning"),
      ]);
      const peminjamanData = peminjamanRes?.data?.data || peminjamanRes?.data || [];
      setPeminjaman(peminjamanData);
      setProfile(meRes?.data?.data || meRes?.data || null);
      const warningData = warningRes?.data?.data || warningRes?.data || [];
      const filteredWarningData = warningData.filter((item: Peminjaman) =>
        item.status === 'terlambat' && item.verified === true
      );
      setWarningList(filteredWarningData);
      // Update dashboard data
      const menungguVerifikasi = peminjamanData.filter((item: Peminjaman) => item.verified === false).length;
      const totalMember = peminjamanRes?.data?.total_member || 0;
      const pinjamanAktif = peminjamanData.filter((item: Peminjaman) =>
        item.verified === true && item.status === 'dipinjam'
      ).length;
      const warningCount = filteredWarningData.length;
      setDashboardData({
        total_member: totalMember,
        pinjaman_aktif: pinjamanAktif,
        warning_count: warningCount,
        menunggu_verifikasi: menungguVerifikasi
      });
    } catch (e: any) {
      console.error("Error fetching data:", e?.message || e);
    } finally {
      setInitialLoading(false)
      setLoadingPeminjaman(false);
      setLoadingProfile(false);
      setLoadingWarning(false);
    }
  };
  const fetchMemberList = async () => {
    try {
      setLoadingMember(true)
      const res = await api.get("/health-monitor/member-haid")
      setMemberList(res?.data?.data || res?.data || [])
    } catch (e: any) {
      console.error("Error fetching member:", e)
      Alert.alert("Error", "Gagal memuat data member")
    } finally {
      setLoadingMember(false)
    }
  }
  const openCreateForm = async () => {
    setFormMode('create')
    setSelectedMember(null)
    setFormData({
      keterangan: '',
    })
    await fetchMemberList()
    setShowFormModal(true)
  }
  const openEditForm = async (item: Peminjaman) => {
    setFormMode('edit')
    setSelectedItem(item)
    setSelectedMember({
      id: item.user.id,
      nama: item.user.nama,
      nomor_induk: item.user.nomor_induk,
      divisi_name: item.user.divisi,
      jk: 'P',
    })
    setFormData({
      keterangan: item.keterangan || '',
    })
    await fetchMemberList()
    setShowFormModal(true)
  }
  const handleSaveForm = async () => {
    if (!selectedMember) {
      Alert.alert("Peringatan", "Pilih member terlebih dahulu")
      return
    }
    try {
      setSavingForm(true)
      const payload = {
        id_user: selectedMember.id,
        tanggal_pinjam: new Date().toISOString().slice(0, 10),
        keterangan: formData.keterangan.trim() || null,
      }
      if (formMode === 'create') {
        await api.post("/health-monitor", payload)
        await fetchAllData()
        Alert.alert("Berhasil", "Data peminjaman berhasil ditambahkan. Menunggu verifikasi.")
      } else if (formMode === 'edit' && selectedItem) {
        await api.put(`/health-monitor/${selectedItem.id}`, {
          keterangan: formData.keterangan.trim() || null,
        })
        await fetchAllData()
        Alert.alert("Berhasil", "Data peminjaman berhasil diupdate")
      }
      setShowFormModal(false)
      setSelectedItem(null)
      setSelectedMember(null)
    } catch (e: any) {
      console.error("Error saving form:", e)
      Alert.alert("Error", e?.response?.data?.message || "Gagal menyimpan data")
    } finally {
      setSavingForm(false)
    }
  }
  const handleReturn = async () => {
    if (!selectedItem) return
    try {
      setActionLoading(true)
      await api.put(`/health-monitor/${selectedItem.id}`, {
        tanggal_kembali: new Date().toISOString().slice(0, 10)
      })
      await fetchAllData()
      setModalVisible(false)
      setSelectedItem(null)
      Alert.alert("Berhasil", "Pita berhasil dikembalikan")
    } catch (e: any) {
      console.error("Error returning item:", e?.response?.data?.message || e?.message)
      Alert.alert("Error", e?.response?.data?.message || "Gagal mengembalikan pita")
    } finally {
      setActionLoading(false)
    }
  }
  const handleDelete = async () => {
    if (!selectedItem) return
    try {
      setActionLoading(true)
      await api.delete(`/health-monitor/${selectedItem.id}`)
      await fetchAllData()
      setModalVisible(false)
      setSelectedItem(null)
      Alert.alert("Berhasil", "Data peminjaman berhasil dihapus")
    } catch (e: any) {
      console.error("Error deleting item:", e?.response?.data?.message || e?.message)
      Alert.alert("Error", e?.response?.data?.message || "Gagal menghapus data")
    } finally {
      setActionLoading(false)
    }
  }
  const handleVerify = async () => {
    if (!selectedItem) return
    try {
      setActionLoading(true)
      await api.put(`/health-monitor/${selectedItem.id}/accept-verifikasi`)
      await fetchAllData()
      setModalVisible(false)
      setSelectedItem(null)
      Alert.alert("Berhasil", "Peminjaman berhasil diverifikasi")
    } catch (e: any) {
      console.error("Error verifying item:", e?.response?.data?.message || e?.message)
      Alert.alert("Error", e?.response?.data?.message || "Gagal memverifikasi peminjaman")
    } finally {
      setActionLoading(false)
    }
  }
  const handleReject = async () => {
    if (!selectedItem) return
    try {
      setRejectLoading(true)
      await api.put(`/health-monitor/${selectedItem.id}/reject-verifikasi`, {
        alasan_penolakan: rejectReason
      })
      await fetchAllData()
      setRejectModalVisible(false)
      setSelectedItem(null)
      setRejectReason("")
      Alert.alert("Berhasil", "Peminjaman berhasil ditolak")
    } catch (e: any) {
      console.error("Error rejecting item:", e?.response?.data?.message || e?.message)
      Alert.alert("Error", e?.response?.data?.message || "Gagal menolak peminjaman")
    } finally {
      setRejectLoading(false)
    }
  }
  const filteredPeminjaman = peminjaman.filter(item => {
    const statusMatch = filterStatus === 'all' || item.status === filterStatus
    const verifiedMatch = showNonActive ? item.verified === false : item.verified === true
    const searchMatch = searchQuery === '' ||
      item.user.nama.toLowerCase().includes(searchQuery.toLowerCase()) ||
      item.user.nomor_induk.toLowerCase().includes(searchQuery.toLowerCase())

    return statusMatch && verifiedMatch && searchMatch
  })
  const getGreetingTime = (): string => {
    const now = new Date()
    const hour = now.getHours()
    if (hour >= 0 && hour < 12) return "Selamat pagi 🌥️"
    if (hour >= 12 && hour < 15) return "Selamat siang 🌞"
    if (hour >= 15 && hour < 18) return "Selamat sore 🌤️"
    return "Selamat malam 🌜"
  }
  const formatDate = (dateString: string) => {
    if (!dateString) return '-'
    const date = new Date(dateString)
    return date.toLocaleDateString('id-ID', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit'
    })
  }
  const calculateLatedays = (estimasi: string) => {
    const today = new Date()
    const estimasiDate = new Date(estimasi)
    const diffTime = today.getTime() - estimasiDate.getTime()
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return diffDays > 0 ? diffDays : 0
  }
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
  const handleCheckNow = () => {
    setActive("peminjaman")
    setShowNonActive(true)
  }
  const hasDivisi = Boolean(profile?.divisi || profile?.id_divisi);
  const tertiaryColor = profile?.color_cover || profile?.tertiary_color || Colors.defaultTertiary || '#2e7d32';

  useEffect(() => {
    if (!profile?.divisi && !profile?.id_divisi) return;
    const animation = Animated.loop(
      Animated.sequence([
        Animated.timing(shimmerAnim, {
          toValue: 1,
          duration: 2600,
          useNativeDriver: true,
        }),
        Animated.delay(1800),
      ])
    );
    animation.start();
    return () => animation.stop();
  }, [profile?.divisi, profile?.id_divisi, shimmerAnim]);

  const shimmerTranslateX = shimmerAnim.interpolate({
    inputRange: [0, 1],
    outputRange: [-SCREEN_WIDTH * 0.9, SCREEN_WIDTH * 1.3],
  });
  const shimmerOpacity = shimmerAnim.interpolate({
    inputRange: [0, 0.15, 0.85, 1],
    outputRange: [0, 1, 1, 0],
  });

  return (
    <SafeAreaView edges={["top", "left", "right"]} style={[styles.safe, { flex: 1 }]}>
      <StatusBar barStyle="dark-content" backgroundColor={Colors.bg} />
      <ScrollView
        contentContainerStyle={[
          styles.scroll,
          {
            flexGrow: 1,
            paddingBottom: verticalScale(140),
            paddingTop: verticalScale(30),
            paddingHorizontal: isSmallScreen ? horizontalScale(12) : horizontalScale(16)
          }
        ]}
        showsVerticalScrollIndicator={false}
        overScrollMode="never"
        bounces={false}
        keyboardShouldPersistTaps="handled"
      >
        <Animated.View style={[
          styles.header,
          { 
            opacity: fadeMount, 
            transform: [{ translateY: slideMount }],
            shadowColor: hasDivisi ? tertiaryColor : "#000",
            shadowOpacity: hasDivisi ? 0.16 : 0.08,
            shadowRadius: 14,
            borderColor: hasDivisi ? tertiaryColor + '30' : Colors.border,
            borderWidth: hasDivisi ? 1.2 : StyleSheet.hairlineWidth,
          }
        ]}>
          {hasDivisi && (
            <View style={StyleSheet.absoluteFill} pointerEvents="none">
              {/* Subtle top accent line */}
              <View 
                style={{
                  position: 'absolute',
                  top: 0,
                  left: 0,
                  right: 0,
                  height: 3,
                  backgroundColor: tertiaryColor,
                  opacity: 0.9,
                }} 
              />
              {/* Ambient radial glow in top right */}
              <View 
                style={{
                  position: 'absolute',
                  top: -moderateScale(25),
                  right: -moderateScale(15),
                  width: moderateScale(130),
                  height: moderateScale(130),
                  borderRadius: moderateScale(65),
                  backgroundColor: tertiaryColor,
                  opacity: 0.08,
                }} 
              />
              {/* Animated Light Sweep (cahaya berjalan ke samping sesuai warna divisi) */}
              <Animated.View
                style={[
                  StyleSheet.absoluteFill,
                  {
                    width: SCREEN_WIDTH * 0.85,
                    opacity: shimmerOpacity,
                    transform: [
                      { translateX: shimmerTranslateX },
                      { skewX: '-22deg' },
                    ],
                  },
                ]}
              >
                <Svg height="100%" width="100%">
                  <Defs>
                    <SvgLinearGradient id="shimmerBeamHM" x1="0%" y1="0%" x2="100%" y2="0%">
                      <Stop offset="0%" stopColor={tertiaryColor} stopOpacity="0" />
                      <Stop offset="30%" stopColor={tertiaryColor} stopOpacity="0.10" />
                      <Stop offset="48%" stopColor="#ffffff" stopOpacity="0.65" />
                      <Stop offset="52%" stopColor={tertiaryColor} stopOpacity="0.30" />
                      <Stop offset="70%" stopColor={tertiaryColor} stopOpacity="0.12" />
                      <Stop offset="100%" stopColor={tertiaryColor} stopOpacity="0" />
                    </SvgLinearGradient>
                  </Defs>
                  <Rect x="0" y="0" width="100%" height="100%" fill="url(#shimmerBeamHM)" />
                </Svg>
              </Animated.View>
            </View>
          )}

          <View style={styles.headerRow}>
            <View style={styles.greetingContainer}>
              {active === "profil" ? (
                <View style={[styles.profileHeroTag, { backgroundColor: `${tertiaryColor}15` }]}>
                  <Icon name="user-check" size={11} color={tertiaryColor} />
                  <Text style={[styles.profileHeroTagText, { color: tertiaryColor }]}>
                    Profil Petugas
                  </Text>
                </View>
              ) : (
                <Text style={styles.greet}>
                  {getGreetingTime()},
                </Text>
              )}
              <Text style={styles.name} numberOfLines={2}>
                {profile?.nama || "Health Monitor"}
              </Text>
              <View style={{ flexDirection: 'row', alignItems: 'center', gap: horizontalScale(6), flexWrap: 'wrap', marginTop: verticalScale(3) }}>
                {profile?.divisi ? (
                  <View style={[
                    styles.divisiBadge, 
                    {
                      backgroundColor: tertiaryColor + '14', 
                      borderColor: tertiaryColor + '38',
                      borderWidth: 1,
                      marginTop: 0,
                    }
                  ]}>
                    <View style={[styles.divisiDot, { backgroundColor: tertiaryColor }]} />
                    <Text style={[styles.divisiBadgeText, { color: tertiaryColor }]} numberOfLines={1}>
                      {profile.divisi}
                    </Text>
                  </View>
                ) : null}
                {active === "profil" && (
                  <View style={[
                    styles.divisiBadge,
                    {
                      backgroundColor: '#0d948815',
                      borderColor: '#0d948835',
                      borderWidth: 1,
                      marginTop: 0,
                    }
                  ]}>
                    <Icon name="shield" size={10} color="#0d9488" />
                    <Text style={[styles.divisiBadgeText, { color: '#0d9488' }]} numberOfLines={1}>
                      {profile?.level || 'Health Monitor'}
                    </Text>
                  </View>
                )}
              </View>
            </View>
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
                  backgroundColor: tertiaryColor + '12', 
                  borderColor: tertiaryColor + '40',
                  borderWidth: 1.5,
                  shadowColor: tertiaryColor,
                  shadowOpacity: 0.15,
                  shadowRadius: 6,
                  overflow: 'hidden' 
                },
                pressed && { opacity: 0.85, transform: [{ scale: 0.96 }] }
              ]}
            >
              {profile?.avatar ? (
                <Image source={{ uri: profile.avatar }} style={{ width: '100%', height: '100%' }} />
              ) : (
                <Icon name="user" size={20} color={tertiaryColor} />
              )}
              {active === "profil" && (
                <View style={[styles.avatarEditIconBadge, { backgroundColor: tertiaryColor }]}>
                  <Icon name="camera" size={9} color={Colors.white} />
                </View>
              )}
            </Pressable>
          </View>
        </Animated.View>
        <Animated.View style={{ opacity: fadeTab }}>
          {active === "dashboard" && (
            <View style={{ gap: verticalScale(12) }}>
              <Card>
                <View style={styles.cardHeader}>
                  <View style={styles.cardIconBg}>
                    <Icon name="activity" size={20} color="#ec4899" />
                  </View>
                  <View>
                    <Text style={styles.cardTitle}>Dashboard Health Monitor</Text>
                    <Text style={styles.cardSubtitle}>Ringkasan data kesehatan member</Text>
                  </View>
                </View>
              </Card>
              {loadingPeminjaman ? (
                <Card>
                  <View style={styles.centerRow}>
                    <ActivityIndicator color={Colors.primary} />
                    <Text style={styles.mutedText}>Memuat...</Text>
                  </View>
                </Card>
              ) : (
                <>
                  {/* STATS GRID - Layout Responsif 2x2 */}
                  <View style={styles.statsGrid}>
                    <View style={styles.statRow}>
                      <StatCard
                        icon="users"
                        label="Total Member"
                        value={dashboardData.total_member}
                        color="#ec4899"
                        bgColor="#fce7f3"
                      />
                      <StatCard
                        icon="clipboard"
                        label="Pinjaman Aktif"
                        value={dashboardData.pinjaman_aktif}
                        color="#3b82f6"
                        bgColor="#dbeafe"
                      />
                    </View>
                    <View style={styles.statRow}>
                      <StatCard
                        icon="alert-triangle"
                        label="Terlambat"
                        value={dashboardData.warning_count}
                        color="#ef4444"
                        bgColor="#fee2e2"
                      />
                      <StatCard
                        icon="clock"
                        label="Menunggu Verifikasi"
                        value={dashboardData.menunggu_verifikasi}
                        color="#f59e0b"
                        bgColor="#fef3c7"
                      />
                    </View>
                  </View>
                  {/* WARNING CARD - Layout Responsif */}
                  {dashboardData.warning_count > 0 && (
                    <Card style={styles.warningCard}>
                      <View style={styles.warningContent}>
                        <View style={styles.warningIconContainer}>
                          <Icon name="alert-triangle" size={20} color="#f59e0b" />
                        </View>
                        <View style={styles.warningTextContainer}>
                          <Text style={styles.warningTitle}>Perhatian!</Text>
                          <Text style={styles.warningText}>
                            Ada {dashboardData.warning_count} peminjaman pita yang sudah melewati estimasi waktu pengembalian.
                          </Text>
                        </View>
                      </View>
                    </Card>
                  )}
                  {/* VERIFICATION CARD - Layout Responsif */}
                  {dashboardData.menunggu_verifikasi > 0 && (
                    <Card style={[styles.warningCard, { backgroundColor: '#f0f9ff', borderColor: '#bfdbfe' }]}>
                      <View style={styles.warningContent}>
                        <View style={styles.warningIconContainer}>
                          <Icon name="clock" size={20} color="#3b82f6" />
                        </View>
                        <View style={styles.warningTextContainer}>
                          <Text style={[styles.warningTitle, { color: '#1e40af' }]}>Verifikasi Menunggu!</Text>
                          <Text style={[styles.warningText, { color: '#1e40af' }]}>
                            Ada {dashboardData.menunggu_verifikasi} peminjaman pita yang menunggu verifikasi.
                          </Text>
                        </View>
                      </View>
                      <Pressable
                        style={styles.checkNowButton}
                        onPress={handleCheckNow}
                      >
                        <Text style={styles.checkNowText}>Cek Sekarang</Text>
                      </Pressable>
                    </Card>
                  )}
                  {/* QUICK ACTIONS - Layout Responsif */}
                  <Card>
                    <Text style={styles.cardTitle}>Aksi Cepat</Text>
                    <View style={styles.quickActions}>
                      <QuickActionButton
                        icon="plus"
                        label="Pinjam Pita Baru"
                        color="#3b82f6"
                        onPress={openCreateForm}
                      />
                      <QuickActionButton
                        icon="list"
                        label="Lihat Semua Peminjaman"
                        color="#10b981"
                        onPress={() => setActive("peminjaman")}
                      />
                      <QuickActionButton
                        icon="alert-triangle"
                        label="Cek Warning"
                        color="#f59e0b"
                        onPress={() => setActive("warning")}
                      />
                    </View>
                  </Card>
                </>
              )}
            </View>
          )}
          {active === "peminjaman" && (
            <View style={{ gap: verticalScale(12) }}>
              <Card>
                <View style={styles.cardHeader}>
                  <View style={styles.cardIconBg}>
                    <Icon name="clipboard" size={20} color="#ec4899" />
                  </View>
                  <View style={{ flex: 1 }}>
                    <Text style={styles.cardTitle}>
                      {showNonActive ? 'Verifikasi Peminjaman' : 'Peminjaman Pita'}
                    </Text>
                    <Text style={styles.cardSubtitle}>
                      {showNonActive
                        ? 'Manajemen permintaan peminjaman yang menunggu verifikasi'
                        : 'Manajemen peminjaman pita member yang sudah diverifikasi'
                      }
                    </Text>
                  </View>
                  <Pressable
                    style={styles.addButton}
                    onPress={openCreateForm}
                  >
                    <Icon name="plus" size={16} color={Colors.white} />
                  </Pressable>
                </View>
              </Card>
              {/* Search Box dan Filter Non-Active */}
              <Card style={{ padding: moderateScale(12) }}>
                <View style={styles.searchContainer}>
                  <View style={styles.searchInputContainer}>
                    <Icon name="search" size={16} color={Colors.muted} />
                    <TextInput
                      value={searchQuery}
                      onChangeText={setSearchQuery}
                      placeholder="Cari berdasarkan Nama atau Nomor Induk..."
                      placeholderTextColor={Colors.muted}
                      style={styles.searchInput}
                    />
                    {searchQuery !== '' && (
                      <Pressable onPress={() => setSearchQuery('')}>
                        <Icon name="x" size={16} color={Colors.muted} />
                      </Pressable>
                    )}
                  </View>

                  <Pressable
                    style={[styles.nonActiveToggle, showNonActive && styles.nonActiveToggleActive]}
                    onPress={() => setShowNonActive(!showNonActive)}
                  >
                    <Icon
                      name={showNonActive ? "check-square" : "square"}
                      size={16}
                      color={showNonActive ? Colors.white : Colors.muted}
                    />
                    <Text style={[styles.nonActiveText, showNonActive && styles.nonActiveTextActive]}>
                      Belum Diverifikasi
                      {!showNonActive && dashboardData.menunggu_verifikasi > 0 && (
                        <Text style={{ color: '#ef4444' }}> ({dashboardData.menunggu_verifikasi})</Text>
                      )}
                    </Text>
                  </Pressable>
                </View>
              </Card>
              {/* Filter Status - hanya tampil untuk yang sudah diverifikasi */}
              {!showNonActive && (
                <Card style={{ padding: moderateScale(12) }}>
                  <ScrollView horizontal showsHorizontalScrollIndicator={false}>
                    <View style={styles.filterContainer}>
                      {/* Filter Buttons - Semua */}
                      <FilterButton
                        label="Semua"
                        active={filterStatus === "all"}
                        onPress={() => setFilterStatus("all")}
                      />
                      {/* Filter Buttons - Status Dipinjam */}
                      <FilterButton
                        label="Dipinjam"
                        active={filterStatus === "dipinjam"}
                        onPress={() => setFilterStatus("dipinjam")}
                      />
                      {/* Filter Buttons - Status Dikembalikan */}
                      <FilterButton
                        label="Dikembalikan"
                        active={filterStatus === "dikembalikan"}
                        onPress={() => setFilterStatus("dikembalikan")}
                      />
                      {/* Filter Buttons - Status Terlambat */}
                      <FilterButton
                        label="Terlambat"
                        active={filterStatus === "terlambat"}
                        onPress={() => setFilterStatus("terlambat")}
                      />
                      {/* Filter Buttons - Status Ditolak */}
                      <FilterButton
                        label="Ditolak"
                        active={filterStatus === "ditolak"}
                        onPress={() => setFilterStatus("ditolak")}
                      />
                    </View>
                  </ScrollView>
                </Card>
              )}
              {loadingPeminjaman ? (
                <Card>
                  <View style={styles.centerRow}>
                    <ActivityIndicator color={Colors.primary} />
                    <Text style={styles.mutedText}>Memuat...</Text>
                  </View>
                </Card>
              ) : filteredPeminjaman.length === 0 ? (
                <Card>
                  <View style={styles.emptyState}>
                    <Icon name="clipboard" size={40} color={Colors.muted} />
                    <Text style={[styles.mutedText, { textAlign: 'center', marginTop: 8 }]}>
                      {showNonActive ?
                        "Tidak ada peminjaman yang belum diverifikasi" :
                        "Tidak ada data peminjaman"
                      }
                    </Text>
                  </View>
                </Card>
              ) : (
                filteredPeminjaman.map((item) => (
                  <PeminjamanCard
                    key={item.id}
                    item={item}
                    formatDate={formatDate}
                    calculateLatedays={calculateLatedays}
                    onEdit={() => openEditForm(item)}
                    onReturn={() => {
                      setSelectedItem(item)
                      setModalType('return')
                      setModalVisible(true)
                    }}
                    onDelete={() => {
                      setSelectedItem(item)
                      setModalType('delete')
                      setModalVisible(true)
                    }}
                    onVerify={() => {
                      setSelectedItem(item)
                      setModalType('verify')
                      setModalVisible(true)
                    }}
                    onReject={() => {
                      setSelectedItem(item)
                      setRejectModalVisible(true)
                    }}
                    showNonActive={showNonActive}
                  />
                ))
              )}
            </View>
          )}
          {active === "warning" && (
            <View style={{ gap: verticalScale(12) }}>
              <Card>
                <View style={styles.cardHeader}>
                  <View style={[styles.cardIconBg, { backgroundColor: '#fef3c7' }]}>
                    <Icon name="alert-triangle" size={20} color="#f59e0b" />
                  </View>
                  <View>
                    <Text style={styles.cardTitle}>Peringatan</Text>
                    <Text style={styles.cardSubtitle}>Peminjaman yang terlambat</Text>
                  </View>
                </View>
              </Card>
              {loadingWarning ? (
                <Card>
                  <View style={styles.centerRow}>
                    <ActivityIndicator color={Colors.primary} />
                    <Text style={styles.mutedText}>Memuat...</Text>
                  </View>
                </Card>
              ) : warningList.length === 0 ? (
                <Card>
                  <View style={styles.emptyState}>
                    <Icon name="check-circle" size={40} color="#10b981" />
                    <Text style={[styles.mutedText, { textAlign: 'center', marginTop: 8, color: '#10b981' }]}>
                      Tidak ada peminjaman yang terlambat
                    </Text>
                  </View>
                </Card>
              ) : (
                warningList.map((item) => (
                  <PeminjamanCard
                    key={item.id}
                    item={item}
                    formatDate={formatDate}
                    calculateLatedays={calculateLatedays}
                    onEdit={() => openEditForm(item)}
                    onReturn={() => {
                      setSelectedItem(item)
                      setModalType('return')
                      setModalVisible(true)
                    }}
                    onDelete={() => {
                      setSelectedItem(item)
                      setModalType('delete')
                      setModalVisible(true)
                    }}
                    showNonActive={false}
                  />
                ))
              )}
            </View>
          )}
          {active === "profil" && (
            <View style={{ gap: verticalScale(14) }}>
              {/* Quick Stat Highlights */}
              {profile && (
                <View style={styles.profileStatGrid}>
                  {/* Card 1: Peran */}
                  <View style={styles.profileStatCard}>
                    <View style={[styles.profileStatIconBox, { backgroundColor: '#f0fdf4' }]}>
                      <Icon name="shield" size={15} color="#0d9488" />
                    </View>
                    <Text style={styles.profileStatLabel}>PERAN</Text>
                    <Text style={styles.profileStatValue} numberOfLines={1}>
                      {profile.level || 'Petugas'}
                    </Text>
                  </View>

                  {/* Card 2: Divisi */}
                  <View style={styles.profileStatCard}>
                    <View style={[styles.profileStatIconBox, { backgroundColor: `${tertiaryColor}15` }]}>
                      <Icon name="layers" size={15} color={tertiaryColor} />
                    </View>
                    <Text style={styles.profileStatLabel}>DIVISI</Text>
                    <Text style={[styles.profileStatValue, { color: tertiaryColor }]} numberOfLines={1}>
                      {profile.divisi || '-'}
                    </Text>
                  </View>

                  {/* Card 3: Nomor Induk */}
                  <View style={styles.profileStatCard}>
                    <View style={[styles.profileStatIconBox, { backgroundColor: '#eff6ff' }]}>
                      <Icon name="credit-card" size={15} color="#2563eb" />
                    </View>
                    <Text style={styles.profileStatLabel}>NO. INDUK</Text>
                    <Text style={styles.profileStatValue} numberOfLines={1}>
                      {profile.nomor_induk || '-'}
                    </Text>
                  </View>
                </View>
              )}

              {/* Card 1: Informasi Pribadi */}
              <Card>
                <View style={{ flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', marginBottom: verticalScale(8) }}>
                  <View style={{ flex: 1, marginRight: horizontalScale(8) }}>
                    <Text style={styles.cardTitle}>Informasi Pribadi</Text>
                    <Text style={{ fontSize: moderateScale(11), color: Colors.muted, marginTop: verticalScale(1) }}>
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
                        backgroundColor: `${tertiaryColor}14`,
                        borderWidth: 1,
                        borderColor: `${tertiaryColor}32`,
                      },
                      pressed && { opacity: 0.8, transform: [{ scale: 0.98 }] },
                    ]}
                  >
                    <Icon name="edit-3" size={12} color={tertiaryColor} />
                    <Text style={{ fontSize: moderateScale(11.5), fontWeight: '700', color: tertiaryColor }}>Edit Profil</Text>
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
                  <Text style={styles.cardTitle}>Akun & Keamanan</Text>
                  <Text style={{ fontSize: moderateScale(11), color: Colors.muted, marginTop: verticalScale(1) }}>
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
                    onPress={() => setPasswordModalVisible(true)}
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
      {/* Modal for Change Password */}
      <Modal
        animationType="slide"
        transparent={true}
        visible={passwordModalVisible}
        onRequestClose={() => setPasswordModalVisible(false)}
      >
        <View style={styles.modalOverlayGP}>
          <View style={styles.modalContentGP}>
            <Text style={styles.cardTitle}>Ganti Password</Text>
            <Text style={styles.label}>Kata Sandi Saat Ini</Text>
            <TextInput
              value={currentPassword}
              onChangeText={setCurrentPassword}
              placeholder="Masukkan kata sandi saat ini"
              placeholderTextColor={Colors.muted}
              style={styles.input}
              secureTextEntry
            />
            <Text style={styles.label}>Kata Sandi Baru</Text>
            <TextInput
              value={newPassword}
              onChangeText={setNewPassword}
              placeholder="Masukkan kata sandi baru"
              placeholderTextColor={Colors.muted}
              style={styles.input}
              secureTextEntry
            />
            <Text style={styles.label}>Ketik Ulang Kata Sandi Baru</Text>
            <TextInput
              value={retypePassword}
              onChangeText={setRetypePassword}
              placeholder="Ketik ulang kata sandi baru"
              placeholderTextColor={Colors.muted}
              style={styles.input}
              secureTextEntry
            />
            {passwordError && <Text style={[styles.mutedText, { color: "#dc2626", marginTop: verticalScale(8) }]}>{passwordError}</Text>}
            <View style={styles.modalActionsGP}>
              <Pressable onPress={() => setPasswordModalVisible(false)} style={[styles.modalButtonGP, { backgroundColor: Colors.muted }]}>
                <Text style={styles.modalButtonTextGP}>Batal</Text>
              </Pressable>
              <Pressable
                onPress={handleChangePassword}
                disabled={savingPassword || !currentPassword || !newPassword || !retypePassword}
                style={({ pressed }) => [styles.modalButtonGP, { backgroundColor: Colors.primary, opacity: savingPassword || !currentPassword || !newPassword || !retypePassword ? 0.6 : pressed ? 0.9 : 1 }]}
              >
                {savingPassword ? <ActivityIndicator color={Colors.white} /> : <Text style={styles.modalButtonTextGP}>Simpan</Text>}
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
        themeColor={tertiaryColor}
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

      <AnimatedBottomTabs items={tabs} activeKey={active} onChange={setActive} themeColor={tertiaryColor} />
      <ConfirmModal
        visible={modalVisible}
        type={modalType}
        item={selectedItem}
        loading={actionLoading}
        onClose={() => {
          setModalVisible(false)
          setSelectedItem(null)
        }}
        onConfirm={
          modalType === 'return' ? handleReturn :
            modalType === 'delete' ? handleDelete :
              handleVerify
        }
      />
      <RejectModal
        visible={rejectModalVisible}
        item={selectedItem}
        loading={rejectLoading}
        reason={rejectReason}
        onReasonChange={setRejectReason}
        onClose={() => {
          setRejectModalVisible(false)
          setSelectedItem(null)
          setRejectReason("")
        }}
        onConfirm={handleReject}
      />
      <FormModal
        visible={showFormModal}
        mode={formMode}
        memberList={memberList}
        selectedMember={selectedMember}
        formData={formData}
        loadingMember={loadingMember}
        saving={savingForm}
        onClose={() => {
          setShowFormModal(false)
          setSelectedItem(null)
          setSelectedMember(null)
        }}
        onSelectMember={setSelectedMember}
        onChangeText={(field: keyof typeof formData, value: string) => setFormData(prev => ({ ...prev, [field]: value }))}
        onSave={handleSaveForm}
      />
      {/* Loading Popup */}
      <Modal
        visible={initialLoading}
        animationType="fade"
        transparent={true}
      >
        <View style={styles.loadingOverlay}>
          <View style={styles.loadingContainer}>
            <ActivityIndicator size="large" color={Colors.primary} />
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
            <ActivityIndicator size="large" color={Colors.primary} />
            <Text style={styles.loadingText}>Sedang keluar...</Text>
          </View>
        </View>
      </Modal>
    </SafeAreaView>
  )
}
// COMPONENT DEFINITIONS dengan layout responsif
const Card = ({ children, style }: { children: React.ReactNode; style?: any }) => (
  <View style={[styles.card, style]}>{children}</View>
)
const StatCard = ({ icon, label, value, color, bgColor }: any) => (
  <View style={styles.statCard}>
    <View style={[styles.statIcon, { backgroundColor: bgColor }]}>
      <Icon name={icon} size={20} color={color} />
    </View>
    <View style={styles.statTextContainer}>
      <Text style={styles.statLabel}>{label}</Text>
      <Text style={styles.statValue}>{value}</Text>
    </View>
  </View>
)
const QuickActionButton = ({ icon, label, color, onPress }: any) => (
  <Pressable
    style={[styles.quickActionBtn, { backgroundColor: `${color}15` }]}
    onPress={onPress}
  >
    <Icon name={icon} size={18} color={color} />
    <Text style={[styles.quickActionText, { color }]} numberOfLines={1}>{label}</Text>
  </Pressable>
)
const FilterButton = ({ label, active, onPress }: any) => (
  <Pressable
    style={[styles.filterBtn, active && styles.filterBtnActive]}
    onPress={onPress}
  >
    <Text style={[styles.filterText, active && styles.filterTextActive]}>{label}</Text>
  </Pressable>
)
const PeminjamanCard = ({ item, formatDate, calculateLatedays, onEdit, onReturn, onDelete, onVerify, onReject, showNonActive }: any) => {
  const statusConfig = {
    menunggu: { bg: '#fef3c7', color: '#f59e0b', text: 'Menunggu' },
    dipinjam: { bg: '#dbeafe', color: '#3b82f6', text: 'Dipinjam' },
    dikembalikan: { bg: '#dcfce7', color: '#10b981', text: 'Dikembalikan' },
    terlambat: { bg: '#fee2e2', color: '#ef4444', text: 'Terlambat' },
    ditolak: { bg: '#fee2e2', color: '#ef4444', text: 'Ditolak' },
  }

  const statusKey = (item?.status || 'menunggu').toLowerCase() as keyof typeof statusConfig;
  const config = statusConfig[statusKey] || statusConfig.menunggu;
  const lateDays = item.status === 'terlambat' ? calculateLatedays(item.estimasi_selesai_haid) : 0
  return (
    <Card>
      <View style={styles.peminjamanHeader}>
        <View style={styles.userAvatar}>
          <Icon name="user" size={18} color="#ec4899" />
        </View>
        <View style={{ flex: 1 }}>
          <Text style={styles.userName}>{item.user.nama}</Text>
          <Text style={styles.userNoInduk}>Nomor Induk: {item.user.nomor_induk}</Text>
        </View>
        <View style={{ alignItems: 'flex-end', gap: 4 }}>
          <View style={[styles.statusBadge, { backgroundColor: config.bg }]}>
            <Text style={[styles.statusText, { color: config.color }]}>{config.text}</Text>
          </View>
          {showNonActive && item.verified === false && (
            <View style={styles.unverifiedBadge}>
              <Icon name="clock" size={10} color={Colors.white} />
              <Text style={styles.unverifiedText}>Menunggu Verifikasi</Text>
            </View>
          )}
        </View>
      </View>
      <View style={styles.peminjamanDetails}>
        <DetailRow icon="calendar" label="Tanggal Pinjam" value={formatDate(item.tanggal_pinjam)} />
        <DetailRow icon="package" label="Jumlah Pita" value={`${item.jumlah_pita} pita`} />
        <DetailRow icon="clock" label="Estimasi Selesai" value={formatDate(item.estimasi_selesai_haid)} />
        {item.tanggal_kembali && (
          <DetailRow icon="check" label="Tanggal Kembali" value={formatDate(item.tanggal_kembali)} />
        )}
        {lateDays > 0 && (
          <Text style={styles.lateText}>{lateDays} hari terlambat</Text>
        )}
        {item.keterangan && (
          <View style={styles.keteranganBox}>
            <Icon name="info" size={12} color={Colors.muted} />
            <Text style={styles.keteranganText}>{item.keterangan}</Text>
          </View>
        )}
      </View>
      <View style={styles.actionButtons}>
        {/* Button untuk yang belum diverifikasi */}
        {showNonActive && item.verified === false && (
          <>
            <Pressable style={[styles.actionBtn, styles.verifyBtn]} onPress={onVerify}>
              <Icon name="check" size={16} color="#10b981" />
              <Text style={styles.verifyBtnText}>Setujui</Text>
            </Pressable>
            <Pressable style={[styles.actionBtn, styles.rejectBtn]} onPress={onReject}>
              <Icon name="x" size={16} color="#ef4444" />
              <Text style={styles.rejectBtnText}>Tolak</Text>
            </Pressable>
          </>
        )}

        {/* Button untuk yang sudah diverifikasi dan bukan dikembalikan/ditolak */}
        {!showNonActive && item.verified === true && item.status !== 'dikembalikan' && item.status !== 'ditolak' && (
          <>
            {/* Edit button - hanya untuk status dipinjam (bukan terlambat) */}
            {item.status !== 'terlambat' && (
              <Pressable style={[styles.actionBtn, styles.editBtn]} onPress={onEdit}>
                <Icon name="edit-2" size={16} color="#3b82f6" />
                <Text style={styles.editBtnText}>Edit</Text>
              </Pressable>
            )}
            {/* Kembalikan button - untuk semua status kecuali dikembalikan dan ditolak */}
            <Pressable style={[styles.actionBtn, styles.returnBtn]} onPress={onReturn}>
              <Icon name="check-circle" size={16} color="#10b981" />
              <Text style={styles.returnBtnText}>Kembalikan</Text>
            </Pressable>

            {/* Delete button - hanya untuk status dipinjam (bukan terlambat) */}
            {item.status !== 'terlambat' && (
              <Pressable style={[styles.actionBtn, styles.deleteBtn]} onPress={onDelete}>
                <Icon name="trash-2" size={16} color="#ef4444" />
                <Text style={styles.deleteBtnText}>Hapus</Text>
              </Pressable>
            )}
          </>
        )}
        {/* Button Hapus untuk status ditolak */}
        {!showNonActive && item.status === 'ditolak' && (
          <Pressable style={[styles.actionBtn, styles.deleteBtn]} onPress={onDelete}>
            <Icon name="trash-2" size={16} color="#ef4444" />
            <Text style={styles.deleteBtnText}>Hapus</Text>
          </Pressable>
        )}
      </View>
    </Card>
  )
}
const DetailRow = ({ icon, label, value }: any) => (
  <View style={styles.detailRow}>
    <Icon name={icon} size={14} color={Colors.muted} />
    <Text style={styles.detailLabel}>{label}:</Text>
    <Text style={styles.detailValue}>{value}</Text>
  </View>
)
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
  const displayVal = value && value.trim() !== '' && value !== '-' ? value : null;

  return (
    <View style={[styles.profileRowContainer, !isLast && styles.profileRowDivider]}>
      <View style={styles.profileRowLeft}>
        <View style={[styles.profileRowIconBox, { backgroundColor: iconBg }]}>
          <Icon name={icon as any} size={16} color={iconColor} />
        </View>
        <Text style={styles.profileRowLabel} numberOfLines={1}>
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
              styles.profileRowValue,
              !displayVal && styles.profileRowValueEmpty,
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
}: AccountActionItemProps) => (
  <Pressable
    onPress={onPress}
    style={({ pressed }) => [
      styles.actionItem,
      !isLast && styles.actionItemDivider,
      pressed && styles.actionItemPressed,
    ]}
  >
    <View style={[styles.actionIconBox, { backgroundColor: iconBg }]}>
      <Icon name={icon as any} size={18} color={iconColor} />
    </View>
    <View style={styles.actionTextBox}>
      <Text style={[styles.actionTitle, isDanger && { color: '#dc2626' }]} numberOfLines={1}>
        {title}
      </Text>
      <Text style={[styles.actionSubtitle, isDanger && { color: '#ef4444' }]} numberOfLines={1}>
        {subtitle}
      </Text>
    </View>
    <Icon
      name="chevron-right"
      size={18}
      color={isDanger ? '#f87171' : Colors.muted}
      style={{ opacity: 0.6 }}
    />
  </Pressable>
);
const ConfirmModal = ({ visible, type, item, loading, onClose, onConfirm }: any) => {
  const isReturn = type === 'return'
  const isDelete = type === 'delete'
  const isVerify = type === 'verify'
  const getModalConfig = () => {
    if (isVerify) {
      return {
        icon: 'check-circle',
        iconColor: '#10b981',
        iconBg: '#dcfce7',
        title: 'Konfirmasi Verifikasi',
        message: `Apakah Anda yakin ingin memverifikasi peminjaman pita A.N ${item?.user.nama}?`,
        confirmText: 'Ya, Verifikasi',
        confirmColor: '#10b981'
      }
    }
    if (isReturn) {
      return {
        icon: 'check-circle',
        iconColor: '#10b981',
        iconBg: '#dcfce7',
        title: 'Konfirmasi Pengembalian',
        message: `Apakah pita dari ${item?.user.nama} sudah dikembalikan?`,
        confirmText: 'Ya, Kembalikan',
        confirmColor: '#10b981'
      }
    }
    return {
      icon: 'alert-triangle',
      iconColor: '#ef4444',
      iconBg: '#fee2e2',
      title: 'Konfirmasi Hapus',
      message: `Apakah Anda yakin ingin menghapus data peminjaman pita A.N ${item?.user.nama}?`,
      confirmText: 'Ya, Hapus',
      confirmColor: '#ef4444'
    }
  }
  const config = getModalConfig()
  return (
    <Modal visible={visible} transparent animationType="fade">
      <Pressable style={styles.modalOverlay} onPress={loading ? undefined : onClose}>
        <View style={styles.modalContent} onStartShouldSetResponder={() => true}>
          <View style={styles.modalHeader}>
            <View style={[styles.modalIcon, { backgroundColor: config.iconBg }]}>
              <Icon name={config.icon} size={24} color={config.iconColor} />
            </View>
            <Text style={styles.modalTitle}>{config.title}</Text>
            <Text style={styles.modalMessage}>{config.message}</Text>
            {isDelete && (
              <View style={styles.warningBox}>
                <Icon name="info" size={14} color="#f59e0b" />
                <Text style={styles.warningBoxText}>
                  Data yang sudah dihapus tidak dapat dikembalikan
                </Text>
              </View>
            )}
          </View>
          <View style={styles.modalActions}>
            <Pressable
              style={styles.modalBtnCancel}
              onPress={onClose}
              disabled={loading}
            >
              <Text style={styles.modalBtnCancelText}>Batal</Text>
            </Pressable>
            <Pressable
              style={[styles.modalBtnConfirm, {
                backgroundColor: config.confirmColor,
                opacity: loading ? 0.6 : 1
              }]}
              onPress={onConfirm}
              disabled={loading}
            >
              {loading ? (
                <ActivityIndicator color={Colors.white} size="small" />
              ) : (
                <Text style={styles.modalBtnConfirmText}>{config.confirmText}</Text>
              )}
            </Pressable>
          </View>
        </View>
      </Pressable>
    </Modal>
  )
}
const RejectModal = ({ visible, item, loading, reason, onReasonChange, onClose, onConfirm }: any) => {
  return (
    <Modal visible={visible} transparent animationType="fade">
      <Pressable style={styles.modalOverlay} onPress={loading ? undefined : onClose}>
        <View style={styles.modalContent} onStartShouldSetResponder={() => true}>
          <View style={styles.modalHeader}>
            <View style={[styles.modalIcon, { backgroundColor: '#fee2e2' }]}>
              <Icon name="x-circle" size={24} color="#ef4444" />
            </View>
            <Text style={styles.modalTitle}>Tolak Peminjaman</Text>
            <Text style={styles.modalMessage}>
              Tolak peminjaman pita dari {item?.user.nama}?
            </Text>

            <View style={styles.formGroup}>
              <Text style={styles.formLabel}>Alasan Penolakan (Opsional)</Text>
              <TextInput
                value={reason}
                onChangeText={onReasonChange}
                placeholder="Berikan alasan penolakan..."
                placeholderTextColor={Colors.muted}
                style={styles.formInput}
                multiline
                numberOfLines={3}
                editable={!loading}
              />
            </View>
          </View>
          <View style={styles.modalActions}>
            <Pressable
              style={styles.modalBtnCancel}
              onPress={onClose}
              disabled={loading}
            >
              <Text style={styles.modalBtnCancelText}>Batal</Text>
            </Pressable>
            <Pressable
              style={[styles.modalBtnConfirm, {
                backgroundColor: '#ef4444',
                opacity: loading ? 0.6 : 1
              }]}
              onPress={onConfirm}
              disabled={loading}
            >
              {loading ? (
                <ActivityIndicator color={Colors.white} size="small" />
              ) : (
                <Text style={styles.modalBtnConfirmText}>Tolak</Text>
              )}
            </Pressable>
          </View>
        </View>
      </Pressable>
    </Modal>
  )
}
const FormModal = ({ visible, mode, memberList, selectedMember, formData, loadingMember, saving, onClose, onSelectMember, onChangeText, onSave }: any) => {
  const { width, height } = useWindowDimensions();
  const isSmallScreen = width < 375;
  return (
    <Modal visible={visible} transparent animationType="slide">
      <View style={styles.formModalOverlay}>
        <View style={[styles.formModalContent, { width: '100%', maxHeight: height * 0.9 }]}>
          <View style={[styles.formModalHeader, { padding: moderateScale(16) }]}>
            <Text style={[styles.formModalTitle, { fontSize: moderateScale(18) }]}>
              {mode === 'create' ? 'Pinjam Pita Baru' : 'Edit Peminjaman Pita'}
            </Text>
            <Text style={[styles.formModalSubtitle, { fontSize: moderateScale(12) }]}>
              Catat peminjaman pita untuk member yang sedang haid
            </Text>
            <Pressable style={[styles.closeButton, { width: moderateScale(28), height: moderateScale(28), borderRadius: moderateScale(14) }]} onPress={onClose} disabled={saving}>
              <Icon name="x" size={moderateScale(20)} color={Colors.text} />
            </Pressable>
          </View>
          <ScrollView
            style={[styles.formBody, { padding: moderateScale(16), maxHeight: height * 0.6 }]}
            showsVerticalScrollIndicator={false}
            keyboardShouldPersistTaps="handled"
          >
            <View style={styles.formGroup}>
              <Text style={[styles.formLabel, { fontSize: moderateScale(13) }]}>Pilih Member <Text style={styles.required}>*</Text></Text>
              {loadingMember ? (
                <View style={[styles.formLoading, { padding: moderateScale(12), borderRadius: moderateScale(10) }]}>
                  <ActivityIndicator color={Colors.primary} size="small" />
                  <Text style={[styles.mutedText, { fontSize: moderateScale(13) }]}>Memuat data member...</Text>
                </View>
              ) : mode === 'edit' && selectedMember ? (
                <View style={[styles.selectedMemberCard, { padding: moderateScale(10), borderRadius: moderateScale(10) }]}>
                  <View style={styles.userAvatar}>
                    <Icon name="user" size={moderateScale(16)} color="#ec4899" />
                  </View>
                  <View style={{ flex: 1 }}>
                    <Text style={[styles.userName, { fontSize: moderateScale(13) }]}>{selectedMember.nama}</Text>
                    <Text style={[styles.userNoInduk, { fontSize: moderateScale(11) }]}>Nomor Induk: {selectedMember.nomor_induk} • {selectedMember.divisi || '-'}</Text>
                  </View>
                  <View style={[styles.lockedBadge, { padding: moderateScale(6), borderRadius: moderateScale(6) }]}>
                    <Icon name="lock" size={moderateScale(12)} color={Colors.muted} />
                  </View>
                </View>
              ) : (
                <ScrollView style={[styles.picker, { maxHeight: verticalScale(200), borderRadius: moderateScale(10) }]} nestedScrollEnabled>
                  {memberList.length === 0 ? (
                    <View style={[styles.emptyPicker, { padding: moderateScale(24) }]}>
                      <Icon name="users" size={moderateScale(28)} color={Colors.muted} />
                      <Text style={[styles.emptyPickerText, { fontSize: moderateScale(14) }]}>Tidak ada member yang sedang haid</Text>
                      <Text style={[styles.emptyPickerSubtext, { fontSize: moderateScale(12) }]}>
                        Pastikan data haid member sudah diinput
                      </Text>
                    </View>
                  ) : (
                    memberList.map((member: Member) => (
                      <Pressable
                        key={member.id}
                        style={[
                          styles.pickerItem,
                          { padding: moderateScale(12), borderBottomWidth: StyleSheet.hairlineWidth },
                          selectedMember?.id === member.id && styles.pickerItemActive
                        ]}
                        onPress={() => onSelectMember(member)}
                      >
                        <View style={styles.pickerItemLeft}>
                          <Icon
                            name={selectedMember?.id === member.id ? "check-circle" : "circle"}
                            size={moderateScale(16)}
                            color={selectedMember?.id === member.id ? Colors.primary : Colors.muted}
                          />
                          <View style={{ marginLeft: horizontalScale(10), flex: 1 }}>
                            <Text style={[styles.pickerItemName, { fontSize: moderateScale(14) }, selectedMember?.id === member.id && styles.pickerItemNameActive]}>
                              {member.nama}
                            </Text>
                            <Text style={[styles.pickerItemNoInduk, { fontSize: moderateScale(12) }]}>
                              Nomor Induk: {member.nomor_induk} • {member.divisi_name || '-'}
                            </Text>
                            {member.dataHaid && (
                              <Text style={[styles.haidInfo, { fontSize: moderateScale(11) }]}>
                                <Icon name="calendar" size={moderateScale(8)} color="#10b981" /> Mulai: {new Date(member.dataHaid.tanggal_mulai).toLocaleDateString('id-ID')}
                              </Text>
                            )}
                          </View>
                        </View>
                      </Pressable>
                    ))
                  )}
                </ScrollView>
              )}
            </View>
            <View style={styles.formGroup}>
              <Text style={[styles.formLabel, { fontSize: moderateScale(13) }]}>Keterangan</Text>
              <TextInput
                value={formData.keterangan}
                onChangeText={(val) => onChangeText('keterangan', val)}
                placeholder="Keterangan tambahan (opsional)"
                placeholderTextColor={Colors.muted}
                style={[styles.formInput, styles.formTextArea, { paddingVertical: verticalScale(10), fontSize: moderateScale(14), height: verticalScale(70) }]}
                multiline
                numberOfLines={3}
                editable={!saving}
              />
            </View>
            <View style={[styles.infoBox, { padding: moderateScale(12), borderRadius: moderateScale(10), borderLeftWidth: moderateScale(2) }]}>
              <Icon name="info" size={moderateScale(14)} color="#f59e0b" />
              <View style={{ flex: 1, marginLeft: horizontalScale(8) }}>
                <Text style={[styles.infoTitle, { fontSize: moderateScale(12) }]}>Catatan Penting</Text>
                <Text style={[styles.infoText, { fontSize: moderateScale(10), lineHeight: moderateScale(14) }]}>• Hanya member yang sedang dalam masa haid yang dapat meminjam pita</Text>
                <Text style={[styles.infoText, { fontSize: moderateScale(10), lineHeight: moderateScale(14) }]}>• Tanggal pinjam otomatis diset hari ini</Text>
                <Text style={[styles.infoText, { fontSize: moderateScale(10), lineHeight: moderateScale(14) }]}>• Estimasi selesai: tanggal mulai haid + 7 hari</Text>
                <Text style={[styles.infoText, { fontSize: moderateScale(10), lineHeight: moderateScale(14) }]}>• Member akan mendapat warning jika belum mengembalikan setelah estimasi</Text>
              </View>
            </View>
          </ScrollView>
          <View style={[styles.formActions, { padding: moderateScale(16), gap: horizontalScale(8) }]}>
            <Pressable
              style={[styles.formBtnCancel, { paddingVertical: verticalScale(10), borderRadius: moderateScale(8) }]}
              onPress={onClose}
              disabled={saving}
            >
              <Text style={[styles.formBtnCancelText, { fontSize: moderateScale(14) }]}>Batal</Text>
            </Pressable>
            <Pressable
              style={[styles.formBtnSave, {
                paddingVertical: verticalScale(10),
                borderRadius: moderateScale(8),
                opacity: saving || !selectedMember ? 0.6 : 1
              }]}
              onPress={onSave}
              disabled={saving || !selectedMember}
            >
              {saving ? (
                <ActivityIndicator color={Colors.white} size="small" />
              ) : (
                <Text style={[styles.formBtnSaveText, { fontSize: moderateScale(14) }]}>
                  {mode === 'create' ? 'Simpan' : 'Update'}
                </Text>
              )}
            </Pressable>
          </View>
        </View>
      </View>
    </Modal>
  );
};
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
  label: { color: Colors.muted, marginBottom: verticalScale(6), marginTop: verticalScale(2), fontSize: moderateScale(14) },
  input: { backgroundColor: Colors.white, borderWidth: 1, borderColor: Colors.border, borderRadius: moderateScale(12), paddingHorizontal: horizontalScale(12), paddingVertical: verticalScale(12), color: Colors.text, fontSize: moderateScale(16) },
  modalOverlayGP: { flex: 1, backgroundColor: 'rgba(0, 0, 0, 0.5)', justifyContent: 'center', alignItems: 'center' },
  modalContentGP: { backgroundColor: Colors.white, borderRadius: moderateScale(16), padding: moderateScale(20), width: horizontalScale(300), gap: verticalScale(12) },
  modalActionsGP: { flexDirection: 'row', justifyContent: 'space-between', marginTop: verticalScale(16) },
  modalButtonGP: { flex: 1, paddingVertical: verticalScale(12), borderRadius: moderateScale(12), alignItems: 'center', marginHorizontal: horizontalScale(4) },
  modalButtonTextGP: { color: Colors.white, fontWeight: '700', fontSize: moderateScale(16) },
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
    flexWrap: 'wrap'
  },
  profileBadge: {
    backgroundColor: Colors.primary,
    width: moderateScale(40),
    height: moderateScale(40),
    borderRadius: moderateScale(20),
    alignItems: 'center',
    justifyContent: 'center',
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
    gap: verticalScale(12),
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: moderateScale(12)
  },
  cardIconBg: {
    width: moderateScale(40),
    height: moderateScale(40),
    borderRadius: moderateScale(10),
    backgroundColor: '#fce7f3',
    alignItems: 'center',
    justifyContent: 'center',
  },
  cardTitle: {
    color: Colors.text,
    fontSize: moderateScale(18),
    fontWeight: "700"
  },
  cardSubtitle: {
    color: Colors.muted,
    fontSize: moderateScale(12),
    marginTop: verticalScale(2)
  },
  // STATS GRID - Layout Responsif 2x2
  statsGrid: {
    gap: verticalScale(12),
  },
  statRow: {
    flexDirection: 'row',
    gap: horizontalScale(8),
  },
  statCard: {
    flex: 1,
    backgroundColor: Colors.white,
    borderRadius: moderateScale(12),
    padding: moderateScale(16),
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: Colors.border,
    minHeight: verticalScale(80),
  },
  statIcon: {
    width: moderateScale(40),
    height: moderateScale(40),
    borderRadius: moderateScale(10),
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: moderateScale(12),
  },
  statTextContainer: {
    flex: 1,
  },
  statLabel: {
    color: Colors.muted,
    fontSize: moderateScale(12),
    marginBottom: verticalScale(4)
  },
  statValue: {
    color: Colors.text,
    fontSize: moderateScale(20),
    fontWeight: '700'
  },
  // WARNING CARD - Layout Responsif
  warningCard: {
    backgroundColor: '#fef3c7',
    borderColor: '#fcd34d'
  },
  warningContent: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    flex: 1,
  },
  warningIconContainer: {
    marginRight: moderateScale(12),
    marginTop: moderateScale(2),
  },
  warningTextContainer: {
    flex: 1,
  },
  warningTitle: {
    color: '#92400e',
    fontSize: moderateScale(14),
    fontWeight: '700',
    marginBottom: 4
  },
  warningText: {
    color: '#92400e',
    fontSize: moderateScale(13),
    lineHeight: moderateScale(18),
    flexWrap: 'wrap',
  },
  // QUICK ACTIONS - Layout Responsif
  quickActions: {
    gap: verticalScale(8)
  },
  quickActionBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: moderateScale(12),
    borderRadius: moderateScale(10),
    gap: horizontalScale(8),
    minHeight: verticalScale(50),
  },
  quickActionText: {
    fontSize: moderateScale(14),
    fontWeight: '600',
    flex: 1,
    flexWrap: 'wrap',
  },
  addButton: {
    backgroundColor: '#ec4899',
    width: moderateScale(32),
    height: moderateScale(32),
    borderRadius: moderateScale(8),
    alignItems: 'center',
    justifyContent: 'center',
  },
  filterContainer: {
    flexDirection: 'row',
    gap: horizontalScale(8)
  },
  filterBtn: {
    paddingHorizontal: horizontalScale(16),
    paddingVertical: verticalScale(8),
    borderRadius: moderateScale(8),
    backgroundColor: '#f3f4f6',
  },
  filterBtnActive: {
    backgroundColor: '#ec4899'
  },
  filterText: {
    color: '#6b7280',
    fontSize: moderateScale(14),
    fontWeight: '500'
  },
  filterTextActive: {
    color: Colors.white
  },

  // Search and Filter Styles
  searchContainer: {
    gap: verticalScale(12),
  },
  searchInputContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: Colors.bg,
    borderRadius: moderateScale(12),
    paddingHorizontal: horizontalScale(12),
    paddingVertical: verticalScale(10),
    gap: horizontalScale(8),
  },
  searchInput: {
    flex: 1,
    color: Colors.text,
    fontSize: moderateScale(14),
  },
  nonActiveToggle: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: horizontalScale(8),
    padding: moderateScale(10),
    borderRadius: moderateScale(8),
    borderWidth: 1,
    borderColor: Colors.border,
    backgroundColor: Colors.white,
    alignSelf: 'flex-start',
  },
  nonActiveToggleActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  nonActiveText: {
    color: Colors.muted,
    fontSize: moderateScale(12),
    fontWeight: '500',
  },
  nonActiveTextActive: {
    color: Colors.white,
  },
  peminjamanHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: horizontalScale(12)
  },
  userAvatar: {
    width: moderateScale(40),
    height: moderateScale(40),
    borderRadius: moderateScale(20),
    backgroundColor: '#fce7f3',
    alignItems: 'center',
    justifyContent: 'center',
  },
  userName: {
    color: Colors.text,
    fontSize: moderateScale(14),
    fontWeight: '700'
  },
  userNoInduk: {
    color: Colors.muted,
    fontSize: moderateScale(12),
    marginTop: 2
  },
  statusBadge: {
    paddingHorizontal: horizontalScale(8),
    paddingVertical: verticalScale(4),
    borderRadius: moderateScale(6),
  },
  statusText: {
    fontSize: moderateScale(11),
    fontWeight: '600'
  },

  // Unverified Badge
  unverifiedBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: horizontalScale(4),
    backgroundColor: '#f59e0b',
    paddingHorizontal: horizontalScale(6),
    paddingVertical: verticalScale(2),
    borderRadius: moderateScale(4),
  },
  unverifiedText: {
    color: Colors.white,
    fontSize: moderateScale(10),
    fontWeight: '500',
  },
  peminjamanDetails: {
    gap: verticalScale(6)
  },
  detailRow: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: horizontalScale(6)
  },
  detailLabel: {
    color: Colors.muted,
    fontSize: moderateScale(12)
  },
  detailValue: {
    color: Colors.text,
    fontSize: moderateScale(12),
    fontWeight: '600'
  },
  lateText: {
    color: '#ef4444',
    fontSize: moderateScale(11),
    marginTop: 4,
    fontStyle: 'italic'
  },
  keteranganBox: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    gap: horizontalScale(6),
    backgroundColor: Colors.bg,
    padding: moderateScale(8),
    borderRadius: moderateScale(6),
    marginTop: verticalScale(4),
  },
  keteranganText: {
    flex: 1,
    color: Colors.muted,
    fontSize: moderateScale(11),
    lineHeight: moderateScale(16),
  },
  actionButtons: {
    flexDirection: 'row',
    gap: horizontalScale(8),
    marginTop: verticalScale(8)
  },
  actionBtn: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: horizontalScale(6),
    paddingVertical: verticalScale(10),
    borderRadius: moderateScale(8),
  },
  editBtn: {
    backgroundColor: '#dbeafe'
  },
  returnBtn: {
    backgroundColor: '#dcfce7'
  },
  deleteBtn: {
    backgroundColor: '#fee2e2'
  },
  verifyBtn: {
    backgroundColor: '#dcfce7'
  },
  rejectBtn: {
    backgroundColor: '#fee2e2'
  },
  editBtnText: {
    color: '#3b82f6',
    fontSize: moderateScale(13),
    fontWeight: '600'
  },
  returnBtnText: {
    color: '#10b981',
    fontSize: moderateScale(13),
    fontWeight: '600'
  },
  deleteBtnText: {
    color: '#ef4444',
    fontSize: moderateScale(13),
    fontWeight: '600'
  },
  verifyBtnText: {
    color: '#10b981',
    fontSize: moderateScale(13),
    fontWeight: '600'
  },
  rejectBtnText: {
    color: '#ef4444',
    fontSize: moderateScale(13),
    fontWeight: '600'
  },
  // Profile Styles
  profileHeaderCard: {
    flexDirection: 'row',
    alignItems: 'center',
    padding: moderateScale(20),
  },
  profileAvatar: {
    backgroundColor: Colors.primary,
    width: moderateScale(60),
    height: moderateScale(60),
    borderRadius: moderateScale(30),
    alignItems: 'center',
    justifyContent: 'center',
    marginRight: moderateScale(16),
  },
  profileInfo: {
    flex: 1
  },
  profileName: {
    color: Colors.text,
    fontSize: moderateScale(20),
    fontWeight: '700',
    marginBottom: verticalScale(4),
  },
  profileRole: {
    color: Colors.muted,
    fontSize: moderateScale(14),
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
  // Modal Styles
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'center',
    alignItems: 'center',
    padding: horizontalScale(20),
  },
  modalContent: {
    backgroundColor: Colors.white,
    borderRadius: moderateScale(16),
    width: '100%',
    maxWidth: 400,
  },
  modalHeader: {
    padding: moderateScale(20),
    alignItems: 'center'
  },
  modalIcon: {
    width: moderateScale(60),
    height: moderateScale(60),
    borderRadius: moderateScale(30),
    alignItems: 'center',
    justifyContent: 'center',
    marginBottom: verticalScale(12),
  },
  modalTitle: {
    color: Colors.text,
    fontSize: moderateScale(18),
    fontWeight: '700',
    marginBottom: verticalScale(8),
    textAlign: 'center',
  },
  modalMessage: {
    color: Colors.muted,
    fontSize: moderateScale(14),
    textAlign: 'center',
    lineHeight: moderateScale(20),
  },
  warningBox: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: horizontalScale(8),
    backgroundColor: '#fef3c7',
    padding: moderateScale(10),
    borderRadius: moderateScale(8),
    marginTop: verticalScale(12),
  },
  warningBoxText: {
    flex: 1,
    color: '#92400e',
    fontSize: moderateScale(12),
    lineHeight: moderateScale(16),
  },
  modalActions: {
    flexDirection: 'row',
    gap: horizontalScale(12),
    padding: moderateScale(16),
    backgroundColor: '#f9fafb',
    borderBottomLeftRadius: moderateScale(16),
    borderBottomRightRadius: moderateScale(16),
  },
  modalBtnCancel: {
    flex: 1,
    paddingVertical: verticalScale(12),
    borderRadius: moderateScale(8),
    borderWidth: 1,
    borderColor: Colors.border,
    backgroundColor: Colors.white,
    alignItems: 'center',
  },
  modalBtnCancelText: {
    color: Colors.text,
    fontSize: moderateScale(14),
    fontWeight: '600',
  },
  modalBtnConfirm: {
    flex: 1,
    paddingVertical: verticalScale(12),
    borderRadius: moderateScale(8),
    alignItems: 'center',
  },
  modalBtnConfirmText: {
    color: Colors.white,
    fontSize: moderateScale(14),
    fontWeight: '600',
  },
  // Form Modal Styles
  formModalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0,0,0,0.5)',
    justifyContent: 'flex-end',
  },
  formModalContent: {
    backgroundColor: Colors.white,
    borderTopLeftRadius: moderateScale(24),
    borderTopRightRadius: moderateScale(24),
    maxHeight: SCREEN_HEIGHT * 0.9,
  },
  formModalHeader: {
    padding: moderateScale(20),
    borderBottomWidth: 1,
    borderBottomColor: Colors.border,
    position: 'relative',
  },
  formModalTitle: {
    color: Colors.text,
    fontSize: moderateScale(20),
    fontWeight: '700',
    marginBottom: verticalScale(4),
  },
  formModalSubtitle: {
    color: Colors.muted,
    fontSize: moderateScale(13),
    marginTop: verticalScale(4),
  },
  closeButton: {
    position: 'absolute',
    top: moderateScale(20),
    right: moderateScale(20),
    width: moderateScale(32),
    height: moderateScale(32),
    borderRadius: moderateScale(16),
    backgroundColor: Colors.bg,
    alignItems: 'center',
    justifyContent: 'center',
  },
  formBody: {
    padding: moderateScale(20),
    maxHeight: SCREEN_HEIGHT * 0.6,
  },
  formGroup: {
    marginBottom: verticalScale(16),
  },
  formLabel: {
    color: Colors.text,
    fontSize: moderateScale(14),
    fontWeight: '600',
    marginBottom: verticalScale(8),
  },
  required: {
    color: '#ef4444'
  },
  formInput: {
    backgroundColor: Colors.white,
    borderWidth: 1,
    borderColor: Colors.border,
    borderRadius: moderateScale(12),
    paddingHorizontal: horizontalScale(14),
    paddingVertical: verticalScale(12),
    color: Colors.text,
    fontSize: moderateScale(15),
  },
  formTextArea: {
    height: verticalScale(80),
    textAlignVertical: 'top',
  },
  formLoading: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: horizontalScale(8),
    padding: moderateScale(16),
    backgroundColor: Colors.bg,
    borderRadius: moderateScale(12),
  },
  selectedMemberCard: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: horizontalScale(12),
    padding: moderateScale(12),
    backgroundColor: '#f0f9ff',
    borderRadius: moderateScale(12),
    borderWidth: 1,
    borderColor: '#bfdbfe',
  },
  lockedBadge: {
    backgroundColor: Colors.bg,
    padding: moderateScale(8),
    borderRadius: moderateScale(8),
  },
  picker: {
    maxHeight: verticalScale(220),
    borderWidth: 1,
    borderColor: Colors.border,
    borderRadius: moderateScale(12),
    backgroundColor: Colors.bg,
  },
  emptyPicker: {
    alignItems: 'center',
    justifyContent: 'center',
    padding: moderateScale(32),
  },
  emptyPickerText: {
    color: Colors.text,
    fontSize: moderateScale(14),
    fontWeight: '600',
    marginTop: verticalScale(12),
  },
  emptyPickerSubtext: {
    color: Colors.muted,
    fontSize: moderateScale(12),
    marginTop: verticalScale(4),
    textAlign: 'center',
  },
  pickerItem: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    padding: moderateScale(12),
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: Colors.border,
  },
  pickerItemActive: {
    backgroundColor: '#f0f9ff',
  },
  pickerItemLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  pickerItemName: {
    color: Colors.text,
    fontSize: moderateScale(14),
    fontWeight: '600',
  },
  pickerItemNameActive: {
    color: Colors.primary,
  },
  pickerItemNoInduk: {
    color: Colors.muted,
    fontSize: moderateScale(12),
    marginTop: 2,
  },
  haidInfo: {
    color: '#10b981',
    fontSize: moderateScale(11),
    marginTop: 4,
  },
  infoBox: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    backgroundColor: '#fef3c7',
    padding: moderateScale(14),
    borderRadius: moderateScale(12),
    borderLeftWidth: 3,
    borderLeftColor: '#f59e0b',
    marginTop: verticalScale(8),
  },
  infoTitle: {
    color: '#92400e',
    fontSize: moderateScale(13),
    fontWeight: '700',
    marginBottom: verticalScale(6),
  },
  infoText: {
    color: '#92400e',
    fontSize: moderateScale(11),
    lineHeight: moderateScale(16),
    marginBottom: verticalScale(2),
  },
  formActions: {
    flexDirection: 'row',
    gap: horizontalScale(12),
    padding: moderateScale(20),
    borderTopWidth: 1,
    borderTopColor: Colors.border,
    backgroundColor: '#f9fafb',
    borderBottomLeftRadius: moderateScale(24),
    borderBottomRightRadius: moderateScale(24),
  },
  formBtnCancel: {
    flex: 1,
    paddingVertical: verticalScale(14),
    borderRadius: moderateScale(12),
    borderWidth: 1,
    borderColor: Colors.border,
    backgroundColor: Colors.white,
    alignItems: 'center',
  },
  formBtnCancelText: {
    color: Colors.text,
    fontSize: moderateScale(15),
    fontWeight: '600',
  },
  formBtnSave: {
    flex: 1,
    paddingVertical: verticalScale(14),
    borderRadius: moderateScale(12),
    backgroundColor: Colors.primary,
    alignItems: 'center',
    shadowColor: Colors.primary,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.3,
    shadowRadius: 8,
    elevation: 4,
  },
  formBtnSaveText: {
    color: Colors.white,
    fontSize: moderateScale(15),
    fontWeight: '700',
  },
  centerRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: horizontalScale(8),
    justifyContent: 'center',
    paddingVertical: verticalScale(20),
  },
  emptyState: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: verticalScale(40),
  },
  mutedText: {
    color: Colors.muted,
    fontSize: moderateScale(14),
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
  checkNowButton: {
    backgroundColor: '#3b82f6',
    paddingVertical: verticalScale(8),
    paddingHorizontal: horizontalScale(12),
    borderRadius: moderateScale(6),
    alignSelf: 'flex-end',
    marginTop: verticalScale(8),
  },
  checkNowText: {
    color: Colors.white,
    fontSize: moderateScale(12),
    fontWeight: '600',
  },
})
export default HomeScreenHealthMonitor