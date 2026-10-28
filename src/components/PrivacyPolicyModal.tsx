import React, { useState } from "react"
import {
    Modal,
    View,
    Text,
    StyleSheet,
    ScrollView,
    Pressable,
    NativeSyntheticEvent,
    NativeScrollEvent
} from "react-native"
import { SafeAreaView } from "react-native-safe-area-context"
import { Colors } from "../settings/Colors"
import Icon from "react-native-vector-icons/MaterialCommunityIcons"

interface PrivacyPolicyModalProps {
    visible: boolean
    onClose: () => void
    onAgree: () => void
    isAlreadyAgreed?: boolean
}

export const PrivacyPolicyModal: React.FC<PrivacyPolicyModalProps> = ({
    visible,
    onClose,
    onAgree,
    isAlreadyAgreed = false,
}) => {
    const [hasScrolledToBottom, setHasScrolledToBottom] = useState(isAlreadyAgreed)
    const [scrollPercentage, setScrollPercentage] = useState(isAlreadyAgreed ? 100 : 0)

    const handleScroll = (event: NativeSyntheticEvent<NativeScrollEvent>) => {
        const { layoutMeasurement, contentOffset, contentSize } = event.nativeEvent
        const totalHeight = contentSize.height - layoutMeasurement.height

        if (totalHeight > 0) {
            const currentPct = Math.min(100, Math.max(0, Math.round((contentOffset.y / totalHeight) * 100)))
            setScrollPercentage(currentPct)

            // Buffer 35px from bottom
            if (layoutMeasurement.height + contentOffset.y >= contentSize.height - 35) {
                if (!hasScrolledToBottom) {
                    setHasScrolledToBottom(true)
                }
            }
        }
    }

    const handleConfirmAgree = () => {
        if (hasScrolledToBottom) {
            onAgree()
            onClose()
        }
    }

    return (
        <Modal
            visible={visible}
            animationType="slide"
            transparent={false}
            onRequestClose={onClose}
        >
            <SafeAreaView style={styles.container}>
                {/* Header */}
                <View style={styles.header}>
                    <View style={styles.headerTitleContainer}>
                        <Icon name="shield-check" size={24} color={Colors.primary} />
                        <View style={{ marginLeft: 8 }}>
                            <Text style={styles.headerTitle}>Kebijakan Privasi</Text>
                            <Text style={styles.headerSubtitle}>Aku Sehat • Neura Cakrawira Asia</Text>
                        </View>
                    </View>
                    <Pressable onPress={onClose} style={styles.closeButton}>
                        <Icon name="close" size={22} color="#64748B" />
                    </Pressable>
                </View>

                {/* Banner Guidance */}
                <View style={[styles.banner, hasScrolledToBottom ? styles.bannerSuccess : styles.bannerWarning]}>
                    <Icon
                        name={hasScrolledToBottom ? "check-circle" : "arrow-down-circle"}
                        size={18}
                        color={hasScrolledToBottom ? "#166534" : "#92400E"}
                    />
                    <Text style={[styles.bannerText, hasScrolledToBottom ? styles.bannerTextSuccess : styles.bannerTextWarning]}>
                        {hasScrolledToBottom
                            ? "Dokumen selesai dibaca. Silakan klik tombol setuju di bawah."
                            : "Harap baca dan scroll hingga ke bagian paling bawah untuk menyetujui."}
                    </Text>
                    <Text style={styles.percentageText}>{scrollPercentage}%</Text>
                </View>

                {/* Scrollable Privacy Content */}
                <ScrollView
                    style={styles.scrollView}
                    contentContainerStyle={styles.scrollContent}
                    onScroll={handleScroll}
                    scrollEventThrottle={16}
                    showsVerticalScrollIndicator={true}
                >
                    <View style={styles.metaCard}>
                        <Text style={styles.metaText}>Tanggal berlaku: 21 Oktober 2026</Text>
                        <Text style={styles.metaText}>Terakhir diperbarui: 21 Oktober 2026</Text>
                    </View>

                    <Text style={styles.paragraph}>
                        Kebijakan Privasi ini menjelaskan bagaimana <Text style={styles.bold}>Neura Cakrawira Asia</Text> (“kami”) mengumpulkan, menggunakan, dan membagikan data pribadi saat Anda mengunjungi <Text style={styles.link}>https://akusehat.neuracakrawira.asia/</Text> atau menggunakan aplikasi <Text style={styles.bold}>Aku Sehat</Text> (bersama-sama disebut “Layanan”), serta pilihan dan hak yang Anda miliki.
                    </Text>

                    <Text style={styles.sectionTitle}>Siapa kami</Text>
                    <Text style={styles.paragraph}>
                        Layanan ini dikelola oleh <Text style={styles.bold}>Neura Cakrawira Asia</Text>, seorang individu. Kami berdomisili di Indonesia. Kami bertanggung jawab atas data pribadi Anda. Undang-undang pelindungan data menyebut peran ini sebagai “pengendali data pribadi”.
                    </Text>
                    <View style={styles.infoBox}>
                        <Text style={styles.infoItem}>• <Text style={styles.bold}>Email:</Text> neuracakrawira@yahoo.com</Text>
                        <Text style={styles.infoItem}>• <Text style={styles.bold}>Alamat surat:</Text> neuracakrawira@yahoo.com</Text>
                        <Text style={styles.infoItem}>• <Text style={styles.bold}>Telepon:</Text> +62 895-3831-07479</Text>
                    </View>

                    <Text style={styles.sectionTitle}>Informasi yang kami kumpulkan</Text>
                    <Text style={styles.subSectionTitle}>Informasi yang Anda berikan kepada kami:</Text>
                    <Text style={styles.bulletItem}>• <Text style={styles.bold}>Saat Anda menghubungi kami:</Text> nama, alamat email, isi pesan Anda</Text>
                    <Text style={styles.bulletItem}>• <Text style={styles.bold}>Saat Anda membuat akun:</Text> nama, alamat email, nama pengguna, kata sandi, nomor telepon, tanggal lahir atau usia</Text>
                    <Text style={styles.bulletItem}>• <Text style={styles.bold}>Saat Anda melakukan pemesanan:</Text> nama, alamat email, detail pembayaran, detail dan riwayat pesanan</Text>

                    <Text style={styles.subSectionTitle}>Informasi yang dikumpulkan secara otomatis:</Text>
                    <Text style={styles.paragraph}>
                        <Text style={styles.bold}>Log server</Text>, disimpan untuk menjalankan Layanan dan menjaga keamanannya: alamat IP, jenis perangkat dan browser, tanggal dan waktu akses.
                    </Text>

                    <Text style={styles.subSectionTitle}>Informasi yang dapat diakses aplikasi dengan izin Anda:</Text>
                    <Text style={styles.paragraph}>
                        Aplikasi hanya meminta izin berikut saat suatu fitur membutuhkannya. Anda dapat mencabut izin kapan saja di pengaturan perangkat Anda:
                    </Text>
                    <Text style={styles.bulletItem}>• Kamera</Text>
                    <Text style={styles.bulletItem}>• Foto dan media</Text>
                    <Text style={styles.bulletItem}>• Notifikasi</Text>
                    <Text style={styles.bulletItem}>• Perkiraan lokasi & Lokasi presisi</Text>

                    <View style={styles.highlightBox}>
                        <Text style={styles.highlightText}>
                            <Text style={styles.bold}>Kegunaannya bagi kami:</Text> Lokasi untuk fitur tracking run. Kamera, Foto, dan Media untuk mengunggah dengan yang berkaitan dengan media, seperti foto profile, dll.
                        </Text>
                    </View>

                    <Text style={styles.subSectionTitle}>Informasi dari sumber lain:</Text>
                    <Text style={styles.paragraph}>
                        Jika Anda masuk dengan akun dari penyedia lain (Sign in with Google), penyedia tersebut mengirimkan detail profil dasar kepada kami, seperti nama dan alamat email Anda.
                    </Text>
                    <Text style={styles.paragraph}>
                        Penyedia pembayaran kami memberi tahu kami apakah suatu pembayaran berhasil. Kami tidak menerima atau menyimpan nomor kartu lengkap Anda.
                    </Text>

                    <Text style={styles.sectionTitle}>Cara kami menggunakan informasi Anda</Text>
                    <Text style={styles.paragraph}>Kami menggunakan data pribadi hanya untuk tujuan di bawah ini:</Text>
                    <Text style={styles.bulletItem}>• Menjawab pertanyaan dan permintaan Anda (Dasar hukum: kepentingan sah kami).</Text>
                    <Text style={styles.bulletItem}>• Membuat dan mengelola akun Anda (Dasar hukum: pelaksanaan kontrak).</Text>
                    <Text style={styles.bulletItem}>• Memproses pesanan, pembayaran, pengiriman, dan pengembalian dana.</Text>
                    <Text style={styles.bulletItem}>• Menyimpan catatan yang diwajibkan oleh undang-undang perpajakan dan akuntansi.</Text>
                    <Text style={styles.bulletItem}>• Menjaga keamanan Layanan, mencegah penyalahgunaan, dan memperbaiki kesalahan.</Text>
                    <Text style={styles.bulletItem}>• Menyediakan fitur aplikasi yang memerlukan izin perangkat (persetujuan Anda).</Text>
                    <Text style={styles.bulletItem}>• Menyediakan fitur berbasis AI (pelaksanaan kontrak kami dengan Anda).</Text>

                    <Text style={styles.sectionTitle}>Cookie dan teknologi serupa</Text>
                    <Text style={styles.paragraph}>
                        Cookie adalah berkas kecil yang disimpan di perangkat Anda. Digunakan untuk esensial agar layanan berfungsi, misalnya menjaga Anda tetap masuk atau mengingat pilihan yang Anda buat.
                    </Text>

                    <Text style={styles.sectionTitle}>Kepada siapa kami membagikan informasi</Text>
                    <Text style={styles.paragraph}>Penyedia berikut memproses informasi atas nama kami agar kami dapat menjalankan Layanan:</Text>
                    <Text style={styles.bulletItem}>• <Text style={styles.bold}>Pemrosesan pembayaran:</Text> Google Play Billing (Google LLC)</Text>
                    <Text style={styles.bulletItem}>• <Text style={styles.bold}>Masuk:</Text> Sign in with Google (Google LLC)</Text>
                    <Text style={styles.bulletItem}>• <Text style={styles.bold}>Fitur peta:</Text> Google Maps (Google LLC)</Text>
                    <Text style={styles.bulletItem}>• <Text style={styles.bold}>Keamanan & Anti-Bot:</Text> Cloudflare Turnstile (Cloudflare, Inc.)</Text>
                    <Text style={styles.bulletItem}>• <Text style={styles.bold}>Fitur AI:</Text> Google Gemini API (Google LLC)</Text>
                    <Text style={[styles.paragraph, styles.bold, { marginTop: 6, color: Colors.primary }]}>
                        Kami tidak menjual data pribadi.
                    </Text>

                    <Text style={styles.sectionTitle}>Fitur AI & Transfer Internasional</Text>
                    <Text style={styles.paragraph}>
                        Sebagian fitur menggunakan kecerdasan buatan. Teks atau berkas yang Anda kirimkan ke fitur tersebut dikirim ke penyedia AI kami untuk menghasilkan keluarannya. Informasi Anda dapat ditransfer ke luar negara Anda dengan pengamanan standar (Komisi Eropa/UK).
                    </Text>

                    <Text style={styles.sectionTitle}>Berapa lama kami menyimpan informasi & Perlindungan</Text>
                    <Text style={styles.paragraph}>
                        Kami menyimpan data pribadi hanya selama diperlukan untuk tujuan dalam kebijakan ini, lalu menghapus atau menganonimkannya. Kami menerapkan langkah teknis dan organisasi yang wajar untuk melindungi data pribadi Anda.
                    </Text>

                    <Text style={styles.sectionTitle}>Hak Anda</Text>
                    <Text style={styles.paragraph}>
                        Anda dapat menanyakan kepada kami kapan saja data pribadi apa yang kami simpan tentang Anda, dan meminta kami memperbaiki atau menghapusnya melalui email ke <Text style={styles.bold}>neuracakrawira@yahoo.com</Text>. Kami mematuhi regulasi GDPR, PIPEDA, LGPD, dan Australian Privacy Principles.
                    </Text>

                    <Text style={styles.sectionTitle}>Anak-anak & Menghapus Akun</Text>
                    <Text style={styles.paragraph}>
                        Layanan ini tidak ditujukan bagi anak di bawah usia 13 tahun. Untuk menghapus akun Anda beserta informasi yang terkait, tulis ke <Text style={styles.bold}>neuracakrawira@yahoo.com</Text>.
                    </Text>

                    {/* End marker */}
                    <View style={styles.endMarker}>
                        <Icon name="check-decagram" size={28} color={Colors.primary} />
                        <Text style={styles.endMarkerTitle}>Akhir Dokumen Kebijakan Privasi</Text>
                        <Text style={styles.endMarkerSub}>Anda telah membaca seluruh ketentuan. Klik tombol di bawah untuk menyetujui.</Text>
                    </View>
                </ScrollView>

                {/* Footer with Agree Action Button */}
                <View style={styles.footer}>
                    <Pressable
                        onPress={handleConfirmAgree}
                        disabled={!hasScrolledToBottom}
                        style={[
                            styles.agreeButton,
                            hasScrolledToBottom ? styles.agreeButtonActive : styles.agreeButtonDisabled
                        ]}
                    >
                        <Icon
                            name="check-circle"
                            size={20}
                            color="#fff"
                            style={{ marginRight: 8 }}
                        />
                        <Text style={styles.agreeButtonText}>
                            {hasScrolledToBottom ? "Saya Telah Membaca & Menyetujui" : "Scroll ke Bawah untuk Menyetujui"}
                        </Text>
                    </Pressable>
                </View>
            </SafeAreaView>
        </Modal>
    )
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        backgroundColor: "#FFFFFF",
    },
    header: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "space-between",
        paddingHorizontal: 16,
        paddingVertical: 12,
        borderBottomWidth: 1,
        borderBottomColor: "#E2E8F0",
    },
    headerTitleContainer: {
        flexDirection: "row",
        alignItems: "center",
    },
    headerTitle: {
        fontSize: 16,
        fontWeight: "bold",
        color: "#0F172A",
    },
    headerSubtitle: {
        fontSize: 11,
        color: "#64748B",
    },
    closeButton: {
        padding: 6,
        borderRadius: 8,
        backgroundColor: "#F1F5F9",
    },
    banner: {
        flexDirection: "row",
        alignItems: "center",
        paddingHorizontal: 14,
        paddingVertical: 10,
        borderBottomWidth: 1,
    },
    bannerWarning: {
        backgroundColor: "#FEF3C7",
        borderBottomColor: "#FDE68A",
    },
    bannerSuccess: {
        backgroundColor: "#DCFCE7",
        borderBottomColor: "#BBF7D0",
    },
    bannerText: {
        flex: 1,
        fontSize: 11,
        marginLeft: 8,
    },
    bannerTextWarning: {
        color: "#92400E",
    },
    bannerTextSuccess: {
        color: "#166534",
        fontWeight: "600",
    },
    percentageText: {
        fontSize: 11,
        fontWeight: "bold",
        color: "#475569",
        marginLeft: 6,
    },
    scrollView: {
        flex: 1,
    },
    scrollContent: {
        padding: 16,
        paddingBottom: 30,
    },
    metaCard: {
        backgroundColor: "#F8FAFC",
        padding: 10,
        borderRadius: 8,
        marginBottom: 14,
        borderWidth: 1,
        borderColor: "#E2E8F0",
    },
    metaText: {
        fontSize: 11,
        color: "#64748B",
    },
    sectionTitle: {
        fontSize: 15,
        fontWeight: "bold",
        color: "#0F172A",
        marginTop: 18,
        marginBottom: 6,
    },
    subSectionTitle: {
        fontSize: 13,
        fontWeight: "bold",
        color: "#334155",
        marginTop: 10,
        marginBottom: 4,
    },
    paragraph: {
        fontSize: 12,
        lineHeight: 18,
        color: "#334155",
        marginBottom: 8,
    },
    bold: {
        fontWeight: "bold",
        color: "#0F172A",
    },
    link: {
        color: "#2563EB",
        textDecorationLine: "underline",
    },
    bulletItem: {
        fontSize: 12,
        lineHeight: 18,
        color: "#334155",
        marginBottom: 4,
        paddingLeft: 6,
    },
    infoBox: {
        backgroundColor: "#F1F5F9",
        padding: 10,
        borderRadius: 8,
        marginVertical: 6,
    },
    infoItem: {
        fontSize: 12,
        color: "#334155",
        marginBottom: 3,
    },
    highlightBox: {
        backgroundColor: "#ECFDF5",
        padding: 10,
        borderRadius: 8,
        borderWidth: 1,
        borderColor: "#A7F3D0",
        marginVertical: 8,
    },
    highlightText: {
        fontSize: 12,
        lineHeight: 18,
        color: "#065F46",
    },
    endMarker: {
        alignItems: "center",
        justifyContent: "center",
        backgroundColor: "#F0FDF4",
        padding: 16,
        borderRadius: 12,
        borderWidth: 1,
        borderColor: "#BBF7D0",
        marginTop: 20,
    },
    endMarkerTitle: {
        fontSize: 13,
        fontWeight: "bold",
        color: "#166534",
        marginTop: 6,
    },
    endMarkerSub: {
        fontSize: 11,
        color: "#64748B",
        textAlign: "center",
        marginTop: 2,
    },
    footer: {
        padding: 14,
        borderTopWidth: 1,
        borderTopColor: "#E2E8F0",
        backgroundColor: "#F8FAFC",
    },
    agreeButton: {
        flexDirection: "row",
        alignItems: "center",
        justifyContent: "center",
        paddingVertical: 14,
        borderRadius: 12,
    },
    agreeButtonActive: {
        backgroundColor: Colors.primary,
        shadowColor: Colors.primary,
        shadowOffset: { width: 0, height: 4 },
        shadowOpacity: 0.3,
        shadowRadius: 6,
        elevation: 4,
    },
    agreeButtonDisabled: {
        backgroundColor: "#94A3B8",
    },
    agreeButtonText: {
        fontSize: 13,
        fontWeight: "bold",
        color: "#FFFFFF",
    },
})
