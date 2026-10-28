import React, { useState, useEffect } from "react";
import {
  Modal,
  View,
  Text,
  TextInput,
  Pressable,
  Image,
  StyleSheet,
  ActivityIndicator,
  Alert,
  ScrollView,
  Dimensions,
} from "react-native";
import Icon from "react-native-vector-icons/Feather";
import { launchImageLibrary } from "react-native-image-picker";
import { Colors } from "../settings/Colors";
import api from "../api/axiosConfig";

const { height: SCREEN_HEIGHT } = Dimensions.get("window");

const AVATAR_PRESETS = [
  "https://api.dicebear.com/7.x/adventurer/png?seed=Alex",
  "https://api.dicebear.com/7.x/adventurer/png?seed=Maria",
  "https://api.dicebear.com/7.x/adventurer/png?seed=Sam",
  "https://api.dicebear.com/7.x/adventurer/png?seed=Jessica",
  "https://api.dicebear.com/7.x/bottts/png?seed=MediBot",
  "https://api.dicebear.com/7.x/bottts/png?seed=CareBot",
];

interface EditProfileModalProps {
  visible: boolean;
  onClose: () => void;
  currentProfile: {
    id: string | number;
    nama: string;
    email?: string;
    username: string;
    avatar?: string | null;
    [key: string]: any;
  } | null;
  onProfileUpdated: (updatedUser: any) => void;
  themeColor?: string;
}

