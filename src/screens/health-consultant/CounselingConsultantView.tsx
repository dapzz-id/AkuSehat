"use client"

import React, { useState, useEffect, useCallback } from "react"
import {
  View,
  Text,
  StyleSheet,
  Pressable,
  ScrollView,
  Modal,
  TextInput,
  ActivityIndicator,
  Alert,
  useWindowDimensions,
} from "react-native"
import Icon from "react-native-vector-icons/Feather"
import api from "../../api/axiosConfig"
import { Colors } from "../../settings/Colors"
import { moderateScale, horizontalScale, verticalScale } from "../../utils/responsive"

export type ConsultantCounselingSession = {
  id: string | number
  user_id: string | number
  consultant_id?: string | number
  session_date: string
  topic: string
  notes?: string
  status: "menunggu" | "terjadwal" | "disetujui" | "selesai" | "ditolak" | "dibatalkan"
  is_confidential?: boolean
  ruangan?: string
  scheduled_at?: string
  rejection_reason?: string
  hasil_konseling?: string
  created_at: string
  user?: {
    id: string | number
    nama: string
    nomor_induk?: string
    jk?: string
    divisi?: {
      id: string | number
      divisi_name: string
    }
  }
}

interface Props {
  themeColor?: string
  onPendingCountChange?: (count: number) => void
}

