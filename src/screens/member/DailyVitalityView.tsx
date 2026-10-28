import React, { useState, useEffect } from "react";
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ScrollView,
  ActivityIndicator,
  Alert,
  TextInput,
  Dimensions,
} from "react-native";
import Icon from "react-native-vector-icons/Feather";
import { Colors } from "../../settings/Colors";
import api from "../../api/axiosConfig";

const { width: SCREEN_WIDTH } = Dimensions.get("window");

const MOODS = [
  { score: 5, emoji: "🤩", label: "Sangat Baik" },
  { score: 4, emoji: "😊", label: "Baik" },
  { score: 3, emoji: "😐", label: "Biasa" },
  { score: 2, emoji: "😔", label: "Lelah" },
  { score: 1, emoji: "😫", label: "Stres" },
];

export default function DailyVitalityView() {
  const [loading, setLoading] = useState(false);
  const [waterMl, setWaterMl] = useState(0);
  const [waterTarget] = useState(2000);
  const [selectedMood, setSelectedMood] = useState<number | null>(null);
  const [sleepHours, setSleepHours] = useState<string>("");
  const [systolic, setSystolic] = useState<string>("");
  const [diastolic, setDiastolic] = useState<string>("");
  const [weightKg, setWeightKg] = useState<string>("");
  const [isSavingVitals, setIsSavingVitals] = useState(false);

  useEffect(() => {
    fetchTodayVitals();
  }, []);

  const fetchTodayVitals = async () => {
    setLoading(true);
    try {
      const res = await api.get("/vital-logs/today");
      if (res.data?.success && res.data.data) {
        const d = res.data.data;
        setWaterMl(d.water_intake_ml || 0);
        if (d.mood_score) setSelectedMood(d.mood_score);
        if (d.sleep_hours) setSleepHours(d.sleep_hours.toString());
        if (d.systolic) setSystolic(d.systolic.toString());
        if (d.diastolic) setDiastolic(d.diastolic.toString());
        if (d.weight_kg) setWeightKg(d.weight_kg.toString());
      }
    } catch (e) {
      console.log("Error fetch vitals", e);
    } finally {
      setLoading(false);
    }
  };

  const handleAddWater = async (amount: number) => {
    const prev = waterMl;
    const nextVal = Math.max(0, prev + amount);
    setWaterMl(nextVal);

    try {
      await api.post("/vital-logs/water/add", { amount });
    } catch (e) {
      setWaterMl(prev);
      Alert.alert("Error", "Gagal memperbarui konsumsi air.");
    }
  };

  const handleSaveMood = async (score: number) => {
    setSelectedMood(score);
    try {
      await api.post("/vital-logs", { mood_score: score });
    } catch (e) {
      console.log("Error save mood", e);
    }
  };

  const handleSaveFullVitals = async () => {
    setIsSavingVitals(true);
    try {
      const payload: any = {};
      if (sleepHours) {
        const val = parseFloat(sleepHours.replace(',', '.'));
        if (!isNaN(val) && val >= 0 && val <= 24) payload.sleep_hours = val;
      }
      if (systolic) {
        const val = parseInt(systolic, 10);
        if (!isNaN(val) && val > 0 && val <= 300) payload.systolic = val;
      }
      if (diastolic) {
        const val = parseInt(diastolic, 10);
        if (!isNaN(val) && val > 0 && val <= 200) payload.diastolic = val;
      }
      if (weightKg) {
        const val = parseFloat(weightKg.replace(',', '.'));
        if (!isNaN(val) && val > 0 && val <= 500) payload.weight_kg = val;
      }

      const res = await api.post("/vital-logs", payload);
      if (res.data?.success) {
        Alert.alert("Tersimpan!", "Catatan vitalitas harian Anda berhasil disimpan.");
      }
    } catch (e) {
      Alert.alert("Gagal", "Gagal menyimpan data kesehatan harian.");
    } finally {
      setIsSavingVitals(false);
    }
  };

  const waterPercent = Math.min(100, Math.round((waterMl / waterTarget) * 100));

  if (loading) {
    return (
      <View style={styles.centerContainer}>
        <ActivityIndicator size="large" color={Colors.primary} />
      </View>
    );
  }

  return (
    <ScrollView contentContainerStyle={styles.container} showsVerticalScrollIndicator={false}>
      {/* 1. HYDRATION TRACKER CARD */}
      <View style={styles.card}>
        <View style={styles.cardHeaderRow}>
          <View style={styles.cardIconBox}>
            <Icon name="droplet" size={20} color="#0284C7" />
          </View>
          <View style={{ flex: 1 }}>
            <Text style={styles.cardTitle}>Target Hidrasi Harian</Text>
            <Text style={styles.cardSubtitle}>Cukupi kebutuhan air agar metabolisme optimal</Text>
          </View>
          <Text style={styles.waterPercentText}>{waterPercent}%</Text>
        </View>

        {/* Big Water Amount */}
        <View style={styles.waterAmountRow}>
          <Text style={styles.bigWaterText}>{waterMl}</Text>
          <Text style={styles.waterUnit}>/ {waterTarget} ml</Text>
        </View>

        {/* Progress Bar */}
        <View style={styles.progressBarTrack}>
          <View style={[styles.progressBarFill, { width: `${waterPercent}%` }]} />
        </View>

        {/* Action Quick Buttons */}
        <View style={styles.waterActionRow}>
          <TouchableOpacity
            style={styles.waterAddBtn}
            activeOpacity={0.8}
            onPress={() => handleAddWater(250)}
          >
            <Icon name="plus" size={16} color="#0284C7" />
            <Text style={styles.waterAddBtnText}>+250 ml (Gelas)</Text>
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.waterAddBtn}
            activeOpacity={0.8}
            onPress={() => handleAddWater(500)}
          >
            <Icon name="plus" size={16} color="#0284C7" />
            <Text style={styles.waterAddBtnText}>+500 ml (Botol)</Text>
          </TouchableOpacity>

          {waterMl > 0 && (
            <TouchableOpacity
              style={styles.waterUndoBtn}
              onPress={() => handleAddWater(-250)}
            >
              <Icon name="rotate-ccw" size={16} color="#94A3B8" />
            </TouchableOpacity>
          )}
        </View>
      </View>

      {/* 2. 10-SECOND DAILY MOOD PULSE */}
      <View style={styles.card}>
        <View style={styles.cardHeaderRow}>
          <View style={[styles.cardIconBox, { backgroundColor: "#FEF3C7" }]}>
            <Icon name="smile" size={20} color="#D97706" />
          </View>
          <View style={{ flex: 1 }}>
            <Text style={styles.cardTitle}>Mood & Energi Hari Ini</Text>
            <Text style={styles.cardSubtitle}>Cek kondisi mentalmu dalam 10 detik</Text>
          </View>
        </View>

        <View style={styles.moodGrid}>
          {MOODS.map((m) => {
            const isSelected = selectedMood === m.score;
            return (
              <TouchableOpacity
                key={m.score}
                style={[styles.moodItem, isSelected && styles.moodItemActive]}
                onPress={() => handleSaveMood(m.score)}
              >
                <Text style={styles.moodEmoji}>{m.emoji}</Text>
                <Text style={[styles.moodLabel, isSelected && styles.moodLabelActive]}>
                  {m.label}
                </Text>
              </TouchableOpacity>
            );
          })}
        </View>
      </View>

      {/* 3. VITAL SIGN CHECK (Tidur, Tensi, Berat) */}
      <View style={styles.card}>
        <View style={styles.cardHeaderRow}>
          <View style={[styles.cardIconBox, { backgroundColor: "#F3E8FF" }]}>
            <Icon name="heart" size={20} color="#9333EA" />
          </View>
          <View style={{ flex: 1 }}>
            <Text style={styles.cardTitle}>Biomarker & Pemulihan</Text>
            <Text style={styles.cardSubtitle}>Catatan kualitas istirahat & tanda vital</Text>
          </View>
        </View>

        <View style={styles.inputGrid}>
          {/* Jam Tidur */}
          <View style={styles.inputCol}>
            <Text style={styles.inputLabel}>Tidur Tadi Malam</Text>
            <View style={styles.inputWrapper}>
              <TextInput
                style={styles.textInput}
                keyboardType="numeric"
                placeholder="7.5"
                placeholderTextColor="#94A3B8"
                value={sleepHours}
                onChangeText={setSleepHours}
              />
              <Text style={styles.inputSuffix}>Jam</Text>
            </View>
          </View>

          {/* Berat Badan */}
          <View style={styles.inputCol}>
            <Text style={styles.inputLabel}>Berat Badan</Text>
            <View style={styles.inputWrapper}>
              <TextInput
                style={styles.textInput}
                keyboardType="numeric"
                placeholder="60"
                placeholderTextColor="#94A3B8"
                value={weightKg}
                onChangeText={setWeightKg}
              />
              <Text style={styles.inputSuffix}>Kg</Text>
            </View>
          </View>
        </View>

        <View style={styles.inputGrid}>
          {/* Tensi Sistol */}
          <View style={styles.inputCol}>
            <Text style={styles.inputLabel}>Tekanan Darah (Sistol)</Text>
            <View style={styles.inputWrapper}>
              <TextInput
                style={styles.textInput}
                keyboardType="numeric"
                placeholder="120"
                placeholderTextColor="#94A3B8"
                value={systolic}
                onChangeText={setSystolic}
              />
              <Text style={styles.inputSuffix}>mmHg</Text>
            </View>
          </View>

          {/* Tensi Diastol */}
          <View style={styles.inputCol}>
            <Text style={styles.inputLabel}>Tekanan Darah (Diastol)</Text>
            <View style={styles.inputWrapper}>
              <TextInput
                style={styles.textInput}
                keyboardType="numeric"
                placeholder="80"
                placeholderTextColor="#94A3B8"
                value={diastolic}
                onChangeText={setDiastolic}
              />
              <Text style={styles.inputSuffix}>mmHg</Text>
            </View>
          </View>
        </View>

        <TouchableOpacity
          style={styles.saveBtn}
          activeOpacity={0.85}
          onPress={handleSaveFullVitals}
          disabled={isSavingVitals}
        >
          {isSavingVitals ? (
            <ActivityIndicator color="#fff" />
          ) : (
            <Text style={styles.saveBtnText}>Simpan Pembaruan Vitalitas</Text>
          )}
        </TouchableOpacity>
      </View>

      {/* 4. SOS EMERGENCY MEDICAL PASSPORT CARD */}
      <View style={styles.emergencyCard}>
        <View style={styles.emergencyHeader}>
          <Icon name="shield" size={20} color="#DC2626" />
          <Text style={styles.emergencyTitle}>KARTU DARURAT MEDIS (SOS)</Text>
        </View>
        <Text style={styles.emergencyDesc}>
          Informasi ini berguna untuk pertolongan pertama oleh tim medis institusi atau rumah sakit.
        </Text>

        <View style={styles.emergencyInfoBox}>
          <View style={styles.emergencyItem}>
            <Text style={styles.emergencyItemLabel}>Golongan Darah</Text>
            <Text style={styles.emergencyItemVal}>O Rhesus (+)</Text>
          </View>
          <View style={styles.emergencyDivider} />
          <View style={styles.emergencyItem}>
            <Text style={styles.emergencyItemLabel}>Alergi Obat</Text>
            <Text style={styles.emergencyItemVal}>Tidak Ada</Text>
          </View>
          <View style={styles.emergencyDivider} />
          <View style={styles.emergencyItem}>
            <Text style={styles.emergencyItemLabel}>Status HB Terakhir</Text>
            <Text style={[styles.emergencyItemVal, { color: Colors.primary }]}>Normal (13.5)</Text>
          </View>
        </View>
      </View>
    </ScrollView>
  );
}

