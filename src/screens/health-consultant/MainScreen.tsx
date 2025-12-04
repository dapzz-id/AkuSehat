"use client"

import React, { useEffect, useMemo, useRef, useState } from "react"
import {
  ActivityIndicator,
  Animated,
  Dimensions,
  Pressable,
  ScrollView,
  StatusBar,
  StyleSheet,
  Text,
  View,
  Modal,
  TextInput,
  Alert,
} from "react-native"
import { SafeAreaView } from "react-native-safe-area-context"
import { Colors } from "../../settings/Colors"
import api from "../../api/axiosConfig"
import AnimatedBottomTabs, { type TabItem } from "../../components/AnimatedBottomTabs"
import Icon from "react-native-vector-icons/Feather"
import { logout } from "../../utils/authHelper"
import { useNavigation, useFocusEffect } from '@react-navigation/native';
import { checkAuthStatus } from '../../utils/authHelper';

const { width: SCREEN_WIDTH, height: SCREEN_HEIGHT } = Dimensions.get("window")
const guidelineBaseWidth = 375
const guidelineBaseHeight = 812

const horizontalScale = (size: number) => (SCREEN_WIDTH / guidelineBaseWidth) * size
const verticalScale = (size: number) => (SCREEN_HEIGHT / guidelineBaseHeight) * size
const moderateScale = (size: number, factor = 0.5) => size + (horizontalScale(size) - size) * factor
const isSmallScreen = SCREEN_WIDTH < 375

type HealthSummary = { obesitas: number; kurus: number; normal: number; total: number }
type HealthRecord = {
  tgl: string
  bb: string
  tb: string
  score: number
  status: string
  status_darah: string
  perilaku_beresiko: string
  gangguan_reproduksi: string
}
type Member = {
  id: number
  nama: string
  nomor_induk: string
  divisi: string
  jk: string
  username: string
  pemeriksaan_terakhir: string | null
  avg_bmi: number
  riwayat_kesehatan: HealthRecord[]
}
type DivisiItem = {
  id: number
  divisi: string
  obesitas: number
  total_member: number
  kurus: number
  normal: number
  data_member: Member[]
}
type Profile = { id: number; nama: string; username: string; level?: string, nomor_induk?: string; divisi?: string; jk?: "L" | "P" }
type Notif = {
  id_kesehatan: number
  id_user: number
  tgl: string
  bb: string
  tb: string
  sistol: string
  diastol: string
  status_darah: string
  imt: string
  status: string
  pesan_imt: string
  pesan_tkd: string
  kondisi_telinga: string
  kondisi_gigi: string
  perilaku_beresiko: string
  gangguan_reproduksi: string
  created_at: string
  updated_at: string
  user: { id: number; nomor_induk: string; nama: string; level: string; jk: string }
}

const tabs: TabItem[] = [
  { key: "ringkasan", icon: <Icon name="home" size={22} color="#444" /> },
  { key: "divisi", icon: <Icon name="users" size={22} color="#444" /> },
  { key: "notifikasi", icon: <Icon name="bell" size={22} color="#444" /> },
  { key: "profil", icon: <Icon name="user" size={22} color="#444" /> },
]

