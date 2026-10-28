"use client"

import React, { useEffect, useRef } from "react"
import {
  Animated,
  Pressable,
  StyleSheet,
  Text,
  View,
  useWindowDimensions,
  Platform,
} from "react-native"
import { Colors } from "../settings/Colors"
import { useSafeAreaInsets } from "react-native-safe-area-context"
import { moderateScale, horizontalScale, verticalScale } from "../utils/responsive"
import { useTheme } from "../theme/ThemeContext"

export type TabItem = {
  key: string
  label?: string
  icon?: React.ReactNode
  badgeCount?: number
}

type Props = {
  items: TabItem[]
  activeKey: string
  onChange: (key: string) => void
  themeColor?: string
}

// Default Indonesian human-friendly labels for all ages
const DEFAULT_LABELS: Record<string, string> = {
  ringkasan: "Ringkasan",
  dashboard: "Dasbor",
  olahraga: "Aktivitas",
  vital: "Vitalitas",
  kesehatan: "Kesehatan",
  hemoglobin: "Hb",
  konseling: "Konseling",
  divisi: "Divisi",
  notifikasi: "Notifikasi",
  peminjaman: "Pita UKS",
  warning: "Peringatan",
  profil: "Profil",
}

export default function AnimatedBottomTabs({ items, activeKey, onChange, themeColor }: Props) {
  const insets = useSafeAreaInsets()
  const { width } = useWindowDimensions()
  const isTabletOrLandscape = width >= 600
  const isSmall = width < 360
  const { theme, themeId, isDark } = useTheme()

  const isAtlas = themeId === 'atlas_modern'
  const primaryColor = isAtlas ? (isDark ? '#22D3EE' : '#4F46E5') : (themeColor || theme.primary || Colors.primary)
  const bottomSafe = Math.max(insets.bottom, Platform.OS === 'ios' ? 16 : 8)

  return (
    <View pointerEvents="box-none" style={styles.outerWrapper}>
      <View
        style={[
          styles.container,
          isAtlas ? {
            // ATLAS NEO MODERN: Floating Cyber-Dock
            marginHorizontal: horizontalScale(16),
            marginBottom: Math.max(bottomSafe - 4, verticalScale(10)),
            borderRadius: moderateScale(28),
            borderWidth: 1.5,
            borderColor: isDark ? 'rgba(99, 102, 241, 0.45)' : '#C7D2FE',
            backgroundColor: isDark ? 'rgba(15, 23, 42, 0.95)' : 'rgba(255, 255, 255, 0.98)',
            shadowColor: '#6366F1',
            shadowOffset: { width: 0, height: 8 },
            shadowOpacity: isDark ? 0.45 : 0.22,
            shadowRadius: 18,
            elevation: 20,
            paddingTop: verticalScale(6),
            paddingBottom: verticalScale(6),
            paddingHorizontal: horizontalScale(6),
          } : {
            // EMERALD CLASSIC: Traditional Clinical Edge-to-Edge Bar
            width: "100%",
            backgroundColor: Colors.white,
            borderTopLeftRadius: moderateScale(22),
            borderTopRightRadius: moderateScale(22),
            borderBottomLeftRadius: 0,
            borderBottomRightRadius: 0,
            borderTopWidth: 1,
            borderTopColor: "#F1F5F9",
            shadowColor: "#0F172A",
            shadowOffset: { width: 0, height: -4 },
            shadowOpacity: 0.08,
            shadowRadius: 12,
            elevation: 16,
            paddingTop: verticalScale(8),
            paddingBottom: bottomSafe,
          },
          {
            maxWidth: isTabletOrLandscape ? 680 : "100%",
            alignSelf: "center",
          },
        ]}
      >
        <View style={styles.tabsRow}>
          {items.map((item) => {
            const active = item.key === activeKey
            const label = item.label || DEFAULT_LABELS[item.key] || item.key

            return (
              <TabButton
                key={item.key}
                item={item}
                label={label}
                active={active}
                onPress={() => onChange(item.key)}
                primaryColor={primaryColor}
                isSmall={isSmall}
                isTabletOrLandscape={isTabletOrLandscape}
                isAtlas={isAtlas}
              />
            )
          })}
        </View>
      </View>
    </View>
  )
}

interface TabButtonProps {
  item: TabItem
  label: string
  active: boolean
  onPress: () => void
  primaryColor: string
  isSmall: boolean
  isTabletOrLandscape: boolean
  isAtlas: boolean
}

