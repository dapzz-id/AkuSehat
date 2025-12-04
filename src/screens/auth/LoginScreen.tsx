"use client"

import { useEffect, useMemo, useRef, useState } from "react"
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
    BackHandler,
    Image
} from "react-native"
import { SafeAreaView } from 'react-native-safe-area-context';
import { login } from "../../utils/authHelper"
import { Colors } from "../../settings/Colors"
import Icon from 'react-native-vector-icons/MaterialCommunityIcons';

const LoginScreen = ({ navigation }: any) => {
    const [username, setUsername] = useState("")
    const [password, setPassword] = useState("")
    const [licenseKey, setLicenseKey] = useState("")
    const [loading, setLoading] = useState(false)
    const [showPassword, setShowPassword] = useState(false)
    const [error, setError] = useState<string | null>(null)

    const cardOpacity = useRef(new Animated.Value(0)).current
    const cardTranslate = useRef(new Animated.Value(20)).current
    const logoScale = useRef(new Animated.Value(0.95)).current
    const buttonScale = useRef(new Animated.Value(1)).current
    const bgRotate = useRef(new Animated.Value(0)).current
    const bgPulse = useRef(new Animated.Value(0)).current

    useEffect(() => {
        const backAction = () => {
            Alert.alert('Keluar', 'Apakah kamu yakin ingin keluar?', [
                {
                    text: 'Batal',
                    onPress: () => null,
                    style: 'cancel',
                },
                { text: 'Ya', onPress: () => BackHandler.exitApp() },
            ]);
            return true;
        };

        const backHandler = BackHandler.addEventListener(
            'hardwareBackPress',
            backAction,
        );

        Animated.loop(
            Animated.sequence([
                Animated.timing(bgPulse, {
                    toValue: 1,
                    duration: 3000,
                    useNativeDriver: true,
                }),
                Animated.timing(bgPulse, {
                    toValue: 0,
                    duration: 3000,
                    useNativeDriver: true,
                }),
            ])
        ).start();

        Animated.parallel([
            Animated.timing(cardOpacity, {
                toValue: 1,
                duration: 600,
                useNativeDriver: true,
            }),
            Animated.timing(cardTranslate, {
                toValue: 0,
                duration: 600,
                useNativeDriver: true,
            }),
        ]).start()

        const loop = Animated.loop(
            Animated.sequence([
                Animated.timing(logoScale, {
                    toValue: 1.02,
                    duration: 1500,
                    useNativeDriver: true,
                }),
                Animated.timing(logoScale, {
                    toValue: 0.98,
                    duration: 1500,
                    useNativeDriver: true,
                }),
            ]),
        )
        loop.start()

        Animated.loop(
            Animated.timing(bgRotate, {
                toValue: 1,
                duration: 15000,
                useNativeDriver: true,
            })
        ).start()

        return () => {
            loop.stop()
            backHandler.remove()
        }
    }, [cardOpacity, cardTranslate, logoScale, bgRotate, bgPulse])

    const onPressIn = () => {
        Animated.spring(buttonScale, {
            toValue: 0.95,
            useNativeDriver: true,
            speed: 40,
            bounciness: 6,
        }).start()
    }

    const onPressOut = () => {
        Animated.spring(buttonScale, {
            toValue: 1,
            useNativeDriver: true,
            speed: 40,
            bounciness: 6,
        }).start()
    }

    const handleLogin = async () => {
        if (!username || !password) {
            setError("Username dan password wajib diisi.")
            return
        }

        setError(null)
        setLoading(true)

        try {
            const dataLogin = await login(username, password)

            if (dataLogin.status === true) {
                if (dataLogin.user.level === "Member") {
                    navigation.reset({ index: 0, routes: [{ name: "HomeMember" }] })
                } else if (dataLogin.user.level === "Health Consultant") {
                    navigation.reset({ index: 0, routes: [{ name: "HomeHealthConsultant" }] })
                }else if (dataLogin.user.level === "Health Monitor") {
                    navigation.reset({ index: 0, routes: [{ name: "HomeHealthMonitor" }] })
                }
            } else {
                setError(dataLogin.message || "Login gagal")
            }
        } catch (e: any) {
            const msg = e?.message || "Periksa username dan password"
            setError(msg)
        } finally {
            setLoading(false)
        }
    }

    const isDisabled = useMemo(() => loading || !username || !password, [loading, username, password])

    const bgRotation = bgRotate.interpolate({
        inputRange: [0, 1],
        outputRange: ['0deg', '360deg']
    })

    const bgOpacity = bgPulse.interpolate({
        inputRange: [0, 1],
        outputRange: [0.03, 0.08]
    })

    return (
        <SafeAreaView style={styles.safe}>
            <StatusBar barStyle="dark-content" backgroundColor={Colors.bg} />
            
            {/* Animated Background Elements */}
            <Animated.View style={[styles.bgCircle1, { 
                opacity: bgOpacity,
                transform: [{ rotate: bgRotation }] 
            }]} />
            <Animated.View style={[styles.bgCircle2, { 
                opacity: bgOpacity,
                transform: [{ rotate: bgRotation }] 
            }]} />
            <Animated.View style={[styles.bgCircle3, { 
                opacity: bgOpacity,
                transform: [{ rotate: bgRotation }] 
            }]} />
            
            <KeyboardAvoidingView style={styles.flex} behavior={Platform.select({ ios: "padding", android: undefined })}>
                <ScrollView 
                    contentContainerStyle={styles.scroll} 
                    showsVerticalScrollIndicator={false} 
                    showsHorizontalScrollIndicator={false} 
                    overScrollMode="never" 
                    bounces={false} 
                    keyboardShouldPersistTaps="handled"
                >
                    <Animated.View style={[styles.header, { transform: [{ scale: logoScale }] }]}>
                        <View style={styles.logoContainer}>
                            <Image 
                                source={require('../../assets/aku_sehat_icon.png')} 
                                style={styles.appIcon}
                                resizeMode="contain"
                            />
                        </View>
                        <Text style={styles.brand}>Aku Sehat</Text>
                        <Text style={styles.subtitle}>Kelola kesehatan Anda dengan mudah melalui aplikasi ini!</Text>
                    </Animated.View>

                    <Animated.View
                        style={[
                            styles.card,
                            {
                                opacity: cardOpacity,
                                transform: [{ translateY: cardTranslate }],
                            },
                        ]}
                    >
                        <View style={styles.field}>
                            <View style={styles.inputContainer}>
                                <Icon name="account-outline" size={20} color={Colors.primary} style={styles.inputIcon} />
                                <TextInput
                                    style={styles.input}
                                    placeholder="Masukkan username"
                                    placeholderTextColor="#94A3B8"
                                    value={username}
                                    onChangeText={setUsername}
                                    autoCapitalize="none"
                                    autoCorrect={false}
                                    returnKeyType="next"
                                    accessibilityLabel="Input Username"
                                />
                            </View>
                        </View>

                        <View style={styles.field}>
                            <View style={styles.inputContainer}>
                                <Icon name="lock-outline" size={20} color={Colors.primary} style={styles.inputIcon} />
                                <TextInput
                                    style={styles.input}
                                    placeholder="Masukkan password"
                                    placeholderTextColor="#94A3B8"
                                    secureTextEntry={!showPassword}
                                    value={password}
                                    onChangeText={setPassword}
                                    returnKeyType="done"
                                    onSubmitEditing={handleLogin}
                                    accessibilityLabel="Input Password"
                                />
                                <Pressable
                                    onPress={() => setShowPassword((s) => !s)}
                                    accessibilityRole="button"
                                    accessibilityLabel={showPassword ? "Sembunyikan password" : "Tampilkan password"}
                                    style={styles.eyeIcon}
                                >
                                    <Icon 
                                        name={showPassword ? "eye-off-outline" : "eye-outline"} 
                                        size={20} 
                                        color={Colors.accent} 
                                    />
                                </Pressable>
                            </View>
                        </View>

                        {/* Error Message */}
                        {error ? (
                            <View style={styles.errorContainer}>
                                <Icon name="alert-circle-outline" size={18} color={Colors.error} />
                                <Text style={styles.error}> {error}</Text>
                            </View>
                        ) : null}

                        <Animated.View style={{ transform: [{ scale: buttonScale }] }}>
                            <Pressable
                                onPressIn={onPressIn}
                                onPressOut={onPressOut}
                                onPress={handleLogin}
                                disabled={isDisabled}
                                accessibilityRole="button"
                                accessibilityLabel="Tombol Login"
                                style={({ pressed }) => [
                                    styles.button,
                                    pressed && !isDisabled ? styles.buttonPressed : null,
                                    isDisabled ? styles.buttonDisabled : null,
                                ]}
                            >
                                {loading ? (
                                    <View style={styles.loadingContainer}>
                                        <Animated.View style={[styles.spinner, { transform: [{ rotate: bgRotation }] }]}>
                                            <Icon name="loading" size={20} color="#fff" />
                                        </Animated.View>
                                        <Text style={styles.buttonText}>Memproses...</Text>
                                    </View>
                                ) : (
                                    <View style={styles.buttonContent}>
                                        <Icon name="login" size={20} color="#fff" style={styles.buttonIcon} />
                                        <Text style={styles.buttonText}>Masuk</Text>
                                    </View>
                                )}
                            </Pressable>
                        </Animated.View>
                    </Animated.View>
                </ScrollView>
            </KeyboardAvoidingView>
        </SafeAreaView>
    )
}