const HomeScreenHealthConsultant = () => {
  const [active, setActive] = useState("ringkasan")
  const fadeMount = useRef(new Animated.Value(0)).current
  const slideMount = useRef(new Animated.Value(16)).current
  const fadeTab = useRef(new Animated.Value(1)).current

  const [summary, setSummary] = useState<HealthSummary | null>(null)
  const [divisi, setDivisi] = useState<DivisiItem[]>([])
  const [profile, setProfile] = useState<Profile | null>(null)
  const [notifications, setNotifications] = useState<Notif[]>([])
  const [loading, setLoading] = useState(false)
  const [loadingProfile, setLoadingProfile] = useState(false)
  const [loadingNotif, setLoadingNotif] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [selectedMember, setSelectedMember] = useState<Member | null>(null)
  const [detailModalVisible, setDetailModalVisible] = useState(false)
  const [filterYear, setFilterYear] = useState<string>("")
  const [filterMonth, setFilterMonth] = useState<string>("")

  const navigation = useNavigation();

  // State untuk halaman divisi - menampilkan daftar member
  const [showMemberList, setShowMemberList] = useState(false)
  const [selectedDivisionData, setSelectedDivisionData] = useState<DivisiItem | null>(null)

  // Filter & Search untuk Divisi
  const [searchDivisi, setSearchDivisi] = useState("")

  // Filter & Search untuk Member
  const [searchMember, setSearchMember] = useState("")
  const [filterJenisKelamin, setFilterJenisKelamin] = useState<string>("")

  // State untuk ganti password
  const [passwordModalVisible, setPasswordModalVisible] = useState(false)
  const [currentPassword, setCurrentPassword] = useState("")
  const [newPassword, setNewPassword] = useState("")
  const [retypePassword, setRetypePassword] = useState("")
  const [passwordError, setPasswordError] = useState<string | null>(null)
  const [savingPassword, setSavingPassword] = useState(false)

  // State untuk loading popup
  const [initialLoading, setInitialLoading] = useState(false)

  // State untuk logout loading
  const [isLoggingOut, setIsLoggingOut] = useState(false)

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
      Alert.alert("Kata sandi berhasil diubah.")
    } catch (e: any) {
      setPasswordError(e?.response?.data?.message || "Gagal mengubah kata sandi.")
    } finally {
      setSavingPassword(false)
    }
  }

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
    const load = async () => {
      try {
        setInitialLoading(true)
        setLoading(true)
        setLoadingProfile(true)
        setLoadingNotif(true)
        setError(null)
        const [sumRes, divisiRes, meRes, notifRes] = await Promise.all([
          api.get("/health-consultant/health-summary"),
          api.get("/health-consultant/health-by-division"),
          api.get("/me"),
          api.get("/health-consultant/health-notification"),
        ])
        setSummary(sumRes?.data?.data || sumRes?.data || null)
        setDivisi(divisiRes?.data?.data || divisiRes?.data || [])
        setProfile(meRes?.data?.data || meRes?.data || null)
        setNotifications(notifRes?.data?.data || notifRes?.data || [])
      } catch (e: any) {
        setError(e?.message || "Gagal memuat data")
      } finally {
        setInitialLoading(false)
        setLoading(false)
        setLoadingProfile(false)
        setLoadingNotif(false)
      }
    }
    load()
  }, [])

  // Reset filter dan search ketika berganti tab
  useEffect(() => {
    if (active === "divisi") {
      setShowMemberList(false)
      setSelectedDivisionData(null)
      setSearchMember("")
      setFilterJenisKelamin("")
    }
  }, [active])

  const totals = useMemo(
    () => ({
      obesitas: summary?.obesitas ?? 0,
      kurus: summary?.kurus ?? 0,
      normal: summary?.normal ?? 0,
      total: summary?.total ?? 0,
    }),
    [summary],
  )

  const percent = (n: number, total: number) => (total === 0 ? 0 : Math.round((n / total) * 100))

  const formatDate = (dateString: string) => {
    const date = new Date(dateString)
    return date.toLocaleDateString("id-ID", { day: "2-digit", month: "2-digit", year: "numeric" })
  }

  const handleShowMemberList = (divisionData: DivisiItem) => {
    setSelectedDivisionData(divisionData)
    setShowMemberList(true)
    setSearchMember("")
    setFilterJenisKelamin("")
  }

  const handleBackToDivisionList = () => {
    setShowMemberList(false)
    setSelectedDivisionData(null)
    setSearchMember("")
    setFilterJenisKelamin("")
  }

  const handleShowMemberDetail = (member: Member) => {
    setSelectedMember(member)
    setFilterYear("")
    setFilterMonth("")
    setDetailModalVisible(true)
  }

  const getAvailableYears = (records: HealthRecord[]) => {
    const years = records.map((r) => new Date(r.tgl).getFullYear())
    return Array.from(new Set(years)).sort((a, b) => b - a)
  }

  const getAvailableMonths = (records: HealthRecord[], year: string) => {
    if (!year) return []
    const months = records
      .filter((r) => new Date(r.tgl).getFullYear().toString() === year)
      .map((r) => new Date(r.tgl).getMonth() + 1)
    return Array.from(new Set(months)).sort((a, b) => a - b)
  }

  const filteredHealthRecords = useMemo(() => {
    if (!selectedMember) return []
    let filtered = selectedMember.riwayat_kesehatan
    if (filterYear) {
      filtered = filtered.filter((r) => new Date(r.tgl).getFullYear().toString() === filterYear)
    }
    if (filterMonth) {
      filtered = filtered.filter((r) => (new Date(r.tgl).getMonth() + 1).toString() === filterMonth)
    }
    return filtered.sort((a, b) => new Date(b.tgl).getTime() - new Date(a.tgl).getTime())
  }, [selectedMember, filterYear, filterMonth])

  // Filter divisi berdasarkan search
  const filteredDivisi = useMemo(() => {
    if (!searchDivisi || searchDivisi.trim() === "") {
      return divisi
    }
    
    const searchLower = searchDivisi.toLowerCase()
    return divisi.filter((item) => {
      return item.divisi.toLowerCase().includes(searchLower)
    })
  }, [divisi, searchDivisi])

  // Filter member berdasarkan search dan filter
  const filteredMembers = useMemo(() => {
    if (!selectedDivisionData) return []

    let filtered = selectedDivisionData.data_member

    // Filter by search (nama atau nomor induk)
    if (searchMember) {
      filtered = filtered.filter((m) =>
        m.nama.toLowerCase().includes(searchMember.toLowerCase()) ||
        m.nomor_induk.toLowerCase().includes(searchMember.toLowerCase())
      )
    }

    // Filter by jenis kelamin
    if (filterJenisKelamin) {
      filtered = filtered.filter((m) => m.jk === filterJenisKelamin)
    }

    return filtered
  }, [selectedDivisionData, searchMember, filterJenisKelamin])

  const monthNames = [
    "Januari",
    "Februari",
    "Maret",
    "April",
    "Mei",
    "Juni",
    "Juli",
    "Agustus",
    "September",
    "Oktober",
    "November",
    "Desember",
  ]

  return (
    <SafeAreaView style={[styles.safe, { paddingHorizontal: isSmallScreen ? horizontalScale(12) : horizontalScale(16) }]}>
      <StatusBar barStyle="dark-content" backgroundColor={Colors.bg} />
      <ScrollView
        contentContainerStyle={[
          styles.scroll,
          { paddingBottom: verticalScale(120), paddingTop: verticalScale(30) },
        ]}
        showsVerticalScrollIndicator={false}
      >
        <Animated.View style={[styles.header, { opacity: fadeMount, transform: [{ translateY: slideMount }] }]}>
          <View>
            <Text style={styles.greet}>Dashboard</Text>
            <Text style={styles.name}>Health Consultant</Text>
          </View>
        </Animated.View>

        <Animated.View style={{ opacity: fadeTab }}>
          {active === "ringkasan" && (
            <View style={{ gap: verticalScale(12) }}>
              <Card>
                <Text style={styles.cardTitle}>Status Kesehatan Member</Text>
                {loading ? (
                  <View style={styles.centerRow}>
                    <ActivityIndicator color={Colors.primary} />
                    <Text style={styles.mutedText}>Memuat...</Text>
                  </View>
                ) : error ? (
                  <Text style={[styles.mutedText, { color: "#dc2626" }]}>{error}</Text>
                ) : (
                  <>
                    <View style={styles.grid2}>
                      <StatCard label="Obesitas" value={`${totals.obesitas}`} color="#ef4444" />
                      <StatCard label="Underweight" value={`${totals.kurus}`} color="#f59e0b" />
                      <StatCard label="Normal" value={`${totals.normal}`} color={Colors.primary} />
                      <StatCard label="Total" value={`${totals.total}`} color={Colors.accent} />
                    </View>

                    <View style={{ gap: verticalScale(8) }}>
                      <Text style={styles.mutedText}>
                        Obesitas {percent(totals.obesitas, totals.total)}% • Underweight {" "}
                        {percent(totals.kurus, totals.total)}% • Normal {percent(totals.normal, totals.total)}%
                      </Text>
                      <View style={styles.stackBg}>
                        <View style={[styles.stackPiece, { flex: totals.obesitas || 0, backgroundColor: "#ef4444" }]} />
                        <View style={[styles.stackPiece, { flex: totals.kurus || 0, backgroundColor: "#f59e0b" }]} />
                        <View
                          style={[styles.stackPiece, { flex: totals.normal || 0, backgroundColor: Colors.primary }]}
                        />
                      </View>
                    </View>
                  </>
                )}
              </Card>

              <TipCard
                title="Anjuran"
                text="Prioritaskan konseling gizi bagi member obesitas/underweight dan dorong aktivitas fisik aman."
              />
            </View>
          )}

          {active === "divisi" && (
            <View style={{ gap: verticalScale(12) }}>
              {!showMemberList ? (
                // Tampilan Daftar Divisi
                <Card>
                  <Text style={styles.cardTitle}>Kesehatan per Divisi</Text>

                  {/* Search Box untuk Divisi */}
                  <View style={styles.searchContainer}>
                    <Icon name="search" size={18} color={Colors.muted} style={styles.searchIcon} />
                    <TextInput
                      value={searchDivisi}
                      onChangeText={setSearchDivisi}
                      placeholder="Cari divisi..."
                      placeholderTextColor={Colors.muted}
                      style={styles.searchInput}
                    />
                    {searchDivisi.length > 0 && (
                      <Pressable onPress={() => setSearchDivisi("")} style={styles.clearButton}>
                        <Icon name="x" size={18} color={Colors.muted} />
                      </Pressable>
                    )}
                  </View>

                  {loading ? (
                    <View style={styles.centerRow}>
                      <ActivityIndicator color={Colors.primary} />
                      <Text style={styles.mutedText}>Memuat...</Text>
                    </View>
                  ) : error ? (
                    <Text style={[styles.mutedText, { color: "#dc2626" }]}>{error}</Text>
                  ) : !Array.isArray(filteredDivisi) || filteredDivisi.length === 0 ? (
                    <View style={styles.emptyState}>
                      <Icon name="folder" size={40} color={Colors.muted} />
                      <Text style={[styles.mutedText, { textAlign: 'center', marginTop: 8 }]}>
                        Tidak ada divisi ditemukan
                      </Text>
                    </View>
                  ) : (
                    <>
                      {filteredDivisi.map((k) => {
                        const total = (k.obesitas || 0) + (k.kurus || 0) + (k.normal || 0)
                        return (
                          <View key={k.id} style={[styles.listItem, styles.listLine]}>
                            <View style={{ flex: 1 }}>
                              <Text style={styles.listTitle}>
                                {k.divisi}
                              </Text>
                              <View
                                style={{
                                  flexDirection: "row",
                                  gap: isSmallScreen ? horizontalScale(4) : horizontalScale(8),
                                  marginTop: verticalScale(6),
                                }}
                              >
                                <Badge color="#ef4444" label={`O | ${k.obesitas}`} />
                                <Badge color="#f59e0b" label={`U | ${k.kurus}`} />
                                <Badge color={Colors.primary} label={`N | ${k.normal}`} />
                              </View>
                              <View style={{ marginTop: verticalScale(10) }}>
                                <View style={styles.stackBg}>
                                  <View
                                    style={[styles.stackPiece, { flex: k.obesitas || 0, backgroundColor: "#ef4444" }]}
                                  />
                                  <View
                                    style={[styles.stackPiece, { flex: k.kurus || 0, backgroundColor: "#f59e0b" }]}
                                  />
                                  <View
                                    style={[styles.stackPiece, { flex: k.normal || 0, backgroundColor: Colors.primary }]}
                                  />
                                </View>
                                <Text style={styles.mutedText}>
                                  Total {k.total_member} member • O {percent(k.obesitas, total)}% • K {percent(k.kurus, total)}% • N{" "}
                                  {percent(k.normal, total)}%
                                </Text>
                              </View>
                            </View>
                            <Pressable
                              onPress={() => handleShowMemberList(k)}
                              style={styles.ghostBtn}
                              accessibilityRole="button"
                            >
                              <Text style={styles.ghostBtnText}>Detail</Text>
                            </Pressable>
                          </View>
                        )
                      })}
                    </>
                  )}
                </Card>
              ) : (
                // Tampilan Daftar Member
                <Card>
                  <View style={styles.memberListHeader}>
                    <Pressable onPress={handleBackToDivisionList} style={styles.backButton}>
                      <Icon name="arrow-left" size={20} color={Colors.primary} />
                    </Pressable>
                    <View style={{ flex: 1 }}>
                      <Text style={styles.cardTitle}>Daftar Member</Text>
                      <Text style={styles.mutedText}>{selectedDivisionData?.divisi}</Text>
                    </View>
                  </View>

                  {/* Search & Filter untuk Member */}
                  <View style={{ gap: verticalScale(8) }}>
                    {/* Search Box */}
                    <View style={styles.searchContainer}>
                      <Icon name="search" size={18} color={Colors.muted} style={styles.searchIcon} />
                      <TextInput
                        value={searchMember}
                        onChangeText={setSearchMember}
                        placeholder="Cari nama atau nomor induk..."
                        placeholderTextColor={Colors.muted}
                        style={styles.searchInput}
                      />
                      {searchMember.length > 0 && (
                        <Pressable onPress={() => setSearchMember("")} style={styles.clearButton}>
                          <Icon name="x" size={18} color={Colors.muted} />
                        </Pressable>
                      )}
                    </View>

                    {/* Filter Jenis Kelamin */}
                    <View style={styles.filterContainer}>
                      <Text style={styles.filterLabel}>Filter:</Text>
                      <View style={styles.filterButtons}>
                        <Pressable
                          onPress={() => setFilterJenisKelamin("")}
                          style={[
                            styles.filterBtn,
                            !filterJenisKelamin && styles.filterBtnActive
                          ]}
                        >
                          <Text style={[
                            styles.filterBtnText,
                            !filterJenisKelamin && styles.filterBtnTextActive
                          ]}>Semua</Text>
                        </Pressable>
                        <Pressable
                          onPress={() => setFilterJenisKelamin("L")}
                          style={[
                            styles.filterBtn,
                            filterJenisKelamin === "L" && styles.filterBtnActive
                          ]}
                        >
                          <Text style={[
                            styles.filterBtnText,
                            filterJenisKelamin === "L" && styles.filterBtnTextActive
                          ]}>Laki-laki</Text>
                        </Pressable>
                        <Pressable
                          onPress={() => setFilterJenisKelamin("P")}
                          style={[
                            styles.filterBtn,
                            filterJenisKelamin === "P" && styles.filterBtnActive
                          ]}
                        >
                          <Text style={[
                            styles.filterBtnText,
                            filterJenisKelamin === "P" && styles.filterBtnTextActive
                          ]}>Perempuan</Text>
                        </Pressable>
                      </View>
                    </View>
                  </View>

                  {/* Daftar Member */}
                  {filteredMembers.length === 0 ? (
                    <View style={styles.emptyState}>
                      <Icon name="users" size={40} color={Colors.muted} />
                      <Text style={[styles.mutedText, { textAlign: 'center', marginTop: 8 }]}>
                        Tidak ada member ditemukan
                      </Text>
                    </View>
                  ) : (
                    <>
                      <Text style={[styles.mutedText, { marginTop: verticalScale(8) }]}>
                        Menampilkan {filteredMembers.length} dari {selectedDivisionData?.data_member.length || 0} member
                      </Text>
                      {filteredMembers.map((member) => (
                        <Pressable
                          key={member.id}
                          onPress={() => handleShowMemberDetail(member)}
                          style={[styles.listItem, styles.listLine, { paddingVertical: verticalScale(12) }]}
                        >
                          <View style={{ flex: 1 }}>
                            <Text style={styles.listTitle}>{member.nama}</Text>
                            <Text style={styles.mutedText}>Nomor Induk: {member.nomor_induk}</Text>
                            <Text style={styles.mutedText}>
                              Jenis Kelamin: {member.jk === "L" ? "Laki-laki" : "Perempuan"}
                            </Text>
                            {member.pemeriksaan_terakhir && (
                              <Text style={styles.mutedText}>
                                Pemeriksaan Terakhir: {formatDate(member.pemeriksaan_terakhir)}
                              </Text>
                            )}
                            {member.avg_bmi > 0 && (
                              <View style={{ marginTop: verticalScale(4) }}>
                                <Badge
                                  color={
                                    member.avg_bmi < 18.5
                                      ? "#f59e0b"
                                      : member.avg_bmi >= 25
                                        ? "#ef4444"
                                        : Colors.primary
                                  }
                                  label={`BMI Rata-rata: ${member.avg_bmi.toFixed(1)}`}
                                />
                              </View>
                            )}
                          </View>
                          <Icon name="chevron-right" size={20} color={Colors.muted} />
                        </Pressable>
                      ))}
                    </>
                  )}
                </Card>
              )}
            </View>
          )}

          {active === "notifikasi" && (
            <View style={{ gap: verticalScale(12) }}>
              <Card>
                <Text style={styles.cardTitle}>Notifikasi</Text>
                {loadingNotif ? (
                  <View style={styles.centerRow}>
                    <ActivityIndicator color={Colors.primary} />
                    <Text style={styles.mutedText}>Memuat...</Text>
                  </View>
                ) : notifications.length === 0 ? (
                  <Text style={styles.mutedText}>Belum ada notifikasi.</Text>
                ) : (
                  notifications.map((n) => (
                    <View
                      key={n.id_kesehatan}
                      style={{
                        paddingVertical: verticalScale(10),
                        borderBottomWidth: StyleSheet.hairlineWidth,
                        borderBottomColor: Colors.border,
                      }}
                    >
                      <Text style={{ color: Colors.text, fontWeight: "700" }}>
                        {n.user.nama} - {n.status} ({formatDate(n.tgl)})
                      </Text>
                      <Text style={{ color: Colors.muted }}>
                        {n.pesan_imt || n.pesan_tkd || "Perhatikan kondisi kesehatan member."}
                      </Text>
                      <Text style={{ color: Colors.muted, fontSize: moderateScale(12), marginTop: verticalScale(4) }}>
                        {formatDate(n.created_at)}
                      </Text>
                    </View>
                  ))
                )}
              </Card>
            </View>
          )}

          {active === "profil" && (
            <View style={{ gap: verticalScale(12) }}>
              {/* Profile Header Card */}
              <Card style={styles.profileHeaderCard}>
                <View style={styles.profileAvatar}>
                  <Icon name="user" size={40} color={Colors.white} />
                </View>
                <View style={styles.profileInfo}>
                  <Text style={styles.profileName}>{profile?.nama || "Health Consultant"}</Text>
                  <Text style={styles.profileRole}>Health Consultant</Text>
                </View>
              </Card>

              {/* Informasi Pribadi */}
              <Card>
                <Text style={styles.cardTitle}>Informasi Pribadi</Text>
                {loadingProfile ? (
                  <View style={styles.centerRow}>
                    <ActivityIndicator color={Colors.primary} />
                    <Text style={styles.mutedText}>Memuat...</Text>
                  </View>
                ) : profile ? (
                  <View style={styles.profileDetails}>
                    <ProfileField icon="user" label="Nama" value={profile.nama} />
                    <ProfileField icon="at-sign" label="Username" value={profile.username} />
                    <ProfileField icon="key" label="Nomor Induk" value={profile.nomor_induk || "-"} />
                    <ProfileField icon="book" label="Divisi" value={profile.divisi || "-"} />
                    <ProfileField icon="award" label="Peran" value={profile.level || "Health Consultant"} />
                  </View>
                ) : (
                  <View style={styles.emptyState}>
                    <Icon name="user-x" size={40} color={Colors.muted} />
                    <Text style={[styles.mutedText, { textAlign: 'center', marginTop: 8 }]}>Profil tidak tersedia</Text>
                  </View>
                )}
              </Card>

              {/* Akun Section dengan Ganti Password dan Logout */}
              <Card>
                <Text style={styles.cardTitle}>Akun</Text>
                <View style={styles.accountActions}>
                  <Pressable onPress={() => setPasswordModalVisible(true)} style={styles.accountButton}>
                    <Icon name="lock" size={20} color={Colors.primary} />
                    <Text style={styles.accountButtonText}>Ganti Password</Text>
                    <Icon name="chevron-right" size={18} color={Colors.muted} />
                  </Pressable>
                  <Pressable
                    onPress={handleLogout}
                    disabled={isLoggingOut}
                    style={[
                      styles.accountButton,
                      styles.logoutButton,
                      { opacity: isLoggingOut ? 0.6 : 1 }
                    ]}
                  >
                    <Icon name="log-out" size={20} color="#dc2626" />
                    <Text style={[styles.accountButtonText, { color: '#dc2626' }]}>Keluar</Text>
                  </Pressable>
                </View>
              </Card>
            </View>
          )}
        </Animated.View>
      </ScrollView>

      {/* Modal for Member Detail with Health Records */}
      <Modal
        animationType="slide"
        transparent={true}
        visible={detailModalVisible}
        onRequestClose={() => setDetailModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={[styles.modalContent, { maxHeight: SCREEN_HEIGHT * 0.85 }]}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>Detail Member</Text>
              <Pressable onPress={() => setDetailModalVisible(false)} accessibilityRole="button">
                <Icon name="x" size={24} color={Colors.text} />
              </Pressable>
            </View>
            <ScrollView showsVerticalScrollIndicator={false}>
              {selectedMember && (
                <>
                  {/* Member Info */}
                  <View style={[styles.card, { marginBottom: verticalScale(12) }]}>
                    <Text style={styles.cardTitle}>{selectedMember.nama}</Text>
                    <View style={{ gap: verticalScale(6), marginTop: verticalScale(8) }}>
                      <View style={styles.rowBetween}>
                        <Text style={styles.mutedText}>Nomor Induk</Text>
                        <Text style={styles.listTitle}>{selectedMember.nomor_induk}</Text>
                      </View>
                      <View style={styles.rowBetween}>
                        <Text style={styles.mutedText}>Username</Text>
                        <Text style={styles.listTitle}>{selectedMember.username}</Text>
                      </View>
                      <View style={styles.rowBetween}>
                        <Text style={styles.mutedText}>Divisi</Text>
                        <Text style={styles.listTitle}>{selectedMember.divisi}</Text>
                      </View>
                      <View style={styles.rowBetween}>
                        <Text style={styles.mutedText}>Jenis Kelamin</Text>
                        <Text style={styles.listTitle}>
                          {selectedMember.jk === "L" ? "Laki-laki" : "Perempuan"}
                        </Text>
                      </View>
                      {selectedMember.avg_bmi > 0 && (
                        <View style={styles.rowBetween}>
                          <Text style={styles.mutedText}>BMI Rata-rata</Text>
                          <Text
                            style={[
                              styles.listTitle,
                              {
                                color:
                                  selectedMember.avg_bmi < 18.5
                                    ? "#f59e0b"
                                    : selectedMember.avg_bmi >= 25
                                      ? "#ef4444"
                                      : Colors.primary,
                              },
                            ]}
                          >
                            {selectedMember.avg_bmi.toFixed(1)}
                          </Text>
                        </View>
                      )}
                      {selectedMember.pemeriksaan_terakhir && (
                        <View style={styles.rowBetween}>
                          <Text style={styles.mutedText}>Pemeriksaan Terakhir</Text>
                          <Text style={styles.listTitle}>{formatDate(selectedMember.pemeriksaan_terakhir)}</Text>
                        </View>
                      )}
                    </View>
                  </View>

                  {/* Health Records */}
                  <View style={styles.card}>
                    <Text style={styles.cardTitle}>Riwayat Kesehatan</Text>

                    {/* Filters */}
                    {selectedMember.riwayat_kesehatan.length > 0 && (
                      <View style={{ gap: verticalScale(8), marginTop: verticalScale(8) }}>
                        <Text style={styles.mutedText}>Filter:</Text>
                        <View
                          style={{
                            flexDirection: "row",
                            gap: horizontalScale(8),
                            flexWrap: "wrap",
                          }}
                        >
                          {/* Year Filter */}
                          <Pressable
                            onPress={() => {
                              setFilterYear("")
                              setFilterMonth("")
                            }}
                            style={[
                              styles.filterBtn,
                              !filterYear && { backgroundColor: Colors.primary, borderColor: Colors.primary },
                            ]}
                          >
                            <Text style={[styles.filterBtnText, !filterYear && { color: Colors.white }]}>Semua</Text>
                          </Pressable>
                          {getAvailableYears(selectedMember.riwayat_kesehatan).map((year) => (
                            <Pressable
                              key={year}
                              onPress={() => {
                                setFilterYear(year.toString())
                                setFilterMonth("")
                              }}
                              style={[
                                styles.filterBtn,
                                filterYear === year.toString() && {
                                  backgroundColor: Colors.primary,
                                  borderColor: Colors.primary,
                                },
                              ]}
                            >
                              <Text
                                style={[
                                  styles.filterBtnText,
                                  filterYear === year.toString() && { color: Colors.white },
                                ]}
                              >
                                {year}
                              </Text>
                            </Pressable>
                          ))}
                        </View>

                        {/* Month Filter */}
                        {filterYear && getAvailableMonths(selectedMember.riwayat_kesehatan, filterYear).length > 0 && (
                          <View
                            style={{
                              flexDirection: "row",
                              gap: horizontalScale(8),
                              flexWrap: "wrap",
                            }}
                          >
                            <Text style={[styles.mutedText, { width: "100%" }]}>Bulan:</Text>
                            {getAvailableMonths(selectedMember.riwayat_kesehatan, filterYear).map((month) => (
                              <Pressable
                                key={month}
                                onPress={() => setFilterMonth(month.toString())}
                                style={[
                                  styles.filterBtn,
                                  filterMonth === month.toString() && {
                                    backgroundColor: Colors.primary,
                                    borderColor: Colors.primary,
                                  },
                                ]}
                              >
                                <Text
                                  style={[
                                    styles.filterBtnText,
                                    filterMonth === month.toString() && { color: Colors.white },
                                  ]}
                                >
                                  {monthNames[month - 1]}
                                </Text>
                              </Pressable>
                            ))}
                          </View>
                        )}
                      </View>
                    )}

                    {/* Records List */}
                    {filteredHealthRecords.length === 0 ? (
                      <Text style={[styles.mutedText, { marginTop: verticalScale(12) }]}>
                        {selectedMember.riwayat_kesehatan.length === 0
                          ? "Belum ada riwayat kesehatan terbaru."
                          : "Tidak ada data untuk filter yang dipilih."}
                      </Text>
                    ) : (
                      <View style={{ gap: verticalScale(8), marginTop: verticalScale(12) }}>
                        {filteredHealthRecords.map((record, index) => (
                          <View
                            key={index}
                            style={{
                              padding: moderateScale(12),
                              backgroundColor: Colors.bg,
                              borderRadius: moderateScale(12),
                              borderWidth: StyleSheet.hairlineWidth,
                              borderColor: Colors.border,
                            }}
                          >
                            <View style={styles.rowBetween}>
                              <Text style={styles.listTitle}>{formatDate(record.tgl)}</Text>
                              <Badge
                                color={
                                  record.status === "Normal"
                                    ? Colors.primary
                                    : record.status === "Underweight"
                                      ? "#fff9c4"
                                      : record.status === "Obesitas Level 1"
                                        ? "#ef4444"
                                        : Colors.muted
                                }
                                label={record.status}
                              />
                            </View>
                            <View style={{ gap: verticalScale(4), marginTop: verticalScale(8) }}>
                              <View style={styles.rowBetween}>
                                <Text style={styles.mutedText}>Berat Badan</Text>
                                <Text style={styles.listTitle}>{record.bb} kg</Text>
                              </View>
                              <View style={styles.rowBetween}>
                                <Text style={styles.mutedText}>Tinggi Badan</Text>
                                <Text style={styles.listTitle}>{record.tb} cm</Text>
                              </View>
                              <View style={styles.rowBetween}>
                                <Text style={styles.mutedText}>BMI</Text>
                                <Text
                                  style={[
                                    styles.listTitle,
                                    {
                                      color:
                                        record.score < 18.5
                                          ? "#f59e0b"
                                          : record.score >= 25
                                            ? "#ef4444"
                                            : Colors.primary,
                                    },
                                  ]}
                                >
                                  {record.score.toFixed(1)}
                                </Text>
                              </View>
                              <View style={styles.rowBetween}>
                                <Text style={styles.mutedText}>Status Darah</Text>
                                <Text style={styles.listTitle}>{record.status_darah}</Text>
                              </View>
                              {record.perilaku_beresiko && (
                                <View style={{ marginTop: verticalScale(4) }}>
                                  <Text style={styles.mutedText}>Perilaku Beresiko:</Text>
                                  <Text style={[styles.listTitle, { color: "#ef4444" }]}>
                                    {record.perilaku_beresiko}
                                  </Text>
                                </View>
                              )}
                              {record.gangguan_reproduksi && (
                                <View style={{ marginTop: verticalScale(4) }}>
                                  <Text style={styles.mutedText}>Gangguan Reproduksi:</Text>
                                  <Text style={[styles.listTitle, { color: "#ef4444" }]}>
                                    {record.gangguan_reproduksi}
                                  </Text>
                                </View>
                              )}
                            </View>
                          </View>
                        ))}
                      </View>
                    )}
                  </View>
                </>
              )}
            </ScrollView>

            <Pressable
              onPress={() => setDetailModalVisible(false)}
              style={[styles.ghostBtn, { alignSelf: "center", marginTop: verticalScale(12) }]}
              accessibilityRole="button"
            >
              <Text style={styles.ghostBtnText}>Tutup</Text>
            </Pressable>
          </View>
        </View>
      </Modal>

      {/* Modal for Change Password */}
      <Modal
        animationType="slide"
        transparent={true}
        visible={passwordModalVisible}
        onRequestClose={() => setPasswordModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalContent}>
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
            <View style={styles.modalActions}>
              <Pressable onPress={() => setPasswordModalVisible(false)} style={[styles.modalButton, { backgroundColor: Colors.muted }]}>
                <Text style={styles.modalButtonText}>Batal</Text>
              </Pressable>
              <Pressable
                onPress={handleChangePassword}
                disabled={savingPassword || !currentPassword || !newPassword || !retypePassword}
                style={({ pressed }) => [styles.modalButton, { backgroundColor: Colors.primary, opacity: savingPassword || !currentPassword || !newPassword || !retypePassword ? 0.6 : pressed ? 0.9 : 1 }]}
              >
                {savingPassword ? <ActivityIndicator color={Colors.white} /> : <Text style={styles.modalButtonText}>Simpan</Text>}
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

      <AnimatedBottomTabs items={tabs} activeKey={active} onChange={setActive} />
    </SafeAreaView>
  )
}
const Card = ({ children, style }: { children: React.ReactNode; style?: any }) => (
  <View style={[styles.card, style]}>{children}</View>
)
const StatCard = ({ label, value, color }: { label: string; value: string; color: string }) => (
  <View style={styles.statCard}>
    <Text style={[styles.statValue, { color }]}>{value}</Text>
    <Text style={styles.statLabel}>{label}</Text>
  </View>
)
const Badge = ({ color, label }: { color: string; label: string }) => (
  <View style={[styles.badgeSoft, { backgroundColor: `${color}22`, borderColor: `${color}55` }]}>
    <Text style={[styles.badgeSoftText, { color }]}>{label}</Text>
  </View>
)
const TipCard = ({ title, text }: { title: string; text: string }) => (
  <View style={styles.tipCard}>
    <View style={{ flex: 1 }}>
      <Text style={styles.tipTitle}>{title}</Text>
      <Text style={styles.tipText}>{text}</Text>
    </View>
  </View>
)
const ProfileField = ({ icon, label, value }: { icon: string; label: string; value: string }) => (
  <View style={styles.profileField}>
    <View style={styles.fieldLeft}>
      <Icon name={icon as any} size={18} color={Colors.primary} style={styles.fieldIcon} />
      <Text style={styles.fieldLabel}>{label}</Text>
    </View>
    <Text style={styles.fieldValue}>{value}</Text>
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
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    shadowColor: "#000",
    shadowOpacity: 0.06,
    shadowRadius: moderateScale(12),
    shadowOffset: { width: 0, height: moderateScale(6) },
    elevation: 3,
  },
  greet: { color: Colors.muted, fontSize: moderateScale(14), marginBottom: verticalScale(4) },
  name: { color: Colors.text, fontSize: moderateScale(20), fontWeight: "700" },
  badge: {
    backgroundColor: Colors.accent,
    paddingHorizontal: horizontalScale(12),
    paddingVertical: verticalScale(8),
    borderRadius: moderateScale(12),
  },
  badgeText: { color: Colors.white, fontWeight: "700" },
  card: {
    backgroundColor: Colors.white,
    borderRadius: moderateScale(16),
    marginBottom: verticalScale(8),
    padding: moderateScale(16),
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: Colors.border,
    shadowColor: "#000",
    shadowOpacity: 0.04,
    shadowRadius: moderateScale(6),
    shadowOffset: { width: 0, height: moderateScale(2) },
    elevation: 3,
    gap: verticalScale(12),
  },
  cardTitle: { color: Colors.text, fontSize: moderateScale(16), fontWeight: "700" },
  // Profile Styles
  profileHeaderCard: { flexDirection: 'row', alignItems: 'center', padding: moderateScale(20) },
  profileAvatar: { backgroundColor: Colors.primary, width: moderateScale(60), height: moderateScale(60), borderRadius: moderateScale(30), alignItems: 'center', justifyContent: 'center', marginRight: moderateScale(16) },
  profileInfo: { flex: 1 },
  profileName: { color: Colors.text, fontSize: moderateScale(20), fontWeight: '700', marginBottom: verticalScale(4) },
  profileRole: { color: Colors.muted, fontSize: moderateScale(14) },
  profileDetails: { gap: verticalScale(16) },
  profileField: { flexDirection: 'row', alignItems: 'center', justifyContent: 'space-between', paddingVertical: verticalScale(8) },
  fieldLeft: { flexDirection: 'row', alignItems: 'center', flex: 1 },
  fieldIcon: { marginRight: moderateScale(12) },
  fieldLabel: { color: Colors.muted, fontSize: moderateScale(14), flex: 1 },
  fieldValue: { color: Colors.text, fontSize: moderateScale(14), fontWeight: '600' },
  accountActions: { gap: verticalScale(8) },
  accountButton: { flexDirection: 'row', alignItems: 'center', paddingVertical: verticalScale(12), paddingHorizontal: moderateScale(8), borderRadius: moderateScale(8), backgroundColor: Colors.bg },
  accountButtonText: { color: Colors.text, fontSize: moderateScale(14), fontWeight: '500', flex: 1, marginLeft: moderateScale(12) },
  logoutButton: { backgroundColor: 'rgba(220, 38, 38, 0.1)', marginTop: verticalScale(8) },
  emptyState: { alignItems: 'center', justifyContent: 'center', paddingVertical: verticalScale(40) },
  // Search Container
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: Colors.bg,
    borderRadius: moderateScale(12),
    borderWidth: 1,
    borderColor: Colors.border,
    paddingHorizontal: horizontalScale(12),
    paddingVertical: verticalScale(8),
  },
  searchIcon: {
    marginRight: horizontalScale(8),
  },
  searchInput: {
    flex: 1,
    color: Colors.text,
    fontSize: moderateScale(14),
    paddingVertical: 0,
  },
  clearButton: {
    padding: moderateScale(4),
  },
  // Filter Container
  filterContainer: {
    gap: verticalScale(6),
  },
  filterLabel: {
    color: Colors.muted,
    fontSize: moderateScale(13),
    fontWeight: '600',
  },
  filterButtons: {
    flexDirection: 'row',
    gap: horizontalScale(8),
    flexWrap: 'wrap',
  },
  // Member List Header
  memberListHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: horizontalScale(12),
    marginBottom: verticalScale(8),
  },
  backButton: {
    padding: moderateScale(8),
    borderRadius: moderateScale(8),
    backgroundColor: Colors.bg,
  },
  // Input and Modal Styles
  label: { color: Colors.muted, marginBottom: verticalScale(6), marginTop: verticalScale(2), fontSize: moderateScale(14) },
  input: { backgroundColor: Colors.white, borderWidth: 1, borderColor: Colors.border, borderRadius: moderateScale(12), paddingHorizontal: horizontalScale(12), paddingVertical: verticalScale(12), color: Colors.text, fontSize: moderateScale(16) },
  modalOverlay: { flex: 1, backgroundColor: 'rgba(0, 0, 0, 0.5)', justifyContent: 'center', alignItems: 'center' },
  modalContent: { backgroundColor: Colors.white, borderRadius: moderateScale(16), padding: moderateScale(20), width: horizontalScale(300), gap: verticalScale(12) },
  modalActions: { flexDirection: 'row', justifyContent: 'space-between', marginTop: verticalScale(16) },
  modalButton: { flex: 1, paddingVertical: verticalScale(12), borderRadius: moderateScale(12), alignItems: 'center', marginHorizontal: horizontalScale(4) },
  modalButtonText: { color: Colors.white, fontWeight: '700', fontSize: moderateScale(16) },
  grid2: {
    flexDirection: "row",
    flexWrap: "wrap",
    gap: isSmallScreen ? horizontalScale(6) : horizontalScale(10),
  },
  statCard: {
    width: isSmallScreen ? "100%" : "48%",
    backgroundColor: Colors.white,
    borderRadius: moderateScale(16),
    padding: moderateScale(16),
    borderWidth: StyleSheet.hairlineWidth,
    borderColor: Colors.border,
    shadowColor: "#000",
    shadowOpacity: 0.06,
    shadowRadius: moderateScale(12),
    shadowOffset: { width: 0, height: moderateScale(6) },
    elevation: 3,
  },
  statValue: { fontSize: moderateScale(22), fontWeight: "800" },
  statLabel: { color: Colors.muted, marginTop: verticalScale(4) },
  listItem: { paddingVertical: verticalScale(10), flexDirection: "row", alignItems: "center", gap: horizontalScale(12) },
  listLine: { borderBottomWidth: StyleSheet.hairlineWidth, borderBottomColor: Colors.border },
  listTitle: { color: Colors.text, fontWeight: "700" },
  ghostBtn: {
    borderWidth: 1,
    borderColor: Colors.border,
    paddingHorizontal: horizontalScale(12),
    paddingVertical: verticalScale(8),
    borderRadius: moderateScale(10),
    backgroundColor: Colors.white,
  },
  ghostBtnText: { color: Colors.accent, fontWeight: "700" },
  badgeSoft: {
    borderWidth: 1,
    paddingHorizontal: horizontalScale(10),
    paddingVertical: verticalScale(6),
    borderRadius: 999,
  },
  badgeSoftText: { fontWeight: "700", fontSize: moderateScale(12) },
  stackBg: {
    height: moderateScale(12),
    backgroundColor: Colors.border,
    borderRadius: moderateScale(8),
    overflow: "hidden",
    flexDirection: "row",
  },
  stackPiece: { height: "100%" },
  tipCard: {
    backgroundColor: Colors.text,
    borderRadius: moderateScale(16),
    padding: moderateScale(16),
    flexDirection: "row",
    alignItems: "center",
    gap: horizontalScale(12),
    marginTop: verticalScale(8),
  },
  tipTitle: { color: Colors.white, fontWeight: "800", marginBottom: verticalScale(2) },
  tipText: { color: Colors.white, opacity: 0.9 },
  mutedText: { color: Colors.muted, fontSize: moderateScale(13) },
  centerRow: { flexDirection: "row", alignItems: "center", gap: horizontalScale(8) },
  rowBetween: { flexDirection: "row", justifyContent: "space-between", alignItems: "center" },
  modalHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: verticalScale(12),
  },
  modalTitle: {
    color: Colors.text,
    fontSize: moderateScale(16),
    fontWeight: "700",
  },
  filterBtn: {
    borderWidth: 1,
    borderColor: Colors.border,
    paddingHorizontal: horizontalScale(12),
    paddingVertical: verticalScale(6),
    borderRadius: moderateScale(8),
    backgroundColor: Colors.white,
  },
  filterBtnActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  filterBtnText: {
    color: Colors.text,
    fontWeight: "600",
    fontSize: moderateScale(13),
  },
  filterBtnTextActive: {
    color: Colors.white,
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
export default HomeScreenHealthConsultant