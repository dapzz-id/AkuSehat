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
} from "react-native";
import Icon from "react-native-vector-icons/Feather";
import api from "../../api/axiosConfig";

interface BurnoutMemberViewProps {
  themeColor?: string;
  onNavigateToCounseling?: () => void;
}

const QUESTIONS = [
  "Saya merasa lelah secara fisik dan emosional saat memulai hari.",
  "Saya merasa sulit berkonsentrasi atau mudah terdistraksi dalam tugas.",
  "Waktu istirahat atau tidur saya terasa kurang menyegarkan.",
  "Saya merasa beban tugas saat ini terlalu menekan.",
  "Antusiasme dan motivasi saya menurun dibanding biasanya.",
];

export default function BurnoutMemberView({
  themeColor = "#9333ea",
  onNavigateToCounseling,
}: BurnoutMemberViewProps) {
  const [loading, setLoading] = useState(false);
  const [submitting, setSubmitting] = useState(false);
  const [answers, setAnswers] = useState<number[]>([2, 2, 2, 2, 2]); // scale 1 - 5
  const [workload, setWorkload] = useState(3);
  const [notes, setNotes] = useState("");
  const [latestResult, setLatestResult] = useState<any>(null);

  useEffect(() => {
    fetchLatest();
  }, []);

  const fetchLatest = async () => {
    setLoading(true);
    try {
      const res = await api.get("/burnout/latest");
      if (res.data?.success && res.data.data) {
        setLatestResult(res.data.data);
      }
    } catch (e) {
      console.log("Error fetch burnout", e);
    } finally {
      setLoading(false);
    }
  };

  const handleSetAnswer = (qIndex: number, val: number) => {
    const next = [...answers];
    next[qIndex] = val;
    setAnswers(next);
  };

  const calculateScore = () => {
    // sum of 5 answers (min 5, max 25) -> normalized to 0 - 100
    const sum = answers.reduce((a, b) => a + b, 0);
    return Math.round(((sum - 5) / 20) * 100);
  };

  const handleSubmit = async () => {
    setSubmitting(true);
    const score = calculateScore();
    try {
      const res = await api.post("/burnout", {
        stress_score: score,
        workload_rating: workload,
        notes: notes || undefined,
      });

      if (res.data?.success) {
        setLatestResult(res.data.data);
        Alert.alert(
          "Hasil Evaluasi Tersimpan",
          `Skor stres Anda: ${score}/100 (${res.data.data.burnout_level.toUpperCase()}). Simak rekomendasi di bawah.`
        );
      }
    } catch (e) {
      Alert.alert("Error", "Gagal menyimpan hasil skrining.");
    } finally {
      setSubmitting(false);
    }
  };

  const getLevelBadge = (level: string) => {
    switch (level) {
      case "rendah":
        return { label: "Tingkat Stres Rendah (Aman)", color: "#16a34a", bg: "#dcfce7" };
      case "sedang":
        return { label: "Tingkat Stres Sedang (Waspada)", color: "#d97706", bg: "#fef3c7" };
      case "tinggi":
        return { label: "Tingkat Kejenuhan Tinggi (Perlu Rehat)", color: "#dc2626", bg: "#fee2e2" };
      default:
        return { label: "Belum Dievaluasi", color: "#64748b", bg: "#f1f5f9" };
    }
  };

  if (loading) {
    return (
      <View style={styles.centerBox}>
        <ActivityIndicator size="large" color={themeColor} />
        <Text style={styles.loadingText}>Memuat skrining stres...</Text>
      </View>
    );
  }

  return (
    <ScrollView contentContainerStyle={styles.container} showsVerticalScrollIndicator={false}>
      {/* 1. Latest Screening Card if exists */}
      {latestResult && (
        <View style={[styles.card, styles.resultCard]}>
          <View style={styles.resultHeader}>
            <View>
              <Text style={styles.resultDate}>Hasil Skrining Terakhir: {latestResult.screening_date}</Text>
              <View style={[styles.badge, { backgroundColor: getLevelBadge(latestResult.burnout_level).bg }]}>
                <Text style={[styles.badgeText, { color: getLevelBadge(latestResult.burnout_level).color }]}>
                  {getLevelBadge(latestResult.burnout_level).label}
                </Text>
              </View>
            </View>
            <View style={styles.resultScoreBox}>
              <Text style={[styles.resultScoreVal, { color: getLevelBadge(latestResult.burnout_level).color }]}>
                {latestResult.stress_score}
              </Text>
              <Text style={styles.resultScoreUnit}>/ 100</Text>
            </View>
          </View>

          {latestResult.burnout_level === "tinggi" && (
            <View style={styles.warningBanner}>
              <Icon name="alert-triangle" size={16} color="#dc2626" style={{ marginTop: 2 }} />
              <View style={{ flex: 1 }}>
                <Text style={styles.warningText}>
                  Tingkat kejenuhan Anda cukup tinggi. Sangat disarankan untuk mengambil jeda istirahat dan berkonsultasi secara privat dengan konselor institusi Anda.
                </Text>
                {onNavigateToCounseling && (
                  <TouchableOpacity
                    style={[styles.counselingBtn, { backgroundColor: themeColor }]}
                    onPress={onNavigateToCounseling}
                  >
                    <Icon name="message-square" size={14} color="#ffffff" style={{ marginRight: 6 }} />
                    <Text style={styles.counselingBtnText}>Buka Jadwal Konseling</Text>
                  </TouchableOpacity>
                )}
              </View>
            </View>
          )}
        </View>
      )}

      {/* 2. Questionnaire */}
      <View style={styles.card}>
        <Text style={styles.sectionTitle}>Kuesioner Evaluasi Mandiri (5 Pertanyaan)</Text>
        <Text style={styles.sectionSub}>Pilih skala 1 (Sangat Jarang) hingga 5 (Sangat Sering)</Text>

        <View style={{ gap: 16 }}>
          {QUESTIONS.map((q, idx) => (
            <View key={idx} style={styles.questionBox}>
              <Text style={styles.questionText}>
                {idx + 1}. {q}
              </Text>
              <View style={styles.ratingRow}>
                {[1, 2, 3, 4, 5].map((val) => {
                  const isSelected = answers[idx] === val;
                  return (
                    <TouchableOpacity
                      key={val}
                      onPress={() => handleSetAnswer(idx, val)}
                      style={[
                        styles.ratingBtn,
                        isSelected && { backgroundColor: themeColor, borderColor: themeColor },
                      ]}
                    >
                      <Text style={[styles.ratingBtnText, isSelected && { color: "#ffffff", fontWeight: "700" }]}>
                        {val}
                      </Text>
                    </TouchableOpacity>
                  );
                })}
              </View>
              <View style={styles.ratingLabels}>
                <Text style={styles.ratingLabelText}>Jarang</Text>
                <Text style={styles.ratingLabelText}>Sering</Text>
              </View>
            </View>
          ))}
        </View>
      </View>

      {/* 3. Workload Rating */}
      <View style={styles.card}>
        <Text style={styles.sectionTitle}>Tingkat Beban Aktivitas / Kerja</Text>
        <Text style={styles.sectionSub}>Bagaimana Anda menilai volume tugas mingguan Anda?</Text>
        <View style={styles.workloadRow}>
          {[
            { val: 1, label: "Sangat Ringan" },
            { val: 2, label: "Ringan" },
            { val: 3, label: "Sedang" },
            { val: 4, label: "Berat" },
            { val: 5, label: "Sangat Berat" },
          ].map((item) => {
            const isSelected = workload === item.val;
            return (
              <TouchableOpacity
                key={item.val}
                onPress={() => setWorkload(item.val)}
                style={[
                  styles.workloadChip,
                  isSelected && { backgroundColor: `${themeColor}15`, borderColor: themeColor },
                ]}
              >
                <Text
                  style={[
                    styles.workloadChipText,
                    isSelected && { color: themeColor, fontWeight: "700" },
                  ]}
                >
                  {item.label}
                </Text>
              </TouchableOpacity>
            );
          })}
        </View>

        {/* Notes */}
        <Text style={[styles.sectionTitle, { marginTop: 14 }]}>Catatan Tambahan (Opsional)</Text>
        <TextInput
          style={styles.textInput}
          placeholder="Ceritakan hal yang membuat Anda tertekan..."
          placeholderTextColor="#94a3b8"
          value={notes}
          onChangeText={setNotes}
          multiline
        />
      </View>

      {/* Submit Button */}
      <TouchableOpacity
        style={[styles.submitBtn, { backgroundColor: themeColor }]}
        onPress={handleSubmit}
        disabled={submitting}
      >
        {submitting ? (
          <ActivityIndicator color="#ffffff" />
        ) : (
          <>
            <Icon name="check-circle" size={18} color="#ffffff" style={{ marginRight: 8 }} />
            <Text style={styles.submitBtnText}>Simpan & Lihat Hasil Skrining</Text>
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
  resultCard: {
    backgroundColor: "#faf5ff",
    borderColor: "#e9d5ff",
  },
  resultHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },
  resultDate: {
    fontSize: 11,
    color: "#7e22ce",
    marginBottom: 4,
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
  resultScoreBox: {
    alignItems: "center",
    justifyContent: "center",
  },
  resultScoreVal: {
    fontSize: 24,
    fontWeight: "800",
  },
  resultScoreUnit: {
    fontSize: 10,
    color: "#94a3b8",
  },
  warningBanner: {
    flexDirection: "row",
    gap: 10,
    backgroundColor: "#fff1f2",
    borderRadius: 12,
    padding: 12,
    marginTop: 12,
    borderWidth: 1,
    borderColor: "#fecdd3",
  },
  warningText: {
    fontSize: 11,
    color: "#9f1239",
    lineHeight: 16,
  },
  counselingBtn: {
    flexDirection: "row",
    alignItems: "center",
    alignSelf: "flex-start",
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 8,
    marginTop: 8,
  },
  counselingBtnText: {
    fontSize: 11,
    fontWeight: "700",
    color: "#ffffff",
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
  questionBox: {
    backgroundColor: "#f8fafc",
    padding: 12,
    borderRadius: 12,
  },
  questionText: {
    fontSize: 13,
    color: "#1e293b",
    fontWeight: "600",
    marginBottom: 10,
    lineHeight: 18,
  },
  ratingRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    gap: 8,
  },
  ratingBtn: {
    flex: 1,
    height: 38,
    borderRadius: 10,
    backgroundColor: "#ffffff",
    borderWidth: 1,
    borderColor: "#e2e8f0",
    alignItems: "center",
    justifyContent: "center",
  },
  ratingBtnText: {
    fontSize: 14,
    fontWeight: "600",
    color: "#334155",
  },
  ratingLabels: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginTop: 4,
    paddingHorizontal: 2,
  },
  ratingLabelText: {
    fontSize: 10,
    color: "#94a3b8",
  },
  workloadRow: {
    flexDirection: "row",
    flexWrap: "wrap",
    gap: 8,
  },
  workloadChip: {
    paddingHorizontal: 12,
    paddingVertical: 7,
    borderRadius: 10,
    backgroundColor: "#f8fafc",
    borderWidth: 1,
    borderColor: "#e2e8f0",
  },
  workloadChipText: {
    fontSize: 12,
    color: "#475569",
    fontWeight: "500",
  },
  textInput: {
    backgroundColor: "#f8fafc",
    borderWidth: 1,
    borderColor: "#e2e8f0",
    borderRadius: 12,
    padding: 10,
    fontSize: 12,
    color: "#1e293b",
    height: 60,
    textAlignVertical: "top",
    marginTop: 6,
  },
  submitBtn: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 14,
    borderRadius: 14,
    marginTop: 4,
    marginBottom: 20,
  },
  submitBtnText: {
    fontSize: 14,
    fontWeight: "700",
    color: "#ffffff",
  },
});
