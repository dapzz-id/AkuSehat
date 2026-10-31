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
            // ATLAS NEO MODERN: Edge-to-Edge Bar with Top Radius (matching Emerald height)
            width: "100%",
            marginHorizontal: 0,
            marginBottom: 0,
            borderTopLeftRadius: moderateScale(22),
            borderTopRightRadius: moderateScale(22),
            borderBottomLeftRadius: 0,
            borderBottomRightRadius: 0,
            borderTopWidth: 1,
            borderTopColor: isDark ? 'rgba(99, 102, 241, 0.35)' : 'rgba(224, 231, 255, 0.9)',
            borderBottomWidth: 0,
            borderLeftWidth: 0,
            borderRightWidth: 0,
            backgroundColor: isDark ? 'rgba(15, 23, 42, 0.98)' : '#FFFFFF',
            shadowColor: isDark ? '#000000' : '#4F46E5',
            shadowOffset: { width: 0, height: -4 },
            shadowOpacity: isDark ? 0.35 : 0.08,
            shadowRadius: 12,
            elevation: 16,
            paddingTop: verticalScale(8),
            paddingBottom: bottomSafe,
            paddingHorizontal: horizontalScale(8),
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
            maxWidth: isTabletOrLandscape ? 640 : "100%",
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
                itemsCount={items.length}
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
  itemsCount: number
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
  itemsCount,
}: TabButtonProps) {
  const scaleAnim = useRef(new Animated.Value(1)).current
  const activeAnim = useRef(new Animated.Value(active ? 1 : 0)).current

  useEffect(() => {
    Animated.spring(activeAnim, {
      toValue: active ? 1 : 0,
      friction: 8,
      tension: 110,
      useNativeDriver: false,
    }).start()
  }, [active])

  const handlePressIn = () => {
    Animated.spring(scaleAnim, {
      toValue: 0.94,
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

  const { theme, isDark } = useTheme()

  const activePillBg = activeAnim.interpolate({
    inputRange: [0, 1],
    outputRange: [
      "rgba(241, 245, 249, 0)", 
      isAtlas 
        ? (isDark ? "rgba(34, 211, 238, 0.16)" : "rgba(79, 70, 229, 0.10)") 
        : primaryColor + "14"
    ],
  })

  const iconTint = active 
    ? (isAtlas ? (isDark ? '#22D3EE' : primaryColor) : primaryColor) 
    : (isDark ? theme.textSecondary : "#94A3B8")

  // --- ATLAS NEO MODERN: EXPANDING CAPSULE PILL DESIGN (IMAGE 3) ---
  if (isAtlas) {
    const activeFlex = itemsCount > 4 ? 1.45 : 1.7
    return (
      <Pressable
        onPress={onPress}
        onPressIn={handlePressIn}
        onPressOut={handlePressOut}
        style={[
          styles.tabPressableAtlas,
          { flex: active ? activeFlex : 1 }
        ]}
        accessibilityRole="tab"
        accessibilityState={{ selected: active }}
        accessibilityLabel={label}
        hitSlop={{ top: 8, bottom: 8, left: 4, right: 4 }}
      >
        <Animated.View
          style={[
            styles.tabButtonInnerAtlas,
            {
              backgroundColor: activePillBg,
              transform: [{ scale: scaleAnim }],
              paddingHorizontal: active ? horizontalScale(isSmall ? 8 : 12) : 0,
            },
          ]}
        >
          <View style={styles.iconContainerAtlas}>
            {item.icon &&
              React.cloneElement(item.icon as React.ReactElement<any>, {
                color: iconTint,
                size: isSmall ? 19 : isTabletOrLandscape ? 23 : 21,
              })}

            {typeof item.badgeCount === "number" && item.badgeCount > 0 && (
              <View style={[styles.badge, styles.badgeAtlas]}>
                <Text style={styles.badgeText}>
                  {item.badgeCount > 99 ? "99+" : item.badgeCount}
                </Text>
              </View>
            )}
          </View>

          {active && (
            <Animated.View
              style={{
                opacity: activeAnim,
                transform: [
                  {
                    scale: activeAnim.interpolate({
                      inputRange: [0, 1],
                      outputRange: [0.85, 1],
                    }),
                  },
                ],
                marginLeft: horizontalScale(6),
                flexShrink: 1,
              }}
            >
              <Text
                numberOfLines={1}
                ellipsizeMode="tail"
                style={{
                  fontSize: isSmall ? moderateScale(11) : moderateScale(12),
                  color: isDark ? '#E2E8F0' : primaryColor,
                  fontWeight: "700",
                  letterSpacing: -0.2,
                }}
              >
                {label}
              </Text>
            </Animated.View>
          )}
        </Animated.View>
      </Pressable>
    )
  }

  // --- EMERALD CLASSIC: TRADITIONAL CLINICAL DESIGN ---
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
          {
            borderRadius: moderateScale(14),
            paddingVertical: verticalScale(5),
            minHeight: verticalScale(46),
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
              color: active ? primaryColor : Colors.muted,
              fontWeight: active ? "700" : "500",
              letterSpacing: -0.2,
            },
          ]}
        >
          {label}
        </Text>

        {active && (
          <View
            style={[
              styles.activeDot,
              { backgroundColor: primaryColor },
            ]}
          />
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
    justifyContent: "space-between",
    paddingHorizontal: 4,
  },
  // Classic Tab Styles
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
  // Atlas Neo Modern Tab Styles
  tabPressableAtlas: {
    alignItems: "center",
    justifyContent: "center",
  },
  tabButtonInnerAtlas: {
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "center",
    borderRadius: moderateScale(22),
    height: verticalScale(40),
    minWidth: moderateScale(42),
  },
  iconContainerAtlas: {
    position: "relative",
    alignItems: "center",
    justifyContent: "center",
    width: 24,
    height: 24,
  },
  badgeAtlas: {
    top: -6,
    right: -8,
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
