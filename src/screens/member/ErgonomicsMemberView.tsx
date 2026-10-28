import React, { useState, useEffect } from "react";
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ScrollView,
  ActivityIndicator,
  Alert,
} from "react-native";
import Icon from "react-native-vector-icons/Feather";
import { Colors } from "../../settings/Colors";
import api from "../../api/axiosConfig";

interface ErgonomicsMemberViewProps {
  themeColor?: string;
}

const POSTURE_COMPLAINTS = [
  { id: "tidak_ada", label: "Bebas Keluhan", icon: "smile" },
  { id: "leher", label: "Leher Kaku", icon: "alert-circle" },
  { id: "punggung", label: "Punggung Bawah", icon: "activity" },
  { id: "bahu", label: "Bahu Tegang", icon: "arrow-up" },
  { id: "pergelangan", label: "Pergelangan", icon: "mouse-pointer" },
  { id: "mata", label: "Mata Lelah", icon: "eye" },
];

const STRETCHES = [
  {
    title: "Peregangan Leher",
    duration: "20 detik",
    desc: "Miringkan kepala perlahan ke bahu kanan, tahan 10 dtk, ulangi ke kiri.",
    icon: "smile",
  },
  {
    title: "Putar Bahu & Dada",
    duration: "20 detik",
    desc: "Tarik bahu ke belakang dan putar melingkar untuk membuka rongga dada.",
    icon: "refresh-cw",
  },
  {
    title: "Peregangan Punggung",
    duration: "30 detik",
    desc: "Duduk tegak, letakkan tangan di pinggang dan lengkungkan tubuh ke belakang lembut.",
    icon: "activity",
  },
  {
    title: "Fleksi Pergelangan",
    duration: "15 detik",
    desc: "Luruskan tangan ke depan, tarik jemari ke belakang menggunakan tangan lain.",
    icon: "hand",
  },
];

