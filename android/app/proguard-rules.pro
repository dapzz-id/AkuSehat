# For more details, see
#   http://developer.android.com/guide/developing/tools/proguard.html


############ REACT NATIVE CORE ############
# Jaga semua class inti React Native
-keep class com.facebook.** { *; }
-keep class com.facebook.react.** { *; }
-keep class com.facebook.hermes.** { *; }
-keep class com.facebook.jni.** { *; }
-keep class com.facebook.soloader.** { *; }

# TurboModules & Fabric
-keep class com.facebook.react.turbomodule.** { *; }
-keep class com.facebook.react.fabric.** { *; }

-dontwarn com.facebook.react.**

# React Native bridge
-keep class * extends com.facebook.react.bridge.JavaScriptModule { *; }
-keep class * extends com.facebook.react.bridge.NativeModule { *; }
-keep class com.facebook.react.bridge.CatalystInstanceImpl { *; }


############ HERMES ENGINE ############
-dontwarn com.facebook.hermes.**
-keep class com.facebook.hermes.** { *; }


############ OKHTTP (React Native Networking) ############
-dontwarn okhttp3.**
-keep class okhttp3.** { *; }
-keep interface okhttp3.** { *; }
-keep class okio.** { *; }


############ GLIDE (React Native Image) ############
-dontwarn com.bumptech.glide.**
-keep class com.bumptech.glide.** { *; }

# Generated API
-keep class com.bumptech.glide.GeneratedAppGlideModule { *; }
-keep class * extends com.bumptech.glide.AppGlideModule { *; }
-keep public class * implements com.bumptech.glide.module.GlideModule { *; }


############ ANDROIDX ############
-dontwarn androidx.**
-keep class androidx.** { *; }


############ KOTLIN SUPPORT ############
-dontwarn kotlin.**
-keep class kotlin.** { *; }
-keepclassmembers class kotlin.Metadata { *; }


############ ASYNC STORAGE ############
-keep class com.reactnativecommunity.asyncstorage.** { *; }


############ SAFE AREA CONTEXT ############
-keep class com.th3rdwave.safeareacontext.** { *; }


############ VECTOR ICONS ############
-keep class com.oblador.vectoricons.** { *; }


############ BLUR VIEW ############
-keep class com.reactnativecommunity.blurview.** { *; }


############ MISC ############
# Jangan hilangkan anotasi
-keepattributes *Annotation*

# Untuk mencegah crash soal enum
-keepclassmembers enum * { *; }

# JNI (native) methods
-keepclasseswithmembernames class * {
    native <methods>;
}