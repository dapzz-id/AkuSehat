"use client"

import React, { useState, useEffect, useCallback } from "react"
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
  useWindowDimensions,
} from "react-native"
import Icon from "react-native-vector-icons/Feather"
import api from "../../api/axiosConfig"
import { Colors } from "../../settings/Colors"
import { moderateScale, horizontalScale, verticalScale } from "../../utils/responsive"
import { getDivisiTitle } from "../../utils/authHelper"

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

export default function FemHealthConsultantView({ themeColor, onLateCountChange }: Props) {
  const primaryColor = themeColor || Colors.primary

  const [peminjamanList, setPeminjamanList] = useState<PeminjamanItem[]>([])
  const [warningList, setWarningList] = useState<PeminjamanItem[]>([])
  const [loading, setLoading] = useState(false)
  const [refreshing, setRefreshing] = useState(false)
  const [activeSubTab, setActiveSubTab] = useState<"aktif" | "verifikasi" | "warning">("aktif")
  const [searchQuery, setSearchQuery] = useState("")

  // Modal Action State
  const [selectedItem, setSelectedItem] = useState<PeminjamanItem | null>(null)
  const [actionLoading, setActionLoading] = useState(false)
  const [confirmModalType, setConfirmModalType] = useState<"verify" | "reject" | "return" | null>(null)
  const [rejectReason, setRejectReason] = useState("")

  // Catat Peminjaman Modal State
  const [isCatatModalOpen, setIsCatatModalOpen] = useState(false)
  const [haidMembers, setHaidMembers] = useState<any[]>([])
  const [loadingHaidMembers, setLoadingHaidMembers] = useState(false)
  const [selectedHaidUserId, setSelectedHaidUserId] = useState<string | number>("")
  const [catatTanggalPinjam, setCatatTanggalPinjam] = useState(new Date().toISOString().split("T")[0])
  const [catatKeterangan, setCatatKeterangan] = useState("")
  const [catatSubmitting, setCatatSubmitting] = useState(false)

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
      await fetchData()
    } catch (e: any) {
      Alert.alert("Gagal", e?.response?.data?.message || "Gagal mencatat peminjaman pita.")
    } finally {
      setCatatSubmitting(false)
    }
  }

  const fetchData = useCallback(async () => {
    try {
      setLoading(true)
      const [resPeminjaman, resWarning] = await Promise.all([
        api.get("/health-monitor"),
        api.get("/health-monitor/warning"),
      ])

      const list: PeminjamanItem[] = resPeminjaman?.data?.data || []
      const warnings: PeminjamanItem[] = resWarning?.data?.data || []

      setPeminjamanList(list)
      setWarningList(warnings)

      if (onLateCountChange) {
        onLateCountChange(warnings.length)
      }
    } catch (e: any) {
      console.warn("Gagal memuat data FemHealth:", e)
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }, [onLateCountChange])

  useEffect(() => {
    fetchData()
  }, [fetchData])

  const onRefresh = () => {
    setRefreshing(true)
    fetchData()
  }

  const handleVerify = async () => {
    if (!selectedItem) return
    try {
      setActionLoading(true)
      await api.put(`/health-monitor/${selectedItem.id}/accept-verifikasi`)
      Alert.alert("Berhasil", "Permintaan peminjaman pita berhasil disetujui.")
      setConfirmModalType(null)
      setSelectedItem(null)
      await fetchData()
    } catch (e: any) {
      Alert.alert("Gagal", e?.response?.data?.message || "Gagal menyetujui peminjaman.")
    } finally {
      setActionLoading(false)
    }
  }

  const handleReject = async () => {
    if (!selectedItem) return
    try {
      setActionLoading(true)
      await api.put(`/health-monitor/${selectedItem.id}/reject-verifikasi`, {
        alasan: rejectReason.trim() || undefined,
      })
      Alert.alert("Ditolak", "Permintaan peminjaman pita telah ditolak.")
      setConfirmModalType(null)
      setSelectedItem(null)
      setRejectReason("")
      await fetchData()
    } catch (e: any) {
      Alert.alert("Gagal", e?.response?.data?.message || "Gagal menolak permohonan.")
    } finally {
      setActionLoading(false)
    }
  }

  const handleReturn = async () => {
    if (!selectedItem) return
    try {
      setActionLoading(true)
      await api.put(`/health-monitor/${selectedItem.id}`, {
        status: "dikembalikan",
        tanggal_kembali: new Date().toISOString().split("T")[0],
      })
      Alert.alert("Selesai", "Pita UKS telah dikembalikan.")
      setConfirmModalType(null)
      setSelectedItem(null)
      await fetchData()
    } catch (e: any) {
      Alert.alert("Gagal", e?.response?.data?.message || "Gagal memproses pengembalian.")
    } finally {
      setActionLoading(false)
    }
  }

  const getFilteredList = () => {
    let base = peminjamanList
    if (activeSubTab === "verifikasi") {
      base = peminjamanList.filter((p) => p.status === "menunggu" || !p.verified)
    } else if (activeSubTab === "warning") {
      base = warningList
    } else {
      base = peminjamanList.filter((p) => p.status === "dipinjam" || p.status === "terlambat")
    }

    if (searchQuery.trim()) {
      const q = searchQuery.toLowerCase().trim()
      base = base.filter(
        (p) =>
          p.user?.nama?.toLowerCase().includes(q) ||
          p.user?.nomor_induk?.toLowerCase().includes(q)
      )
    }
    return base
  }

  const waitingCount = peminjamanList.filter((p) => p.status === "menunggu" || !p.verified).length
  const activeCount = peminjamanList.filter((p) => p.status === "dipinjam").length
  const displayList = getFilteredList()

  return (
    <ScrollView
      showsVerticalScrollIndicator={false}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[primaryColor]} />
      }
      contentContainerStyle={{ paddingBottom: verticalScale(30), gap: verticalScale(14) }}
    >
      {/* Header Banner */}
      <View style={styles.headerBox}>
        <View style={[styles.headerIcon, { backgroundColor: "#FCE7F3" }]}>
          <Icon name="heart" size={20} color="#DB2777" />
        </View>
        <View style={{ flex: 1 }}>
          <Text style={styles.headerTitle}>Siklus & Peminjaman Pita Haid</Text>
          <Text style={styles.headerSubtitle}>
            Pantau ketersediaan pita UKS, verifikasi permohonan siswi, dan deteksi dini siklus menstruasi terlambat
          </Text>
        </View>
      </View>

      {/* KPI Cards */}
      <View style={styles.kpiRow}>
        <View style={styles.kpiCard}>
          <Text style={[styles.kpiVal, { color: "#DB2777" }]}>{activeCount}</Text>
          <Text style={styles.kpiLbl}>Pinjaman Aktif</Text>
        </View>
        <View style={styles.kpiCard}>
          <Text style={[styles.kpiVal, { color: "#D97706" }]}>{waitingCount}</Text>
          <Text style={styles.kpiLbl}>Menunggu Verif</Text>
        </View>
        <View style={styles.kpiCard}>
          <Text style={[styles.kpiVal, { color: "#DC2626" }]}>{warningList.length}</Text>
          <Text style={styles.kpiLbl}>Warning Siklus</Text>
        </View>
      </View>

      {/* Sub Tabs */}
      <View style={styles.subTabRow}>
        <Pressable
          onPress={() => setActiveSubTab("aktif")}
          style={[styles.subTabBtn, activeSubTab === "aktif" && styles.subTabBtnActive]}
        >
          <Text style={[styles.subTabText, activeSubTab === "aktif" && styles.subTabTextActive]}>
            Pita Aktif ({activeCount})
          </Text>
        </Pressable>
        <Pressable
          onPress={() => setActiveSubTab("verifikasi")}
          style={[styles.subTabBtn, activeSubTab === "verifikasi" && styles.subTabBtnActive]}
        >
          <Text style={[styles.subTabText, activeSubTab === "verifikasi" && styles.subTabTextActive]}>
            Verifikasi ({waitingCount})
          </Text>
        </Pressable>
        <Pressable
          onPress={() => setActiveSubTab("warning")}
          style={[styles.subTabBtn, activeSubTab === "warning" && styles.subTabBtnActive]}
        >
          <Text style={[styles.subTabText, activeSubTab === "warning" && styles.subTabTextActive]}>
            Warning ({warningList.length})
          </Text>
        </Pressable>
      </View>

      {/* Search Bar */}
      <View style={styles.searchRow}>
        <Icon name="search" size={16} color="#94A3B8" style={{ marginLeft: 12 }} />
        <TextInput
          value={searchQuery}
          onChangeText={setSearchQuery}
          placeholder="Cari nama siswi / nomor induk..."
          placeholderTextColor="#94A3B8"
          style={styles.searchInput}
        />
        {searchQuery.length > 0 && (
          <Pressable onPress={() => setSearchQuery("")} style={{ padding: 8 }}>
            <Icon name="x" size={14} color="#94A3B8" />
          </Pressable>
        )}
      </View>

      {/* Cards List */}
      <View style={{ gap: verticalScale(10) }}>
        {loading && !refreshing ? (
          <ActivityIndicator size="small" color={primaryColor} style={{ marginTop: 20 }} />
        ) : displayList.length === 0 ? (
          <View style={styles.emptyCard}>
            <Icon name="check-circle" size={36} color="#CBD5E1" />
            <Text style={styles.emptyTitle}>Tidak Ada Data Peminjaman</Text>
            <Text style={styles.emptyText}>Semua pita UKS tertib dan tidak ada permohonan tertunda.</Text>
          </View>
        ) : (
          displayList.map((item) => {
            const isLate = item.status === "terlambat" || activeSubTab === "warning"
            const isPending = item.status === "menunggu" || !item.verified
            return (
              <View key={item.id} style={[styles.itemCard, isLate && { borderColor: "#FECDD3" }]}>
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
                    <Text style={styles.detailVal}>{item.tanggal_pinjam}</Text>
                  </View>
                  <View style={styles.detailItem}>
                    <Text style={styles.detailLbl}>Estimasi Selesai</Text>
                    <Text style={[styles.detailVal, isLate && { color: "#DC2626", fontWeight: "700" }]}>
                      {item.estimasi_selesai_haid}
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
                          setSelectedItem(item)
                          setConfirmModalType("verify")
                        }}
                        style={[styles.btnAction, { backgroundColor: primaryColor }]}
                      >
                        <Icon name="check" size={13} color="#FFF" />
                        <Text style={styles.btnActionText}>Setujui Pinjam</Text>
                      </Pressable>
                      <Pressable
                        onPress={() => {
                          setSelectedItem(item)
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
                        setSelectedItem(item)
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

      {/* Modal: Catat Peminjaman Pita UKS */}
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

      {/* Confirmation Modal */}
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
                ? `Verifikasi peminjaman pita untuk ${selectedItem?.user?.nama}?`
                : confirmModalType === "reject"
                ? `Alasan penolakan permohonan ${selectedItem?.user?.nama}:`
                : `Pastikan pita dari ${selectedItem?.user?.nama} sudah diserahkan kembali ke UKS.`}
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
                  setSelectedItem(null)
                }}
                style={styles.modalCancelBtn}
              >
                <Text style={styles.modalCancelText}>Batal</Text>
              </Pressable>
              <Pressable
                disabled={actionLoading}
                onPress={() => {
                  if (confirmModalType === "verify") handleVerify()
                  else if (confirmModalType === "reject") handleReject()
                  else if (confirmModalType === "return") handleReturn()
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
    width: horizontalScale(42),
    height: horizontalScale(42),
    borderRadius: moderateScale(12),
    alignItems: "center",
    justifyContent: "center",
  },
  headerTitle: {
    fontSize: moderateScale(14),
    fontWeight: "700",
    color: "#0F172A",
  },
  headerSubtitle: {
    fontSize: moderateScale(11),
    color: "#64748B",
    marginTop: 2,
    lineHeight: 16,
  },
  kpiRow: {
    flexDirection: "row",
    gap: 8,
  },
  kpiCard: {
    flex: 1,
    backgroundColor: "#FFF",
    padding: moderateScale(12),
    borderRadius: moderateScale(14),
    borderWidth: 1,
    borderColor: "#E2E8F0",
    alignItems: "center",
  },
  kpiVal: {
    fontSize: moderateScale(18),
    fontWeight: "800",
  },
  kpiLbl: {
    fontSize: moderateScale(10),
    color: "#64748B",
    marginTop: 2,
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
    fontSize: moderateScale(11.5),
    fontWeight: "600",
    color: "#64748B",
  },
  subTabTextActive: {
    color: "#DB2777",
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
    paddingVertical: verticalScale(10),
    fontSize: moderateScale(12.5),
    color: "#0F172A",
  },
  emptyCard: {
    backgroundColor: "#FFF",
    padding: moderateScale(30),
    borderRadius: moderateScale(16),
    alignItems: "center",
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  emptyTitle: {
    fontSize: moderateScale(14),
    fontWeight: "700",
    color: "#475569",
    marginTop: 10,
  },
  emptyText: {
    fontSize: moderateScale(11.5),
    color: "#94A3B8",
    textAlign: "center",
    marginTop: 4,
    lineHeight: 18,
  },
  itemCard: {
    backgroundColor: "#FFF",
    padding: moderateScale(14),
    borderRadius: moderateScale(16),
    borderWidth: 1,
    borderColor: "#E2E8F0",
    gap: verticalScale(10),
  },
  cardHeaderRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
  },
  studentName: {
    fontSize: moderateScale(13.5),
    fontWeight: "700",
    color: "#0F172A",
  },
  metaRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    marginTop: 2,
  },
  metaText: {
    fontSize: moderateScale(11),
    color: "#64748B",
  },
  divisiBadge: {
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
    fontSize: moderateScale(10.5),
    fontWeight: "700",
  },
  detailBox: {
    flexDirection: "row",
    backgroundColor: "#F8FAFC",
    padding: moderateScale(10),
    borderRadius: moderateScale(12),
  },
  detailItem: {
    flex: 1,
    alignItems: "center",
  },
  detailLbl: {
    fontSize: moderateScale(10),
    color: "#64748B",
    marginBottom: 2,
  },
  detailVal: {
    fontSize: moderateScale(12),
    fontWeight: "600",
    color: "#0F172A",
  },
  notesText: {
    fontSize: moderateScale(11),
    color: "#64748B",
    fontStyle: "italic",
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

