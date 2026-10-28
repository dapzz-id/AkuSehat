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
  Switch,
  RefreshControl,
  useWindowDimensions,
} from "react-native"
import Icon from "react-native-vector-icons/Feather"
import api from "../../api/axiosConfig"
import { Colors } from "../../settings/Colors"
import { moderateScale, horizontalScale, verticalScale } from "../../utils/responsive"

export type CounselingSession = {
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
  consultant?: {
    id: string | number
    nama: string
    level: string
  }
}

interface Props {
  themeColor?: string
  profile?: any
}

const MONTH_NAMES = [
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

const DAY_NAMES = ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"]

const COUNSELING_TOPICS = [
  "Akademik & Studi",
  "Kecemasan, Stres & Beban Pikiran",
  "Hubungan Sosial & Teman",
  "Keluarga & Masalah Pribadi",
  "Kebugaran & Pola Hidup Sehat",
  "Lainnya",
]

export default function CounselingMemberView({ themeColor, profile }: Props) {
  const { width } = useWindowDimensions()
  const isSmallScreen = width < 360
  const primaryColor = themeColor || Colors.primary

  const [sessions, setSessions] = useState<CounselingSession[]>([])
  const [loading, setLoading] = useState(false)
  const [refreshing, setRefreshing] = useState(false)
  const [filterStatus, setFilterStatus] = useState<string>("all")

  // Dynamic Consultant Label
  const consultantTitle = React.useMemo(() => {
    const custom = profile?.instansi?.custom_role_labels?.["Health Consultant"]
    if (custom && !custom.toLowerCase().includes("guru bk")) {
      return custom
    }
    const jenis = profile?.instansi?.jenis_instansi
    if (jenis === "Universitas") return "Dosen Pembimbing / Konselor"
    if (jenis === "Perusahaan") return "Konselor K3 / HR"
    if (jenis === "Pemerintah") return "Konselor ASN"
    if (jenis === "Klinik") return "Dokter / Konselor Medis"
    return "Konselor"
  }, [profile])

  // Modal State
  const [bookModalVisible, setBookModalVisible] = useState(false)
  const [topic, setTopic] = useState(COUNSELING_TOPICS[0])
  const [sessionDate, setSessionDate] = useState("")
  const [notes, setNotes] = useState("")
  const [isConfidential, setIsConfidential] = useState(true)
  const [submitting, setSubmitting] = useState(false)
  const [cancelingId, setCancelingId] = useState<string | number | null>(null)

  // Calendar Datepicker Modal State
  const [calendarModalVisible, setCalendarModalVisible] = useState(false)
  const [calendarYear, setCalendarYear] = useState(new Date().getFullYear())
  const [calendarMonth, setCalendarMonth] = useState(new Date().getMonth())

  const fetchSessions = useCallback(async () => {
    try {
      setLoading(true)
      const res = await api.get("/member/counseling")
      setSessions(res?.data?.data || [])
    } catch (e: any) {
      console.warn("Gagal memuat jadwal konseling:", e)
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }, [])

  useEffect(() => {
    fetchSessions()
  }, [fetchSessions])

  const onRefresh = () => {
    setRefreshing(true)
    fetchSessions()
  }

  // Helper to calculate date ahead formatted as YYYY-MM-DD
  const getDateAhead = (daysAhead: number) => {
    const d = new Date()
    d.setDate(d.getDate() + daysAhead)
    const yyyy = d.getFullYear()
    const mm = String(d.getMonth() + 1).padStart(2, "0")
    const dd = String(d.getDate()).padStart(2, "0")
    return `${yyyy}-${mm}-${dd}`
  }

  // Set default date to days ahead in YYYY-MM-DD
  const setQuickDate = (daysAhead: number) => {
    setSessionDate(getDateAhead(daysAhead))
  }

  // Quick date active checks
  const isBesokActive = sessionDate === getDateAhead(1)
  const isLusaActive = sessionDate === getDateAhead(2)
  const isPlus3Active = sessionDate === getDateAhead(3)

  // Calendar Helpers
  const getDaysInMonth = (year: number, month: number) => new Date(year, month + 1, 0).getDate()
  const getFirstDayOfWeek = (year: number, month: number) => new Date(year, month, 1).getDay()

  const openCalendarModal = () => {
    let initialDate = new Date()
    if (sessionDate && /^\d{4}-\d{2}-\d{2}$/.test(sessionDate)) {
      const parts = sessionDate.split("-").map(Number)
      initialDate = new Date(parts[0], parts[1] - 1, parts[2])
    } else {
      initialDate.setDate(initialDate.getDate() + 1)
    }
    setCalendarYear(initialDate.getFullYear())
    setCalendarMonth(initialDate.getMonth())
    setCalendarModalVisible(true)
  }

  const prevMonth = () => {
    if (calendarMonth === 0) {
      setCalendarMonth(11)
      setCalendarYear(calendarYear - 1)
    } else {
      setCalendarMonth(calendarMonth - 1)
    }
  }

  const nextMonth = () => {
    if (calendarMonth === 11) {
      setCalendarMonth(0)
      setCalendarYear(calendarYear + 1)
    } else {
      setCalendarMonth(calendarMonth + 1)
    }
  }

  const selectCalendarDay = (day: number) => {
    const mm = String(calendarMonth + 1).padStart(2, "0")
    const dd = String(day).padStart(2, "0")
    setSessionDate(`${calendarYear}-${mm}-${dd}`)
    setCalendarModalVisible(false)
  }

  const isDayPast = (day: number) => {
    const today = new Date()
    today.setHours(0, 0, 0, 0)
    const target = new Date(calendarYear, calendarMonth, day, 0, 0, 0, 0)
    return target.getTime() < today.getTime()
  }

  const isDaySelected = (day: number) => {
    const mm = String(calendarMonth + 1).padStart(2, "0")
    const dd = String(day).padStart(2, "0")
    return sessionDate === `${calendarYear}-${mm}-${dd}`
  }

  const isDayToday = (day: number) => {
    const today = new Date()
    return (
      today.getFullYear() === calendarYear &&
      today.getMonth() === calendarMonth &&
      today.getDate() === day
    )
  }

  const handleBookSession = async () => {
    if (!topic.trim()) {
      Alert.alert("Perhatian", "Pilih topik konseling terlebih dahulu.")
      return
    }
    if (!sessionDate.trim()) {
      Alert.alert("Perhatian", "Tentukan tanggal sesi konseling yang diharapkan.")
      return
    }

    try {
      setSubmitting(true)
      const payload = {
        topic: topic.trim(),
        session_date: sessionDate.trim(),
        notes: notes.trim() || undefined,
        is_confidential: isConfidential,
      }

      const res = await api.post("/member/counseling/book", payload)
      Alert.alert(
        "Pengajuan Berhasil",
        res?.data?.message || "Jadwal konseling Anda telah diajukan kepada konselor institusi."
      )
      setBookModalVisible(false)
      setNotes("")
      setSessionDate("")
      await fetchSessions()
    } catch (e: any) {
      Alert.alert("Gagal", e?.response?.data?.message || "Gagal mengajukan jadwal konseling.")
    } finally {
      setSubmitting(false)
    }
  }

  const handleCancelSession = (session: CounselingSession) => {
    Alert.alert(
      "Batalkan Konseling",
      "Apakah Anda yakin ingin membatalkan jadwal konseling ini?",
      [
        { text: "Batal", style: "cancel" },
        {
          text: "Ya, Batalkan",
          style: "destructive",
          onPress: async () => {
            try {
              setCancelingId(session.id)
              const res = await api.put(`/member/counseling/${session.id}/cancel`)
              Alert.alert("Dibatalkan", res?.data?.message || "Sesi konseling telah dibatalkan.")
              await fetchSessions()
            } catch (e: any) {
              Alert.alert("Gagal", e?.response?.data?.message || "Gagal membatalkan sesi.")
            } finally {
              setCancelingId(null)
            }
          },
        },
      ]
    )
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
        return { label: "Dibatalkan / Ditolak", bg: "#FEE2E2", text: "#B91C1C", icon: "x-circle" }
      default:
        return { label: status, bg: "#F1F5F9", text: "#475569", icon: "help-circle" }
    }
  }

  const filteredSessions = sessions.filter((s) => {
    if (filterStatus === "all") return true
    if (filterStatus === "menunggu") return s.status === "menunggu"
    if (filterStatus === "terjadwal") return s.status === "terjadwal" || s.status === "disetujui"
    if (filterStatus === "selesai") return s.status === "selesai"
    return true
  })

  return (
    <View style={styles.container}>
      {/* Hero SafeSpace Banner */}
      <View style={[styles.heroCard, { borderLeftColor: primaryColor }]}>
        <View style={styles.heroHeaderRow}>
          <View style={[styles.shieldIconWrapper, { backgroundColor: primaryColor + "18" }]}>
            <Icon name="shield" size={24} color={primaryColor} />
          </View>
          <View style={{ flex: 1, marginLeft: moderateScale(12) }}>
            <Text style={styles.heroTitle}>SafeSpace & Konseling</Text>
            <Text style={styles.heroSubtitle}>
              Ruang aman dan privat bersama {consultantTitle} atau Tenaga Profesional.
            </Text>
          </View>
        </View>

        <View style={styles.confidentialBadge}>
          <Icon name="lock" size={14} color="#059669" />
          <Text style={styles.confidentialText}>100% Konfidensial & Privasi Terjaga</Text>
        </View>

        {/* Primary Action Button */}
        <Pressable
          onPress={() => {
            setQuickDate(1)
            setBookModalVisible(true)
          }}
          style={({ pressed }) => [
            styles.bookButton,
            { backgroundColor: primaryColor, opacity: pressed ? 0.9 : 1 },
          ]}
        >
          <Icon name="plus-circle" size={18} color={Colors.white} />
          <Text style={styles.bookButtonText}>Ajukan Jadwal Konseling</Text>
        </Pressable>
      </View>

      {/* Filter Tabs */}
      <View style={styles.filterRow}>
        {[
          { key: "all", label: "Semua" },
          { key: "menunggu", label: "Menunggu" },
          { key: "terjadwal", label: "Terjadwal" },
          { key: "selesai", label: "Selesai" },
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
      </View>

      {/* Sessions List */}
      {loading && !refreshing ? (
        <View style={styles.centerLoading}>
          <ActivityIndicator color={primaryColor} size="large" />
          <Text style={styles.loadingText}>Memuat riwayat konseling...</Text>
        </View>
      ) : filteredSessions.length === 0 ? (
        <View style={styles.emptyCard}>
          <Icon name="message-square" size={44} color="#CBD5E1" />
          <Text style={styles.emptyTitle}>Belum Ada Sesi Konseling</Text>
          <Text style={styles.emptySubtitle}>
            Jika kamu membutuhkan teman bercerita, konsultasi kesehatan mental, atau bimbingan profesional,
            jangan ragu mengajukan jadwal melalui tombol di atas.
          </Text>
        </View>
      ) : (
        <View style={{ gap: verticalScale(12) }}>
          {filteredSessions.map((session) => {
            const badge = getStatusBadge(session.status)
            const isPending = session.status === "menunggu"
            const isCanceling = cancelingId === session.id

            return (
              <View key={session.id} style={styles.sessionCard}>
                <View style={styles.sessionHeader}>
                  <View style={[styles.statusBadge, { backgroundColor: badge.bg }]}>
                    <Icon name={badge.icon} size={12} color={badge.text} />
                    <Text style={[styles.statusBadgeText, { color: badge.text }]}>
                      {badge.label}
                    </Text>
                  </View>
                  <Text style={styles.dateText}>
                    {new Date(session.session_date).toLocaleDateString("id-ID", {
                      day: "numeric",
                      month: "short",
                      year: "numeric",
                    })}
                  </Text>
                </View>

                <Text style={styles.topicText}>{session.topic}</Text>

                {session.notes ? (
                  <View style={styles.notesBox}>
                    <Text style={styles.notesLabel}>Catatan Pengajuan:</Text>
                    <Text style={styles.notesContent}>{session.notes}</Text>
                  </View>
                ) : null}

                {/* Info Ruangan / Jadwal Terkonfirmasi */}
                {session.scheduled_at ? (
                  <View style={styles.confirmedBox}>
                    <Icon name="calendar" size={14} color="#15803D" />
                    <Text style={styles.confirmedText}>
                      Jadwal fix:{" "}
                      {new Date(session.scheduled_at).toLocaleString("id-ID", {
                        dateStyle: "medium",
                        timeStyle: "short",
                      })}
                    </Text>
                  </View>
                ) : null}

                {session.ruangan ? (
                  <View style={styles.confirmedBox}>
                    <Icon name="map-pin" size={14} color="#15803D" />
                    <Text style={styles.confirmedText}>Ruangan: {session.ruangan}</Text>
                  </View>
                ) : null}

                {session.consultant ? (
                  <View style={styles.consultantRow}>
                    <Icon name="user-check" size={14} color={primaryColor} />
                    <Text style={styles.consultantName}>
                      Konselor: {session.consultant.nama}
                    </Text>
                  </View>
                ) : null}

                {session.hasil_konseling ? (
                  <View style={styles.resultBox}>
                    <Text style={styles.resultTitle}>Catatan & Rekomendasi Konselor:</Text>
                    <Text style={styles.resultText}>{session.hasil_konseling}</Text>
                  </View>
                ) : null}

                {session.rejection_reason ? (
                  <View style={styles.rejectionBox}>
                    <Text style={styles.rejectionTitle}>Alasan Penolakan:</Text>
                    <Text style={styles.rejectionText}>{session.rejection_reason}</Text>
                  </View>
                ) : null}

                {/* Cancel action for pending sessions */}
                {isPending && (
                  <Pressable
                    onPress={() => handleCancelSession(session)}
                    disabled={isCanceling}
                    style={({ pressed }) => [
                      styles.cancelButton,
                      { opacity: isCanceling ? 0.6 : pressed ? 0.8 : 1 },
                    ]}
                  >
                    {isCanceling ? (
                      <ActivityIndicator size="small" color="#DC2626" />
                    ) : (
                      <>
                        <Icon name="x" size={14} color="#DC2626" />
                        <Text style={styles.cancelButtonText}>Batalkan Pengajuan</Text>
                      </>
                    )}
                  </Pressable>
                )}
              </View>
            )
          })}
        </View>
      )}

      {/* Modal Booking Form */}
      <Modal
        visible={bookModalVisible}
        transparent
        animationType="slide"
        onRequestClose={() => setBookModalVisible(false)}
      >
        <View style={styles.modalOverlay}>
          <View
            style={[
              styles.modalSheet,
              calendarModalVisible && { opacity: 0 },
            ]}
          >
            <View style={styles.modalHeader}>
              <View>
                <Text style={styles.modalTitle}>Ajukan Jadwal Konseling</Text>
                <Text style={styles.modalSubtitle}>Pilih topik dan tentukan tanggal sesi</Text>
              </View>
              <Pressable
                onPress={() => setBookModalVisible(false)}
                style={styles.modalCloseButton}
              >
                <Icon name="x" size={20} color="#64748B" />
              </Pressable>
            </View>

            <ScrollView
              style={{ maxHeight: verticalScale(480) }}
              showsVerticalScrollIndicator={false}
              keyboardShouldPersistTaps="handled"
            >
              {/* Topic Picker */}
              <Text style={styles.inputLabel}>Pilih Topik Konseling</Text>
              <View style={styles.topicChipsContainer}>
                {COUNSELING_TOPICS.map((item) => {
                  const isSelected = topic === item
                  return (
                    <Pressable
                      key={item}
                      onPress={() => setTopic(item)}
                      style={[
                        styles.topicChip,
                        isSelected && {
                          backgroundColor: primaryColor + "18",
                          borderColor: primaryColor,
                        },
                      ]}
                    >
                      <Text
                        style={[
                          styles.topicChipText,
                          isSelected && { color: primaryColor, fontWeight: "700" },
                        ]}
                      >
                        {item}
                      </Text>
                    </Pressable>
                  )
                })}
              </View>

              {/* Tanggal Konseling */}
              <Text style={styles.inputLabel}>Tanggal yang Diharapkan (YYYY-MM-DD)</Text>
              <View style={styles.quickDateRow}>
                <Pressable
                  onPress={() => setQuickDate(1)}
                  style={[
                    styles.quickDateButton,
                    isBesokActive
                      ? { backgroundColor: primaryColor, borderColor: primaryColor }
                      : { backgroundColor: "#F8FAFC", borderColor: "#E2E8F0" },
                  ]}
                >
                  <Text
                    style={[
                      styles.quickDateText,
                      isBesokActive
                        ? { color: Colors.white, fontWeight: "700" }
                        : { color: "#64748B", fontWeight: "500" },
                    ]}
                  >
                    Besok
                  </Text>
                </Pressable>
                <Pressable
                  onPress={() => setQuickDate(2)}
                  style={[
                    styles.quickDateButton,
                    isLusaActive
                      ? { backgroundColor: primaryColor, borderColor: primaryColor }
                      : { backgroundColor: "#F8FAFC", borderColor: "#E2E8F0" },
                  ]}
                >
                  <Text
                    style={[
                      styles.quickDateText,
                      isLusaActive
                        ? { color: Colors.white, fontWeight: "700" }
                        : { color: "#64748B", fontWeight: "500" },
                    ]}
                  >
                    Lusa
                  </Text>
                </Pressable>
                <Pressable
                  onPress={() => setQuickDate(3)}
                  style={[
                    styles.quickDateButton,
                    isPlus3Active
                      ? { backgroundColor: primaryColor, borderColor: primaryColor }
                      : { backgroundColor: "#F8FAFC", borderColor: "#E2E8F0" },
                  ]}
                >
                  <Text
                    style={[
                      styles.quickDateText,
                      isPlus3Active
                        ? { color: Colors.white, fontWeight: "700" }
                        : { color: "#64748B", fontWeight: "500" },
                    ]}
                  >
                    +3 Hari
                  </Text>
                </Pressable>
              </View>

              {/* Date Input Field - Tapping opens calendar popup */}
              <Pressable
                onPress={openCalendarModal}
                style={[
                  styles.textInput,
                  {
                    flexDirection: "row",
                    alignItems: "center",
                    justifyContent: "space-between",
                    paddingVertical: verticalScale(12),
                  },
                ]}
              >
                <Text
                  style={{
                    fontSize: moderateScale(13),
                    color: sessionDate ? "#0F172A" : "#94A3B8",
                    fontWeight: sessionDate ? "600" : "400",
                  }}
                >
                  {sessionDate || "YYYY-MM-DD"}
                </Text>
                <Icon name="calendar" size={18} color="#64748B" />
              </Pressable>

              {/* Catatan Awal */}
              <Text style={styles.inputLabel}>Catatan / Keluhan Singkat (Opsional)</Text>
              <TextInput
                style={[styles.textInput, styles.textArea]}
                placeholder={`Tuliskan secara singkat hal yang ingin dibahas agar ${consultantTitle.toLowerCase()} dapat mempersiapkan diri...`}
                placeholderTextColor="#94A3B8"
                multiline
                numberOfLines={3}
                value={notes}
                onChangeText={setNotes}
              />

              {/* Jaminan Privasi */}
              <View style={styles.confidentialSwitchRow}>
                <View style={{ flex: 1 }}>
                  <Text style={styles.switchTitle}>Jaga Kerahasiaan Penuh</Text>
                  <Text style={styles.switchSubtitle}>
                    Hanya {consultantTitle.toLowerCase()} yang berhak membaca catatan ini.
                  </Text>
                </View>
                <Switch
                  value={isConfidential}
                  onValueChange={setIsConfidential}
                  trackColor={{ false: "#CBD5E1", true: primaryColor }}
                />
              </View>
            </ScrollView>

            {/* Modal Actions */}
            <View style={styles.modalActionButtons}>
              <Pressable
                onPress={() => setBookModalVisible(false)}
                style={styles.modalCancelBtn}
              >
                <Text style={styles.modalCancelBtnText}>Batal</Text>
              </Pressable>

              <Pressable
                onPress={handleBookSession}
                disabled={submitting}
                style={[
                  styles.modalSubmitBtn,
                  { backgroundColor: primaryColor, opacity: submitting ? 0.6 : 1 },
                ]}
              >
                {submitting ? (
                  <ActivityIndicator color={Colors.white} size="small" />
                ) : (
                  <Text style={styles.modalSubmitBtnText}>Kirim Pengajuan</Text>
                )}
              </Pressable>
            </View>
          </View>

          {/* Calendar Datepicker Dialog Popup */}
          {calendarModalVisible && (
            <Pressable
              style={styles.calendarPopupOverlay}
              onPress={() => setCalendarModalVisible(false)}
            >
              <Pressable
                style={styles.calendarCard}
                onPress={(e) => e.stopPropagation?.()}
              >
                {/* Calendar Header */}
                <View style={styles.calendarHeader}>
                  <Pressable onPress={prevMonth} style={styles.monthNavBtn}>
                    <Icon name="chevron-left" size={20} color="#334155" />
                  </Pressable>
                  <Text style={styles.monthTitle}>
                    {MONTH_NAMES[calendarMonth]} {calendarYear}
                  </Text>
                  <Pressable onPress={nextMonth} style={styles.monthNavBtn}>
                    <Icon name="chevron-right" size={20} color="#334155" />
                  </Pressable>
                </View>

                {/* Weekday Names */}
                <View style={styles.weekDaysRow}>
                  {DAY_NAMES.map((dayName, idx) => (
                    <Text
                      key={dayName}
                      style={[
                        styles.weekDayText,
                        (idx === 0 || idx === 6) && { color: "#EF4444" },
                      ]}
                    >
                      {dayName}
                    </Text>
                  ))}
                </View>

                {/* Days Grid */}
                <View style={styles.daysGrid}>
                  {Array.from({ length: getFirstDayOfWeek(calendarYear, calendarMonth) }).map((_, i) => (
                    <View key={`empty-${i}`} style={styles.dayCellEmpty} />
                  ))}
                  {Array.from({ length: getDaysInMonth(calendarYear, calendarMonth) }).map((_, i) => {
                    const dayNum = i + 1
                    const past = isDayPast(dayNum)
                    const selected = isDaySelected(dayNum)
                    const today = isDayToday(dayNum)

                    return (
                      <Pressable
                        key={`day-${dayNum}`}
                        disabled={past}
                        onPress={() => selectCalendarDay(dayNum)}
                        style={[
                          styles.dayCell,
                          today && !selected && { borderColor: primaryColor, borderWidth: 1.5 },
                          selected && { backgroundColor: primaryColor },
                          past && { opacity: 0.35 },
                        ]}
                      >
                        <Text
                          style={[
                            styles.dayCellText,
                            selected && { color: Colors.white, fontWeight: "700" },
                            past && { color: "#94A3B8" },
                            today && !selected && { color: primaryColor, fontWeight: "700" },
                          ]}
                        >
                          {dayNum}
                        </Text>
                      </Pressable>
                    )
                  })}
                </View>

                {/* Bottom Quick Select & Close */}
                <View style={styles.calendarBottomRow}>
                  <View style={{ flexDirection: "row", gap: 8 }}>
                    <Pressable
                      onPress={() => {
                        setQuickDate(0)
                        setCalendarModalVisible(false)
                      }}
                      style={styles.calendarQuickActionBtn}
                    >
                      <Text style={[styles.calendarQuickActionText, { color: primaryColor }]}>Hari Ini</Text>
                    </Pressable>
                    <Pressable
                      onPress={() => {
                        setQuickDate(1)
                        setCalendarModalVisible(false)
                      }}
                      style={styles.calendarQuickActionBtn}
                    >
                      <Text style={[styles.calendarQuickActionText, { color: primaryColor }]}>Besok</Text>
                    </Pressable>
                  </View>
                  <Pressable
                    onPress={() => setCalendarModalVisible(false)}
                    style={styles.calendarCloseBtn}
                  >
                    <Text style={styles.calendarCloseText}>Tutup</Text>
                  </Pressable>
                </View>
              </Pressable>
            </Pressable>
          )}
        </View>
      </Modal>
    </View>
  )
}

const styles = StyleSheet.create({
  container: {
    gap: verticalScale(14),
  },
  heroCard: {
    backgroundColor: Colors.white,
    borderRadius: 18,
    padding: moderateScale(18),
    borderWidth: 1,
    borderColor: "#F1F5F9",
    borderLeftWidth: 4,
    shadowColor: "#0F172A",
    shadowOffset: { width: 0, height: 3 },
    shadowOpacity: 0.05,
    shadowRadius: 8,
    elevation: 3,
  },
  heroHeaderRow: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: verticalScale(12),
  },
  shieldIconWrapper: {
    width: 44,
    height: 44,
    borderRadius: 12,
    alignItems: "center",
    justifyContent: "center",
  },
  heroTitle: {
    fontSize: moderateScale(16),
    fontWeight: "800",
    color: "#0F172A",
    letterSpacing: -0.2,
  },
  heroSubtitle: {
    fontSize: moderateScale(12),
    color: "#64748B",
    marginTop: 2,
    lineHeight: 18,
  },
  confidentialBadge: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    backgroundColor: "#ECFDF5",
    paddingHorizontal: moderateScale(10),
    paddingVertical: verticalScale(5),
    borderRadius: 8,
    marginBottom: verticalScale(14),
    alignSelf: "flex-start",
  },
  confidentialText: {
    fontSize: moderateScale(11),
    fontWeight: "600",
    color: "#047857",
  },
  bookButton: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 8,
    paddingVertical: verticalScale(12),
    borderRadius: 12,
  },
  bookButtonText: {
    color: Colors.white,
    fontSize: moderateScale(13.5),
    fontWeight: "700",
  },
  filterRow: {
    flexDirection: "row",
    gap: 8,
    marginVertical: 4,
  },
  filterChip: {
    paddingHorizontal: moderateScale(12),
    paddingVertical: verticalScale(6),
    borderRadius: 20,
    backgroundColor: Colors.white,
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  filterChipText: {
    fontSize: moderateScale(12),
    color: "#64748B",
    fontWeight: "500",
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
    marginTop: 12,
  },
  emptySubtitle: {
    fontSize: moderateScale(12),
    color: "#64748B",
    textAlign: "center",
    marginTop: 6,
    lineHeight: 18,
  },
  sessionCard: {
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
  sessionHeader: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
  },
  statusBadge: {
    flexDirection: "row",
    alignItems: "center",
    gap: 5,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
  },
  statusBadgeText: {
    fontSize: moderateScale(11),
    fontWeight: "700",
  },
  dateText: {
    fontSize: moderateScale(11.5),
    color: "#64748B",
    fontWeight: "500",
  },
  topicText: {
    fontSize: moderateScale(14.5),
    fontWeight: "700",
    color: "#0F172A",
  },
  notesBox: {
    backgroundColor: "#F8FAFC",
    padding: moderateScale(10),
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  notesLabel: {
    fontSize: moderateScale(11),
    fontWeight: "700",
    color: "#475569",
    marginBottom: 2,
  },
  notesContent: {
    fontSize: moderateScale(12),
    color: "#334155",
    lineHeight: 18,
  },
  confirmedBox: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    backgroundColor: "#F0FDF4",
    padding: moderateScale(8),
    borderRadius: 8,
  },
  confirmedText: {
    fontSize: moderateScale(12),
    fontWeight: "600",
    color: "#166534",
  },
  consultantRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    marginTop: 2,
  },
  consultantName: {
    fontSize: moderateScale(12),
    fontWeight: "600",
    color: "#475569",
  },
  resultBox: {
    backgroundColor: "#EFF6FF",
    padding: moderateScale(10),
    borderRadius: 8,
    borderLeftWidth: 3,
    borderLeftColor: "#2563EB",
  },
  resultTitle: {
    fontSize: moderateScale(11.5),
    fontWeight: "700",
    color: "#1E40AF",
    marginBottom: 2,
  },
  resultText: {
    fontSize: moderateScale(12),
    color: "#1E3A8A",
    lineHeight: 18,
  },
  rejectionBox: {
    backgroundColor: "#FEF2F2",
    padding: moderateScale(10),
    borderRadius: 8,
    borderLeftWidth: 3,
    borderLeftColor: "#DC2626",
  },
  rejectionTitle: {
    fontSize: moderateScale(11.5),
    fontWeight: "700",
    color: "#991B1B",
    marginBottom: 2,
  },
  rejectionText: {
    fontSize: moderateScale(12),
    color: "#7F1D1D",
  },
  cancelButton: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 6,
    paddingVertical: verticalScale(8),
    borderRadius: 8,
    borderWidth: 1,
    borderColor: "#FCA5A5",
    backgroundColor: "#FFF5F5",
    marginTop: 4,
  },
  cancelButtonText: {
    fontSize: moderateScale(12),
    fontWeight: "600",
    color: "#DC2626",
  },

  // Modal Styles
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
  },
  modalHeader: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    borderBottomWidth: 1,
    borderBottomColor: "#F1F5F9",
    paddingBottom: verticalScale(12),
  },
  modalTitle: {
    fontSize: moderateScale(17),
    fontWeight: "800",
    color: "#0F172A",
  },
  modalSubtitle: {
    fontSize: moderateScale(12),
    color: "#64748B",
    marginTop: 2,
  },
  modalCloseButton: {
    padding: 6,
  },
  inputLabel: {
    fontSize: moderateScale(12.5),
    fontWeight: "700",
    color: "#334155",
    marginTop: verticalScale(6),
    marginBottom: verticalScale(4),
  },
  topicChipsContainer: {
    flexDirection: "row",
    flexWrap: "wrap",
    gap: 8,
    marginBottom: verticalScale(8),
  },
  topicChip: {
    paddingHorizontal: moderateScale(12),
    paddingVertical: verticalScale(7),
    borderRadius: 10,
    borderWidth: 1,
    borderColor: "#E2E8F0",
    backgroundColor: "#F8FAFC",
  },
  topicChipText: {
    fontSize: moderateScale(12),
    color: "#475569",
    fontWeight: "500",
  },
  quickDateRow: {
    flexDirection: "row",
    gap: 8,
    marginBottom: verticalScale(6),
  },
  quickDateButton: {
    borderWidth: 1,
    borderRadius: 8,
    paddingHorizontal: moderateScale(10),
    paddingVertical: verticalScale(4),
    backgroundColor: Colors.white,
  },
  quickDateText: {
    fontSize: moderateScale(11),
    fontWeight: "600",
  },
  textInput: {
    backgroundColor: "#F8FAFC",
    borderWidth: 1,
    borderColor: "#E2E8F0",
    borderRadius: 10,
    paddingHorizontal: moderateScale(12),
    paddingVertical: verticalScale(10),
    fontSize: moderateScale(13),
    color: "#0F172A",
  },
  textArea: {
    height: 80,
    textAlignVertical: "top",
  },
  confidentialSwitchRow: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    marginVertical: verticalScale(8),
    padding: moderateScale(10),
    backgroundColor: "#F8FAFC",
    borderRadius: 10,
  },
  switchTitle: {
    fontSize: moderateScale(12.5),
    fontWeight: "700",
    color: "#1E293B",
  },
  switchSubtitle: {
    fontSize: moderateScale(11),
    color: "#64748B",
    marginTop: 2,
  },
  modalActionButtons: {
    flexDirection: "row",
    gap: 10,
    marginTop: verticalScale(8),
  },
  modalCancelBtn: {
    flex: 1,
    paddingVertical: verticalScale(12),
    borderRadius: 12,
    borderWidth: 1,
    borderColor: "#E2E8F0",
    alignItems: "center",
    justifyContent: "center",
  },
  modalCancelBtnText: {
    fontSize: moderateScale(13),
    fontWeight: "600",
    color: "#64748B",
  },
  modalSubmitBtn: {
    flex: 2,
    paddingVertical: verticalScale(12),
    borderRadius: 12,
    alignItems: "center",
    justifyContent: "center",
  },
  modalSubmitBtnText: {
    fontSize: moderateScale(13),
    fontWeight: "700",
    color: Colors.white,
  },
  dateInputWrapper: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
  },
  calendarPopupOverlay: {
    ...StyleSheet.absoluteFillObject,
    backgroundColor: "rgba(15, 23, 42, 0.65)",
    alignItems: "center",
    justifyContent: "center",
    padding: moderateScale(20),
    zIndex: 9999,
    elevation: 20,
  },
  calendarCard: {
    width: "100%",
    maxWidth: 360,
    backgroundColor: Colors.white,
    borderRadius: 20,
    padding: moderateScale(16),
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 8 },
    shadowOpacity: 0.15,
    shadowRadius: 16,
    elevation: 8,
  },
  calendarHeader: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    marginBottom: verticalScale(14),
    paddingHorizontal: moderateScale(4),
  },
  monthNavBtn: {
    padding: 8,
    borderRadius: 8,
    backgroundColor: "#F1F5F9",
  },
  monthTitle: {
    fontSize: moderateScale(15),
    fontWeight: "700",
    color: "#0F172A",
  },
  weekDaysRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginBottom: verticalScale(8),
    borderBottomWidth: 1,
    borderBottomColor: "#F1F5F9",
    paddingBottom: verticalScale(6),
  },
  weekDayText: {
    width: "14.28%",
    textAlign: "center",
    fontSize: moderateScale(11),
    fontWeight: "600",
    color: "#64748B",
  },
  daysGrid: {
    flexDirection: "row",
    flexWrap: "wrap",
    marginBottom: verticalScale(14),
  },
  dayCellEmpty: {
    width: "14.28%",
    height: 38,
  },
  dayCell: {
    width: "14.28%",
    height: 38,
    alignItems: "center",
    justifyContent: "center",
    borderRadius: 19,
    marginVertical: 2,
  },
  dayCellText: {
    fontSize: moderateScale(13),
    fontWeight: "500",
    color: "#1E293B",
  },
  calendarBottomRow: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    borderTopWidth: 1,
    borderTopColor: "#F1F5F9",
    paddingTop: verticalScale(12),
  },
  calendarQuickActionBtn: {
    paddingHorizontal: moderateScale(10),
    paddingVertical: verticalScale(6),
    borderRadius: 8,
    backgroundColor: "#F8FAFC",
  },
  calendarQuickActionText: {
    fontSize: moderateScale(12),
    fontWeight: "600",
  },
  calendarCloseBtn: {
    paddingHorizontal: moderateScale(12),
    paddingVertical: verticalScale(6),
    borderRadius: 8,
    backgroundColor: "#E2E8F0",
  },
  calendarCloseText: {
    fontSize: moderateScale(12),
    fontWeight: "600",
    color: "#475569",
  },
})