export default function CounselingConsultantView({ themeColor, onPendingCountChange }: Props) {
  const { width } = useWindowDimensions()
  const isSmallScreen = width < 360
  const primaryColor = themeColor || Colors.primary

  const [sessions, setSessions] = useState<ConsultantCounselingSession[]>([])
  const [loading, setLoading] = useState(false)
  const [refreshing, setRefreshing] = useState(false)
  const [filterStatus, setFilterStatus] = useState<string>("menunggu")

  // Modal Actions State
  const [activeSession, setActiveSession] = useState<ConsultantCounselingSession | null>(null)
  const [modalType, setModalType] = useState<"approve" | "reject" | "complete" | null>(null)
  const [modalInputDate, setModalInputDate] = useState("")
  const [modalInputRoom, setModalInputRoom] = useState("")
  const [modalInputNotes, setModalInputNotes] = useState("")
  const [actionLoading, setActionLoading] = useState(false)

  const fetchSessions = useCallback(async () => {
    try {
      setLoading(true)
      const res = await api.get("/health-consultant/counseling")
      const data: ConsultantCounselingSession[] = res?.data?.data || []
      setSessions(data)

      const pending = data.filter((s) => s.status === "menunggu").length
      if (onPendingCountChange) {
        onPendingCountChange(pending)
      }
    } catch (e: any) {
      console.warn("Gagal memuat sesi konseling:", e)
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }, [onPendingCountChange])

  useEffect(() => {
    fetchSessions()
  }, [fetchSessions])

  const openApproveModal = (session: ConsultantCounselingSession) => {
    setActiveSession(session)
    setModalType("approve")
    setModalInputDate(session.session_date ? `${session.session_date} 09:00` : "")
    setModalInputRoom("Ruang Konseling")
    setModalInputNotes("")
  }

  const openRejectModal = (session: ConsultantCounselingSession) => {
    setActiveSession(session)
    setModalType("reject")
    setModalInputNotes("")
  }

  const openCompleteModal = (session: ConsultantCounselingSession) => {
    setActiveSession(session)
    setModalType("complete")
    setModalInputNotes("")
  }

  const closeModal = () => {
    setModalType(null)
    setActiveSession(null)
    setModalInputDate("")
    setModalInputRoom("")
    setModalInputNotes("")
  }

  const handleApprove = async () => {
    if (!activeSession) return
    if (!modalInputDate.trim()) {
      Alert.alert("Perhatian", "Tentukan tanggal & waktu pelaksanaan konseling.")
      return
    }

    try {
      setActionLoading(true)
      const res = await api.put(`/health-consultant/counseling/${activeSession.id}/approve`, {
        scheduled_at: modalInputDate.trim(),
        ruangan: modalInputRoom.trim() || undefined,
        notes: modalInputNotes.trim() || undefined,
      })
      Alert.alert("Disetujui", res?.data?.message || "Sesi konseling berhasil disetujui.")
      closeModal()
      await fetchSessions()
    } catch (e: any) {
      Alert.alert("Gagal", e?.response?.data?.message || "Gagal menyetujui sesi konseling.")
    } finally {
      setActionLoading(false)
    }
  }

  const handleReject = async () => {
    if (!activeSession) return
    if (!modalInputNotes.trim()) {
      Alert.alert("Perhatian", "Harap isi alasan penolakan agar anggota memahami alasannya.")
      return
    }

    try {
      setActionLoading(true)
      const res = await api.put(`/health-consultant/counseling/${activeSession.id}/reject`, {
        alasan: modalInputNotes.trim(),
      })
      Alert.alert("Ditolak", res?.data?.message || "Sesi konseling telah ditolak.")
      closeModal()
      await fetchSessions()
    } catch (e: any) {
      Alert.alert("Gagal", e?.response?.data?.message || "Gagal menolak sesi konseling.")
    } finally {
      setActionLoading(false)
    }
  }

  const handleComplete = async () => {
    if (!activeSession) return
    if (!modalInputNotes.trim()) {
      Alert.alert("Perhatian", "Tuliskan catatan hasil konseling atau rekomendasi tindak lanjut.")
      return
    }

    try {
      setActionLoading(true)
      const res = await api.put(`/health-consultant/counseling/${activeSession.id}/complete`, {
        notes: modalInputNotes.trim(),
      })
      Alert.alert("Selesai", res?.data?.message || "Sesi konseling telah ditandai selesai.")
      closeModal()
      await fetchSessions()
    } catch (e: any) {
      Alert.alert("Gagal", e?.response?.data?.message || "Gagal menyelesaikan sesi konseling.")
    } finally {
      setActionLoading(false)
    }
  }

  const getStatusBadge = (status: string) => {
    switch (status.toLowerCase()) {
      case "menunggu":
        return { label: "Menunggu Konfirmasi", bg: "#FEF3C7", text: "#D97706", icon: "clock" }
      case "disetujui":
      case "terjadwal":
        return { label: "Disetujui / Terjadwal", bg: "#DCFCE7", text: "#15803D", icon: "check-circle" }
      case "selesai":
        return { label: "Selesai", bg: "#DBEAFE", text: "#1D4ED8", icon: "check-square" }
      case "ditolak":
      case "dibatalkan":
        return { label: "Ditolak / Dibatalkan", bg: "#FEE2E2", text: "#B91C1C", icon: "x-circle" }
      default:
        return { label: status, bg: "#F1F5F9", text: "#475569", icon: "help-circle" }
    }
  }

  const filteredSessions = sessions.filter((s) => {
    if (filterStatus === "all") return true
    if (filterStatus === "menunggu") return s.status === "menunggu"
    if (filterStatus === "terjadwal") return s.status === "terjadwal" || s.status === "disetujui"
    if (filterStatus === "selesai") return s.status === "selesai"
    if (filterStatus === "ditolak") return s.status === "ditolak" || s.status === "dibatalkan"
    return true
  })

  return (
    <View style={styles.container}>
      {/* Consultant Header Card */}
      <View style={[styles.headerCard, { borderTopColor: primaryColor }]}>
        <View style={styles.headerRow}>
          <View style={[styles.headerIconWrapper, { backgroundColor: primaryColor + "18" }]}>
            <Icon name="message-square" size={22} color={primaryColor} />
          </View>
          <View style={{ flex: 1, marginLeft: moderateScale(12) }}>
            <Text style={styles.headerTitle}>Manajemen Konseling</Text>
            <Text style={styles.headerSubtitle}>
              Kelola pengajuan bimbingan mental dan konsultasi kesehatan anggota.
            </Text>
          </View>
          <Pressable onPress={fetchSessions} style={styles.refreshIconBtn}>
            <Icon name="rotate-cw" size={18} color="#64748B" />
          </Pressable>
        </View>
      </View>

      {/* Filter Chips */}
      <ScrollView
        horizontal
        showsHorizontalScrollIndicator={false}
        contentContainerStyle={styles.filterScroll}
      >
        {[
          { key: "menunggu", label: "Menunggu" },
          { key: "terjadwal", label: "Terjadwal" },
          { key: "selesai", label: "Selesai" },
          { key: "ditolak", label: "Ditolak" },
          { key: "all", label: "Semua Sesi" },
        ].map((tab) => {
          const isActive = filterStatus === tab.key
          return (
            <Pressable
              key={tab.key}
              onPress={() => setFilterStatus(tab.key)}
              style={[
                styles.filterChip,
                isActive && { backgroundColor: primaryColor, borderColor: primaryColor },
              ]}
            >
              <Text
                style={[
                  styles.filterChipText,
                  isActive && { color: Colors.white, fontWeight: "700" },
                ]}
              >
                {tab.label}
              </Text>
            </Pressable>
          )
        })}
      </ScrollView>

      {/* Session Cards */}
      {loading ? (
        <View style={styles.centerLoading}>
          <ActivityIndicator color={primaryColor} size="large" />
          <Text style={styles.loadingText}>Memuat antrean konseling...</Text>
        </View>
      ) : filteredSessions.length === 0 ? (
        <View style={styles.emptyCard}>
          <Icon name="inbox" size={42} color="#CBD5E1" />
          <Text style={styles.emptyTitle}>Tidak Ada Pengajuan</Text>
          <Text style={styles.emptySubtitle}>
            Belum ada sesi konseling dengan filter status ini.
          </Text>
        </View>
      ) : (
        <View style={{ gap: verticalScale(12) }}>
          {filteredSessions.map((session) => {
            const badge = getStatusBadge(session.status)
            const isPending = session.status === "menunggu"
            const isScheduled = session.status === "terjadwal" || session.status === "disetujui"

            return (
              <View key={session.id} style={styles.card}>
                {/* Header User info */}
                <View style={styles.userRow}>
                  <View style={styles.userAvatar}>
                    <Icon name="user" size={18} color={primaryColor} />
                  </View>
                  <View style={{ flex: 1, marginLeft: 10 }}>
                    <Text style={styles.userName}>{session.user?.nama || "Member"}</Text>
                    <Text style={styles.userMeta}>
                      {session.user?.divisi?.divisi_name || "Divisi/Kelas"} • NIS/NIM:{" "}
                      {session.user?.nomor_induk || "-"}
                    </Text>
                  </View>
                  <View style={[styles.statusBadge, { backgroundColor: badge.bg }]}>
                    <Icon name={badge.icon} size={11} color={badge.text} />
                    <Text style={[styles.statusBadgeText, { color: badge.text }]}>
                      {badge.label}
                    </Text>
                  </View>
                </View>

                {/* Topic & Dates */}
                <View style={styles.topicRow}>
                  <Text style={styles.topicLabel}>Topik:</Text>
                  <Text style={styles.topicValue}>{session.topic}</Text>
                </View>

                <View style={styles.dateRow}>
                  <Icon name="calendar" size={13} color="#64748B" />
                  <Text style={styles.dateLabel}>
                    Tanggal Diminta:{" "}
                    {new Date(session.session_date).toLocaleDateString("id-ID", {
                      day: "numeric",
                      month: "long",
                      year: "numeric",
                    })}
                  </Text>
                </View>

                {/* Initial Notes from Member */}
                {session.notes ? (
                  <View style={styles.notesBox}>
                    <Text style={styles.notesHeader}>Catatan / Keluhan Anggota:</Text>
                    <Text style={styles.notesBody}>{session.notes}</Text>
                  </View>
                ) : null}

                {/* Confirmed schedule & room */}
                {session.scheduled_at ? (
                  <View style={styles.infoRowGreen}>
                    <Icon name="check-circle" size={14} color="#15803D" />
                    <Text style={styles.infoTextGreen}>
                      Jadwal Fix: {session.scheduled_at} • {session.ruangan || "Ruang Konseling"}
                    </Text>
                  </View>
                ) : null}

                {/* Results note if completed */}
                {session.hasil_konseling ? (
                  <View style={styles.resultBox}>
                    <Text style={styles.resultHeader}>Hasil Konseling:</Text>
                    <Text style={styles.resultBody}>{session.hasil_konseling}</Text>
                  </View>
                ) : null}

                {/* Action Buttons */}
                {isPending && (
                  <View style={styles.actionRow}>
                    <Pressable
                      onPress={() => openRejectModal(session)}
                      style={styles.rejectBtn}
                    >
                      <Icon name="x" size={14} color="#DC2626" />
                      <Text style={styles.rejectBtnText}>Tolak</Text>
                    </Pressable>

                    <Pressable
                      onPress={() => openApproveModal(session)}
                      style={[styles.approveBtn, { backgroundColor: primaryColor }]}
                    >
                      <Icon name="check" size={14} color={Colors.white} />
                      <Text style={styles.approveBtnText}>Setujui Jadwal</Text>
                    </Pressable>
                  </View>
                )}

                {isScheduled && (
                  <Pressable
                    onPress={() => openCompleteModal(session)}
                    style={[styles.completeBtn, { backgroundColor: "#2563EB" }]}
                  >
                    <Icon name="check-square" size={15} color={Colors.white} />
                    <Text style={styles.completeBtnText}>Selesaikan Sesi Konseling</Text>
                  </Pressable>
                )}
              </View>
            )
          })}
        </View>
      )}

      {/* Modal Actions */}
      <Modal
        visible={modalType !== null}
        transparent
        animationType="slide"
        onRequestClose={closeModal}
      >
        <View style={styles.modalOverlay}>
          <View style={styles.modalSheet}>
            <View style={styles.modalHeader}>
              <Text style={styles.modalTitle}>
                {modalType === "approve"
                  ? "Persetujuan Jadwal Konseling"
                  : modalType === "reject"
                  ? "Tolak Pengajuan Konseling"
                  : "Selesaikan Sesi Konseling"}
              </Text>
              <Pressable onPress={closeModal} style={styles.modalCloseBtn}>
                <Icon name="x" size={20} color="#64748B" />
              </Pressable>
            </View>

            <ScrollView showsVerticalScrollIndicator={false} keyboardShouldPersistTaps="handled">
              {activeSession && (
                <View style={styles.modalStudentCard}>
                  <Text style={styles.modalStudentName}>
                    {activeSession.user?.nama || "Member"}
                  </Text>
                  <Text style={styles.modalStudentTopic}>
                    Topik: {activeSession.topic}
                  </Text>
                </View>
              )}

              {modalType === "approve" && (
                <>
                  <Text style={styles.inputLabel}>Jadwal Pasti (Tanggal & Waktu)</Text>
                  <TextInput
                    style={styles.modalInput}
                    placeholder="Contoh: 2026-10-27 10:00"
                    placeholderTextColor="#94A3B8"
                    value={modalInputDate}
                    onChangeText={setModalInputDate}
                  />

                  <Text style={styles.inputLabel}>Lokasi / Ruangan</Text>
                  <TextInput
                    style={styles.modalInput}
                    placeholder="Contoh: Ruang Konseling / Klinik / Online"
                    placeholderTextColor="#94A3B8"
                    value={modalInputRoom}
                    onChangeText={setModalInputRoom}
                  />

                  <Text style={styles.inputLabel}>Catatan Tambahan untuk Anggota (Opsional)</Text>
                  <TextInput
                    style={[styles.modalInput, styles.modalTextArea]}
                    placeholder="Catatan seperti persiapan atau petunjuk..."
                    placeholderTextColor="#94A3B8"
                    multiline
                    value={modalInputNotes}
                    onChangeText={setModalInputNotes}
                  />
                </>
              )}

              {modalType === "reject" && (
                <>
                  <Text style={styles.inputLabel}>Alasan Penolakan</Text>
                  <TextInput
                    style={[styles.modalInput, styles.modalTextArea]}
                    placeholder="Jelaskan alasan penolakan jadwal (misal: konselor berhalangan / sarankan ganti tanggal)..."
                    placeholderTextColor="#94A3B8"
                    multiline
                    value={modalInputNotes}
                    onChangeText={setModalInputNotes}
                  />
                </>
              )}

              {modalType === "complete" && (
                <>
                  <Text style={styles.inputLabel}>Catatan Hasil Konseling & Tindak Lanjut</Text>
                  <TextInput
                    style={[styles.modalInput, styles.modalTextArea]}
                    placeholder="Tuliskan resume bimbingan, komitmen dan tindak lanjut..."
                    placeholderTextColor="#94A3B8"
                    multiline
                    value={modalInputNotes}
                    onChangeText={setModalInputNotes}
                  />
                </>
              )}
            </ScrollView>

            {/* Submit buttons */}
            <View style={styles.modalActionsRow}>
              <Pressable onPress={closeModal} style={styles.modalCancelButton}>
                <Text style={styles.modalCancelButtonText}>Batal</Text>
              </Pressable>

              <Pressable
                onPress={() => {
                  if (modalType === "approve") handleApprove()
                  else if (modalType === "reject") handleReject()
                  else if (modalType === "complete") handleComplete()
                }}
                disabled={actionLoading}
                style={[
                  styles.modalSubmitButton,
                  {
                    backgroundColor:
                      modalType === "reject" ? "#DC2626" : primaryColor,
                    opacity: actionLoading ? 0.6 : 1,
                  },
                ]}
              >
                {actionLoading ? (
                  <ActivityIndicator color={Colors.white} size="small" />
                ) : (
                  <Text style={styles.modalSubmitButtonText}>
                    {modalType === "approve"
                      ? "Konfirmasi Jadwal"
                      : modalType === "reject"
                      ? "Tolak Jadwal"
                      : "Simpan & Selesaikan"}
                  </Text>
                )}
              </Pressable>
            </View>
          </View>
        </View>
      </Modal>
    </View>
  )
}

const styles = StyleSheet.create({
  container: {
    gap: verticalScale(14),
  },
  headerCard: {
    backgroundColor: Colors.white,
    borderRadius: 18,
    padding: moderateScale(16),
    borderWidth: 1,
    borderColor: "#F1F5F9",
    borderTopWidth: 3.5,
    shadowColor: "#0F172A",
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 3,
  },
  headerRow: {
    flexDirection: "row",
    alignItems: "center",
  },
  headerIconWrapper: {
    width: 44,
    height: 44,
    borderRadius: 12,
    alignItems: "center",
    justifyContent: "center",
  },
  headerTitle: {
    fontSize: moderateScale(16),
    fontWeight: "800",
    color: "#0F172A",
  },
  headerSubtitle: {
    fontSize: moderateScale(12),
    color: "#64748B",
    marginTop: 2,
    lineHeight: 17,
  },
  refreshIconBtn: {
    padding: 8,
  },
  filterScroll: {
    flexDirection: "row",
    gap: 8,
    paddingVertical: 2,
  },
  filterChip: {
    paddingHorizontal: moderateScale(14),
    paddingVertical: verticalScale(7),
    borderRadius: 20,
    backgroundColor: Colors.white,
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  filterChipText: {
    fontSize: moderateScale(12),
    color: "#64748B",
    fontWeight: "600",
  },
  centerLoading: {
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: verticalScale(30),
  },
  loadingText: {
    marginTop: 8,
    fontSize: moderateScale(12),
    color: "#64748B",
  },
  emptyCard: {
    backgroundColor: Colors.white,
    borderRadius: 16,
    padding: moderateScale(24),
    alignItems: "center",
    justifyContent: "center",
    borderWidth: 1,
    borderColor: "#F1F5F9",
  },
  emptyTitle: {
    fontSize: moderateScale(15),
    fontWeight: "700",
    color: "#0F172A",
    marginTop: 10,
  },
  emptySubtitle: {
    fontSize: moderateScale(12),
    color: "#64748B",
    textAlign: "center",
    marginTop: 4,
  },
  card: {
    backgroundColor: Colors.white,
    borderRadius: 16,
    padding: moderateScale(16),
    borderWidth: 1,
    borderColor: "#F1F5F9",
    shadowColor: "#0F172A",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.04,
    shadowRadius: 6,
    elevation: 2,
    gap: verticalScale(8),
  },
  userRow: {
    flexDirection: "row",
    alignItems: "center",
  },
  userAvatar: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: "#F1F5F9",
    alignItems: "center",
    justifyContent: "center",
  },
  userName: {
    fontSize: moderateScale(14),
    fontWeight: "700",
    color: "#0F172A",
  },
  userMeta: {
    fontSize: moderateScale(11),
    color: "#64748B",
    marginTop: 1,
  },
  statusBadge: {
    flexDirection: "row",
    alignItems: "center",
    gap: 4,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
  },
  statusBadgeText: {
    fontSize: moderateScale(10.5),
    fontWeight: "700",
  },
  topicRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
  },
  topicLabel: {
    fontSize: moderateScale(12),
    color: "#64748B",
  },
  topicValue: {
    fontSize: moderateScale(13),
    fontWeight: "700",
    color: "#0F172A",
  },
  dateRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
  },
  dateLabel: {
    fontSize: moderateScale(12),
    color: "#475569",
  },
  notesBox: {
    backgroundColor: "#F8FAFC",
    padding: moderateScale(10),
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  notesHeader: {
    fontSize: moderateScale(11),
    fontWeight: "700",
    color: "#475569",
    marginBottom: 2,
  },
  notesBody: {
    fontSize: moderateScale(12),
    color: "#334155",
    lineHeight: 17,
  },
  infoRowGreen: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    backgroundColor: "#F0FDF4",
    padding: moderateScale(8),
    borderRadius: 8,
  },
  infoTextGreen: {
    fontSize: moderateScale(11.5),
    fontWeight: "600",
    color: "#166534",
  },
  resultBox: {
    backgroundColor: "#EFF6FF",
    padding: moderateScale(10),
    borderRadius: 8,
    borderLeftWidth: 3,
    borderLeftColor: "#2563EB",
  },
  resultHeader: {
    fontSize: moderateScale(11.5),
    fontWeight: "700",
    color: "#1E40AF",
    marginBottom: 2,
  },
  resultBody: {
    fontSize: moderateScale(12),
    color: "#1E3A8A",
    lineHeight: 18,
  },
  actionRow: {
    flexDirection: "row",
    gap: 10,
    marginTop: 4,
  },
  rejectBtn: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 6,
    paddingVertical: verticalScale(9),
    borderRadius: 10,
    borderWidth: 1,
    borderColor: "#FCA5A5",
    backgroundColor: "#FFF5F5",
  },
  rejectBtnText: {
    fontSize: moderateScale(12),
    fontWeight: "700",
    color: "#DC2626",
  },
  approveBtn: {
    flex: 2,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 6,
    paddingVertical: verticalScale(9),
    borderRadius: 10,
  },
  approveBtnText: {
    fontSize: moderateScale(12),
    fontWeight: "700",
    color: Colors.white,
  },
  completeBtn: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 6,
    paddingVertical: verticalScale(10),
    borderRadius: 10,
    marginTop: 4,
  },
  completeBtnText: {
    fontSize: moderateScale(12.5),
    fontWeight: "700",
    color: Colors.white,
  },

  // Modal styles
  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(15, 23, 42, 0.5)",
    justifyContent: "flex-end",
  },
  modalSheet: {
    backgroundColor: Colors.white,
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    padding: moderateScale(20),
    gap: verticalScale(12),
    maxHeight: "85%",
  },
  modalHeader: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    borderBottomWidth: 1,
    borderBottomColor: "#F1F5F9",
    paddingBottom: verticalScale(10),
  },
  modalTitle: {
    fontSize: moderateScale(16),
    fontWeight: "800",
    color: "#0F172A",
  },
  modalCloseBtn: {
    padding: 6,
  },
  modalStudentCard: {
    backgroundColor: "#F8FAFC",
    padding: moderateScale(10),
    borderRadius: 10,
    marginBottom: verticalScale(6),
  },
  modalStudentName: {
    fontSize: moderateScale(13.5),
    fontWeight: "700",
    color: "#0F172A",
  },
  modalStudentTopic: {
    fontSize: moderateScale(12),
    color: "#64748B",
    marginTop: 2,
  },
  inputLabel: {
    fontSize: moderateScale(12),
    fontWeight: "700",
    color: "#334155",
    marginTop: verticalScale(6),
    marginBottom: verticalScale(4),
  },
  modalInput: {
    backgroundColor: "#F8FAFC",
    borderWidth: 1,
    borderColor: "#E2E8F0",
    borderRadius: 10,
    paddingHorizontal: moderateScale(12),
    paddingVertical: verticalScale(9),
    fontSize: moderateScale(13),
    color: "#0F172A",
  },
  modalTextArea: {
    height: 75,
    textAlignVertical: "top",
  },
  modalActionsRow: {
    flexDirection: "row",
    gap: 10,
    marginTop: verticalScale(10),
  },
  modalCancelButton: {
    flex: 1,
    paddingVertical: verticalScale(11),
    borderRadius: 12,
    borderWidth: 1,
    borderColor: "#E2E8F0",
    alignItems: "center",
    justifyContent: "center",
  },
  modalCancelButtonText: {
    fontSize: moderateScale(13),
    fontWeight: "600",
    color: "#64748B",
  },
  modalSubmitButton: {
    flex: 2,
    paddingVertical: verticalScale(11),
    borderRadius: 12,
    alignItems: "center",
    justifyContent: "center",
  },
  modalSubmitButtonText: {
    fontSize: moderateScale(13),
    fontWeight: "700",
    color: Colors.white,
  },
})
