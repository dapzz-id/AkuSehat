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

interface VaccineMemberViewProps {
  themeColor?: string;
  userName?: string;
}

const COMMON_VACCINES = [
  "HPV (Kanker Serviks)",
  "Tetanus Toxoid (TT)",
  "Hepatitis B",
  "Influenza",
  "COVID-19 Booster",
];

export default function VaccineMemberView({
  themeColor = "#2563eb",
  userName = "Member",
}: VaccineMemberViewProps) {
  const [loading, setLoading] = useState(false);
  const [saving, setSaving] = useState(false);
  const [records, setRecords] = useState<any[]>([]);
  const [showAddForm, setShowAddForm] = useState(false);

  // Form State
  const [vaccineName, setVaccineName] = useState("");
  const [doseNumber, setDoseNumber] = useState(1);
  const [administeredDate, setAdministeredDate] = useState(
    new Date().toISOString().split("T")[0]
  );
  const [batchNumber, setBatchNumber] = useState("");
  const [administeredBy, setAdministeredBy] = useState("UKS / Puskesmas Setempat");
  const [allergyNotes, setAllergyNotes] = useState("");

  useEffect(() => {
    fetchVaccines();
  }, []);

  const fetchVaccines = async () => {
    setLoading(true);
    try {
      const res = await api.get("/vaccine");
      if (res.data?.success && res.data.data) {
        setRecords(res.data.data);
      }
    } catch (e) {
      console.log("Error fetch vaccines", e);
    } finally {
      setLoading(false);
    }
  };

  const handleSaveVaccine = async () => {
    if (!vaccineName.trim()) {
      Alert.alert("Perhatian", "Harap pilih atau masukkan nama vaksin.");
      return;
    }

    setSaving(true);
    try {
      const res = await api.post("/vaccine", {
        vaccine_name: vaccineName.trim(),
        dose_number: doseNumber,
        administered_date: administeredDate,
        batch_number: batchNumber.trim() || undefined,
        administered_by: administeredBy.trim() || undefined,
        allergy_notes: allergyNotes.trim() || undefined,
      });

      if (res.data?.success) {
        setShowAddForm(false);
        setVaccineName("");
        setBatchNumber("");
        setAllergyNotes("");
        fetchVaccines();
        Alert.alert("Sukses", "Vaksinasi berhasil dicatat ke dalam Paspor Vaksin!");
      }
    } catch (e) {
      Alert.alert("Error", "Gagal menyimpan riwayat vaksinasi.");
    } finally {
      setSaving(false);
    }
  };

  const handleDelete = async (id: string) => {
    Alert.alert("Konfirmasi", "Hapus riwayat vaksinasi ini?", [
      { text: "Batal", style: "cancel" },
      {
        text: "Hapus",
        style: "destructive",
        onPress: async () => {
          try {
            await api.delete(`/vaccine/${id}`);
            fetchVaccines();
          } catch (e) {
            Alert.alert("Error", "Gagal menghapus data vaksin.");
          }
        },
      },
    ]);
  };

  if (loading) {
    return (
      <View style={styles.centerBox}>
        <ActivityIndicator size="large" color={themeColor} />
        <Text style={styles.loadingText}>Memuat paspor vaksin digital...</Text>
      </View>
    );
  }

  return (
    <ScrollView contentContainerStyle={styles.container} showsVerticalScrollIndicator={false}>
      {/* 1. Digital Vaccine Passport Card */}
      <View style={[styles.card, styles.passportCard]}>
        <View style={styles.passportHeader}>
          <View>
            <View style={styles.passportBadge}>
              <Icon name="shield" size={12} color="#ffffff" style={{ marginRight: 4 }} />
              <Text style={styles.passportBadgeText}>PASPOR VAKSIN DIGITAL</Text>
            </View>
            <Text style={styles.passportName}>{userName}</Text>
          </View>
          <View style={styles.passportSeal}>
            <Icon name="check-circle" size={28} color="#2563eb" />
          </View>
        </View>

        <View style={styles.passportStatsRow}>
          <View>
            <Text style={styles.passportStatLabel}>TOTAL DOSIS TERCATAT</Text>
            <Text style={styles.passportStatVal}>{records.length} Dosis</Text>
          </View>
          <View style={{ alignItems: "flex-end" }}>
            <Text style={styles.passportStatLabel}>STATUS KEBIJAKAN</Text>
            <Text style={[styles.passportStatVal, { color: "#16a34a" }]}>
              {records.length > 0 ? "Terverifikasi Aktif" : "Menunggu Rekap"}
            </Text>
          </View>
        </View>
      </View>

      {/* 2. Add Vaccine Button or Form */}
      {!showAddForm ? (
        <TouchableOpacity
          style={[styles.addToggleBtn, { borderColor: themeColor }]}
          onPress={() => setShowAddForm(true)}
        >
          <Icon name="plus-circle" size={18} color={themeColor} style={{ marginRight: 6 }} />
          <Text style={[styles.addToggleBtnText, { color: themeColor }]}>
            Tambah Riwayat Vaksinasi Baru
          </Text>
        </TouchableOpacity>
      ) : (
        <View style={styles.card}>
          <View style={{ flexDirection: "row", justifyContent: "space-between", alignItems: "center" }}>
            <Text style={styles.sectionTitle}>Input Riwayat Imunisasi</Text>
            <TouchableOpacity onPress={() => setShowAddForm(false)}>
              <Icon name="x" size={20} color="#64748b" />
            </TouchableOpacity>
          </View>
          <Text style={styles.sectionSub}>Pilih jenis vaksin atau ketik nama vaksin</Text>

          {/* Quick chips */}
          <View style={styles.quickChipsWrap}>
            {COMMON_VACCINES.map((v) => (
              <TouchableOpacity
                key={v}
                style={[
                  styles.quickChip,
                  vaccineName === v && { backgroundColor: `${themeColor}15`, borderColor: themeColor },
                ]}
                onPress={() => setVaccineName(v)}
              >
                <Text
                  style={[
                    styles.quickChipText,
                    vaccineName === v && { color: themeColor, fontWeight: "700" },
                  ]}
                >
                  {v}
                </Text>
              </TouchableOpacity>
            ))}
          </View>

          <Text style={styles.inputLabel}>Nama Vaksin</Text>
          <TextInput
            style={styles.textInput}
            placeholder="Contoh: HPV Dosis 1 / Influenza"
            placeholderTextColor="#94a3b8"
            value={vaccineName}
            onChangeText={setVaccineName}
          />

          {/* Dose Number Row */}
          <Text style={styles.inputLabel}>Dosis Ke-</Text>
          <View style={styles.doseRow}>
            {[1, 2, 3, 4].map((d) => (
              <TouchableOpacity
                key={d}
                onPress={() => setDoseNumber(d)}
                style={[
                  styles.doseBtn,
                  doseNumber === d && { backgroundColor: themeColor, borderColor: themeColor },
                ]}
              >
                <Text style={[styles.doseBtnText, doseNumber === d && { color: "#ffffff", fontWeight: "700" }]}>
                  Dosis {d}
                </Text>
              </TouchableOpacity>
            ))}
          </View>

          <Text style={styles.inputLabel}>Tanggal Penyuntikan (YYYY-MM-DD)</Text>
          <TextInput
            style={styles.textInput}
            value={administeredDate}
            onChangeText={setAdministeredDate}
          />

          <Text style={styles.inputLabel}>Lokasi / Faskes / UKS Penyelenggara</Text>
          <TextInput
            style={styles.textInput}
            value={administeredBy}
            onChangeText={setAdministeredBy}
          />

          <Text style={styles.inputLabel}>Nomor Batch Vaksin (Opsional)</Text>
          <TextInput
            style={styles.textInput}
            placeholder="Contoh: BTH-2026-X8"
            placeholderTextColor="#94a3b8"
            value={batchNumber}
            onChangeText={setBatchNumber}
          />

          {/* Save Button */}
          <TouchableOpacity
            style={[styles.saveBtn, { backgroundColor: themeColor }]}
            onPress={handleSaveVaccine}
            disabled={saving}
          >
            {saving ? (
              <ActivityIndicator color="#ffffff" />
            ) : (
              <>
                <Icon name="check" size={16} color="#ffffff" style={{ marginRight: 6 }} />
                <Text style={styles.saveBtnText}>Simpan ke Paspor Vaksin</Text>
              </>
            )}
          </TouchableOpacity>
        </View>
      )}

      {/* 3. List of Recorded Vaccines */}
      <View style={styles.card}>
        <Text style={styles.sectionTitle}>Riwayat Vaksinasi Tersimpan</Text>
        <Text style={styles.sectionSub}>Buku catatan digital resmi imunisasi Anda</Text>

        {records.length === 0 ? (
          <View style={styles.emptyBox}>
            <Icon name="shield" size={28} color="#cbd5e1" />
            <Text style={styles.emptyText}>Belum ada data vaksinasi tersimpan.</Text>
          </View>
        ) : (
          <View style={{ gap: 10, marginTop: 6 }}>
            {records.map((r) => (
              <View key={r.id} style={styles.recordItem}>
                <View style={styles.recordLeft}>
                  <View style={[styles.doseBadge, { backgroundColor: `${themeColor}15` }]}>
                    <Text style={[styles.doseBadgeText, { color: themeColor }]}>D{r.dose_number}</Text>
                  </View>
                  <View style={{ flex: 1 }}>
                    <Text style={styles.recordTitle}>{r.vaccine_name}</Text>
                    <Text style={styles.recordMeta}>
                      {r.administered_date} • {r.administered_by || "Faskes"}
                    </Text>
                    {r.batch_number && (
                      <Text style={styles.recordBatch}>Batch: {r.batch_number}</Text>
                    )}
                  </View>
                </View>

                <TouchableOpacity onPress={() => handleDelete(r.id)} style={{ padding: 4 }}>
                  <Icon name="trash-2" size={15} color="#ef4444" />
                </TouchableOpacity>
              </View>
            ))}
          </View>
        )}
      </View>
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
  passportCard: {
    backgroundColor: "#1e3a8a",
    borderColor: "#1e40af",
  },
  passportHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },
  passportBadge: {
    flexDirection: "row",
    alignItems: "center",
    alignSelf: "flex-start",
    backgroundColor: "rgba(255,255,255,0.2)",
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 6,
    marginBottom: 6,
  },
  passportBadgeText: {
    fontSize: 9,
    fontWeight: "700",
    color: "#ffffff",
    letterSpacing: 0.5,
  },
  passportName: {
    fontSize: 18,
    fontWeight: "800",
    color: "#ffffff",
  },
  passportSeal: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: "#ffffff",
    alignItems: "center",
    justifyContent: "center",
  },
  passportStatsRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginTop: 20,
    paddingTop: 14,
    borderTopWidth: 1,
    borderTopColor: "rgba(255,255,255,0.15)",
  },
  passportStatLabel: {
    fontSize: 9,
    fontWeight: "700",
    color: "#93c5fd",
    letterSpacing: 0.5,
  },
  passportStatVal: {
    fontSize: 13,
    fontWeight: "700",
    color: "#ffffff",
    marginTop: 2,
  },
  addToggleBtn: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 12,
    borderRadius: 12,
    borderWidth: 1.5,
    borderStyle: "dashed",
    backgroundColor: "#f8fafc",
  },
  addToggleBtnText: {
    fontSize: 13,
    fontWeight: "700",
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
  quickChipsWrap: {
    flexDirection: "row",
    flexWrap: "wrap",
    gap: 6,
    marginBottom: 12,
  },
  quickChip: {
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 8,
    backgroundColor: "#f8fafc",
    borderWidth: 1,
    borderColor: "#e2e8f0",
  },
  quickChipText: {
    fontSize: 11,
    color: "#475569",
  },
  inputLabel: {
    fontSize: 12,
    fontWeight: "600",
    color: "#334155",
    marginBottom: 6,
  },
  textInput: {
    backgroundColor: "#f8fafc",
    borderWidth: 1,
    borderColor: "#e2e8f0",
    borderRadius: 12,
    paddingHorizontal: 12,
    paddingVertical: 10,
    fontSize: 13,
    color: "#1e293b",
    marginBottom: 12,
  },
  doseRow: {
    flexDirection: "row",
    gap: 8,
    marginBottom: 12,
  },
  doseBtn: {
    flex: 1,
    paddingVertical: 8,
    borderRadius: 8,
    backgroundColor: "#f8fafc",
    borderWidth: 1,
    borderColor: "#e2e8f0",
    alignItems: "center",
  },
  doseBtnText: {
    fontSize: 12,
    color: "#475569",
  },
  saveBtn: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 12,
    borderRadius: 12,
    marginTop: 4,
  },
  saveBtnText: {
    fontSize: 13,
    fontWeight: "700",
    color: "#ffffff",
  },
  emptyBox: {
    alignItems: "center",
    paddingVertical: 24,
  },
  emptyText: {
    fontSize: 12,
    color: "#94a3b8",
    marginTop: 8,
  },
  recordItem: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    padding: 12,
    backgroundColor: "#f8fafc",
    borderRadius: 12,
  },
  recordLeft: {
    flexDirection: "row",
    alignItems: "center",
    gap: 12,
    flex: 1,
  },
  doseBadge: {
    width: 34,
    height: 34,
    borderRadius: 10,
    alignItems: "center",
    justifyContent: "center",
  },
  doseBadgeText: {
    fontSize: 13,
    fontWeight: "800",
  },
  recordTitle: {
    fontSize: 13,
    fontWeight: "700",
    color: "#1e293b",
  },
  recordMeta: {
    fontSize: 11,
    color: "#64748b",
    marginTop: 2,
  },
  recordBatch: {
    fontSize: 10,
    color: "#94a3b8",
    marginTop: 1,
  },
});
