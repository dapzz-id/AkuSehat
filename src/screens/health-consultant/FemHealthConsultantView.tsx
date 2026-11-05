"use client"

import React, { useState, useEffect, useCallback, useMemo } from "react"
import {
  View,
  Text,
  StyleSheet,
  Pressable,
  ScrollView,
  TextInput,
  ActivityIndicator,
  RefreshControl,
  Modal,
  Alert,
} from "react-native"
import Icon from "react-native-vector-icons/Feather"
import api from "../../api/axiosConfig"
import { Colors } from "../../settings/Colors"
import { moderateScale, horizontalScale, verticalScale } from "../../utils/responsive"
import { getDivisiTitle } from "../../utils/authHelper"

export type DivisionFilterItem = {
  id: string | number
  divisi_name: string
  kategori?: string
  color_cover?: string
  total_female: number
}

export type HaidSummary = {
  total_wanita: number
  sedang_haid: number
  selesai: number
  warning: number
}

export type HaidRecordUser = {
  id: string | number
  nama: string
  nomor_induk?: string
  level?: string
  jk?: string
  divisi?: {
    id: string | number
    divisi_name: string
    kategori?: string
    color_cover?: string
  } | null
}

export type HaidRecordItem = {
  id: string | number
  id_user: string | number
  user: HaidRecordUser | null
  tanggal_mulai: string | null
  tanggal_selesai: string | null
  durasi_hari: number | null
  hari_ke: number | null
  status: "berlangsung" | "selesai" | string
  catatan: string | null
  is_overdue: boolean
  created_at: string | null
}

export type PeminjamanItem = {
  id: string | number
  user: {
    id: string | number
    nama: string
    nomor_induk?: string
    divisi?: string
  }
  tanggal_pinjam: string
  jumlah_pita: number
  estimasi_selesai_haid: string
  status: "menunggu" | "dipinjam" | "dikembalikan" | "terlambat" | "ditolak"
  tanggal_kembali?: string
  keterangan?: string
  created_at: string
  verified: boolean
}

interface Props {
  themeColor?: string
  onLateCountChange?: (count: number) => void
}

const MONTH_NAMES = [
  "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
  "Jul", "Agu", "Sep", "Okt", "Nov", "Des"
]

function formatIndoDate(dateStr: string | null): string {
  if (!dateStr) return "-"
  try {
    const parts = dateStr.split("-")
    if (parts.length === 3) {
      const year = parts[0]
      const monthIdx = parseInt(parts[1], 10) - 1
      const day = parseInt(parts[2], 10)
      const monthName = MONTH_NAMES[monthIdx] || parts[1]
      return `${day} ${monthName} ${year}`
    }
    return dateStr
  } catch {
    return dateStr
  }
}

