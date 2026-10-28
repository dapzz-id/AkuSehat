"use client"

import React, { useState, useRef, useEffect } from "react"
import {
    Alert,
    Animated,
    KeyboardAvoidingView,
    Platform,
    Pressable,
    ScrollView,
    StatusBar,
    StyleSheet,
    Text,
    TextInput,
    View,
} from "react-native"
import { SafeAreaView } from "react-native-safe-area-context"
import { Colors } from "../../settings/Colors"
import Icon from "react-native-vector-icons/MaterialCommunityIcons"
import { PrivacyPolicyModal } from "../../components/PrivacyPolicyModal"
import AsyncStorage from "@react-native-async-storage/async-storage"
import { register } from "../../utils/authHelper"

const RegisterScreen = ({ navigation }: any) => {
    const [name, setName] = useState("")
    const [username, setUsername] = useState("")
    const [email, setEmail] = useState("")
    const [password, setPassword] = useState("")
    const [passwordConfirmation, setPasswordConfirmation] = useState("")
    const [inviteCode, setInviteCode] = useState("")
    const [showPassword, setShowPassword] = useState(false)
    const [loading, setLoading] = useState(false)
    const [error, setError] = useState<string | null>(null)
    const [isPrivacyAgreed, setIsPrivacyAgreed] = useState(false)
    const [showPrivacyModal, setShowPrivacyModal] = useState(false)

    const cardOpacity = useRef(new Animated.Value(0)).current
    const cardTranslate = useRef(new Animated.Value(20)).current

    useEffect(() => {
        Animated.parallel([
            Animated.timing(cardOpacity, {
                toValue: 1,
                duration: 500,
                useNativeDriver: true,
            }),
            Animated.timing(cardTranslate, {
                toValue: 0,
                duration: 500,
                useNativeDriver: true,
            }),
        ]).start()
    }, [])

    const handleRegister = async () => {
        if (!name.trim() || !username.trim() || !email.trim() || !password) {
            setError("Nama, username, email, dan password wajib diisi.")
            return
        }
        if (password !== passwordConfirmation) {
            setError("Konfirmasi password tidak cocok.")
            return
        }
        if (password.length < 6) {
            setError("Password minimal 6 karakter.")
            return
        }
        if (!isPrivacyAgreed) {
            setError("Harap baca dan setujui Kebijakan Privasi terlebih dahulu.")
            setShowPrivacyModal(true)
            return
        }

        setLoading(true)
        setError(null)

        try {
            const res = await register({
                name: name.trim(),
                username: username.trim(),
                email: email.trim(),
                password: password,
                password_confirmation: passwordConfirmation,
                invite_code: inviteCode.trim() || undefined,
            })

            if (res.status) {
                Alert.alert(
                    "Registrasi Berhasil",
                    res.message || "Akun Anda berhasil didaftarkan!",
                    [
                        {
                            text: "Mulai Sekarang",
                            onPress: () => navigation.reset({ index: 0, routes: [{ name: "HomeMember" }] }),
                        },
                    ]
                )
            } else {
                setError(res.message || "Gagal melakukan registrasi.")
            }
        } catch (err: any) {
            setError("Koneksi gagal ke server. Silakan coba lagi.")
        } finally {
            setLoading(false)
        }
    }

    return (
        <SafeAreaView style={styles.safe}>
            <StatusBar barStyle="dark-content" backgroundColor={Colors.bg} />
            <KeyboardAvoidingView style={styles.flex} behavior={Platform.select({ ios: "padding", android: undefined })}>
                <ScrollView contentContainerStyle={styles.scroll} showsVerticalScrollIndicator={false}>
                    <Animated.View style={[styles.card, { opacity: cardOpacity, transform: [{ translateY: cardTranslate }] }]}>
                        {/* Header */}
                        <View style={styles.header}>
                            <Text style={styles.title}>Daftar Akun Baru</Text>
                            <Text style={styles.subtitle}>
                                Akun pribadi kesehatan untuk Anda. Dapat ditautkan ke instansi/institusi kapan saja.
                            </Text>
                        </View>

                        {error && (
                            <View style={styles.errorContainer}>
                                <Icon name="alert-circle-outline" size={18} color={Colors.error} style={{ marginRight: 6 }} />
                                <Text style={styles.errorText}>{error}</Text>
                            </View>
                        )}

                        {/* Name Input */}
                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>NAMA LENGKAP</Text>
                            <View style={styles.inputWrapper}>
                                <Icon name="account-outline" size={20} color="#94A3B8" style={styles.inputIcon} />
                                <TextInput
                                    style={styles.input}
                                    placeholder="Contoh: Budi Pratama"
                                    placeholderTextColor="#94A3B8"
                                    value={name}
                                    onChangeText={setName}
                                />
                            </View>
                        </View>

                        {/* Username Input */}
                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>USERNAME</Text>
                            <View style={styles.inputWrapper}>
                                <Icon name="at" size={20} color="#94A3B8" style={styles.inputIcon} />
                                <TextInput
                                    style={styles.input}
                                    placeholder="budipratama"
                                    placeholderTextColor="#94A3B8"
                                    autoCapitalize="none"
                                    value={username}
                                    onChangeText={setUsername}
                                />
                            </View>
                        </View>

                        {/* Email Input */}
                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>EMAIL</Text>
                            <View style={styles.inputWrapper}>
                                <Icon name="email-outline" size={20} color="#94A3B8" style={styles.inputIcon} />
                                <TextInput
                                    style={styles.input}
                                    placeholder="budi@example.com"
                                    placeholderTextColor="#94A3B8"
                                    keyboardType="email-address"
                                    autoCapitalize="none"
                                    value={email}
                                    onChangeText={setEmail}
                                />
                            </View>
                        </View>

                        {/* Password Input */}
                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>PASSWORD</Text>
                            <View style={styles.inputWrapper}>
                                <Icon name="lock-outline" size={20} color="#94A3B8" style={styles.inputIcon} />
                                <TextInput
                                    style={styles.input}
                                    placeholder="Minimal 6 karakter"
                                    placeholderTextColor="#94A3B8"
                                    secureTextEntry={!showPassword}
                                    value={password}
                                    onChangeText={setPassword}
                                />
                                <Pressable onPress={() => setShowPassword(!showPassword)} style={styles.eyeButton}>
                                    <Icon name={showPassword ? "eye-off-outline" : "eye-outline"} size={20} color="#94A3B8" />
                                </Pressable>
                            </View>
                        </View>

                        {/* Password Confirmation */}
                        <View style={styles.inputGroup}>
                            <Text style={styles.label}>KONFIRMASI PASSWORD</Text>
                            <View style={styles.inputWrapper}>
                                <Icon name="lock-check-outline" size={20} color="#94A3B8" style={styles.inputIcon} />
                                <TextInput
                                    style={styles.input}
                                    placeholder="Ulangi password Anda"
                                    placeholderTextColor="#94A3B8"
                                    secureTextEntry={!showPassword}
                                    value={passwordConfirmation}
                                    onChangeText={setPasswordConfirmation}
                                />
                            </View>
                        </View>

                        {/* Invite Code Input (Optional) */}
                        <View style={styles.inputGroup}>
                            <View style={{ flexDirection: "row", justifyContent: "space-between", alignItems: "center" }}>
                                <Text style={styles.label}>KODE UNDANGAN INSTANSI (OPSIONAL)</Text>
                                <Text style={styles.optionalBadge}>Opsional</Text>
                            </View>
                            <View style={[styles.inputWrapper, { borderColor: "#A7F3D0" }]}>
                                <Icon name="domain" size={20} color="#059669" style={styles.inputIcon} />
                                <TextInput
                                    style={styles.input}
                                    placeholder="Contoh: SMKT-5542 atau NPSN"
                                    placeholderTextColor="#94A3B8"
                                    autoCapitalize="characters"
                                    value={inviteCode}
                                    onChangeText={setInviteCode}
                                />
                            </View>
                            <Text style={styles.helperText}>
                                Kosongkan jika mendaftar akun pribadi mandiri. Anda tetap bisa menautkannya nanti di dasbor.
                            </Text>
                        </View>

                        {/* Privacy Policy Checkbox Row */}
                        <Pressable
                            onPress={() => setShowPrivacyModal(true)}
                            style={styles.privacyContainer}
                            accessibilityRole="checkbox"
                            accessibilityState={{ checked: isPrivacyAgreed }}
                        >
                            <Icon
                                name={isPrivacyAgreed ? "checkbox-marked" : "checkbox-blank-outline"}
                                size={22}
                                color={isPrivacyAgreed ? Colors.primary : "#94A3B8"}
                                style={styles.privacyCheckboxIcon}
                            />
                            <Text style={styles.privacyText}>
                                Saya telah membaca dan menyetujui{" "}
                                <Text style={styles.privacyLink}>Kebijakan Privasi</Text>
                                <Text style={{ color: Colors.error }}> *</Text>
                            </Text>
                        </Pressable>

                        {/* Register Button */}
                        <Pressable
                            onPress={handleRegister}
                            disabled={loading}
                            style={[styles.button, loading && styles.buttonDisabled]}
                        >
                            <Text style={styles.buttonText}>{loading ? "Mendaftarkan..." : "Daftar Akun Pribadi"}</Text>
                        </Pressable>

                        {/* Login Link */}
                        <View style={styles.loginContainer}>
                            <Text style={styles.loginPrompt}>Sudah punya akun? </Text>
                            <Pressable onPress={() => navigation.navigate("Login")}>
                                <Text style={styles.loginLink}>Masuk di sini</Text>
                            </Pressable>
                        </View>
                    </Animated.View>
                </ScrollView>
            </KeyboardAvoidingView>

            {/* Privacy Policy Modal */}
            <PrivacyPolicyModal
                visible={showPrivacyModal}
                onClose={() => setShowPrivacyModal(false)}
                onAgree={() => {
                    setIsPrivacyAgreed(true)
                    setError(null)
                }}
                isAlreadyAgreed={isPrivacyAgreed}
            />
        </SafeAreaView>
    )
}