function TabButton({
  item,
  label,
  active,
  onPress,
  primaryColor,
  isSmall,
  isTabletOrLandscape,
  isAtlas,
}: TabButtonProps) {
  const scaleAnim = useRef(new Animated.Value(1)).current
  const activeAnim = useRef(new Animated.Value(active ? 1 : 0)).current

  useEffect(() => {
    Animated.spring(activeAnim, {
      toValue: active ? 1 : 0,
      friction: 8,
      tension: 100,
      useNativeDriver: false,
    }).start()
  }, [active])

  const handlePressIn = () => {
    Animated.spring(scaleAnim, {
      toValue: 0.92,
      useNativeDriver: false,
      speed: 50,
      bounciness: 4,
    }).start()
  }

  const handlePressOut = () => {
    Animated.spring(scaleAnim, {
      toValue: 1,
      useNativeDriver: false,
      speed: 40,
      bounciness: 6,
    }).start()
  }

  const activePillBg = activeAnim.interpolate({
    inputRange: [0, 1],
    outputRange: [
      "rgba(241, 245, 249, 0)", 
      isAtlas 
        ? (primaryColor === '#22D3EE' ? "rgba(99, 102, 241, 0.28)" : "rgba(79, 70, 229, 0.12)") 
        : primaryColor + "14"
    ],
  })

  const { theme, isDark } = useTheme()
  const iconTint = active 
    ? (isAtlas ? (isDark ? '#22D3EE' : '#4F46E5') : primaryColor) 
    : (isDark ? theme.textSecondary : Colors.muted)

  return (
    <Pressable
      onPress={onPress}
      onPressIn={handlePressIn}
      onPressOut={handlePressOut}
      style={styles.tabPressable}
      accessibilityRole="tab"
      accessibilityState={{ selected: active }}
      accessibilityLabel={label}
      hitSlop={{ top: 8, bottom: 8, left: 4, right: 4 }}
    >
      <Animated.View
        style={[
          styles.tabButtonInner,
          isAtlas ? {
            borderRadius: moderateScale(16),
            borderWidth: active ? 1.2 : 0,
            borderColor: active ? (isDark ? 'rgba(34, 211, 238, 0.55)' : 'rgba(99, 102, 241, 0.35)') : 'transparent',
            paddingVertical: verticalScale(4),
            minHeight: verticalScale(46),
          } : {
            borderRadius: moderateScale(14),
            paddingVertical: verticalScale(5),
            minHeight: verticalScale(46),
          },
          {
            backgroundColor: activePillBg,
            transform: [{ scale: scaleAnim }],
          },
        ]}
      >
        <View style={styles.iconContainer}>
          {item.icon &&
            React.cloneElement(item.icon as React.ReactElement<any>, {
              color: iconTint,
              size: isSmall ? 20 : isTabletOrLandscape ? 24 : 22,
            })}

          {typeof item.badgeCount === "number" && item.badgeCount > 0 && (
            <View style={styles.badge}>
              <Text style={styles.badgeText}>
                {item.badgeCount > 99 ? "99+" : item.badgeCount}
              </Text>
            </View>
          )}
        </View>

        <Text
          numberOfLines={1}
          style={[
            styles.tabLabel,
            {
              fontSize: isSmall ? moderateScale(9.5) : moderateScale(10.5),
              color: active 
                ? (isAtlas ? (isDark ? '#E2E8F0' : '#312E81') : primaryColor) 
                : (isDark ? theme.textSecondary : Colors.muted),
              fontWeight: active ? (isAtlas ? "800" : "700") : "500",
              letterSpacing: isAtlas ? 0.2 : -0.2,
            },
          ]}
        >
          {label}
        </Text>

        {active && (
          isAtlas ? (
            <View
              style={{
                width: moderateScale(14),
                height: verticalScale(2.5),
                borderRadius: moderateScale(2),
                backgroundColor: isDark ? '#22D3EE' : '#4F46E5',
                marginTop: verticalScale(2),
                shadowColor: isDark ? '#22D3EE' : '#4F46E5',
                shadowOffset: { width: 0, height: 1 },
                shadowOpacity: 0.8,
                shadowRadius: 3,
                elevation: 2,
              }}
            />
          ) : (
            <View
              style={[
                styles.activeDot,
                { backgroundColor: primaryColor },
              ]}
            />
          )
        )}
      </Animated.View>
    </Pressable>
  )
}

const styles = StyleSheet.create({
  outerWrapper: {
    position: "absolute",
    bottom: 0,
    left: 0,
    right: 0,
    zIndex: 9999,
    elevation: 20,
  },
  container: {
    width: "100%",
    backgroundColor: Colors.white,
    borderTopLeftRadius: 22,
    borderTopRightRadius: 22,
    borderTopWidth: 1,
    borderTopColor: "#F1F5F9",
    shadowColor: "#0F172A",
    shadowOffset: { width: 0, height: -4 },
    shadowOpacity: 0.08,
    shadowRadius: 12,
    elevation: 16,
    paddingTop: 8,
  },
  tabsRow: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-around",
    paddingHorizontal: 6,
  },
  tabPressable: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
  },
  tabButtonInner: {
    width: "92%",
    alignItems: "center",
    justifyContent: "center",
    paddingVertical: 5,
    borderRadius: 14,
    minHeight: 46,
  },
  iconContainer: {
    position: "relative",
    alignItems: "center",
    justifyContent: "center",
    height: 24,
    marginBottom: 2,
  },
  tabLabel: {
    textAlign: "center",
    letterSpacing: -0.2,
  },
  activeDot: {
    width: 4,
    height: 4,
    borderRadius: 2,
    marginTop: 2,
  },
  badge: {
    position: "absolute",
    top: -4,
    right: -10,
    backgroundColor: Colors.error,
    borderRadius: 10,
    minWidth: 16,
    height: 16,
    alignItems: "center",
    justifyContent: "center",
    paddingHorizontal: 3,
    borderWidth: 1.5,
    borderColor: Colors.white,
  },
  badgeText: {
    color: Colors.white,
    fontSize: 9,
    fontWeight: "700",
  },
})