export default function FemHealthConsultantView({ themeColor, onLateCountChange }: Props) {
  const primaryColor = themeColor || Colors.primary

  // Mode: "siklus" (Data Siklus Haid Siswi & Guru) or "pita" (Peminjaman Pita UKS)
  const [mainMode, setMainMode] = useState<"siklus" | "pita">("siklus")

  // ==========================================
  // SIKLUS MENSTRUASI STATES
  // ==========================================
  const [haidRecords, setHaidRecords] = useState<HaidRecordItem[]>([])
  const [divisions, setDivisions] = useState<DivisionFilterItem[]>([])
  const [haidSummary, setHaidSummary] = useState<HaidSummary>({
    total_wanita: 0,
    sedang_haid: 0,
    selesai: 0,
    warning: 0,
  })
  const [loadingHaid, setLoadingHaid] = useState(false)
  const [selectedDivisiId, setSelectedDivisiId] = useState<string | number | "all">("all")
  const [selectedHaidStatus, setSelectedHaidStatus] = useState<"semua" | "berlangsung" | "selesai" | "warning">("semua")
  const [haidSearchQuery, setHaidSearchQuery] = useState("")

  // ==========================================
  // PEMINJAMAN PITA UKS STATES
  // ==========================================
  const [peminjamanList, setPeminjamanList] = useState<PeminjamanItem[]>([])
  const [warningList, setWarningList] = useState<PeminjamanItem[]>([])
  const [loadingPita, setLoadingPita] = useState(false)
  const [activePitaSubTab, setActivePitaSubTab] = useState<"aktif" | "verifikasi" | "warning">("aktif")
  const [pitaSearchQuery, setPitaSearchQuery] = useState("")

  // Modal Actions (Pita)
  const [selectedPitaItem, setSelectedPitaItem] = useState<PeminjamanItem | null>(null)
  const [actionLoading, setActionLoading] = useState(false)
  const [confirmModalType, setConfirmModalType] = useState<"verify" | "reject" | "return" | null>(null)
  const [rejectReason, setRejectReason] = useState("")

  // Modal Catat Pita
  const [isCatatModalOpen, setIsCatatModalOpen] = useState(false)
  const [haidMembers, setHaidMembers] = useState<any[]>([])
  const [loadingHaidMembers, setLoadingHaidMembers] = useState(false)
  const [selectedHaidUserId, setSelectedHaidUserId] = useState<string | number>("")
  const [catatTanggalPinjam, setCatatTanggalPinjam] = useState(new Date().toISOString().split("T")[0])
  const [catatKeterangan, setCatatKeterangan] = useState("")
  const [catatSubmitting, setCatatSubmitting] = useState(false)

  const [refreshing, setRefreshing] = useState(false)

  // ==========================================
  // FETCHERS
  // ==========================================
  const fetchHaidData = useCallback(async (isSilent = false) => {
    try {
      if (!isSilent) setLoadingHaid(true)
      const params: any = {}
      if (selectedDivisiId !== "all") {
        params.divisi_id = selectedDivisiId
      }
      if (selectedHaidStatus !== "semua") {
        params.status = selectedHaidStatus
      }
      if (haidSearchQuery.trim()) {
        params.search = haidSearchQuery.trim()
      }

      const res = await api.get("/health-monitor/haid-records", { params })
      if (res?.data?.status === "success") {
        setHaidRecords(res.data.data || [])
        setDivisions(res.data.divisions || [])
        const summaryData = res.data.summary || {
          total_wanita: 0,
          sedang_haid: 0,
          selesai: 0,
          warning: 0,
        }
        setHaidSummary(summaryData)

        if (onLateCountChange) {
          onLateCountChange((warningList.length) + (summaryData.warning || 0))
        }
      }
    } catch (e: any) {
      console.warn("Gagal memuat data siklus haid:", e)
    } finally {
      setLoadingHaid(false)
    }
  }, [selectedDivisiId, selectedHaidStatus, haidSearchQuery, onLateCountChange, warningList.length])

  const fetchPitaData = useCallback(async (isSilent = false) => {
    try {
      if (!isSilent) setLoadingPita(true)
      const [resPeminjaman, resWarning] = await Promise.all([
        api.get("/health-monitor"),
        api.get("/health-monitor/warning"),
      ])

      const list: PeminjamanItem[] = resPeminjaman?.data?.data || []
      const warnings: PeminjamanItem[] = resWarning?.data?.data || []

      setPeminjamanList(list)
      setWarningList(warnings)

      if (onLateCountChange) {
        onLateCountChange(warnings.length + (haidSummary.warning || 0))
      }
    } catch (e: any) {
      console.warn("Gagal memuat data peminjaman pita:", e)
    } finally {
      setLoadingPita(false)
    }
  }, [onLateCountChange, haidSummary.warning])

  const loadAll = useCallback(async () => {
    await Promise.all([fetchHaidData(false), fetchPitaData(false)])
  }, [fetchHaidData, fetchPitaData])

  useEffect(() => {
    loadAll()
  }, [])

  // Refetch haid records on filters change
  useEffect(() => {
    fetchHaidData(false)
  }, [selectedDivisiId, selectedHaidStatus])

  const onRefresh = async () => {
    setRefreshing(true)
    await Promise.all([fetchHaidData(true), fetchPitaData(true)])
    setRefreshing(false)
  }

  // ==========================================
  // PITA ACTIONS
  // ==========================================
  const handleOpenCatatPita = async () => {
    setIsCatatModalOpen(true)
    setSelectedHaidUserId("")
    setCatatKeterangan("")
    try {
      setLoadingHaidMembers(true)
      const res = await api.get("/health-monitor/haid-members-for-pita")
      const list = res?.data?.data || []
      setHaidMembers(list)
    } catch (e: any) {
      console.warn("Gagal memuat siswi sedang haid:", e)
      Alert.alert("Perhatian", "Gagal memuat daftar siswi sedang haid.")
    } finally {
      setLoadingHaidMembers(false)
    }
  }

  const handleSimpanPeminjaman = async () => {
    if (!selectedHaidUserId) {
      Alert.alert("Validasi", "Pilih siswi yang sedang haid terlebih dahulu.")
      return
    }
    try {
      setCatatSubmitting(true)
      await api.post("/health-monitor", {
        id_user: selectedHaidUserId,
        tanggal_pinjam: catatTanggalPinjam,
        keterangan: catatKeterangan.trim() || "Peminjaman langsung via Aplikasi Petugas",
      })
      Alert.alert("Sukses", "Peminjaman pita berhasil dicatat.")
      setIsCatatModalOpen(false)
      setSelectedHaidUserId("")
      setCatatKeterangan("")
      await fetchPitaData(true)
    } catch (e: any) {
      Alert.alert("Gagal", e?.response?.data?.message || "Gagal mencatat peminjaman pita.")
    } finally {
      setCatatSubmitting(false)
    }
  }

  const handleVerifyPita = async () => {
    if (!selectedPitaItem) return
    try {
      setActionLoading(true)
      await api.put(`/health-monitor/${selectedPitaItem.id}/accept-verifikasi`)
      Alert.alert("Berhasil", "Permintaan peminjaman pita berhasil disetujui.")
      setConfirmModalType(null)
      setSelectedPitaItem(null)
      await fetchPitaData(true)
    } catch (e: any) {
      Alert.alert("Gagal", e?.response?.data?.message || "Gagal menyetujui peminjaman.")
    } finally {
      setActionLoading(false)
    }
  }

  const handleRejectPita = async () => {
    if (!selectedPitaItem) return
    try {
      setActionLoading(true)
      await api.put(`/health-monitor/${selectedPitaItem.id}/reject-verifikasi`, {
        alasan: rejectReason.trim() || undefined,
      })
      Alert.alert("Ditolak", "Permintaan peminjaman pita telah ditolak.")
      setConfirmModalType(null)
      setSelectedPitaItem(null)
      setRejectReason("")
      await fetchPitaData(true)
    } catch (e: any) {
      Alert.alert("Gagal", e?.response?.data?.message || "Gagal menolak permohonan.")
    } finally {
      setActionLoading(false)
    }
  }

  const handleReturnPita = async () => {
    if (!selectedPitaItem) return
    try {
      setActionLoading(true)
      await api.put(`/health-monitor/${selectedPitaItem.id}`, {
        status: "dikembalikan",
        tanggal_kembali: new Date().toISOString().split("T")[0],
      })
      Alert.alert("Selesai", "Pita UKS telah dikembalikan.")
      setConfirmModalType(null)
      setSelectedPitaItem(null)
      await fetchPitaData(true)
    } catch (e: any) {
      Alert.alert("Gagal", e?.response?.data?.message || "Gagal memproses pengembalian.")
    } finally {
      setActionLoading(false)
    }
  }

  // Filtered pita list
  const getFilteredPitaList = () => {
    let base = peminjamanList
    if (activePitaSubTab === "verifikasi") {
      base = peminjamanList.filter((p) => p.status === "menunggu" || !p.verified)
    } else if (activePitaSubTab === "warning") {
      base = warningList
    } else {
      base = peminjamanList.filter((p) => p.status === "dipinjam" || p.status === "terlambat")
    }

    if (pitaSearchQuery.trim()) {
      const q = pitaSearchQuery.toLowerCase().trim()
      base = base.filter(
        (p) =>
          p.user?.nama?.toLowerCase().includes(q) ||
          p.user?.nomor_induk?.toLowerCase().includes(q)
      )
    }
    return base
  }

  const pitaWaitingCount = peminjamanList.filter((p) => p.status === "menunggu" || !p.verified).length
  const pitaActiveCount = peminjamanList.filter((p) => p.status === "dipinjam").length
  const displayedPitaList = getFilteredPitaList()

  // Selected Division Name for Siklus view header
  const selectedDivisiName = useMemo(() => {
    if (selectedDivisiId === "all") return "Semua Divisi"
    const found = divisions.find((d) => String(d.id) === String(selectedDivisiId))
    return found ? found.divisi_name : "Divisi Terpilih"
  }, [selectedDivisiId, divisions])

  return (
    <ScrollView
      showsVerticalScrollIndicator={false}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[primaryColor, "#DB2777"]} />
      }
      contentContainerStyle={{ paddingBottom: verticalScale(35), gap: verticalScale(14) }}
    >
      {/* Header Banner */}
      <View style={styles.headerBox}>
        <View style={[styles.headerIcon, { backgroundColor: "#FCE7F3" }]}>
          <Icon name="heart" size={22} color="#DB2777" />
        </View>
        <View style={{ flex: 1 }}>
          <Text style={styles.headerTitle}>Siklus Menstruasi & Kesehatan Reproduksi</Text>
          <Text style={styles.headerSubtitle}>
            Pantau status siklus siswi & guru perempuan per divisi, deteksi keterlambatan haid (&gt; 7 hari), serta kelola peminjaman pita UKS.
          </Text>
        </View>
      </View>

      {/* Main Mode Pill Switcher */}
      <View style={styles.mainSwitcherRow}>
        <Pressable
          onPress={() => setMainMode("siklus")}
          style={[styles.mainSwitcherBtn, mainMode === "siklus" && styles.mainSwitcherBtnActive]}
        >
          <Icon
            name="calendar"
            size={15}
            color={mainMode === "siklus" ? "#DB2777" : "#64748B"}
          />
          <Text
            style={[
              styles.mainSwitcherText,
              mainMode === "siklus" && styles.mainSwitcherTextActive,
            ]}
          >
            Siklus Menstruasi ({haidRecords.length})
          </Text>
        </Pressable>

        <Pressable
          onPress={() => setMainMode("pita")}
          style={[styles.mainSwitcherBtn, mainMode === "pita" && styles.mainSwitcherBtnActive]}
        >
          <Icon
            name="bookmark"
            size={15}
            color={mainMode === "pita" ? "#DB2777" : "#64748B"}
          />
          <Text
            style={[
              styles.mainSwitcherText,
              mainMode === "pita" && styles.mainSwitcherTextActive,
            ]}
          >
            Peminjaman Pita ({peminjamanList.length})
          </Text>
        </Pressable>
      </View>

      {/* ================================================================= */}
      {/* SECTION 1: SIKLUS MENSTRUASI SISWI & GURU PER DIVISI              */}
      {/* ================================================================= */}
      {mainMode === "siklus" ? (
        <>
          {/* KPI Summary Cards */}
          <View style={styles.kpiRow}>
            <View style={styles.kpiCard}>
              <Text style={[styles.kpiVal, { color: "#475569" }]}>{haidSummary.total_wanita}</Text>
              <Text style={styles.kpiLbl}>Total Wanita</Text>
            </View>
            <View style={styles.kpiCard}>
              <Text style={[styles.kpiVal, { color: "#DB2777" }]}>{haidSummary.sedang_haid}</Text>
              <Text style={styles.kpiLbl}>Sedang Haid</Text>
            </View>
            <View style={styles.kpiCard}>
              <Text style={[styles.kpiVal, { color: "#059669" }]}>{haidSummary.selesai}</Text>
              <Text style={styles.kpiLbl}>Selesai</Text>
            </View>
            <View style={styles.kpiCard}>
              <Text style={[styles.kpiVal, { color: "#DC2626" }]}>{haidSummary.warning}</Text>
              <Text style={styles.kpiLbl}>Warning &gt;7hr</Text>
            </View>
          </View>

          {/* Filter Divisi (Horizontal Chips) */}
          <View style={styles.filterSection}>
            <View style={styles.filterHeaderRow}>
              <View style={{ flexDirection: "row", alignItems: "center", gap: 6 }}>
                <Icon name="layers" size={15} color="#475569" />
                <Text style={styles.filterSectionTitle}>Filter Divisi / Kelas</Text>
              </View>
              <Text style={styles.filterSelectedText}>{selectedDivisiName}</Text>
            </View>

            <ScrollView
              horizontal
              showsHorizontalScrollIndicator={false}
              contentContainerStyle={styles.horizontalChipsContainer}
            >
              {/* Chip: Semua Divisi */}
              <Pressable
                onPress={() => setSelectedDivisiId("all")}
                style={[
                  styles.divisiChip,
                  selectedDivisiId === "all" && styles.divisiChipActive,
                ]}
              >
                <Text
                  style={[
                    styles.divisiChipText,
                    selectedDivisiId === "all" && styles.divisiChipTextActive,
                  ]}
                >
                  Semua Divisi ({haidSummary.total_wanita})
                </Text>
              </Pressable>

              {/* Dynamic Division Chips */}
              {divisions.map((d) => {
                const isSelected = String(selectedDivisiId) === String(d.id)
                return (
                  <Pressable
                    key={d.id}
                    onPress={() => setSelectedDivisiId(d.id)}
                    style={[
                      styles.divisiChip,
                      isSelected && styles.divisiChipActive,
                    ]}
                  >
                    {d.color_cover ? (
                      <View
                        style={[
                          styles.divisiColorDot,
                          { backgroundColor: d.color_cover },
                        ]}
                      />
                    ) : null}
                    <Text
                      style={[
                        styles.divisiChipText,
                        isSelected && styles.divisiChipTextActive,
                      ]}
                    >
                      {d.divisi_name} ({d.total_female})
                    </Text>
                  </Pressable>
                )
              })}
            </ScrollView>
          </View>

          {/* Filter Status Siklus Haid */}
          <View style={styles.statusChipsRow}>
            <Pressable
              onPress={() => setSelectedHaidStatus("semua")}
              style={[
                styles.statusChipBtn,
                selectedHaidStatus === "semua" && styles.statusChipBtnActive,
              ]}
            >
              <Text
                style={[
                  styles.statusChipText,
                  selectedHaidStatus === "semua" && styles.statusChipTextActive,
                ]}
              >
                Semua Status
              </Text>
            </Pressable>
            <Pressable
              onPress={() => setSelectedHaidStatus("berlangsung")}
              style={[
                styles.statusChipBtn,
                selectedHaidStatus === "berlangsung" && styles.statusChipBtnActive,
              ]}
            >
              <Text
                style={[
                  styles.statusChipText,
                  selectedHaidStatus === "berlangsung" && styles.statusChipTextActive,
                ]}
              >
                Sedang Haid ({haidSummary.sedang_haid})
              </Text>
            </Pressable>
            <Pressable
              onPress={() => setSelectedHaidStatus("selesai")}
              style={[
                styles.statusChipBtn,
                selectedHaidStatus === "selesai" && styles.statusChipBtnActive,
              ]}
            >
              <Text
                style={[
                  styles.statusChipText,
                  selectedHaidStatus === "selesai" && styles.statusChipTextActive,
                ]}
              >
                Selesai ({haidSummary.selesai})
              </Text>
            </Pressable>
            <Pressable
              onPress={() => setSelectedHaidStatus("warning")}
              style={[
                styles.statusChipBtn,
                selectedHaidStatus === "warning" && styles.statusChipBtnActiveWarning,
              ]}
            >
              <Text
                style={[
                  styles.statusChipText,
                  selectedHaidStatus === "warning" && styles.statusChipTextActiveWarning,
                ]}
              >
                &gt;7 Hari ({haidSummary.warning})
              </Text>
            </Pressable>
          </View>

          {/* Search Bar Siklus */}
          <View style={styles.searchRow}>
            <Icon name="search" size={16} color="#94A3B8" style={{ marginLeft: 12 }} />
            <TextInput
              value={haidSearchQuery}
              onChangeText={setHaidSearchQuery}
              onSubmitEditing={() => fetchHaidData(false)}
              placeholder="Cari nama siswi / guru / nomor induk..."
              placeholderTextColor="#94A3B8"
              style={styles.searchInput}
              returnKeyType="search"
            />
            {haidSearchQuery.length > 0 && (
              <Pressable
                onPress={() => {
                  setHaidSearchQuery("")
                  fetchHaidData(false)
                }}
                style={{ padding: 8 }}
              >
                <Icon name="x" size={14} color="#94A3B8" />
              </Pressable>
            )}
            <Pressable
              onPress={() => fetchHaidData(false)}
              style={[styles.searchActionBtn, { backgroundColor: primaryColor }]}
            >
              <Icon name="arrow-right" size={14} color="#FFF" />
            </Pressable>
          </View>

          {/* Haid Records List */}
          <View style={{ gap: verticalScale(10) }}>
            {loadingHaid && !refreshing ? (
              <View style={styles.loadingBox}>
                <ActivityIndicator size="small" color={primaryColor} />
                <Text style={styles.loadingText}>Memuat log siklus menstruasi...</Text>
              </View>
            ) : haidRecords.length === 0 ? (
              <View style={styles.emptyCard}>
                <Icon name="inbox" size={38} color="#CBD5E1" />
                <Text style={styles.emptyTitle}>Tidak Ada Data Siklus Haid</Text>
                <Text style={styles.emptyText}>
                  {selectedDivisiId !== "all" || selectedHaidStatus !== "semua" || haidSearchQuery
                    ? "Tidak ada data yang cocok dengan filter atau pencarian Anda."
                    : "Belum ada siswi atau guru perempuan yang mencatat siklus menstruasi."}
                </Text>
              </View>
            ) : (
              haidRecords.map((item) => {
                const isOverdue = item.is_overdue
                const isBerlangsung = item.status === "berlangsung"
                const isGuru = item.user?.level?.toLowerCase() === "guru"
                const divisiObj = item.user?.divisi
                const divisiTitle = getDivisiTitle(divisiObj?.divisi_name) || "Tanpa Divisi"

                return (
                  <View
                    key={item.id}
                    style={[
                      styles.haidCard,
                      isOverdue && { borderColor: "#FECDD3", backgroundColor: "#FFF5F5" },
                    ]}
                  >
                    {/* Header Row: User Info + Status Badge */}
                    <View style={styles.cardHeaderRow}>
                      <View style={{ flex: 1 }}>
                        <View style={{ flexDirection: "row", alignItems: "center", gap: 6, flexWrap: "wrap" }}>
                          <Text style={styles.studentName}>{item.user?.nama || "Member"}</Text>
                          {/* Role Badge: Siswi vs Guru */}
                          <View
                            style={[
                              styles.roleBadge,
                              isGuru
                                ? { backgroundColor: "#EDE9FE", borderColor: "#DDD6FE" }
                                : { backgroundColor: "#E0F2FE", borderColor: "#BAE6FD" },
                            ]}
                          >
                            <Text
                              style={[
                                styles.roleBadgeText,
                                isGuru ? { color: "#6D28D9" } : { color: "#0369A1" },
                              ]}
                            >
                              {isGuru ? "Guru" : "Siswi"}
                            </Text>
                          </View>
                        </View>

                        {/* Sub Row: NIS/NIP + Divisi */}
                        <View style={styles.metaRow}>
                          {item.user?.nomor_induk ? (
                            <Text style={styles.metaText}>
                              {isGuru ? "NIP" : "NIS"}: {item.user.nomor_induk}
                            </Text>
                          ) : null}
                          <View
                            style={[
                              styles.divisiBadge,
                              divisiObj?.color_cover
                                ? { borderColor: divisiObj.color_cover, borderWidth: 1 }
                                : null,
                            ]}
                          >
                            {divisiObj?.color_cover ? (
                              <View
                                style={[
                                  styles.divisiColorDot,
                                  { backgroundColor: divisiObj.color_cover, width: 6, height: 6 },
                                ]}
                              />
                            ) : null}
                            <Text style={styles.divisiText}>{divisiTitle}</Text>
                          </View>
                        </View>
                      </View>

                      {/* Status Pill */}
                      <View
                        style={[
                          styles.statusPill,
                          isOverdue
                            ? { backgroundColor: "#FEE2E2" }
                            : isBerlangsung
                            ? { backgroundColor: "#FCE7F3" }
                            : { backgroundColor: "#D1FAE5" },
                        ]}
                      >
                        <Text
                          style={[
                            styles.statusPillText,
                            isOverdue
                              ? { color: "#B91C1C" }
                              : isBerlangsung
                              ? { color: "#DB2777" }
                              : { color: "#047857" },
                          ]}
                        >
                          {isBerlangsung
                            ? `Sedang Haid • H-${item.hari_ke || 1}`
                            : `Selesai (${item.durasi_hari || "-"} Hari)`}
                        </Text>
                      </View>
                    </View>

                    {/* Timeline & Durasi Box */}
                    <View style={styles.detailBox}>
                      <View style={styles.detailItem}>
                        <Text style={styles.detailLbl}>Mulai Haid</Text>
                        <Text style={styles.detailVal}>{formatIndoDate(item.tanggal_mulai)}</Text>
                      </View>
                      <View style={styles.detailItem}>
                        <Text style={styles.detailLbl}>Selesai Haid</Text>
                        <Text
                          style={[
                            styles.detailVal,
                            isBerlangsung && { color: "#DB2777", fontWeight: "700" },
                          ]}
                        >
                          {isBerlangsung ? "Sedang Berlangsung" : formatIndoDate(item.tanggal_selesai)}
                        </Text>
                      </View>
                      <View style={styles.detailItem}>
                        <Text style={styles.detailLbl}>Total Durasi</Text>
                        <Text
                          style={[
                            styles.detailVal,
                            isOverdue && { color: "#DC2626", fontWeight: "800" },
                          ]}
                        >
                          {isBerlangsung ? `${item.hari_ke || 1} Hari` : `${item.durasi_hari || 0} Hari`}
                        </Text>
                      </View>
                    </View>

                    {/* Warning Overdue Banner (> 7 Hari) */}
                    {isOverdue && (
                      <View style={styles.warningBanner}>
                        <Icon name="alert-triangle" size={14} color="#B91C1C" />
                        <Text style={styles.warningBannerText}>
                          Durasi siklus telah mencapai {item.hari_ke} hari (&gt; 7 hari). Lakukan pemantauan kesehatan atau berikan anjuran konsultasi medis di SafeSpace / UKS.
                        </Text>
                      </View>
                    )}

                    {/* Catatan / Keluhan Pengguna */}
                    {item.catatan ? (
                      <View style={styles.catatanBox}>
                        <Icon name="message-circle" size={12} color="#64748B" style={{ marginTop: 2 }} />
                        <Text style={styles.catatanText}>
                          <Text style={{ fontWeight: "700", color: "#475569" }}>Catatan: </Text>
                          {item.catatan}
                        </Text>
                      </View>
                    ) : null}
                  </View>
                )
              })
            )}
          </View>
        </>
      ) : (
        /* ================================================================= */
        /* SECTION 2: PEMINJAMAN PITA UKS (EXISTING LOGIC PRESERVED)         */
        /* ================================================================= */
        <>
          {/* Action Catat Peminjaman Langsung */}
          <Pressable
            onPress={handleOpenCatatPita}
            style={[styles.catatActionBtn, { backgroundColor: primaryColor }]}
          >
            <Icon name="plus-circle" size={16} color="#FFF" />
            <Text style={styles.catatActionText}>Catat Peminjaman Pita UKS</Text>
          </Pressable>

          {/* KPI Cards Pita */}
          <View style={styles.kpiRow}>
            <View style={styles.kpiCard}>
              <Text style={[styles.kpiVal, { color: "#DB2777" }]}>{pitaActiveCount}</Text>
              <Text style={styles.kpiLbl}>Pinjaman Aktif</Text>
            </View>
            <View style={styles.kpiCard}>
              <Text style={[styles.kpiVal, { color: "#D97706" }]}>{pitaWaitingCount}</Text>
              <Text style={styles.kpiLbl}>Menunggu Verif</Text>
            </View>
            <View style={styles.kpiCard}>
              <Text style={[styles.kpiVal, { color: "#DC2626" }]}>{warningList.length}</Text>
              <Text style={styles.kpiLbl}>Warning Pita</Text>
            </View>
          </View>

          {/* Sub Tabs Pita */}
          <View style={styles.subTabRow}>
            <Pressable
              onPress={() => setActivePitaSubTab("aktif")}
              style={[styles.subTabBtn, activePitaSubTab === "aktif" && styles.subTabBtnActive]}
            >
              <Text style={[styles.subTabText, activePitaSubTab === "aktif" && styles.subTabTextActive]}>
                Pita Aktif ({pitaActiveCount})
              </Text>
            </Pressable>
            <Pressable
              onPress={() => setActivePitaSubTab("verifikasi")}
              style={[styles.subTabBtn, activePitaSubTab === "verifikasi" && styles.subTabBtnActive]}
            >
              <Text style={[styles.subTabText, activePitaSubTab === "verifikasi" && styles.subTabTextActive]}>
                Verifikasi ({pitaWaitingCount})
              </Text>
            </Pressable>
            <Pressable
              onPress={() => setActivePitaSubTab("warning")}
              style={[styles.subTabBtn, activePitaSubTab === "warning" && styles.subTabBtnActive]}
            >
              <Text style={[styles.subTabText, activePitaSubTab === "warning" && styles.subTabTextActive]}>
                Warning ({warningList.length})
              </Text>
            </Pressable>
          </View>

          {/* Search Bar Pita */}
          <View style={styles.searchRow}>
            <Icon name="search" size={16} color="#94A3B8" style={{ marginLeft: 12 }} />
            <TextInput
              value={pitaSearchQuery}
              onChangeText={setPitaSearchQuery}
              placeholder="Cari nama peminjam / nomor induk..."
              placeholderTextColor="#94A3B8"
              style={styles.searchInput}
            />
            {pitaSearchQuery.length > 0 && (
              <Pressable onPress={() => setPitaSearchQuery("")} style={{ padding: 8 }}>
                <Icon name="x" size={14} color="#94A3B8" />
              </Pressable>
            )}
          </View>

          {/* Cards List Pita */}
          <View style={{ gap: verticalScale(10) }}>
            {loadingPita && !refreshing ? (
              <ActivityIndicator size="small" color={primaryColor} style={{ marginTop: 20 }} />
            ) : displayedPitaList.length === 0 ? (
              <View style={styles.emptyCard}>
                <Icon name="check-circle" size={36} color="#CBD5E1" />
                <Text style={styles.emptyTitle}>Tidak Ada Data Peminjaman</Text>
                <Text style={styles.emptyText}>Semua pita UKS tertib dan tidak ada permohonan tertunda.</Text>
              </View>
            ) : (
              displayedPitaList.map((item) => {
                const isLate = item.status === "terlambat" || activePitaSubTab === "warning"
                const isPending = item.status === "menunggu" || !item.verified
                return (
                  <View key={item.id} style={[styles.haidCard, isLate && { borderColor: "#FECDD3" }]}>
                    <View style={styles.cardHeaderRow}>
                      <View style={{ flex: 1 }}>
                        <Text style={styles.studentName}>{item.user?.nama || "Siswi"}</Text>
                        <View style={styles.metaRow}>
                          {item.user?.nomor_induk ? (
                            <Text style={styles.metaText}>NIS: {item.user.nomor_induk}</Text>
                          ) : null}
                          {getDivisiTitle(item.user?.divisi) ? (
                            <View style={styles.divisiBadge}>
                              <Text style={styles.divisiText}>{getDivisiTitle(item.user?.divisi)}</Text>
                            </View>
                          ) : null}
                        </View>
                      </View>
                      <View
                        style={[
                          styles.statusPill,
                          isLate
                            ? { backgroundColor: "#FEE2E2" }
                            : isPending
                            ? { backgroundColor: "#FEF3C7" }
                            : { backgroundColor: "#FCE7F3" },
                        ]}
                      >
                        <Text
                          style={[
                            styles.statusPillText,
                            isLate
                              ? { color: "#B91C1C" }
                              : isPending
                              ? { color: "#D97706" }
                              : { color: "#DB2777" },
                          ]}
                        >
                          {isLate ? "Terlambat > 7 Hari" : isPending ? "Menunggu Verif" : "Dipinjam"}
                        </Text>
                      </View>
                    </View>

                    {/* Details */}
                    <View style={styles.detailBox}>
                      <View style={styles.detailItem}>
                        <Text style={styles.detailLbl}>Tgl Pinjam</Text>
                        <Text style={styles.detailVal}>{formatIndoDate(item.tanggal_pinjam)}</Text>
                      </View>
                      <View style={styles.detailItem}>
                        <Text style={styles.detailLbl}>Estimasi Selesai</Text>
                        <Text style={[styles.detailVal, isLate && { color: "#DC2626", fontWeight: "700" }]}>
                          {formatIndoDate(item.estimasi_selesai_haid)}
                        </Text>
                      </View>
                      <View style={styles.detailItem}>
                        <Text style={styles.detailLbl}>Jumlah Pita</Text>
                        <Text style={styles.detailVal}>{item.jumlah_pita || 1} pcs</Text>
                      </View>
                    </View>

                    {item.keterangan ? (
                      <Text style={styles.notesText}>Keterangan: {item.keterangan}</Text>
                    ) : null}

                    {/* Action Buttons */}
                    <View style={styles.actionRow}>
                      {isPending ? (
                        <>
                          <Pressable
                            onPress={() => {
                              setSelectedPitaItem(item)
                              setConfirmModalType("verify")
                            }}
                            style={[styles.btnAction, { backgroundColor: primaryColor }]}
                          >
                            <Icon name="check" size={13} color="#FFF" />
                            <Text style={styles.btnActionText}>Setujui Pinjam</Text>
                          </Pressable>
                          <Pressable
                            onPress={() => {
                              setSelectedPitaItem(item)
                              setConfirmModalType("reject")
                            }}
                            style={[styles.btnAction, { backgroundColor: "#FEE2E2" }]}
                          >
                            <Icon name="x" size={13} color="#DC2626" />
                            <Text style={[styles.btnActionText, { color: "#DC2626" }]}>Tolak</Text>
                          </Pressable>
                        </>
                      ) : item.status === "dipinjam" || isLate ? (
                        <Pressable
                          onPress={() => {
                            setSelectedPitaItem(item)
                            setConfirmModalType("return")
                          }}
                          style={[styles.btnAction, { backgroundColor: "#0284C7" }]}
                        >
                          <Icon name="corner-down-left" size={13} color="#FFF" />
                          <Text style={styles.btnActionText}>Terima Pengembalian</Text>
                        </Pressable>
                      ) : null}
                    </View>
                  </View>
                )
              })
            )}
          </View>
        </>
      )}

      {/* ================================================================= */}
      {/* MODAL: CATAT PEMINJAMAN PITA UKS                                  */}
      {/* ================================================================= */}
      <Modal visible={isCatatModalOpen} transparent animationType="fade">
        <View style={styles.modalBackdrop}>
          <View style={styles.modalBox}>
            <View style={styles.modalHeaderRow}>
              <View style={{ flexDirection: "row", alignItems: "center", gap: 8 }}>
                <Icon name="bookmark" size={18} color="#DB2777" />
                <Text style={styles.modalTitle}>Catat Pinjam Pita UKS</Text>
              </View>
              <Pressable onPress={() => setIsCatatModalOpen(false)}>
                <Icon name="x" size={18} color="#94A3B8" />
              </Pressable>
            </View>

            {loadingHaidMembers ? (
              <View style={{ padding: 20, alignItems: "center" }}>
                <ActivityIndicator size="small" color={primaryColor} />
                <Text style={{ fontSize: 11, color: "#64748B", marginTop: 8 }}>
                  Memuat data siswi yang sedang haid...
                </Text>
              </View>
            ) : haidMembers.length === 0 ? (
              <View style={styles.haidEmptyAlert}>
                <Icon name="alert-circle" size={20} color="#D97706" />
                <Text style={styles.haidEmptyAlertTitle}>Belum Ada Siswi Sedang Haid</Text>
                <Text style={styles.haidEmptyAlertDesc}>
                  Peminjaman pita hanya dapat dicatat jika siswi telah berstatus Mulai Haid dan belum memiliki pita yang sedang dipinjam.
                </Text>
              </View>
            ) : (
              <View style={{ gap: 10, marginTop: 10 }}>
                <Text style={styles.inputLabel}>
                  Pilih Siswi (Sedang Haid Aktif) <Text style={{ color: "#EF4444" }}>*</Text>
                </Text>
                <ScrollView style={{ maxHeight: 180 }} nestedScrollEnabled>
                  {haidMembers.map((m: any) => {
                    const isSelected = selectedHaidUserId === m.id
                    return (
                      <Pressable
                        key={m.id}
                        onPress={() => setSelectedHaidUserId(m.id)}
                        style={[
                          styles.memberOptionCard,
                          isSelected && { borderColor: primaryColor, backgroundColor: "#FCE7F3" },
                        ]}
                      >
                        <View style={{ flex: 1 }}>
                          <Text style={[styles.memberName, isSelected && { color: primaryColor, fontWeight: "700" }]}>
                            {m.nama}
                          </Text>
                          <Text style={styles.memberSub}>
                            {m.divisi?.divisi_name || "Tanpa Kelas"} {m.nomor_induk ? `• NIS: ${m.nomor_induk}` : ""}
                          </Text>
                        </View>
                        {isSelected && <Icon name="check-circle" size={16} color={primaryColor} />}
                      </Pressable>
                    )
                  })}
                </ScrollView>

                <View>
                  <Text style={styles.inputLabel}>Tanggal Pinjam</Text>
                  <TextInput
                    value={catatTanggalPinjam}
                    onChangeText={setCatatTanggalPinjam}
                    placeholder="YYYY-MM-DD"
                    placeholderTextColor="#94A3B8"
                    style={styles.modalInput}
                  />
                </View>

                <View>
                  <Text style={styles.inputLabel}>Keterangan Tambahan</Text>
                  <TextInput
                    value={catatKeterangan}
                    onChangeText={setCatatKeterangan}
                    placeholder="Contoh: Dipinjam saat jam olahraga / UKS"
                    placeholderTextColor="#94A3B8"
                    style={styles.modalInput}
                  />
                </View>
              </View>
            )}

            <View style={[styles.modalBtnRow, { marginTop: 16 }]}>
              <Pressable
                onPress={() => setIsCatatModalOpen(false)}
                style={styles.modalCancelBtn}
              >
                <Text style={styles.modalCancelText}>Batal</Text>
              </Pressable>
              {haidMembers.length > 0 && (
                <Pressable
                  disabled={catatSubmitting || !selectedHaidUserId}
                  onPress={handleSimpanPeminjaman}
                  style={[
                    styles.modalConfirmBtn,
                    { backgroundColor: primaryColor },
                    (!selectedHaidUserId || catatSubmitting) && { opacity: 0.5 },
                  ]}
                >
                  {catatSubmitting ? (
                    <ActivityIndicator size="small" color="#FFF" />
                  ) : (
                    <Text style={styles.modalConfirmText}>Simpan Peminjaman</Text>
                  )}
                </Pressable>
              )}
            </View>
          </View>
        </View>
      </Modal>

      {/* ================================================================= */}
      {/* CONFIRMATION MODAL (VERIFY / REJECT / RETURN)                     */}
      {/* ================================================================= */}
      <Modal visible={confirmModalType !== null} transparent animationType="fade">
        <View style={styles.modalBackdrop}>
          <View style={styles.modalBox}>
            <Text style={styles.modalTitle}>
              {confirmModalType === "verify"
                ? "Setujui Peminjaman Pita"
                : confirmModalType === "reject"
                ? "Tolak Peminjaman Pita"
                : "Konfirmasi Pengembalian"}
            </Text>
            <Text style={styles.modalSubtitle}>
              {confirmModalType === "verify"
                ? `Verifikasi peminjaman pita untuk ${selectedPitaItem?.user?.nama}?`
                : confirmModalType === "reject"
                ? `Alasan penolakan permohonan ${selectedPitaItem?.user?.nama}:`
                : `Pastikan pita dari ${selectedPitaItem?.user?.nama} sudah diserahkan kembali ke UKS.`}
            </Text>

            {confirmModalType === "reject" && (
              <TextInput
                value={rejectReason}
                onChangeText={setRejectReason}
                placeholder="Contoh: Stok pita habis / salah input tanggal..."
                placeholderTextColor="#94A3B8"
                style={styles.modalInput}
              />
            )}

            <View style={styles.modalBtnRow}>
              <Pressable
                onPress={() => {
                  setConfirmModalType(null)
                  setSelectedPitaItem(null)
                }}
                style={styles.modalCancelBtn}
              >
                <Text style={styles.modalCancelText}>Batal</Text>
              </Pressable>
              <Pressable
                disabled={actionLoading}
                onPress={() => {
                  if (confirmModalType === "verify") handleVerifyPita()
                  else if (confirmModalType === "reject") handleRejectPita()
                  else if (confirmModalType === "return") handleReturnPita()
                }}
                style={[
                  styles.modalConfirmBtn,
                  confirmModalType === "reject"
                    ? { backgroundColor: "#DC2626" }
                    : { backgroundColor: primaryColor },
                ]}
              >
                {actionLoading ? (
                  <ActivityIndicator size="small" color="#FFF" />
                ) : (
                  <Text style={styles.modalConfirmText}>Ya, Lanjutkan</Text>
                )}
              </Pressable>
            </View>
          </View>
        </View>
      </Modal>
    </ScrollView>
  )
}

