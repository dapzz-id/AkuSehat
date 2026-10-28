"use client"

export const Colors = {
    primary: "#059669",
    accent: "#14B8A6",
    bg: "#F8FAFC",
    text: "#0F172A",
    border: "#E2E8F0",
    muted: "#64748b",
    // Palet Utama Sistem:
    // 1. Primer: Putih (#FFFFFF / surface)
    // 2. Sekunder: Hijau melambangkan kesehatan (#059669)
    // 3. Tersier: Warna cover divisi (dinamis dikonfigurasi admin instansi)
    white: "#ffffff",
    primaryWhite: "#ffffff",
    secondaryGreen: "#059669",
    defaultTertiary: "#2e7d32",
    
    // Warna sukses - Hijau konfirmasi
    success: '#059669',

    // Warna peringatan - Oranye perhatian
    warning: '#F59E0B',

    // Warna error - Merah darurat medis
    error: '#DC2626',

    // Warna info - Biru informasi
    info: '#3B82F6',

    // Surface colors - untuk kartu dan container
    surface: {
        primary: '#FFFFFF',
        secondary: '#F1F5F9',
        tertiary: '#E2E8F0'
    },

    // Gradient colors untuk efek futuristik
    gradient: {
        start: '#2563EB',
        end: '#10B981'
    }
}

export const isColorDark = (hexColor?: string | null): boolean => {
    if (!hexColor) return false;
    const hex = hexColor.replace('#', '').trim();
    if (hex.length !== 3 && hex.length !== 6) return false;
    const r = parseInt(hex.length === 3 ? hex[0] + hex[0] : hex.substring(0, 2), 16);
    const g = parseInt(hex.length === 3 ? hex[1] + hex[1] : hex.substring(2, 4), 16);
    const b = parseInt(hex.length === 3 ? hex[2] + hex[2] : hex.substring(4, 6), 16);
    if (isNaN(r) || isNaN(g) || isNaN(b)) return false;
    const brightness = (r * 299 + g * 587 + b * 114) / 1000;
    return brightness < 170;
};