const styles = StyleSheet.create({
  container: {
    padding: 16,
    paddingBottom: 110,
    backgroundColor: "#F8FAFC",
  },
  centerContainer: {
    flex: 1,
    justifyContent: "center",
    alignItems: "center",
  },
  card: {
    backgroundColor: "#fff",
    borderRadius: 20,
    padding: 18,
    marginBottom: 16,
    borderWidth: 1,
    borderColor: "#E2E8F0",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.04,
    shadowRadius: 8,
    elevation: 2,
  },
  cardHeaderRow: {
    flexDirection: "row",
    alignItems: "center",
    marginBottom: 14,
    gap: 12,
  },
  cardIconBox: {
    width: 38,
    height: 38,
    borderRadius: 12,
    backgroundColor: "#E0F2FE",
    alignItems: "center",
    justifyContent: "center",
  },
  cardTitle: {
    fontSize: 15,
    fontWeight: "800",
    color: "#0F172A",
  },
  cardSubtitle: {
    fontSize: 11,
    color: "#64748B",
    marginTop: 1,
  },
  waterPercentText: {
    fontSize: 18,
    fontWeight: "900",
    color: "#0284C7",
  },
  waterAmountRow: {
    flexDirection: "row",
    alignItems: "baseline",
    marginBottom: 10,
  },
  bigWaterText: {
    fontSize: 36,
    fontWeight: "900",
    color: "#0F172A",
  },
  waterUnit: {
    fontSize: 15,
    fontWeight: "600",
    color: "#94A3B8",
    marginLeft: 6,
  },
  progressBarTrack: {
    height: 10,
    backgroundColor: "#F1F5F9",
    borderRadius: 5,
    overflow: "hidden",
    marginBottom: 16,
  },
  progressBarFill: {
    height: "100%",
    backgroundColor: "#0284C7",
    borderRadius: 5,
  },
  waterActionRow: {
    flexDirection: "row",
    gap: 10,
    alignItems: "center",
  },
  waterAddBtn: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    backgroundColor: "#F0F9FF",
    borderWidth: 1,
    borderColor: "#BAE6FD",
    paddingVertical: 10,
    borderRadius: 12,
    gap: 6,
  },
  waterAddBtnText: {
    fontSize: 12,
    fontWeight: "700",
    color: "#0284C7",
  },
  waterUndoBtn: {
    width: 40,
    height: 40,
    borderRadius: 12,
    backgroundColor: "#F1F5F9",
    alignItems: "center",
    justifyContent: "center",
  },
  moodGrid: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginTop: 4,
  },
  moodItem: {
    alignItems: "center",
    paddingVertical: 8,
    paddingHorizontal: 6,
    borderRadius: 14,
    width: (SCREEN_WIDTH - 80) / 5,
    borderWidth: 1,
    borderColor: "transparent",
  },
  moodItemActive: {
    backgroundColor: "#FEF3C7",
    borderColor: "#FCD34D",
  },
  moodEmoji: {
    fontSize: 28,
    marginBottom: 4,
  },
  moodLabel: {
    fontSize: 10,
    fontWeight: "600",
    color: "#64748B",
    textAlign: "center",
  },
  moodLabelActive: {
    color: "#B45309",
    fontWeight: "700",
  },
  inputGrid: {
    flexDirection: "row",
    gap: 12,
    marginBottom: 12,
  },
  inputCol: {
    flex: 1,
  },
  inputLabel: {
    fontSize: 11,
    fontWeight: "600",
    color: "#475569",
    marginBottom: 6,
  },
  inputWrapper: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#F8FAFC",
    borderWidth: 1,
    borderColor: "#E2E8F0",
    borderRadius: 12,
    paddingHorizontal: 12,
    height: 44,
  },
  textInput: {
    flex: 1,
    fontSize: 14,
    fontWeight: "700",
    color: "#0F172A",
  },
  inputSuffix: {
    fontSize: 12,
    fontWeight: "600",
    color: "#94A3B8",
  },
  saveBtn: {
    backgroundColor: Colors.primary,
    paddingVertical: 12,
    borderRadius: 14,
    alignItems: "center",
    marginTop: 6,
  },
  saveBtnText: {
    color: "#fff",
    fontSize: 13,
    fontWeight: "700",
  },
  emergencyCard: {
    backgroundColor: "#FEF2F2",
    borderRadius: 20,
    padding: 18,
    borderWidth: 1,
    borderColor: "#FECACA",
  },
  emergencyHeader: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    marginBottom: 6,
  },
  emergencyTitle: {
    fontSize: 13,
    fontWeight: "900",
    color: "#DC2626",
    letterSpacing: 0.5,
  },
  emergencyDesc: {
    fontSize: 11,
    color: "#991B1B",
    lineHeight: 16,
    marginBottom: 14,
  },
  emergencyInfoBox: {
    flexDirection: "row",
    backgroundColor: "#fff",
    borderRadius: 14,
    padding: 12,
  },
  emergencyItem: {
    flex: 1,
    alignItems: "center",
  },
  emergencyItemLabel: {
    fontSize: 10,
    color: "#94A3B8",
    fontWeight: "600",
    marginBottom: 2,
  },
  emergencyItemVal: {
    fontSize: 13,
    fontWeight: "800",
    color: "#1E293B",
  },
  emergencyDivider: {
    width: 1,
    backgroundColor: "#F1F5F9",
  },
});
