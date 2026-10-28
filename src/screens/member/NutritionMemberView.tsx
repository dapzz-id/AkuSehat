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

interface NutritionMemberViewProps {
  themeColor?: string;
}

const MEAL_TYPES = [
  { id: "sarapan", label: "Sarapan", icon: "sun" },
  { id: "makan_siang", label: "Makan Siang", icon: "cloud" },
  { id: "makan_malam", label: "Makan Malam", icon: "moon" },
  { id: "camilan", label: "Camilan Sehat", icon: "coffee" },
];

const CALORIE_CHIPS = [150, 300, 450, 600];

export default function NutritionMemberView({
  themeColor = "#d97706",
}: NutritionMemberViewProps) {
  const [loading, setLoading] = useState(false);
  const [adding, setAdding] = useState(false);
  const [todayData, setTodayData] = useState<any>({
    logs: [],
    total_calories: 0,
    balanced_count: 0,
    target_calories: 2000,
  });

  // Form State
  const [mealType, setMealType] = useState("makan_siang");
  const [foodName, setFoodName] = useState("");
  const [calories, setCalories] = useState("450");
  const [isBalanced, setIsBalanced] = useState(true);

  useEffect(() => {
    fetchToday();
  }, []);

  const fetchToday = async () => {
    setLoading(true);
    try {
      const res = await api.get("/nutrition/today");
      if (res.data?.success && res.data.data) {
        setTodayData(res.data.data);
      }
    } catch (e) {
      console.log("Error fetch nutrition", e);
    } finally {
      setLoading(false);
    }
  };

  const handleAddMeal = async () => {
    if (!foodName.trim()) {
      Alert.alert("Perhatian", "Harap isi nama menu makanan.");
      return;
    }
    const calVal = parseInt(calories, 10);
    if (isNaN(calVal) || calVal < 0) {
      Alert.alert("Perhatian", "Estimasi kalori tidak valid.");
      return;
    }

    setAdding(true);
    try {
      const res = await api.post("/nutrition", {
        meal_type: mealType,
        food_name: foodName.trim(),
        calories: calVal,
        is_balanced: isBalanced,
      });

      if (res.data?.success) {
        setFoodName("");
        setCalories("450");
        fetchToday();
        Alert.alert("Sukses", "Menu berhasil ditambahkan ke catatan harian!");
      }
    } catch (e) {
      Alert.alert("Error", "Gagal menyimpan menu makanan.");
    } finally {
      setAdding(false);
    }
  };

  const handleDeleteMeal = async (id: string) => {
    Alert.alert("Konfirmasi", "Hapus catatan menu ini?", [
      { text: "Batal", style: "cancel" },
      {
        text: "Hapus",
        style: "destructive",
        onPress: async () => {
          try {
            await api.delete(`/nutrition/${id}`);
            fetchToday();
          } catch (e) {
            Alert.alert("Error", "Gagal menghapus catatan.");
          }
        },
      },
    ]);
  };

  const total = todayData.total_calories || 0;
  const target = todayData.target_calories || 2000;
  const progressPct = Math.min(100, Math.round((total / target) * 100));

  if (loading) {
    return (
      <View style={styles.centerBox}>
        <ActivityIndicator size="large" color={themeColor} />
        <Text style={styles.loadingText}>Memuat data nutrisi harian...</Text>
      </View>
    );
  }

  return (
    <ScrollView contentContainerStyle={styles.container} showsVerticalScrollIndicator={false}>
      {/* 1. Calorie Overview Card */}
      <View style={[styles.card, styles.calorieCard]}>
        <View style={styles.calorieHeader}>
          <View>
            <Text style={styles.calorieTitle}>Asupan Kalori Hari Ini</Text>
            <Text style={styles.calorieSub}>Target Harian Seimbang: {target} kkal</Text>
          </View>
          <View style={styles.calorieValueBox}>
            <Text style={[styles.calorieValue, { color: themeColor }]}>{total}</Text>
            <Text style={styles.calorieUnit}>kkal</Text>
          </View>
        </View>

        {/* Progress bar */}
        <View style={styles.progressBarTrack}>
          <View style={[styles.progressBarFill, { width: `${progressPct}%`, backgroundColor: themeColor }]} />
        </View>
        <View style={styles.progressLabelRow}>
          <Text style={styles.progressLabelText}>{progressPct}% dari target</Text>
          <Text style={styles.progressLabelText}>
            {todayData.balanced_count || 0} Menu Gizi Seimbang
          </Text>
        </View>
      </View>

      {/* 2. Isi Piringku Guidelines */}
      <View style={styles.card}>
        <Text style={styles.sectionTitle}>Panduan 'Isi Piringku' Kemenkes</Text>
        <Text style={styles.sectionSub}>Komposisi porsi ideal setiap kali makan</Text>
        <View style={styles.plateGrid}>
          <View style={[styles.plateBox, { backgroundColor: "#fef3c7" }]}>
            <Text style={styles.platePct}>35%</Text>
            <Text style={styles.plateLabel}>Makanan Pokok</Text>
          </View>
          <View style={[styles.plateBox, { backgroundColor: "#dcfce7" }]}>
            <Text style={styles.platePct}>35%</Text>
            <Text style={styles.plateLabel}>Sayur-Mayur</Text>
          </View>
          <View style={[styles.plateBox, { backgroundColor: "#fee2e2" }]}>
            <Text style={styles.platePct}>15%</Text>
            <Text style={styles.plateLabel}>Lauk Pauk</Text>
          </View>
          <View style={[styles.plateBox, { backgroundColor: "#f0fdf4" }]}>
            <Text style={styles.platePct}>15%</Text>
            <Text style={styles.plateLabel}>Buah-buahan</Text>
          </View>
        </View>
      </View>

      {/* 3. Add Meal Form */}
      <View style={styles.card}>
        <Text style={styles.sectionTitle}>Catat Menu Makanan</Text>
        <Text style={styles.sectionSub}>Pilih waktu makan dan masukkan estimasi kalori</Text>

        {/* Meal Type Chips */}
        <View style={styles.mealTypesRow}>
          {MEAL_TYPES.map((m) => {
            const isSelected = mealType === m.id;
            return (
              <TouchableOpacity
                key={m.id}
                onPress={() => setMealType(m.id)}
                style={[
                  styles.mealChip,
                  isSelected && { backgroundColor: `${themeColor}15`, borderColor: themeColor },
                ]}
              >
                <Icon
                  name={m.icon}
                  size={14}
                  color={isSelected ? themeColor : "#64748b"}
                  style={{ marginRight: 6 }}
                />
                <Text style={[styles.mealChipText, isSelected && { color: themeColor, fontWeight: "700" }]}>
                  {m.label}
                </Text>
              </TouchableOpacity>
            );
          })}
        </View>

        {/* Food Name Input */}
        <Text style={styles.inputLabel}>Nama Makanan / Minuman</Text>
        <TextInput
          style={styles.textInput}
          placeholder="Contoh: Nasi Merah, Ayam Bakar, Sayur Sop"
          placeholderTextColor="#94a3b8"
          value={foodName}
          onChangeText={setFoodName}
        />

        {/* Calories Input + Quick Chips */}
        <Text style={styles.inputLabel}>Estimasi Kalori (kkal)</Text>
        <View style={styles.calorieInputRow}>
          <TextInput
            style={[styles.textInput, { flex: 1, marginBottom: 0 }]}
            placeholder="450"
            placeholderTextColor="#94a3b8"
            value={calories}
            onChangeText={setCalories}
            keyboardType="numeric"
          />
          <View style={styles.quickChipsRow}>
            {CALORIE_CHIPS.map((c) => (
              <TouchableOpacity
                key={c}
                style={styles.quickChip}
                onPress={() => setCalories(c.toString())}
              >
                <Text style={styles.quickChipText}>{c}</Text>
              </TouchableOpacity>
            ))}
          </View>
        </View>

        {/* Balanced Toggle */}
        <TouchableOpacity
          style={styles.balancedToggle}
          onPress={() => setIsBalanced(!isBalanced)}
        >
          <Icon
            name={isBalanced ? "check-square" : "square"}
            size={18}
            color={isBalanced ? themeColor : "#94a3b8"}
          />
          <Text style={styles.balancedToggleText}>Mengandung sayur / gizi seimbang</Text>
        </TouchableOpacity>

        {/* Add Button */}
        <TouchableOpacity
          style={[styles.addBtn, { backgroundColor: themeColor }]}
          onPress={handleAddMeal}
          disabled={adding}
        >
          {adding ? (
            <ActivityIndicator color="#ffffff" />
          ) : (
            <>
              <Icon name="plus" size={16} color="#ffffff" style={{ marginRight: 6 }} />
              <Text style={styles.addBtnText}>Tambahkan ke Menu Hari Ini</Text>
            </>
          )}
        </TouchableOpacity>
      </View>

      {/* 4. Today's Meals List */}
      <View style={styles.card}>
        <Text style={styles.sectionTitle}>Riwayat Menu Hari Ini</Text>
        <Text style={styles.sectionSub}>Daftar makanan yang sudah dicatat</Text>

        {todayData.logs?.length === 0 ? (
          <View style={styles.emptyBox}>
            <Icon name="coffee" size={28} color="#cbd5e1" />
            <Text style={styles.emptyText}>Belum ada menu yang dicatat hari ini.</Text>
          </View>
        ) : (
          <View style={{ gap: 8, marginTop: 8 }}>
            {todayData.logs.map((item: any) => (
              <View key={item.id} style={styles.logItem}>
                <View style={styles.logLeft}>
                  <View style={[styles.logIconBox, { backgroundColor: `${themeColor}15` }]}>
                    <Icon name="check" size={14} color={themeColor} />
                  </View>
                  <View style={{ flex: 1 }}>
                    <Text style={styles.logFoodName}>{item.food_name}</Text>
                    <Text style={styles.logMeta}>
                      {item.meal_type.toUpperCase().replace("_", " ")} • {item.is_balanced ? "🥗 Gizi Seimbang" : "Biasa"}
                    </Text>
                  </View>
                </View>

                <View style={styles.logRight}>
                  <Text style={styles.logCalories}>{item.calories} kkal</Text>
                  <TouchableOpacity onPress={() => handleDeleteMeal(item.id)} style={{ padding: 4 }}>
                    <Icon name="trash-2" size={14} color="#ef4444" />
                  </TouchableOpacity>
                </View>
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
  calorieCard: {
    backgroundColor: "#fffbeb",
    borderColor: "#fef3c7",
  },
  calorieHeader: {
    flexDirection: "row",
    justifyContent: "space-between",
    alignItems: "center",
  },
  calorieTitle: {
    fontSize: 14,
    fontWeight: "700",
    color: "#78350f",
  },
  calorieSub: {
    fontSize: 11,
    color: "#92400e",
    marginTop: 2,
  },
  calorieValueBox: {
    alignItems: "flex-end",
  },
  calorieValue: {
    fontSize: 26,
    fontWeight: "800",
  },
  calorieUnit: {
    fontSize: 10,
    color: "#b45309",
    marginTop: -4,
  },
  progressBarTrack: {
    height: 8,
    backgroundColor: "#fde68a",
    borderRadius: 4,
    overflow: "hidden",
    marginTop: 14,
  },
  progressBarFill: {
    height: "100%",
    borderRadius: 4,
  },
  progressLabelRow: {
    flexDirection: "row",
    justifyContent: "space-between",
    marginTop: 6,
  },
  progressLabelText: {
    fontSize: 10,
    color: "#92400e",
    fontWeight: "600",
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
  plateGrid: {
    flexDirection: "row",
    gap: 8,
  },
  plateBox: {
    flex: 1,
    padding: 10,
    borderRadius: 12,
    alignItems: "center",
  },
  platePct: {
    fontSize: 14,
    fontWeight: "800",
    color: "#1e293b",
  },
  plateLabel: {
    fontSize: 9,
    color: "#475569",
    marginTop: 2,
    textAlign: "center",
    fontWeight: "600",
  },
  mealTypesRow: {
    flexDirection: "row",
    flexWrap: "wrap",
    gap: 8,
    marginBottom: 12,
  },
  mealChip: {
    flexDirection: "row",
    alignItems: "center",
    paddingHorizontal: 12,
    paddingVertical: 7,
    borderRadius: 10,
    backgroundColor: "#f8fafc",
    borderWidth: 1,
    borderColor: "#e2e8f0",
  },
  mealChipText: {
    fontSize: 12,
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
  calorieInputRow: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    marginBottom: 12,
  },
  quickChipsRow: {
    flexDirection: "row",
    gap: 4,
  },
  quickChip: {
    paddingHorizontal: 8,
    paddingVertical: 8,
    borderRadius: 8,
    backgroundColor: "#f1f5f9",
    borderWidth: 1,
    borderColor: "#e2e8f0",
  },
  quickChipText: {
    fontSize: 11,
    fontWeight: "600",
    color: "#475569",
  },
  balancedToggle: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
    marginBottom: 14,
  },
  balancedToggleText: {
    fontSize: 12,
    color: "#334155",
    fontWeight: "500",
  },
  addBtn: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 12,
    borderRadius: 12,
  },
  addBtnText: {
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
  logItem: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    padding: 10,
    backgroundColor: "#f8fafc",
    borderRadius: 12,
  },
  logLeft: {
    flexDirection: "row",
    alignItems: "center",
    gap: 10,
    flex: 1,
  },
  logIconBox: {
    width: 28,
    height: 28,
    borderRadius: 8,
    alignItems: "center",
    justifyContent: "center",
  },
  logFoodName: {
    fontSize: 12,
    fontWeight: "700",
    color: "#1e293b",
  },
  logMeta: {
    fontSize: 10,
    color: "#64748b",
    marginTop: 2,
  },
  logRight: {
    flexDirection: "row",
    alignItems: "center",
    gap: 8,
  },
  logCalories: {
    fontSize: 12,
    fontWeight: "700",
    color: "#0f172a",
  },
});
