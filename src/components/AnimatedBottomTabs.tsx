"use client"

import React, { useEffect, useRef, useState } from "react"
import {
  Animated,
  Pressable,
  StyleSheet,
  Text,
  View,
  useWindowDimensions,
  Dimensions,
} from "react-native"
import { Colors } from "../settings/Colors"
import { SafeAreaView, useSafeAreaInsets } from "react-native-safe-area-context"

export type TabItem = { key: string; icon?: React.ReactNode }

type Props = {
  items: TabItem[]
  activeKey: string
  onChange: (key: string) => void
}

const { width: SCREEN_WIDTH, height: SCREEN_HEIGHT } = Dimensions.get('window');
const guidelineBaseWidth = 375;

const horizontalScale = (size: number) => (SCREEN_WIDTH / guidelineBaseWidth) * size;
const moderateScale = (size: number, factor = 0.5) => size + (horizontalScale(size) - size) * factor;

export default function AnimatedBottomTabs({ items, activeKey, onChange }: Props) {
  const [expanded, setExpanded] = useState(false)
  const slideAnim = useRef(new Animated.Value(0)).current
  const scaleAnim = useRef(new Animated.Value(1)).current

  const insets = useSafeAreaInsets()
  const { width, height } = useWindowDimensions()
  const isLandscape = width > height

  const bottomSafe = insets.bottom > 0 ? insets.bottom : 10
  const collapsedHeight = 60

  const expandedHeight = isLandscape
    ? collapsedHeight + 90 + bottomSafe
    : collapsedHeight + 100 + bottomSafe

  const translateY = slideAnim.interpolate({
    inputRange: [0, 1],
    outputRange: [expandedHeight - collapsedHeight, 0],
  })

  useEffect(() => {
    Animated.spring(slideAnim, {
      toValue: expanded ? 1 : 0,
      useNativeDriver: true,
      friction: 8,
      tension: 100,
    }).start()
  }, [expanded])

  const toggleExpanded = () => setExpanded((prev) => !prev)

  const handleItemPress = (key: string) => {
    Animated.sequence([
      Animated.timing(scaleAnim, { toValue: 0.92, duration: 100, useNativeDriver: true }),
      Animated.timing(scaleAnim, { toValue: 1, duration: 150, useNativeDriver: true }),
    ]).start()
    onChange(key)
  }

  return (
    <SafeAreaView
      edges={["bottom"]}
      style={{
        position: "absolute",
        bottom: 0,
        left: 0,
        right: 0,
      }}
    >
      <Animated.View
        style={[
          styles.container,
          {
            height: expandedHeight,
            paddingBottom: bottomSafe + 4,
            transform: [{ translateY }],
          },
        ]}
      >
        <Pressable onPress={toggleExpanded} style={styles.handle}>
          <View style={styles.handleBar} />
          <Text style={styles.handleHint}>{expanded ? "Tutup" : "Menu"}</Text>
        </Pressable>

        {/* Menu grid */}
        {expanded && (
          <View
            style={[
              styles.menuGrid,
              {
                width: "100%",
                paddingHorizontal: isLandscape ? 28 : 16,
                justifyContent: "space-between",
              },
            ]}
          >
            {items.map((item) => {
              const active = item.key === activeKey
              return (
                <Pressable
                  key={item.key}
                  onPress={() => handleItemPress(item.key)}
                  style={({ pressed }) => [
                    styles.menuItem,
                    active && styles.menuItemActive,
                    pressed && styles.menuItemPressed,
                    {
                      flex: 1,
                      marginHorizontal: isLandscape ? 6 : 4,
                      aspectRatio: 1,
                      maxWidth: isLandscape ? 90 : undefined,
                    },
                  ]}
                  accessibilityRole="tab"
                  accessibilityState={{ selected: active }}
                >
                  <Animated.View
                    style={[
                      styles.menuItemInner,
                      active && { transform: [{ scale: scaleAnim }] },
                    ]}
                  >
                    {item.icon && (
                      <View style={[
                        styles.menuItemIcon
                      ]}>
                        {React.cloneElement(item.icon as React.ReactElement<any>, {
                          color: active ? Colors.white : Colors.text,
                          size: isLandscape ? 22 : 22,
                        })}
                      </View>
                    )}
                    
                  </Animated.View>
                </Pressable>
              )
            })}
          </View>
        )}
      </Animated.View>
    </SafeAreaView>
  )
}

const styles = StyleSheet.create({
  container: {
    backgroundColor: Colors.white,
    borderTopLeftRadius: 24,
    borderTopRightRadius: 24,
    shadowColor: "#000",
    shadowOffset: { width: 0, height: -4 },
    shadowOpacity: 0.15,
    shadowRadius: 12,
    elevation: 16,
    overflow: "hidden",
  },
  handle: {
    height: 60,
    alignItems: "center",
    justifyContent: "center",
    paddingTop: 12,
  },
  handleBar: {
    width: 40,
    height: 4,
    borderRadius: 2,
    backgroundColor: Colors.border,
    marginBottom: 8,
  },
  handleHint: {
    fontSize: 12,
    fontWeight: "600",
    color: Colors.muted,
  },
  menuGrid: {
    flexDirection: "row",
    alignItems: "center",
  },
  menuItem: {
    backgroundColor: Colors.bg,
    borderRadius: 16,
    alignItems: "center",
    justifyContent: "center",
    borderWidth: 2,
    borderColor: "transparent",
  },
  menuItemActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.accent,
  },
  menuItemPressed: {
    opacity: 0.8,
  },
  menuItemInner: {
    flex: 1,
    alignItems: "center",
    justifyContent: "center",
    padding: 10,
  },
  menuItemIcon: {
    alignItems: "center",
    justifyContent: "center",
    marginBottom: 8,
  },
  menuItemLabel: {
    fontSize: 13,
    fontWeight: "600",
    color: Colors.text,
    textAlign: "center",
  },
  menuItemLabelActive: {
    color: Colors.white,
  },
  menuItemLabelLandscape: {
    fontSize: 13,
    fontWeight: "600",
  },
})