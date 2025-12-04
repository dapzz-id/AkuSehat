"use client"

import React, { useEffect, useState, useMemo } from "react"
import { View, Text, Dimensions, ActivityIndicator, StyleSheet, Pressable, ScrollView } from "react-native"
import { LineChart } from "react-native-chart-kit"
import { Colors } from "../settings/Colors"
import api from "../api/axiosConfig"

const FILTERS = [
  { key: "1m", label: "1M", months: 1 },
  { key: "3m", label: "3M", months: 3 },
  { key: "6m", label: "6M", months: 6 },
  { key: "1y", label: "1Y", months: 12 },
  { key: "3y", label: "3Y", months: 36 },
  { key: "5y", label: "5Y", months: 60 },
]

export default function HealthLineChart() {
  const [loading, setLoading] = useState(true)
  const [data, setData] = useState<any[]>([])
  const [filter, setFilter] = useState("1m")

  useEffect(() => {
    const fetchSummary = async () => {
      try {
        setLoading(true)
        const res = await api.get("/member/summary/grafik")
        if (res.data?.status) {
          setData(res.data.data || [])
        } else {
          setData([])
        }
      } catch (e) {
        console.log("Fetch summary failed", e)
        setData([])
      } finally {
        setLoading(false)
      }
    }
    fetchSummary()
  }, [])

  const screenWidth = Dimensions.get("window").width - 48

  // ⏳ Filter dan ubah data sesuai jenis grafik
  const filteredData = useMemo(() => {
    if (!Array.isArray(data) || data.length === 0) return []
    const now = new Date()
    const filterObj = FILTERS.find(f => f.key === filter)
    const monthsToShow = filterObj?.months || 3

    // 🔹 Jika 3Y atau 5Y → tampilkan per tahun
    if (filter === "3y" || filter === "5y") {
      const filteredYears = data
        .filter(y => {
          if (!y || typeof y !== 'object') return false
          const year = parseInt(y.year)
          if (isNaN(year)) return false
          return year >= now.getFullYear() - monthsToShow / 12 + 1
        })
        .map(y => ({
          label: String(y.year || ""),
          score: Math.min(parseFloat(y.avg_score) || 0, 100),
        }))
        .filter(item => item.label && !isNaN(item.score))

      return filteredYears
    }

    // 🔹 Jika bulanan (1m, 3m, 6m, 1y)
    const months: any[] = []

    data.forEach(y => {
      if (Array.isArray(y.data)) {
        y.data.forEach((m: any) => {
          if (m && typeof m === 'object') {
            months.push(m)
          }
        })
      }
    })

    const end = new Date()
    const start = new Date()
    start.setMonth(start.getMonth() - monthsToShow)

    const filteredMonths = months
      .filter(i => {
        if (!i || typeof i !== 'object') return false

        let year = i.year
        let month = i.month

        if (year === undefined || month === undefined) return false

        year = parseInt(year)
        month = parseInt(month)

        if (isNaN(year) || isNaN(month)) return false

        const d = new Date(year, month - 1)

        if (filter === "1y") {
          return year === now.getFullYear()
        }

        return d >= start && d <= end
      })
      .map(i => {
        let label = ""
        let score = 0

        if (i.label && typeof i.label === 'string') {
          label = i.label.split(" ")[0]
        } else {
          // Jika tidak ada label, buat dari month/year
          const monthNames = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"]
          const monthIdx = parseInt(i.month) - 1
          label = monthIdx >= 0 && monthIdx < 12 ? monthNames[monthIdx] : `M${i.month}`
        }

        score = Math.min(parseFloat(i.score) || 0, 100)

        return {
          label,
          score,
        }
      })
      .filter(item => item.label && !isNaN(item.score))

    return filteredMonths
  }, [data, filter])

  const chartLabels = filteredData.map(i => i.label)
  const chartValues = filteredData.map(i => i.score)

  const avg = chartValues.length > 0 ?
    chartValues.reduce((a, b) => a + b, 0) / chartValues.length : 0

  const getColor = (n: number) => (n >= 75 ? Colors.primary : n >= 50 ? "#FACC15" : "#EF4444")
  const lineColor = getColor(avg)

  if (loading) {
    return (
      <View style={styles.loadingWrap}>
        <ActivityIndicator color={Colors.primary} />
        <Text style={{ color: Colors.text, marginTop: 6 }}>Memuat data tren...</Text>
      </View>
    )
  }

  return (
    <View style={styles.wrap}>
      <View style={styles.header}>
        <View style={styles.filterRow}>
          {FILTERS.map(f => {
            const active = f.key === filter
            return (
              <Pressable
                key={f.key}
                onPress={() => setFilter(f.key)}
                style={[
                  styles.chip,
                  {
                    backgroundColor: active ? Colors.text : Colors.white,
                    borderColor: active ? Colors.text : Colors.border,
                    marginBottom: 6,
                  },
                ]}
              >
                <Text style={{ color: active ? Colors.white : Colors.text, fontWeight: "600", fontSize: 12 }}>
                  {f.label}
                </Text>
              </Pressable>
            )
          })}
        </View>
      </View>

      {filteredData.length === 0 ? (
        <View style={{ paddingVertical: 20, alignItems: 'center' }}>
          <Text style={{ color: Colors.muted, textAlign: "center" }}>
            Tidak ada data untuk periode ini
          </Text>
        </View>
      ) : (
        <ScrollView horizontal showsHorizontalScrollIndicator={false}>
          <View>
            <LineChart
              data={{
                labels: chartLabels,
                datasets: [
                  {
                    data: chartValues.map(v => Math.max(0, Math.min(100, v))),
                    color: () => lineColor,
                    strokeWidth: 3,
                  },
                ],
              }}
              width={Math.max(screenWidth, chartLabels.length * 50)}
              height={220}
              fromZero
              yAxisSuffix="%"
              withShadow={false}
              withInnerLines
              withOuterLines
              chartConfig={{
                backgroundColor: Colors.white,
                backgroundGradientFrom: Colors.white,
                backgroundGradientTo: Colors.white,
                decimalPlaces: 0,
                color: () => lineColor,
                labelColor: () => Colors.text,
                propsForDots: { r: "4", strokeWidth: "2", stroke: "#fff" },
                propsForBackgroundLines: { strokeDasharray: "3 6" },
              }}
              yLabelsOffset={12}
              style={{ borderRadius: 12, marginVertical: 8 }}
              segments={4}
              bezier
            />
          </View>
        </ScrollView>
      )}
    </View>
  )
}

const styles = StyleSheet.create({
  wrap: {
    borderRadius: 16,
  },
  header: {
    flexDirection: "column",
    justifyContent: "space-between",
    alignItems: "center",
    marginBottom: 12,
  },
  title: {
    fontSize: 18,
    fontWeight: "700",
    color: Colors.text,
    marginBottom: 18,
    marginTop: 6
  },
  filterRow: {
    flexDirection: "row",
    gap: 6,
    flexWrap: 'wrap',
    justifyContent: 'center'
  },
  chip: {
    paddingVertical: 6,
    paddingHorizontal: 10,
    borderRadius: 999,
    borderWidth: 1,
    minWidth: 40,
    alignItems: 'center',
  },
  loadingWrap: {
    alignItems: "center",
    justifyContent: "center",
    height: 180,
  },
})