const styles = StyleSheet.create({
  headerBox: {
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
    backgroundColor: "#FFF",
    padding: moderateScale(14),
    borderRadius: moderateScale(16),
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  headerIcon: {
    width: horizontalScale(44),
    height: horizontalScale(44),
    borderRadius: moderateScale(14),
    alignItems: "center",
    justifyContent: "center",
  },
  headerTitle: {
    fontSize: moderateScale(14),
    fontWeight: "800",
    color: "#0F172A",
  },
  headerSubtitle: {
    fontSize: moderateScale(11),
    color: "#64748B",
    marginTop: 2,
    lineHeight: 16,
  },
  mainSwitcherRow: {
    flexDirection: "row",
    backgroundColor: "#F1F5F9",
    padding: moderateScale(4),
    borderRadius: moderateScale(14),
    gap: 6,
  },
  mainSwitcherBtn: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 6,
    paddingVertical: verticalScale(10),
    borderRadius: moderateScale(10),
  },
  mainSwitcherBtnActive: {
    backgroundColor: "#FFF",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.08,
    shadowRadius: 2,
    elevation: 2,
  },
  mainSwitcherText: {
    fontSize: moderateScale(12),
    fontWeight: "600",
    color: "#64748B",
  },
  mainSwitcherTextActive: {
    color: "#DB2777",
    fontWeight: "700",
  },
  kpiRow: {
    flexDirection: "row",
    gap: 8,
  },
  kpiCard: {
    flex: 1,
    backgroundColor: "#FFF",
    padding: moderateScale(10),
    borderRadius: moderateScale(14),
    borderWidth: 1,
    borderColor: "#E2E8F0",
    alignItems: "center",
  },
  kpiVal: {
    fontSize: moderateScale(17),
    fontWeight: "800",
  },
  kpiLbl: {
    fontSize: moderateScale(9.5),
    color: "#64748B",
    marginTop: 2,
    textAlign: "center",
  },
  filterSection: {
    backgroundColor: "#FFF",
    borderRadius: moderateScale(14),
    padding: moderateScale(12),
    borderWidth: 1,
    borderColor: "#E2E8F0",
    gap: verticalScale(8),
  },
  filterHeaderRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },
  filterSectionTitle: {
    fontSize: moderateScale(12),
    fontWeight: "700",
    color: "#334155",
  },
  filterSelectedText: {
    fontSize: moderateScale(11),
    fontWeight: "600",
    color: "#DB2777",
  },
  horizontalChipsContainer: {
    flexDirection: "row",
    gap: 6,
    paddingVertical: 2,
  },
  divisiChip: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    backgroundColor: "#F8FAFC",
    paddingHorizontal: horizontalScale(10),
    paddingVertical: verticalScale(6),
    borderRadius: moderateScale(20),
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  divisiChipActive: {
    backgroundColor: "#FCE7F3",
    borderColor: "#F472B6",
  },
  divisiChipText: {
    fontSize: moderateScale(11),
    fontWeight: "600",
    color: "#475569",
  },
  divisiChipTextActive: {
    color: "#BE185D",
    fontWeight: "700",
  },
  divisiColorDot: {
    width: 8,
    height: 8,
    borderRadius: 4,
  },
  statusChipsRow: {
    flexDirection: "row",
    backgroundColor: "#FFF",
    padding: moderateScale(4),
    borderRadius: moderateScale(12),
    borderWidth: 1,
    borderColor: "#E2E8F0",
    gap: 4,
  },
  statusChipBtn: {
    flex: 1,
    paddingVertical: verticalScale(7),
    alignItems: "center",
    justifyContent: "center",
    borderRadius: moderateScale(8),
  },
  statusChipBtnActive: {
    backgroundColor: "#FCE7F3",
  },
  statusChipBtnActiveWarning: {
    backgroundColor: "#FEE2E2",
  },
  statusChipText: {
    fontSize: moderateScale(10.5),
    fontWeight: "600",
    color: "#64748B",
    textAlign: "center",
  },
  statusChipTextActive: {
    color: "#DB2777",
    fontWeight: "700",
  },
  statusChipTextActiveWarning: {
    color: "#B91C1C",
    fontWeight: "700",
  },
  searchRow: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#FFF",
    borderRadius: moderateScale(12),
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  searchInput: {
    flex: 1,
    paddingHorizontal: horizontalScale(10),
    paddingVertical: verticalScale(9),
    fontSize: moderateScale(12),
    color: "#0F172A",
  },
  searchActionBtn: {
    paddingHorizontal: horizontalScale(12),
    paddingVertical: verticalScale(10),
    borderTopRightRadius: moderateScale(11),
    borderBottomRightRadius: moderateScale(11),
    alignItems: "center",
    justifyContent: "center",
  },
  loadingBox: {
    padding: moderateScale(25),
    alignItems: "center",
    justifyContent: "center",
  },
  loadingText: {
    fontSize: moderateScale(11.5),
    color: "#64748B",
    marginTop: 8,
  },
  emptyCard: {
    backgroundColor: "#FFF",
    padding: moderateScale(28),
    borderRadius: moderateScale(16),
    alignItems: "center",
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  emptyTitle: {
    fontSize: moderateScale(13.5),
    fontWeight: "700",
    color: "#475569",
    marginTop: 10,
  },
  emptyText: {
    fontSize: moderateScale(11),
    color: "#94A3B8",
    textAlign: "center",
    marginTop: 4,
    lineHeight: 17,
  },
  haidCard: {
    backgroundColor: "#FFF",
    padding: moderateScale(13),
    borderRadius: moderateScale(16),
    borderWidth: 1,
    borderColor: "#E2E8F0",
    gap: verticalScale(9),
  },
  cardHeaderRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
  },
  studentName: {
    fontSize: moderateScale(13),
    fontWeight: "700",
    color: "#0F172A",
  },
  roleBadge: {
    paddingHorizontal: horizontalScale(6),
    paddingVertical: verticalScale(1.5),
    borderRadius: moderateScale(6),
    borderWidth: 1,
  },
  roleBadgeText: {
    fontSize: moderateScale(9.5),
    fontWeight: "700",
  },
  metaRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    marginTop: 3,
    flexWrap: "wrap",
  },
  metaText: {
    fontSize: moderateScale(10.5),
    color: "#64748B",
  },
  divisiBadge: {
    flexDirection: "row",
    alignItems: "center",
    gap: 4,
    backgroundColor: "#F1F5F9",
    paddingHorizontal: horizontalScale(6),
    paddingVertical: verticalScale(2),
    borderRadius: 4,
  },
  divisiText: {
    fontSize: moderateScale(10),
    fontWeight: "600",
    color: "#475569",
  },
  statusPill: {
    paddingHorizontal: horizontalScale(8),
    paddingVertical: verticalScale(4),
    borderRadius: moderateScale(20),
  },
  statusPillText: {
    fontSize: moderateScale(10),
    fontWeight: "700",
  },
  detailBox: {
    flexDirection: "row",
    backgroundColor: "#F8FAFC",
    padding: moderateScale(9),
    borderRadius: moderateScale(12),
  },
  detailItem: {
    flex: 1,
    alignItems: "center",
  },
  detailLbl: {
    fontSize: moderateScale(9.5),
    color: "#64748B",
    marginBottom: 2,
  },
  detailVal: {
    fontSize: moderateScale(11.5),
    fontWeight: "600",
    color: "#0F172A",
  },
  warningBanner: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    backgroundColor: "#FEE2E2",
    borderColor: "#FECDD3",
    borderWidth: 1,
    borderRadius: moderateScale(10),
    paddingHorizontal: horizontalScale(9),
    paddingVertical: verticalScale(7),
  },
  warningBannerText: {
    flex: 1,
    fontSize: moderateScale(10),
    color: "#991B1B",
    lineHeight: 14,
    fontWeight: "500",
  },
  catatanBox: {
    flexDirection: "row",
    alignItems: "flex-start",
    gap: 6,
    backgroundColor: "#F8FAFC",
    borderRadius: moderateScale(8),
    paddingHorizontal: horizontalScale(8),
    paddingVertical: verticalScale(6),
  },
  catatanText: {
    flex: 1,
    fontSize: moderateScale(10.5),
    color: "#475569",
    lineHeight: 15,
  },
  notesText: {
    fontSize: moderateScale(11),
    color: "#64748B",
    fontStyle: "italic",
  },
  catatActionBtn: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 8,
    paddingVertical: verticalScale(11),
    borderRadius: moderateScale(14),
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 4,
    elevation: 2,
  },
  catatActionText: {
    color: "#FFF",
    fontSize: moderateScale(12.5),
    fontWeight: "700",
  },
  subTabRow: {
    flexDirection: "row",
    backgroundColor: "#FFF",
    padding: moderateScale(4),
    borderRadius: moderateScale(12),
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  subTabBtn: {
    flex: 1,
    paddingVertical: verticalScale(8),
    alignItems: "center",
    borderRadius: moderateScale(8),
  },
  subTabBtnActive: {
    backgroundColor: "#FCE7F3",
  },
  subTabText: {
    fontSize: moderateScale(11),
    fontWeight: "600",
    color: "#64748B",
  },
  subTabTextActive: {
    color: "#DB2777",
    fontWeight: "700",
  },
  actionRow: {
    flexDirection: "row",
    gap: 8,
    marginTop: 4,
  },
  btnAction: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 6,
    paddingVertical: verticalScale(8),
    borderRadius: moderateScale(10),
  },
  btnActionText: {
    fontSize: moderateScale(11.5),
    fontWeight: "700",
    color: "#FFF",
  },
  modalBackdrop: {
    flex: 1,
    backgroundColor: "rgba(0,0,0,0.5)",
    alignItems: "center",
    justifyContent: "center",
    padding: horizontalScale(20),
  },
  modalBox: {
    width: "100%",
    backgroundColor: "#FFF",
    borderRadius: moderateScale(20),
    padding: moderateScale(20),
    gap: verticalScale(12),
  },
  modalHeaderRow: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
  },
  modalTitle: {
    fontSize: moderateScale(15),
    fontWeight: "800",
    color: "#0F172A",
  },
  modalSubtitle: {
    fontSize: moderateScale(12),
    color: "#64748B",
    lineHeight: 18,
  },
  modalInput: {
    borderWidth: 1,
    borderColor: "#CBD5E1",
    borderRadius: moderateScale(10),
    paddingHorizontal: horizontalScale(12),
    paddingVertical: verticalScale(8),
    fontSize: moderateScale(12),
    color: "#0F172A",
  },
  inputLabel: {
    fontSize: moderateScale(11),
    fontWeight: "700",
    color: "#334155",
    marginBottom: 4,
  },
  haidEmptyAlert: {
    backgroundColor: "#FEF3C7",
    borderColor: "#FDE68A",
    borderWidth: 1,
    borderRadius: moderateScale(12),
    padding: moderateScale(12),
    alignItems: "center",
    marginTop: 10,
  },
  haidEmptyAlertTitle: {
    fontSize: moderateScale(12),
    fontWeight: "700",
    color: "#92400E",
    marginTop: 4,
  },
  haidEmptyAlertDesc: {
    fontSize: moderateScale(10.5),
    color: "#B45309",
    textAlign: "center",
    marginTop: 2,
    lineHeight: 15,
  },
  memberOptionCard: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    padding: moderateScale(10),
    borderRadius: moderateScale(10),
    borderWidth: 1,
    borderColor: "#E2E8F0",
    marginBottom: 6,
    backgroundColor: "#FAFAFA",
  },
  memberName: {
    fontSize: moderateScale(12),
    fontWeight: "600",
    color: "#0F172A",
  },
  memberSub: {
    fontSize: moderateScale(10),
    color: "#64748B",
    marginTop: 1,
  },
  modalBtnRow: {
    flexDirection: "row",
    gap: 10,
    marginTop: 6,
  },
  modalCancelBtn: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: verticalScale(10),
    borderRadius: moderateScale(10),
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  modalCancelText: {
    fontSize: moderateScale(12),
    fontWeight: "600",
    color: "#64748B",
  },
  modalConfirmBtn: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: verticalScale(10),
    borderRadius: moderateScale(10),
  },
  modalConfirmText: {
    fontSize: moderateScale(12),
    fontWeight: "700",
    color: "#FFF",
  },
})
