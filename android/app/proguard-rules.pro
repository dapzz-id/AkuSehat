# React Native Official ProGuard / R8 Optimization Rules
# Optimized for Google Play Vitals DEX Optimization & Shrinking (>25%)

# ==========================================
# 1. APPLICATION & CUSTOM MODULES
# ==========================================
-keep class neuracakrawira.asia.akusehat.** { *; }

# ==========================================
# 2. REACT NATIVE CORE & BRIDGE
# ==========================================
-keep class com.facebook.react.bridge.NativeModule { *; }
-keep class com.facebook.react.bridge.JavaScriptModule { *; }
-keep class * extends com.facebook.react.bridge.NativeModule
-keep class * extends com.facebook.react.bridge.JavaScriptModule
-keep class * extends com.facebook.react.uimanager.ViewManager
-keep class * extends com.facebook.react.uimanager.ReactShadowNode
-keep class com.facebook.react.bridge.CatalystInstanceImpl { *; }
-keep class com.facebook.react.turbomodule.** { *; }
-keep class com.facebook.react.fabric.** { *; }
-keep class com.facebook.react.uimanager.** { *; }

-dontwarn com.facebook.react.**

# ==========================================
# 3. JNI (NATIVE C++ BINDINGS)
# ==========================================
-keep class com.facebook.jni.** { *; }
-keepclasseswithmembernames class * {
    native <methods>;
}

# ==========================================
# 4. HERMES ENGINE
# ==========================================
-dontwarn com.facebook.hermes.**
-keep class com.facebook.hermes.unicode.** { *; }

# ==========================================
# 5. GOOGLE PLAY SERVICES & PLAY INTEGRITY
# ==========================================
-dontwarn com.google.android.play.core.**
-dontwarn com.google.android.gms.**
-keep class com.google.android.gms.auth.api.signin.** { *; }
-keep class com.google.android.play.core.integrity.** { *; }

# ==========================================
# 6. ANNOTATIONS & REFLECTION SAFETY
# ==========================================
-keepattributes *Annotation*
-keepattributes Signature
-keepattributes InnerClasses
-keepattributes EnclosingMethod

-keepclassmembers enum * { *; }
-keepclassmembers class * {
    @com.facebook.react.bridge.ReactMethod <methods>;
    @com.facebook.react.bridge.ReactMethodSync <methods>;
}

# ==========================================
# 7. SUPPRESS OPTIONAL THIRD-PARTY WARNINGS
# ==========================================
-dontwarn okhttp3.**
-dontwarn okio.**
-dontwarn androidx.**
-dontwarn kotlin.**