const styles = StyleSheet.create({
    flex: { 
        flex: 1,
    },
    safe: {
        flex: 1,
        backgroundColor: Colors.bg,
    },
    scroll: {
        flexGrow: 1,
        paddingHorizontal: 20,
        paddingVertical: 24,
        justifyContent: "center",
    },
    // Background Elements
    bgCircle1: {
        position: 'absolute',
        top: '10%',
        left: '-10%',
        width: 200,
        height: 200,
        borderRadius: 100,
        backgroundColor: Colors.primary,
    },
    bgCircle2: {
        position: 'absolute',
        bottom: '15%',
        right: '-5%',
        width: 150,
        height: 150,
        borderRadius: 75,
        backgroundColor: Colors.accent,
    },
    bgCircle3: {
        position: 'absolute',
        top: '40%',
        right: '20%',
        width: 100,
        height: 100,
        borderRadius: 50,
        backgroundColor: Colors.primary,
    },
    header: {
        alignItems: "center",
        marginBottom: 32,
    },
    logoContainer: {
        width: 120,
        height: 120,
        justifyContent: 'center',
        alignItems: 'center',
        position: 'relative',
    },
    logoBackground: {
        position: 'absolute',
        width: '100%',
        height: '100%',
        backgroundColor: 'rgba(255, 255, 255, 0.9)',
        borderRadius: 60,
        ...Platform.select({
            ios: {
                shadowColor: '#000',
                shadowOffset: { width: 0, height: 4 },
                shadowOpacity: 0.1,
                shadowRadius: 12,
            },
            android: {
                elevation: 8,
            },
        }),
    },
    appIcon: {
        width: 60,
        height: 60,
        position: 'absolute',
        zIndex: 1,
    },
    brand: {
        fontSize: 32,
        fontWeight: "800",
        color: Colors.primary,
        letterSpacing: 0.5,
        marginBottom: 8,
        marginTop: -5,
    },
    subtitle: {
        fontSize: 14,
        color: Colors.text,
        opacity: 0.8,
        textAlign: 'center',
        lineHeight: 20,
        paddingHorizontal: 20,
        marginBottom: 8,
    },
    card: {
        backgroundColor: 'rgba(255, 255, 255, 0.25)',
        borderRadius: 24,
        padding: 24,
        borderWidth: 1,
        borderColor: 'rgba(255, 255, 255, 0.3)',
        overflow: 'hidden',
        ...Platform.select({
            ios: {
                shadowColor: '#000',
                shadowOffset: { width: 0, height: 8 },
                shadowOpacity: 0.1,
                shadowRadius: 20,
                backdropFilter: 'blur(20px)',
            },
            android: {
                elevation: 10,
                backgroundColor: 'rgba(255, 255, 255, 0.9)',
            },
        }),
    },
    field: {
        marginBottom: 20,
    },
    inputContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        backgroundColor: 'rgba(255, 255, 255, 0.8)',
        borderRadius: 16,
        borderWidth: 1,
        borderColor: 'rgba(255, 255, 255, 0.5)',
        overflow: 'hidden',
    },
    inputIcon: {
        paddingHorizontal: 16,
    },
    input: {
        flex: 1,
        paddingVertical: 16,
        paddingRight: 16,
        fontSize: 16,
        color: Colors.text,
        backgroundColor: 'transparent',
    },
    eyeIcon: {
        paddingHorizontal: 16,
    },
    // Error Styles
    errorContainer: {
        backgroundColor: 'rgba(254, 242, 242, 0.8)',
        padding: 16,
        borderRadius: 12,
        borderWidth: 1,
        borderColor: Colors.error,
        marginBottom: 20,
        flexDirection: 'row',
        alignItems: 'center',
    },
    error: {
        color: Colors.error,
        fontSize: 14,
        fontWeight: '500',
        flex: 1,
    },
    button: {
        backgroundColor: Colors.primary,
        paddingVertical: 18,
        borderRadius: 16,
        alignItems: "center",
        justifyContent: "center",
        ...Platform.select({
            ios: {
                shadowColor: Colors.primary,
                shadowOffset: { width: 0, height: 4 },
                shadowOpacity: 0.3,
                shadowRadius: 8,
            },
            android: {
                elevation: 6,
            },
        }),
    },
    buttonPressed: {
        backgroundColor: Colors.accent,
    },
    buttonDisabled: {
        opacity: 0.6,
    },
    buttonContent: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
    },
    buttonIcon: {
        marginRight: 8,
    },
    buttonText: {
        color: "#fff",
        fontWeight: "700",
        fontSize: 16,
    },
    loadingContainer: {
        flexDirection: 'row',
        alignItems: 'center',
        justifyContent: 'center',
    },
    spinner: {
        marginRight: 8,
    },
    toggleText: {
        color: Colors.accent,
        fontWeight: "500",
        fontSize: 13,
        paddingHorizontal: 8,
        paddingVertical: 2,
    },
})

export default LoginScreen