const EditProfileModal: React.FC<EditProfileModalProps> = ({
  visible,
  onClose,
  currentProfile,
  onProfileUpdated,
  themeColor = Colors.primary,
}) => {
  const [nama, setNama] = useState("");
  const [email, setEmail] = useState("");
  const [avatar, setAvatar] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);

  useEffect(() => {
    if (visible && currentProfile) {
      setNama(currentProfile.nama || "");
      setEmail(currentProfile.email || "");
      setAvatar(currentProfile.avatar || null);
      setErrorMessage(null);
    }
  }, [visible, currentProfile]);

  const handlePickFromGallery = async () => {
    try {
      const result = await launchImageLibrary({
        mediaType: "photo",
        maxWidth: 600,
        maxHeight: 600,
        quality: 0.8,
        includeBase64: true,
      });

      if (result.didCancel) return;
      if (result.errorCode) {
        Alert.alert("Gagal Membuka Galeri", result.errorMessage || "Tidak dapat mengakses foto.");
        return;
      }

      const asset = result.assets?.[0];
      if (asset?.base64) {
        setAvatar(`data:${asset.type || "image/jpeg"};base64,${asset.base64}`);
      } else if (asset?.uri) {
        setAvatar(asset.uri);
      }
    } catch (e: any) {
      Alert.alert("Kesalahan", e?.message || "Gagal memilih foto.");
    }
  };

  const handleRemovePhoto = () => {
    setAvatar(null);
  };

  const handleSave = async () => {
    if (!nama.trim()) {
      setErrorMessage("Nama lengkap wajib diisi.");
      return;
    }

    try {
      setLoading(true);
      setErrorMessage(null);

      const payload: { nama: string; email?: string | null; avatar?: string | null; remove_avatar?: boolean } = {
        nama: nama.trim(),
        email: email.trim() ? email.trim() : null,
      };

      if (avatar === null) {
        payload.remove_avatar = true;
      } else {
        payload.avatar = avatar;
      }

      const res = await api.post("/profile", payload);
      const updated = res.data?.data;

      if (updated) {
        onProfileUpdated(updated);
      }

      Alert.alert("Sukses", "Profil berhasil diperbarui!");
      onClose();
    } catch (e: any) {
      const resp = e?.response?.data;
      const msg = resp?.message || resp?.errors?.email?.[0] || resp?.errors?.nama?.[0] || "Gagal memperbarui profil.";
      setErrorMessage(msg);
    } finally {
      setLoading(false);
    }
  };

  return (
    <Modal
      animationType="slide"
      transparent={true}
      visible={visible}
      onRequestClose={onClose}
    >
      <View style={styles.modalOverlay}>
        <View style={styles.modalContent}>
          {/* Header */}
          <View style={styles.modalHeader}>
            <View style={{ flexDirection: "row", alignItems: "center", gap: 8 }}>
              <Icon name="user-check" size={20} color={themeColor} />
              <Text style={styles.modalTitle}>Edit Profil Pengguna</Text>
            </View>
            <Pressable onPress={onClose} hitSlop={10} style={styles.closeBtn}>
              <Icon name="x" size={22} color={Colors.muted} />
            </Pressable>
          </View>

          <ScrollView showsVerticalScrollIndicator={false} contentContainerStyle={styles.scrollContent}>
            {/* Avatar Section */}
            <View style={styles.avatarSection}>
              <View style={[styles.avatarContainer, { borderColor: themeColor }]}>
                {avatar ? (
                  <Image source={{ uri: avatar }} style={styles.avatarImage} />
                ) : (
                  <View style={[styles.avatarPlaceholder, { backgroundColor: themeColor }]}>
                    <Text style={styles.avatarInitial}>
                      {nama ? nama.charAt(0).toUpperCase() : "U"}
                    </Text>
                  </View>
                )}
                <Pressable
                  onPress={handlePickFromGallery}
                  style={[styles.cameraBadge, { backgroundColor: themeColor }]}
                >
                  <Icon name="camera" size={14} color={Colors.white} />
                </Pressable>
              </View>

              <View style={styles.avatarActionButtons}>
                <Pressable onPress={handlePickFromGallery} style={styles.uploadBtn}>
                  <Icon name="image" size={14} color={themeColor} />
                  <Text style={[styles.uploadBtnText, { color: themeColor }]}>Buka Galeri</Text>
                </Pressable>
                {avatar && (
                  <Pressable onPress={handleRemovePhoto} style={styles.removeBtn}>
                    <Icon name="trash-2" size={14} color="#dc2626" />
                    <Text style={styles.removeBtnText}>Hapus</Text>
                  </Pressable>
                )}
              </View>

              {/* Quick Presets */}
              <Text style={styles.presetLabel}>Atau pilih avatar karakter:</Text>
              <View style={styles.presetRow}>
                {AVATAR_PRESETS.map((p, idx) => (
                  <Pressable
                    key={idx}
                    onPress={() => setAvatar(p)}
                    style={[
                      styles.presetItem,
                      avatar === p && { borderColor: themeColor, borderWidth: 2.5 },
                    ]}
                  >
                    <Image source={{ uri: p }} style={styles.presetImg} />
                  </Pressable>
                ))}
              </View>
            </View>

            {/* Error Message */}
            {errorMessage && (
              <View style={styles.errorBox}>
                <Icon name="alert-circle" size={16} color="#dc2626" />
                <Text style={styles.errorText}>{errorMessage}</Text>
              </View>
            )}

            {/* Input Nama Lengkap */}
            <View style={styles.inputGroup}>
              <Text style={styles.fieldLabel}>
                Nama Lengkap <Text style={{ color: "#dc2626" }}>*</Text>
              </Text>
              <View style={styles.inputWrap}>
                <Icon name="user" size={16} color={Colors.muted} style={styles.inputIcon} />
                <TextInput
                  value={nama}
                  onChangeText={setNama}
                  placeholder="Masukkan nama lengkap Anda"
                  placeholderTextColor={Colors.muted}
                  style={styles.textInput}
                />
              </View>
            </View>

            {/* Input Alamat Email */}
            <View style={styles.inputGroup}>
              <Text style={styles.fieldLabel}>Alamat Email</Text>
              <View style={styles.inputWrap}>
                <Icon name="mail" size={16} color={Colors.muted} style={styles.inputIcon} />
                <TextInput
                  value={email}
                  onChangeText={setEmail}
                  placeholder="nama@email.com (opsional)"
                  placeholderTextColor={Colors.muted}
                  keyboardType="email-address"
                  autoCapitalize="none"
                  style={styles.textInput}
                />
              </View>
              <Text style={styles.helperText}>
                Email dapat digunakan untuk autentikasi Google dan notifikasi.
              </Text>
            </View>

            {/* Username (Disabled) */}
            <View style={styles.inputGroup}>
              <Text style={styles.fieldLabel}>Username Login</Text>
              <View style={[styles.inputWrap, styles.disabledInput]}>
                <Icon name="at-sign" size={16} color={Colors.muted} style={styles.inputIcon} />
                <TextInput
                  value={currentProfile?.username || ""}
                  editable={false}
                  style={[styles.textInput, { color: Colors.muted }]}
                />
                <Icon name="lock" size={14} color={Colors.muted} style={{ marginRight: 12 }} />
              </View>
            </View>
          </ScrollView>

          {/* Action Footer */}
          <View style={styles.modalActions}>
            <Pressable
              onPress={onClose}
              disabled={loading}
              style={[styles.btn, styles.btnCancel]}
            >
              <Text style={styles.btnCancelText}>Batal</Text>
            </Pressable>

            <Pressable
              onPress={handleSave}
              disabled={loading || !nama.trim()}
              style={[
                styles.btn,
                styles.btnSave,
                { backgroundColor: themeColor, opacity: loading || !nama.trim() ? 0.6 : 1 },
              ]}
            >
              {loading ? (
                <ActivityIndicator color={Colors.white} size="small" />
              ) : (
                <>
                  <Icon name="check" size={16} color={Colors.white} />
                  <Text style={styles.btnSaveText}>Simpan Profil</Text>
                </>
              )}
            </Pressable>
          </View>
        </View>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  modalOverlay: {
    flex: 1,
    backgroundColor: "rgba(15, 23, 42, 0.65)",
    justifyContent: "center",
    alignItems: "center",
    padding: 16,
  },
  modalContent: {
    width: "100%",
    maxWidth: 380,
    maxHeight: SCREEN_HEIGHT * 0.85,
    backgroundColor: Colors.white,
    borderRadius: 24,
    padding: 20,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 10 },
    shadowOpacity: 0.2,
    shadowRadius: 20,
    elevation: 10,
  },
  modalHeader: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    paddingBottom: 14,
    borderBottomWidth: 1,
    borderBottomColor: "#f1f5f9",
  },
  modalTitle: {
    fontSize: 16,
    fontWeight: "800",
    color: Colors.text,
  },
  closeBtn: {
    padding: 4,
  },
  scrollContent: {
    paddingVertical: 14,
  },
  avatarSection: {
    alignItems: "center",
    marginBottom: 16,
  },
  avatarContainer: {
    width: 84,
    height: 84,
    borderRadius: 42,
    borderWidth: 3,
    position: "relative",
    justifyContent: "center",
    alignItems: "center",
    backgroundColor: "#f8fafc",
    shadowColor: "#000",
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.1,
    shadowRadius: 6,
    elevation: 3,
  },
  avatarImage: {
    width: "100%",
    height: "100%",
    borderRadius: 40,
  },
  avatarPlaceholder: {
    width: "100%",
    height: "100%",
    borderRadius: 40,
    justifyContent: "center",
    alignItems: "center",
  },
  avatarInitial: {
    color: Colors.white,
    fontSize: 32,
    fontWeight: "900",
  },
  cameraBadge: {
    position: "absolute",
    bottom: -2,
    right: -2,
    width: 28,
    height: 28,
    borderRadius: 14,
    justifyContent: "center",
    alignItems: "center",
    borderWidth: 2,
    borderColor: Colors.white,
    elevation: 4,
  },
  avatarActionButtons: {
    flexDirection: "row",
    gap: 8,
    marginTop: 10,
  },
  uploadBtn: {
    flexDirection: "row",
    alignItems: "center",
    gap: 4,
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 20,
    backgroundColor: "#ecfdf5",
  },
  uploadBtnText: {
    fontSize: 12,
    fontWeight: "700",
  },
  removeBtn: {
    flexDirection: "row",
    alignItems: "center",
    gap: 4,
    paddingHorizontal: 12,
    paddingVertical: 6,
    borderRadius: 20,
    backgroundColor: "#fef2f2",
  },
  removeBtnText: {
    fontSize: 12,
    fontWeight: "700",
    color: "#dc2626",
  },
  presetLabel: {
    fontSize: 11,
    color: Colors.muted,
    marginTop: 12,
    marginBottom: 6,
  },
  presetRow: {
    flexDirection: "row",
    gap: 8,
    justifyContent: "center",
  },
  presetItem: {
    width: 36,
    height: 36,
    borderRadius: 18,
    borderWidth: 1.5,
    borderColor: "#e2e8f0",
    overflow: "hidden",
    backgroundColor: "#f8fafc",
  },
  presetImg: {
    width: "100%",
    height: "100%",
  },
  errorBox: {
    flexDirection: "row",
    alignItems: "center",
    gap: 6,
    backgroundColor: "#fef2f2",
    borderWidth: 1,
    borderColor: "#fecaca",
    padding: 10,
    borderRadius: 12,
    marginBottom: 12,
  },
  errorText: {
    fontSize: 12,
    color: "#dc2626",
    flex: 1,
  },
  inputGroup: {
    marginBottom: 12,
  },
  fieldLabel: {
    fontSize: 11,
    fontWeight: "700",
    textTransform: "uppercase",
    letterSpacing: 0.5,
    color: Colors.text,
    marginBottom: 6,
  },
  inputWrap: {
    flexDirection: "row",
    alignItems: "center",
    backgroundColor: "#f8fafc",
    borderWidth: 1,
    borderColor: "#e2e8f0",
    borderRadius: 12,
  },
  disabledInput: {
    backgroundColor: "#f1f5f9",
  },
  inputIcon: {
    paddingLeft: 12,
    paddingRight: 8,
  },
  textInput: {
    flex: 1,
    paddingVertical: 9,
    paddingRight: 12,
    fontSize: 13,
    color: Colors.text,
  },
  helperText: {
    fontSize: 10,
    color: Colors.muted,
    marginTop: 4,
  },
  modalActions: {
    flexDirection: "row",
    gap: 10,
    paddingTop: 12,
    borderTopWidth: 1,
    borderTopColor: "#f1f5f9",
  },
  btn: {
    flex: 1,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    gap: 6,
    paddingVertical: 11,
    borderRadius: 12,
  },
  btnCancel: {
    backgroundColor: "#f1f5f9",
  },
  btnCancelText: {
    fontSize: 13,
    fontWeight: "700",
    color: Colors.muted,
  },
  btnSave: {
    shadowColor: Colors.primary,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.2,
    shadowRadius: 6,
    elevation: 3,
  },
  btnSaveText: {
    fontSize: 13,
    fontWeight: "700",
    color: Colors.white,
  },
});

export default EditProfileModal;
