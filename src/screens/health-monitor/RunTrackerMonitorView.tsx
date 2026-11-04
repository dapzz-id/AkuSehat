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
  useWindowDimensions,
} from "react-native"
import Icon from "react-native-vector-icons/Feather"
import api from "../../api/axiosConfig"
import { Colors } from "../../settings/Colors"
import { moderateScale, horizontalScale, verticalScale } from "../../utils/responsive"

export type ActivityItem = {
  id: string | number
  user_id: string | number
  user_nama: string
  nomor_induk?: string
  divisi_name?: string
  activity_type: "run" | "walk" | "cycling" | string
  title?: string
  distance_meters: number
  duration_seconds: number
  calories: number
  avg_speed_kmh?: number
  started_at: string
}

type Division = {
  id: string | number
  divisi_name: string
}

type Summary = {
  total_distance_km: number
  total_sessions: number
  total_calories: number
  count_run: number
  count_walk: number
  count_cycling: number
}

interface Props {
  themeColor?: string
}

export default function RunTrackerMonitorView({ themeColor }: Props) {
  const { width } = useWindowDimensions()
  const primaryColor = themeColor || Colors.primary

  const [activities, setActivities] = useState<ActivityItem[]>([])
  const [divisions, setDivisions] = useState<Division[]>([])
  const [summary, setSummary] = useState<Summary | null>(null)
  const [loading, setLoading] = useState(false)
  const [refreshing, setRefreshing] = useState(false)

  // Filters
  const [selectedDivisiId, setSelectedDivisiId] = useState<string>("")
  const [selectedType, setSelectedType] = useState<string>("")
  const [searchQuery, setSearchQuery] = useState<string>("")

  const fetchMonitoringData = useCallback(async () => {
    try {
      setLoading(true)
      const params: any = {}
      if (selectedDivisiId) params.divisi_id = selectedDivisiId
      if (selectedType) params.activity_type = selectedType
      if (searchQuery.trim()) params.search = searchQuery.trim()

      const res = await api.get("/activities/monitoring", { params })
      if (res?.data?.success) {
        setActivities(res.data.data?.data || [])
        setDivisions(res.data.divisions || [])
        setSummary(res.data.summary || null)
      }
    } catch (e: any) {
      console.warn("Gagal memuat monitoring aktivitas:", e)
    } finally {
      setLoading(false)
      setRefreshing(false)
    }
  }, [selectedDivisiId, selectedType, searchQuery])

  useEffect(() => {
    fetchMonitoringData()
  }, [fetchMonitoringData])

  const onRefresh = () => {
    setRefreshing(true)
    fetchMonitoringData()
  }

  const formatDuration = (seconds: number) => {
    const mins = Math.floor(seconds / 60)
    const secs = seconds % 60
    if (mins >= 60) {
      const hrs = Math.floor(mins / 60)
      const remMins = mins % 60
      return `${hrs}j ${remMins}m`
    }
    return `${mins}m ${secs}d`
  }

  const getActivityBadge = (type: string) => {
    switch (type.toLowerCase()) {
      case "run":
        return { label: "Lari", icon: "activity", bg: "#DCFCE7", text: "#15803D" }
      case "walk":
        return { label: "Jalan Santai", icon: "map-pin", bg: "#E0F2FE", text: "#0284C7" }
      case "cycling":
        return { label: "Bersepeda", icon: "disc", bg: "#FEF3C7", text: "#D97706" }
      default:
        return { label: "Olahraga", icon: "award", bg: "#F3E8FF", text: "#9333EA" }
    }
  }

  return (
    <ScrollView
      showsVerticalScrollIndicator={false}
      refreshControl={
        <RefreshControl refreshing={refreshing} onRefresh={onRefresh} colors={[primaryColor]} />
      }
      contentContainerStyle={{ paddingBottom: verticalScale(30), gap: verticalScale(14) }}
    >
      {/* Header Info */}
      <View style={styles.headerBox}>
        <View style={[styles.headerIcon, { backgroundColor: `${primaryColor}15` }]}>
          <Icon name="trending-up" size={20} color={primaryColor} />
        </View>
        <View style={{ flex: 1 }}>
          <Text style={styles.headerTitle}>Monitoring Run Tracker Siswa</Text>
          <Text style={styles.headerSubtitle}>
            Pantau hasil aktivitas fisik & lari siswa per kelas / divisi secara realtime
          </Text>
        </View>
      </View>

      {/* Summary KPI Cards */}
      {summary && (
        <View style={styles.summaryGrid}>
          <View style={styles.summaryCard}>
            <View style={[styles.summaryIcon, { backgroundColor: "#DCFCE7" }]}>
              <Icon name="navigation" size={16} color="#15803D" />
            </View>
            <Text style={styles.summaryValue}>{summary.total_distance_km} km</Text>
            <Text style={styles.summaryLabel}>Total Jarak</Text>
          </View>
          <View style={styles.summaryCard}>
            <View style={[styles.summaryIcon, { backgroundColor: "#E0F2FE" }]}>
              <Icon name="activity" size={16} color="#0284C7" />
            </View>
            <Text style={styles.summaryValue}>{summary.total_sessions}</Text>
            <Text style={styles.summaryLabel}>Total Sesi Lari</Text>
          </View>
          <View style={styles.summaryCard}>
            <View style={[styles.summaryIcon, { backgroundColor: "#FEF3C7" }]}>
              <Icon name="zap" size={16} color="#D97706" />
            </View>
            <Text style={styles.summaryValue}>{Math.round(summary.total_calories)}</Text>
            <Text style={styles.summaryLabel}>Kalori (kcal)</Text>
          </View>
        </View>
      )}

      {/* Search Input */}
      <View style={styles.searchRow}>
        <Icon name="search" size={16} color="#94A3B8" style={{ marginLeft: 12 }} />
        <TextInput
          value={searchQuery}
          onChangeText={setSearchQuery}
          placeholder="Cari nama / NIS siswa..."
          placeholderTextColor="#94A3B8"
          style={styles.searchInput}
          returnKeyType="search"
          onSubmitEditing={fetchMonitoringData}
        />
        {searchQuery.length > 0 && (
          <Pressable onPress={() => setSearchQuery("")} style={{ padding: 8 }}>
            <Icon name="x" size={14} color="#94A3B8" />
          </Pressable>
        )}
      </View>

      {/* Division Chips Filter */}
      <View>
        <Text style={styles.filterSectionTitle}>Filter Divisi / Kelas</Text>
        <ScrollView horizontal showsHorizontalScrollIndicator={false} contentContainerStyle={styles.chipScroll}>
          <Pressable
            onPress={() => setSelectedDivisiId("")}
            style={[
              styles.filterChip,
              selectedDivisiId === "" && { backgroundColor: primaryColor, borderColor: primaryColor },
            ]}
          >
            <Text
              style={[
                styles.filterChipText,
                selectedDivisiId === "" && { color: "#FFF", fontWeight: "700" },
              ]}
            >
              Semua Divisi
            </Text>
          </Pressable>
          {divisions.map((div) => {
            const isSelected = String(selectedDivisiId) === String(div.id)
            return (
              <Pressable
                key={div.id}
                onPress={() => setSelectedDivisiId(String(div.id))}
                style={[
                  styles.filterChip,
                  isSelected && { backgroundColor: primaryColor, borderColor: primaryColor },
                ]}
              >
                <Text
                  style={[
                    styles.filterChipText,
                    isSelected && { color: "#FFF", fontWeight: "700" },
                  ]}
                >
                  {div.divisi_name}
                </Text>
              </Pressable>
            )
          })}
        </ScrollView>
      </View>

      {/* Activity Type Chips Filter */}
      <View style={styles.typeRow}>
        {[
          { key: "", label: "Semua Olahraga" },
          { key: "run", label: "Lari" },
          { key: "walk", label: "Jalan Santai" },
          { key: "cycling", label: "Bersepeda" },
        ].map((t) => {
          const isSelected = selectedType === t.key
          return (
            <Pressable
              key={t.key}
              onPress={() => setSelectedType(t.key)}
              style={[
                styles.typeButton,
                isSelected && { backgroundColor: `${primaryColor}15`, borderColor: primaryColor },
              ]}
            >
              <Text
                style={[
                  styles.typeButtonText,
                  isSelected && { color: primaryColor, fontWeight: "700" },
                ]}
              >
                {t.label}
              </Text>
            </Pressable>
          )
        })}
      </View>

      {/* List Activity Cards */}
      <View style={{ gap: verticalScale(10) }}>
        <Text style={styles.sectionHeader}>Log Aktivitas Terbaru ({activities.length})</Text>

        {loading && !refreshing ? (
          <ActivityIndicator size="small" color={primaryColor} style={{ marginTop: 20 }} />
        ) : activities.length === 0 ? (
          <View style={styles.emptyCard}>
            <Icon name="inbox" size={36} color="#CBD5E1" />
            <Text style={styles.emptyTitle}>Belum Ada Aktivitas Olahraga</Text>
            <Text style={styles.emptyText}>
              Siswa di kelas/divisi yang dipilih belum mencatat log lari atau olahraga hari ini.
            </Text>
          </View>
        ) : (
          activities.map((item) => {
            const badge = getActivityBadge(item.activity_type)
            const distKm = (item.distance_meters / 1000).toFixed(2)
            return (
              <View key={item.id} style={styles.activityCard}>
                {/* User & Division Row */}
                <View style={styles.cardTopRow}>
                  <View style={{ flex: 1 }}>
                    <Text style={styles.userName}>{item.user_nama}</Text>
                    <View style={styles.subUserRow}>
                      {item.nomor_induk ? (
                        <Text style={styles.userNis}>NIS: {item.nomor_induk}</Text>
                      ) : null}
                      {item.divisi_name ? (
                        <View style={styles.divisiTag}>
                          <Text style={styles.divisiTagText}>{item.divisi_name}</Text>
                        </View>
                      ) : null}
                    </View>
                  </View>
                  <View style={[styles.activityBadge, { backgroundColor: badge.bg }]}>
                    <Icon name={badge.icon as any} size={11} color={badge.text} />
                    <Text style={[styles.activityBadgeText, { color: badge.text }]}>
                      {badge.label}
                    </Text>
                  </View>
                </View>

                {/* Metrics Grid */}
                <View style={styles.metricsGrid}>
                  <View style={styles.metricItem}>
                    <Text style={styles.metricLabel}>Jarak</Text>
                    <Text style={[styles.metricValue, { color: primaryColor }]}>{distKm} km</Text>
                  </View>
                  <View style={styles.metricItem}>
                    <Text style={styles.metricLabel}>Durasi</Text>
                    <Text style={styles.metricValue}>{formatDuration(item.duration_seconds)}</Text>
                  </View>
                  <View style={styles.metricItem}>
                    <Text style={styles.metricLabel}>Kecepatan</Text>
                    <Text style={styles.metricValue}>{item.avg_speed_kmh || 0} km/j</Text>
                  </View>
                  <View style={styles.metricItem}>
                    <Text style={styles.metricLabel}>Kalori</Text>
                    <Text style={styles.metricValue}>{Math.round(item.calories)} kcal</Text>
                  </View>
                </View>

                {/* Started At Timestamp */}
                <View style={styles.cardFooter}>
                  <Icon name="clock" size={11} color="#94A3B8" />
                  <Text style={styles.footerDate}>
                    {new Date(item.started_at).toLocaleDateString("id-ID", {
                      day: "numeric",
                      month: "short",
                      year: "numeric",
                      hour: "2-digit",
                      minute: "2-digit",
                    })}
                  </Text>
                </View>
              </View>
            )
          })
        )}
      </View>
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
  summaryGrid: {
    flexDirection: "row",
    gap: 8,
  },
  summaryCard: {
    flex: 1,
    backgroundColor: "#FFF",
    padding: moderateScale(12),
    borderRadius: moderateScale(14),
    borderWidth: 1,
    borderColor: "#E2E8F0",
    alignItems: "center",
  },
  summaryIcon: {
    width: 28,
    height: 28,
    borderRadius: 8,
    alignItems: "center",
    justifyContent: "center",
    marginBottom: 6,
  },
  summaryValue: {
    fontSize: moderateScale(14),
    fontWeight: "800",
    color: "#0F172A",
  },
  summaryLabel: {
    fontSize: moderateScale(10),
    color: "#64748B",
    marginTop: 2,
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
  filterSectionTitle: {
    fontSize: moderateScale(12),
    fontWeight: "700",
    color: "#475569",
    marginBottom: 6,
  },
  chipScroll: {
    gap: 8,
  },
  filterChip: {
    paddingHorizontal: horizontalScale(14),
    paddingVertical: verticalScale(7),
    borderRadius: moderateScale(20),
    backgroundColor: "#FFF",
    borderWidth: 1,
    borderColor: "#E2E8F0",
  },
  filterChipText: {
    fontSize: moderateScale(11.5),
    fontWeight: "600",
    color: "#64748B",
  },
  typeRow: {
    flexDirection: "row",
    flexWrap: "wrap",
    gap: 6,
  },
  typeButton: {
    paddingHorizontal: horizontalScale(10),
    paddingVertical: verticalScale(5),
    borderRadius: moderateScale(8),
    borderWidth: 1,
    borderColor: "#E2E8F0",
    backgroundColor: "#FFF",
  },
  typeButtonText: {
    fontSize: moderateScale(11),
    color: "#64748B",
  },
  sectionHeader: {
    fontSize: moderateScale(13),
    fontWeight: "700",
    color: "#334155",
    marginTop: 4,
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
  activityCard: {
    backgroundColor: "#FFF",
    padding: moderateScale(14),
    borderRadius: moderateScale(16),
    borderWidth: 1,
    borderColor: "#E2E8F0",
    gap: verticalScale(10),
  },
  cardTopRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "flex-start",
  },
  userName: {
    fontSize: moderateScale(13.5),
    fontWeight: "700",
    color: "#0F172A",
  },
  subUserRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    marginTop: 2,
  },
  userNis: {
    fontSize: moderateScale(11),
    color: "#64748B",
  },
  divisiTag: {
    backgroundColor: "#F1F5F9",
    paddingHorizontal: horizontalScale(6),
    paddingVertical: verticalScale(2),
    borderRadius: 4,
  },
  divisiTagText: {
    fontSize: moderateScale(10),
    fontWeight: "600",
    color: "#475569",
  },
  activityBadge: {
    flexDirection: "row",
    alignItems: "center",
    gap: 4,
    paddingHorizontal: horizontalScale(8),
    paddingVertical: verticalScale(4),
    borderRadius: moderateScale(20),
  },
  activityBadgeText: {
    fontSize: moderateScale(10.5),
    fontWeight: "700",
  },
  metricsGrid: {
    flexDirection: "row",
    backgroundColor: "#F8FAFC",
    padding: moderateScale(10),
    borderRadius: moderateScale(12),
  },
  metricItem: {
    flex: 1,
    alignItems: "center",
  },
  metricLabel: {
    fontSize: moderateScale(10),
    color: "#64748B",
    marginBottom: 2,
  },
  metricValue: {
    fontSize: moderateScale(12.5),
    fontWeight: "700",
    color: "#0F172A",
  },
  cardFooter: {
    flexDirection: "row",
    alignItems: "center",
    gap: 5,
  },
  footerDate: {
    fontSize: moderateScale(10.5),
    color: "#94A3B8",
  },
})