const styles = StyleSheet.create({
    flex: { flex: 1 },
    safe: { flex: 1, backgroundColor: Colors.bg },
    scroll: {
        flexGrow: 1,
        paddingHorizontal: 20,
        paddingVertical: 24,
        justifyContent: "center",
    },
    card: {
        backgroundColor: "#FFFFFF",
        borderRadius: 20,
        padding: 24,
        shadowColor: "#000",
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.08,
        shadowRadius: 12,
        elevation: 3,
    },
    header: {
        marginBottom: 20,
    },
    title: {
        fontSize: 22,
        fontWeight: "800",
        color: "#0F172A",
        marginBottom: 6,
    },
    subtitle: {
        fontSize: 13,
        color: "#64748B",
        lineHeight: 18,
    },
    errorContainer: {
        flexDirection: "row",
        alignItems: "center",
        backgroundColor: "#FEF2F2",
        padding: 10,
        borderRadius: 10,
        borderWidth: 1,
        borderColor: "#FCA5A5",
        marginBottom: 16,
    },
    errorText: {
        flex: 1,
        fontSize: 12,
        color: "#B91C1C",
    },
    inputGroup: {
        marginBottom: 16,
    },
    label: {
        fontSize: 11,
        fontWeight: "700",
        color: "#475569",
        letterSpacing: 0.5,
        marginBottom: 6,
    },
    optionalBadge: {
        fontSize: 11,
        color: "#059669",
        fontWeight: "600",
    },
    inputWrapper: {
        flexDirection: "row",
        alignItems: "center",
        backgroundColor: "#F8FAFC",
        borderRadius: 12,
        borderWidth: 1,
        borderColor: "#E2E8F0",
        paddingHorizontal: 12,
    },
    inputIcon: {
        marginRight: 10,
    },
    input: {
        flex: 1,
        paddingVertical: 12,
        fontSize: 14,
        color: "#1E293B",
    },
    eyeButton: {
        padding: 6,
    },
    helperText: {
        fontSize: 11,
        color: "#64748B",
        marginTop: 4,
        lineHeight: 15,
    },
    privacyContainer: {
        flexDirection: "row",
        alignItems: "center",
        backgroundColor: "#F8FAFC",
        paddingHorizontal: 12,
        paddingVertical: 10,
        borderRadius: 10,
        borderWidth: 1,
        borderColor: "#E2E8F0",
        marginBottom: 18,
    },
    privacyCheckboxIcon: {
        marginRight: 8,
    },
    privacyText: {
        flex: 1,
        fontSize: 12,
        color: "#475569",
        lineHeight: 17,
    },
    privacyLink: {
        fontWeight: "bold",
        color: Colors.primary,
        textDecorationLine: "underline",
    },
    button: {
        backgroundColor: Colors.primary,
        borderRadius: 12,
        paddingVertical: 14,
        alignItems: "center",
        justifyContent: "center",
    },
    buttonDisabled: {
        opacity: 0.6,
    },
    buttonText: {
        color: "#FFFFFF",
        fontSize: 15,
        fontWeight: "700",
    },
    loginContainer: {
        flexDirection: "row",
        justifyContent: "center",
        alignItems: "center",
        marginTop: 18,
    },
    loginPrompt: {
        fontSize: 13,
        color: "#64748B",
    },
    loginLink: {
        fontSize: 13,
        fontWeight: "700",
        color: Colors.primary,
    },
})

export default RegisterScreen