export default function ErgonomicsMemberView({ themeColor = "#0891b2" }: ErgonomicsMemberViewProps) {
  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);
  const [sedentaryMinutes, setSedentaryMinutes] = useState(180);
  const [breaksTaken, setBreaksTaken] = useState(2);
  const [complaint, setComplaint] = useState("tidak_ada");
  const [score, setScore] = useState(85);

  useEffect(() => {
    fetchToday();
  }, []);

  const fetchToday = async () => {
    setLoading(true);
    try {
      const res = await api.get("/ergonomics/today");
      if (res.data?.success && res.data.data) {
        const d = res.data.data;
        if (d.sedentary_minutes !== undefined) setSedentaryMinutes(d.sedentary_minutes);
        if (d.breaks_taken !== undefined) setBreaksTaken(d.breaks_taken);
        if (d.posture_complaint) setComplaint(d.posture_complaint);
        if (d.score !== undefined) setScore(d.score);
      }
    } catch (e) {
      console.log("Error fetch ergonomics", e);
    } finally {
      setLoading(false);
    }
  };

  const adjustSedentary = (delta: number) => {
    setSedentaryMinutes((prev) => Math.max(0, Math.min(720, prev + delta)));
  };

  const handleTakeBreak = async () => {
    try {
      const res = await api.post("/ergonomics/break");
      if (res.data?.success && res.data.data) {
        setBreaksTaken(res.data.data.breaks_taken);
        setScore(res.data.data.score);
        Alert.alert("Hebat!", "Peregangan postur berhasil dicatat! Skor ergonomi Anda meningkat.");
      }
    } catch (e) {
      Alert.alert("Error", "Gagal mencatat peregangan.");
    }
  };

  const handleSave = async () => {
    setSaving(true);
    try {
      const res = await api.post("/ergonomics", {
        sedentary_minutes: sedentaryMinutes,
        breaks_taken: breaksTaken,
        posture_complaint: complaint,
      });
      if (res.data?.success) {
        if (res.data.data?.score) setScore(res.data.data.score);
        Alert.alert("Tersimpan", "Evaluasi postur dan ergonomi harian Anda berhasil diperbarui.");
      }
    } catch (e) {
      Alert.alert("Error", "Gagal menyimpan evaluasi ergonomi.");
    } finally {
      setSaving(false);
    }
  };

  const getScoreBadge = () => {
    if (score >= 80) return { label: "Prima (Sangat Baik)", color: "#16a34a", bg: "#dcfce7" };
    if (score >= 60) return { label: "Waspada (Cukup)", color: "#ca8a04", bg: "#fef9c3" };
    return { label: "Berisiko (Perlu Istirahat)", color: "#dc2626", bg: "#fee2e2" };
  };

  const badge = getScoreBadge();

  if (loading) {
    return (
      <View style={styles.centerBox}>
        <ActivityIndicator size="large" color={themeColor} />
        <Text style={styles.loadingText}>Memuat evaluasi ergonomi...</Text>
      </View>
    );
  }

  return (
    <ScrollView contentContainerStyle={styles.container} showsVerticalScrollIndicator={false}>
      {/* 1. Score Overview Banner */}
      <View style={[styles.card, styles.scoreCard]}>
        <View style={styles.scoreRow}>
          <View>
            <Text style={styles.scoreTitle}>Indeks Ergonomi Hari Ini</Text>
            <View style={[styles.badge, { backgroundColor: badge.bg }]}>
              <Text style={[styles.badgeText, { color: badge.color }]}>{badge.label}</Text>
            </View>
          </View>
          <View style={[styles.scoreCircle, { borderColor: badge.color }]}>
            <Text style={[styles.scoreValue, { color: badge.color }]}>{score}</Text>
            <Text style={styles.scoreUnit}>/ 100</Text>
          </View>
        </View>
        <Text style={styles.scoreHint}>
          Target: Batasi duduk diam maksimal 2 jam tanpa jeda, lakukan peregangan minimal 3-4 kali sehari.
        </Text>
      </View>

      {/* 2. Sedentary Duration Counter */}
      <View style={styles.card}>
        <Text style={styles.sectionTitle}>Durasi Duduk Diam Hari Ini</Text>
        <Text style={styles.sectionSub}>Waktu yang dihabiskan duduk di meja belajar/kantor</Text>

        <View style={styles.counterRow}>
          <TouchableOpacity style={styles.counterBtn} onPress={() => adjustSedentary(-30)}>
            <Icon name="minus" size={20} color="#334155" />
          </TouchableOpacity>
          <View style={styles.counterDisplay}>
            <Text style={styles.counterValue}>
              {Math.floor(sedentaryMinutes / 60)}j {sedentaryMinutes % 60}m
            </Text>
            <Text style={styles.counterUnit}>({sedentaryMinutes} menit)</Text>
          </View>
          <TouchableOpacity style={styles.counterBtn} onPress={() => adjustSedentary(30)}>
            <Icon name="plus" size={20} color="#334155" />
          </TouchableOpacity>
        </View>

        {/* Quick Break Button */}
        <View style={styles.breakBanner}>
          <View style={{ flex: 1 }}>
            <Text style={styles.breakBannerTitle}>Peregangan Dilakukan: {breaksTaken}x</Text>
            <Text style={styles.breakBannerSub}>Setiap 1 sesi peregangan memulihkan postur & sirkulasi.</Text>
          </View>
          <TouchableOpacity style={[styles.btnAction, { backgroundColor: themeColor }]} onPress={handleTakeBreak}>
            <Icon name="refresh-cw" size={14} color="#ffffff" style={{ marginRight: 6 }} />
            <Text style={styles.btnActionText}>+1 Break</Text>
          </TouchableOpacity>
        </View>
      </View>

      {/* 3. Posture Complaints Selector */}
      <View style={styles.card}>
        <Text style={styles.sectionTitle}>Keluhan Tubuh / Nyeri Postur</Text>
        <Text style={styles.sectionSub}>Pilih keluhan fisik yang Anda rasakan hari ini</Text>
        <View style={styles.chipsContainer}>
          {POSTURE_COMPLAINTS.map((item) => {
            const isSelected = complaint === item.id;
            return (
              <TouchableOpacity
                key={item.id}
                onPress={() => setComplaint(item.id)}
                style={[
                  styles.chip,
                  isSelected && { backgroundColor: `${themeColor}15`, borderColor: themeColor },
                ]}
              >
                <Icon
                  name={item.icon}
                  size={14}
                  color={isSelected ? themeColor : "#64748b"}
                  style={{ marginRight: 6 }}
                />
                <Text style={[styles.chipText, isSelected && { color: themeColor, fontWeight: "700" }]}>
                  {item.label}
                </Text>
              </TouchableOpacity>
            );
          })}
        </View>
      </View>

      {/* 4. Guided Stretches Carousel */}
      <View style={styles.card}>
        <Text style={styles.sectionTitle}>Panduan 4 Peregangan Cepat</Text>
        <Text style={styles.sectionSub}>Lakukan di sela jam belajar atau bekerja</Text>
        <View style={{ gap: 10, marginTop: 10 }}>
          {STRETCHES.map((s, idx) => (
            <View key={idx} style={styles.stretchItem}>
              <View style={[styles.stretchIconBox, { backgroundColor: `${themeColor}15` }]}>
                <Icon name={s.icon} size={16} color={themeColor} />
              </View>
              <View style={{ flex: 1 }}>
                <View style={{ flexDirection: "row", justifyContent: "space-between", alignItems: "center" }}>
                  <Text style={styles.stretchItemTitle}>{s.title}</Text>
                  <Text style={[styles.stretchDuration, { color: themeColor }]}>{s.duration}</Text>
                </View>
                <Text style={styles.stretchItemDesc}>{s.desc}</Text>
              </View>
            </View>
          ))}
        </View>
      </View>

      {/* Save Button */}
      <TouchableOpacity
        style={[styles.saveBtn, { backgroundColor: themeColor }]}
        onPress={handleSave}
        disabled={saving}
      >
        {saving ? (
          <ActivityIndicator color="#ffffff" />
        ) : (
          <>
            <Icon name="check-circle" size={18} color="#ffffff" style={{ marginRight: 8 }} />
            <Text style={styles.saveBtnText}>Simpan Evaluasi Ergonomi</Text>
          </>
        )}
      </TouchableOpacity>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    padding: 16,
    gap: 14,
  },
  centerBox: {
    padding: 40,
    alignItems: "center",
    justifyContent: "center",
  },
  loadingText: {
    marginTop: 10,
    fontSize: 13,
    color: "#64748b",
  },
  card: {
    backgroundColor: "#ffffff",
    borderRadius: 16,
    padding: 16,
    borderWidth: 1,
    borderColor: "#e2e8f0",
  },
  scoreCard: {
    backgroundColor: "#f8fafc",
  },
  scoreRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },
  scoreTitle: {
    fontSize: 14,
    fontWeight: "700",
    color: "#1e293b",
    marginBottom: 6,
  },
  badge: {
    alignSelf: "flex-start",
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 20,
  },
  badgeText: {
    fontSize: 11,
    fontWeight: "700",
  },
  scoreCircle: {
    width: 64,
    height: 64,
    borderRadius: 32,
    borderWidth: 3,
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "#ffffff",
  },
  scoreValue: {
    fontSize: 20,
    fontWeight: "800",
  },
  scoreUnit: {
    fontSize: 9,
    color: "#94a3b8",
    marginTop: -2,
  },
  scoreHint: {
    fontSize: 11,
    color: "#64748b",
    marginTop: 12,
    lineHeight: 16,
  },
  sectionTitle: {
    fontSize: 14,
    fontWeight: "700",
    color: "#1e293b",
  },
  sectionSub: {
    fontSize: 11,
    color: "#64748b",
    marginTop: 2,
    marginBottom: 12,
  },
  counterRow: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    backgroundColor: "#f8fafc",
    borderRadius: 14,
    padding: 8,
    borderWidth: 1,
    borderColor: "#f1f5f9",
  },
  counterBtn: {
    width: 44,
    height: 44,
    borderRadius: 12,
    backgroundColor: "#ffffff",
    alignItems: "center",
    justifyContent: "center",
    borderWidth: 1,
    borderColor: "#e2e8f0",
  },
  counterDisplay: {
    alignItems: "center",
  },
  counterValue: {
    fontSize: 20,
    fontWeight: "800",
    color: "#0f172a",
  },
  counterUnit: {
    fontSize: 11,
    color: "#94a3b8",
    marginTop: 2,
  },
  breakBanner: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    backgroundColor: "#f0fdfa",
    borderRadius: 12,
    padding: 12,
    marginTop: 14,
    borderWidth: 1,
    borderColor: "#ccfbf1",
  },
  breakBannerTitle: {
    fontSize: 13,
    fontWeight: "700",
    color: "#0f766e",
  },
  breakBannerSub: {
    fontSize: 10,
    color: "#115e59",
    marginTop: 2,
  },
  btnAction: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 10,
  },
  btnActionText: {
    fontSize: 12,
    fontWeight: "700",
    color: "#ffffff",
  },
  chipsContainer: {
    flexDirection: "row",
    flexWrap: "wrap",
    gap: 8,
  },
  chip: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 10,
    backgroundColor: "#f8fafc",
    borderWidth: 1,
    borderColor: "#e2e8f0",
  },
  chipText: {
    fontSize: 12,
    color: "#475569",
    fontWeight: "500",
  },
  stretchItem: {
    flexDirection: "row",
    alignItems: "flex-start",
    padding: 10,
    backgroundColor: "#f8fafc",
    borderRadius: 12,
    gap: 12,
  },
  stretchIconBox: {
    width: 34,
    height: 34,
    borderRadius: 10,
    alignItems: "center",
    justifyContent: "center",
  },
  stretchItemTitle: {
    fontSize: 13,
    fontWeight: "700",
    color: "#1e293b",
  },
  stretchDuration: {
    fontSize: 11,
    fontWeight: "600",
  },
  stretchItemDesc: {
    fontSize: 11,
    color: "#64748b",
    marginTop: 2,
    lineHeight: 15,
  },
  saveBtn: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 14,
    borderRadius: 14,
    marginTop: 4,
    marginBottom: 20,
  },
  saveBtnText: {
    fontSize: 14,
    fontWeight: "700",
    color: "#ffffff",
  },
});
